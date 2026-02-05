<?php

namespace App\Http\Controllers;

use App\Models\PaymentHistroy;
use App\Payment\PaymentClientService;
use App\Services\Google\GoogleCalendarClient;
use Carbon\Carbon;
use Google_Service_Calendar;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Log;
use Telegram;

class TinkoffWebhookController extends Controller
{
    private GoogleCalendarClient $googleCalendarClient;
    public function __construct(GoogleCalendarClient $googleCalendarClient)
    {
        $this->googleCalendarClient = $googleCalendarClient;
    }

    public function handle(Request $request, PaymentClientService $paymentClientService, GoogleCalendarClient $calendarClient)
    {
        $data = $request->all();
//        Log::channel('tinkoff')->info('FULL WEBHOOK DATA:', [
//            'full_data' => $data,
//            'data_types' => array_map('gettype', $data),
//            'keys' => array_keys($data),
//            'has_receipt' => isset($data['Receipt']),
//            'has_data_field' => isset($data['Data']),
//        ]);
        if (!isset($data)) {
//            Log::channel('tinkoff')->error('Webhook without token', [
//                'ip' => $request->ip(),
//                'data_keys' => array_keys($data),
//            ]);

            return response('Token required', 400);
        }


        $requestToken = $data['Token'];

        $hash = $this->createTokenForWebhook($data);

        if (!hash_equals($hash, $requestToken)) {
//            Log::channel('tinkoff')->error('Invalid webhook signature', [
//                'ip' => $request->ip(),
//                'order_id' => $request['OrderId'] ?? 'None',
//                'request_token' => $requestToken,
//                'signature_token' => $hash,
//                'timestamp' => now(),
//            ]);

            return response('Invalid webhook signature', 403);
        }

        $this->processRequest($data);

        return response('OK', 200);
    }

    private function createTokenForWebhook(array $data): string
    {
        unset($data['Token']);

        $data['Password'] = config('payment.payment.terminal_password');
        ksort($data);

        $stringToHash = '';
        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $stringValue = $value ? 'true' : 'false';
            } elseif ($value === null) {
                $stringValue = '';
            } else {
                $stringValue = (string)$value;
            }

            $stringToHash .= $stringValue;

//            Log::channel('tinkoff')->debug("Hash part $key: '$stringValue' (original: " . json_encode($value) . ")");
        }

        return hash('sha256', $stringToHash);
    }

    private function processRequest($data)
    {
        $paymentHistory = PaymentHistroy::findByOrderId($data['OrderId']);
        switch ($data['Status']) {
            case 'CONFIRMED':
                if (!$paymentHistory) {
//                    Log::channel('tinkoff')->error('Order not founded', [
//                        'Payment:' => $paymentHistory ?? null,
//                        'Data:' => $data ?? null
//                    ]);
                    return response('Order not found', 404);
                }

                $googleClient = $this->googleCalendarClient->client();
                $googleService = new Google_Service_Calendar($googleClient);
                $event = $googleService->events->get($paymentHistory->getCalendarIdAttribute(), $paymentHistory->getCalendarEventIdAttribute());
                $paymentHistory->markAsPaid();

                if (preg_match('/^(тест)(.*)$/', $event->getSummary(), $match)) {
                    $event->setSummary('ПОС' . $match[2]);
                    $googleService->events->update($paymentHistory->getCalendarIdAttribute(), $event->getId(), $event);
                }

                $amount = $data['Amount'] / 100;
                Telegram::bot('eravrsmm')->sendMessage([
                    'chat_id' => '-1002402724986',
                    'message_thread_id' => '4',
                    'text' => 'Заказ оплачен! Клуб ' . $paymentHistory->getClubNameAttribute() .
                        '. Событие ' . Carbon::parse($event->getStart()?->getDateTime())->format('d.m.Y') . ' c ' .
                        Carbon::parse($event->getStart()?->getDateTime())->format('H:i') . ' ПО ' .
                        $amount . ' Вр ' . $paymentHistory->getVrHeadsetsAttribute() . ' шл ' .
                        $paymentHistory->getHoursAttribute() . ' час ' . $paymentHistory->getClientNameAttribute() .
                        ' ' . $paymentHistory->getClientPhoneAttribute()
                ]);
        }
    }
}
