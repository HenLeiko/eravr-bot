<?php

namespace App\Jobs;

use App\Models\PaymentHistroy;
use App\Services\Google\GoogleCalendarClient;
use Carbon\Carbon;
use Google_Service_Calendar;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Telegram\Bot\Laravel\Facades\Telegram;

class CheckUnpaidRecords implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private GoogleCalendarClient $calendarClient;

    /**
     * Create a new job instance.
     */

    /**
     * Execute the job.
     */
    public function handle(GoogleCalendarClient $calendarClient): void
    {
        $this->calendarClient = $calendarClient;
        $pastHour = now()->subHour();

        $unpaidOrders = PaymentHistroy::where('created_at', '<=', $pastHour)
            ->where('status', '=', 'pending')->get();

        foreach ($unpaidOrders as $unpaidOrder) {
            $unpaidOrder->markAsFailed(408);

            $this->sendTelegramNotification($unpaidOrder);
        }
    }

    private function sendTelegramNotification($unpaidOrder)
    {
        $googleClient = $this->calendarClient->client();
        $googleService = new Google_Service_Calendar($googleClient);
        $event = $googleService->events->get($unpaidOrder->getCalendarIdAttribute(), $unpaidOrder->getCalendarEventIdAttribute());
        $amount = $unpaidOrder->getAmountInRubles();
        $summary = $event->getSummary();
        $event->setSummary($summary . ' - Отказ ПО');
        $googleService->events->update($unpaidOrder->getCalendarIdAttribute(), $event->getId(), $event);

        Telegram::bot('eravrsmm')->sendMessage([
            'chat_id' => '-1002402724986',
            'message_thread_id' => '4',
            'text' =>
                'Заказ НЕ оплачен! Клуб ' . $unpaidOrder->getClubNameAttribute() .
                '. Событие ' . Carbon::parse($event->getStart()?->getDateTime())->format('d.m.Y') . ' c ' .
                Carbon::parse($event->getStart()?->getDateTime())->format('H:i') . ' ПО ' .
                $amount . ' Вр ' . $unpaidOrder->getVrHeadsetsAttribute() . ' шл ' .
                $unpaidOrder->getHoursAttribute() . ' час ' . $unpaidOrder->getClientNameAttribute() .
                ' ' . $unpaidOrder->getClientPhoneAttribute()
        ]);
    }
}
