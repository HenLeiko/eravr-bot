<?php

namespace App\Telegram\Models;

use App\Models\TelegramUser;
use Carbon\Carbon;
use DigitalStars\Sheets\DSheets;
use Spatie\GoogleCalendar\Event;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class CountEventModel
{
    protected string $date;
    protected array $countEvents;
    protected array $calendars = ['ou86tdr91vgt3orsdtsnbu31r8@group.calendar.google.com', '56vb14ndiolefkv6b88h4urrfc@group.calendar.google.com', 'eravrr@gmail.com'];
    protected array $events;

    public function eventCounter(): void
    {
        $this->date = Carbon::createFromFormat('d.m.y H:i', Telegram::getWebhookUpdate()->message->text);
        $eventsMolodega = Event::get(Carbon::parse($this->date), Carbon::now(), [], 'ou86tdr91vgt3orsdtsnbu31r8@group.calendar.google.com');
        $eventsSelega = Event::get(Carbon::parse($this->date), Carbon::now(), [], '56vb14ndiolefkv6b88h4urrfc@group.calendar.google.com');
        $eventsBelyaevo = Event::get(Carbon::parse($this->date), Carbon::now(), [], 'eravrr@gmail.com');
        $this->iterationCount($eventsMolodega);
        $this->iterationCount($eventsSelega);
        $this->iterationCount($eventsBelyaevo);
        $this->appendGoogleTable($eventsBelyaevo, 'Беляево');
        $this->appendGoogleTable($eventsSelega, 'Селигерская');
        $this->appendGoogleTable($eventsMolodega, 'Молодёжная');
        $this->sendResponse();
        TelegramUser::where('user_id', '=', Telegram::getWebhookUpdate()->message->chat->id)->update(['status' => 'none']);
    }

    private function sendResponse(): Message
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
        $response = Telegram::sendMessage([
            'chat_id' => Telegram::getWebhookUpdate()->message->chat->id,
            'text' => "Все данные успешно загружены в таблицу!\nСсылка: https://docs.google.com/spreadsheets/d/10EyxVi9MHMwTpQS6oW21D66FEJcnqE6tsaAAqaz0WYk/edit#gid=128871197\n\nВсегда сверяйтесь с таблицей, так как иногда бывают технические записи!"
        ]);
        return $response;
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

    private function appendGoogleTable($events, $club): void
    {
        $spreadsheet_id = '10EyxVi9MHMwTpQS6oW21D66FEJcnqE6tsaAAqaz0WYk';
        $config_path = storage_path('app/google-calendar/calendar-419415-3bb51b0d7788.json');
        $sheet = DSheets::create($spreadsheet_id, $config_path)->setSheet('Лист2');
        $values = array();
        foreach ($events as $event) {
            // this "if" need for fix bug when Event doesn't have property "creator"
            if (isset($event->googleEvent->creator)) {
                $values[] = [
                    $event->googleEvent->summary,
                    $event->googleEvent->creator->email,
                    $club,
                    Carbon::parse($event->googleEvent->created)->format('d.m.Y H:i'),
                    Carbon::parse($event->googleEvent->start->dateTime)->format('d.m.Y H:i'),
                    Carbon::parse($event->googleEvent->end->dateTime)->format('d.m.Y H:i'),
                    $event->googleEvent->id,
                ];
            }
        }
        $sheet->append($values);
        $sheet->append([['⬆ Данная статистика была собрана: ' . Carbon::now()->format('d.m.Y H:i')]]);
    }
}