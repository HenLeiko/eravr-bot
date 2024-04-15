<?php

namespace App\Telegram\Models;

use App\Models\TelegramUser;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;
use Telegram\Bot\BotsManager;
use Telegram\Bot\Laravel\Facades\Telegram;
use function Pest\Laravel\get;

class CountEventModel
{
    protected string $date;
    protected BotsManager $botsManager;
    protected array $countEvents;
    protected array $calendars = ['ou86tdr91vgt3orsdtsnbu31r8@group.calendar.google.com', '56vb14ndiolefkv6b88h4urrfc@group.calendar.google.com', 'eravrr@gmail.com'];
    protected array $events;

    public function eventCounter(): void
    {
        $this->date = Carbon::createFromFormat('d.m.y H:i', Telegram::getWebhookUpdate()->message->text);
        $eventsMolodega = Event::get(Carbon::parse($this->date), Carbon::today(), [], 'ou86tdr91vgt3orsdtsnbu31r8@group.calendar.google.com');
        $eventsSelega = Event::get(Carbon::parse($this->date), Carbon::today(), [], '56vb14ndiolefkv6b88h4urrfc@group.calendar.google.com');
        $eventsBelyaevo = Event::get(Carbon::parse($this->date), Carbon::today(), [], 'eravrr@gmail.com');
        $this->iterationCount($eventsMolodega);
        $this->iterationCount($eventsSelega);
        $this->iterationCount($eventsBelyaevo);
        $this->sendResponse();
        TelegramUser::where('user_id', '=', Telegram::getWebhookUpdate()->message->chat->id)->update(['status' => 'none']);
        // TODO: добавить выгрузку в таблицу
    }

    private function sendResponse()
    {
        Telegram::sendChatAction([
            'chat_id' => Telegram::getWebhookUpdate()->message->chat->id,
            'action' => 'typing'
        ]);
        foreach ($this->countEvents as $key => $event) {
            $response = Telegram::sendMessage([
                'chat_id' => Telegram::getWebhookUpdate()->message->chat->id,
                'text' => $key . ' создал ' . $event . ' записей.'
            ]);
        }
    }

    private function iterationCount($events): void
    {
        foreach ($events as $event) {
            if (property_exists($event->googleEvent, 'creator')) {
                if (!isset($this->countEvents[$event->creator->email])) {
                    $this->countEvents[$event->creator->email] = 0;
                }
                $this->countEvents[$event->creator->email] += 1;
            }
        }
    }
}
