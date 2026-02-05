<?php

namespace App\Payment;

use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;
//use Illuminate\Support\Facades\Log;

class PaymentClientService
{
    public function paymentInit(int $amount, string $desc): array
    {
        $data = $this->createPaymentObj($amount, $desc);
//        Log::channel('tinkoff')->info('Request data:', ['data' => $data]);
        try {
//            Log::channel('tinkoff')->info('Request to tinkoff api', [
//                'url' => 'v2/Init',
//                'data' => $data,
//                'time' => now()
//            ]);

            $response = Http::asJson()
                ->timeout(30)
                ->post(config('payment.payment.terminal_url'), $data);

//            Log::channel('tinkoff')->info('Response from tinkoff api', [
//                'status' => $response->status(),
//                'headers' => $response->headers(),
//                'body' => $response->body(),
//                'time' => now()
//            ]);

            if ($response->successful()) {
                $body = $response->json();

                if ($body['Success'] === true) {
                    $paymentUrl = $body['PaymentURL'];
                    $paymentId = $body['PaymentId'];
                    $orderId = $body['OrderId'];
                    $responseAmount = $body['Amount'];

                    return [
                        'success' => true,
                        'payment_id' => $paymentId,
                        'payment_url' => $paymentUrl,
                        'order_id' => $orderId,
                        'amount' => $responseAmount,
                    ];
                } else {
                    $errorCode = $body['ErrorCode'];
                    $errorMessage = $body['Message'] ?? 'Ошибка: тело ответа отсутствует';

                    return [
                        'success' => false,
                        'error_code' => $errorCode,
                        'error_message' => $errorMessage
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'error' => 'Ошибка HTTP запроса: ' . $response->status()
                ];
            }
        } catch (RequestException $e) {
            return [
                'success' => false,
                'error' => 'Ошибка подключения: ' . $e->getMessage()
            ];
        }
    }

    private function createPaymentObj(int $amount, string $desc): array
    {
        $orderId = 'VR-' . uniqid() . '_' . Carbon::now()->format('H-i_d-m-y');

        $data = [
            'TerminalKey' => config('payment.payment.terminal_key'),
            'Amount' => $amount,
            'OrderId' => $orderId,
            'Description' => $desc,
            'Receipt' => [
                'FfdVersion' => '1.2',
                'Taxation' => 'usn_income_outcome',
                'Email' => 'testmail@testmail.com',
                'Phone' => '+79999999999',
                'Items' => [[
                    'Name' => $desc,
                    'Price' => $amount,
                    'Quantity' => 1.0,
                    'Amount' => $amount,
                    'PaymentMethod' => 'advance',
                    'PaymentObject' => 'payment',
                    'Tax' => 'none',
                    'MeasurementUnit' => 'раз'
                ]]
            ]
        ];
        $data['Token'] = $this->createToken($data);
        return $data;
    }

    public function createToken($data): string
    {
        if (isset($data['Receipt'])) {
            unset($data['Receipt']);
        }
        $data['Password'] = config('payment.payment.terminal_password');
        ksort($data);
        $token_string = '';
        foreach ($data as $token_item) {
            $token_string .= $token_item;
        }
        return hash('sha256', $token_string);
    }
}
