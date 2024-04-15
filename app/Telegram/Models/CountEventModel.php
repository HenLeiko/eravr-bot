<?php

namespace App\Telegram\Models;

use App\Models\TelegramUser;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;
use Telegram\Bot\BotsManager;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class CountEventModel
{
    protected string $date;
    protected BotsManager $botsManager;


    public function eventCounter(): void
    {
        $this->date = Carbon::createFromFormat('d.m.y H:i', Telegram::getWebhookUpdate()->message->text);
        $eventsMolodega = Event::get(Carbon::parse($this->date), Carbon::today(), [], 'ou86tdr91vgt3orsdtsnbu31r8@group.calendar.google.com');
        $eventsSelega = Event::get(Carbon::parse($this->date), Carbon::today(), [], '56vb14ndiolefkv6b88h4urrfc@group.calendar.google.com');
        $eventsBelyaevo = Event::get(Carbon::parse($this->date), Carbon::today(), [], 'eravrr@gmail.com');
        $countEvents = array();
        foreach ($eventsSelega as $event) {
            if (property_exists($event->googleEvent, 'creator')) {
                if (!isset($countEvents[$event->creator->email])) {
                    $countEvents[$event->creator->email] = 0;
                }
                $countEvents[$event->creator->email] += 1;
            }
        }
        foreach ($eventsBelyaevo as $event) {
            if (property_exists($event->googleEvent, 'creator')) {
                if (!isset($countEvents[$event->creator->email])) {
                    $countEvents[$event->creator->email] = 0;
                }
                $countEvents[$event->creator->email] += 1;
            }
        }
        foreach ($eventsMolodega as $event) {
            if (property_exists($event->googleEvent, 'creator')) {
                if (!isset($countEvents[$event->creator->email])) {
                    $countEvents[$event->creator->email] = 0;
                }
                $countEvents[$event->creator->email] += 1;
            }
        }
        $this->sendResponse($countEvents);
        TelegramUser::where('user_id', '=', Telegram::getWebhookUpdate()->message->chat->id)->update(['status' => 'none']);
        // TODO: добавить выгрузку в таблицу
    }

    private function sendResponse(array $countEvents): Message
    {
        Telegram::sendChatAction([
            'chat_id' => Telegram::getWebhookUpdate()->message->chat->id,
            'action' => 'typing'
        ]);
        foreach ($countEvents as $key => $event) {
            $response = Telegram::sendMessage([
                'chat_id' => Telegram::getWebhookUpdate()->message->chat->id,
                'text' => $key . ' создал ' . $event . ' записей.'
            ]);
        }
        return $response;
    }
}
