<?php

namespace App\Services;

use App\Models\UserState;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

class CountCalendarEventsService
{
    public function start($bot, $userId, $chatId, $message)
    {
        $bot->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Для подсчёта записей админов нужно указать временной промежуток. Для этого укажите точку начала отсчёт.
            Чтобы учитывались все записи в день начала отсчёт укажите время 6:00. Пример "02.12.25 6:00',
        ]);
        $userState = UserState::updateOrCreate([
            'user_id' => $userId,
        ],
            [
                'user_id' => $userId,
                'command' => 'count_calendar_events',
                'state' => 'first_date',
                'data' => [
                    'first_date' => '',
                    'second_date' => '',
                ],
            ]
        );
    }

    public function handle($bot, $userId, $chatId, $message)
    {
        $userState = UserState::where('user_id', $userId)->first();
        $state = $userState->state;

        if (!$userState) {
            return;
        }

        switch ($state) {
            case 'first_date':
                $userState->state = 'second_date';
                $first_date = $this->formDate($message->text);
                $userState->data = array_merge($userState->data ?? [], ['first_date' => $first_date]);
                $userState->save();
                $this->getResponse($bot, $chatId, 'Укажите вторую дату подсчёта. Пример: "31.02.25 23:55"');
                break;
            case 'second_date':
                $second_date = $this->formDate($message->text);
                $userState->data = array_merge($userState->data ?? [], ['second_date' => $second_date]);
                $userState->state = 'done';
                $userState->save();
//                $result = $this->createCertPicture($bot, $userState, $chatId);
//                $result ? $this->getResponse($bot, $chatId, 'Сертификат успешно создан! :)') : $this->getResponse($bot, $chatId, 'Произошла ошибка во время отправки сертификата :(');
                break;
            default: return;
        }
    }

    public function getResponse($bot, $chatId, $message)
    {
        $bot->sendMessage([
            'chat_id' => $chatId,
            'text' => $message,
        ]);
    }

    public function formDate($time)
    {
        $format = 'd.m.y H:i';
        return Carbon::createFromFormat($format, $time, $timezone = 'Europe/Moscow');
    }

    public function getEventsFromInterval($first_date, $second_date)
    {
        $events = Event::get($first_date, $second_date);
        return $events;
    }

//    public function countEvents($events, $chatId, $bot)
//    {
//        $this->getResponse($bot, $chatId, 'Всего создано записей:' . count($events));
//        foreach ($events as $event) {
//
//        }
//    }
}
