<?php

namespace App\Telegram\Command;

use App\Models\TelegramUser;
use Carbon\Carbon;
use phpseclib3\Crypt\EC\Formats\Keys\JWK;
use Spatie\GoogleCalendar\Event;
use Telegram\Bot\Commands\Command;

class CountEventCommand extends Command
{
    protected string $name = 'count_events';
    protected string $description = 'Подсчитать кол-во записей созданных админами за определённый периуд';

    /**
     * @return void
     */
    public function handle(): void
    {
        $this->replyWithMessage([
            'text' => "Укажите дату и время с которых начинать сбор статистики, пример: 16.04.24 5:00 \n \nПримичание: чтобы отобразились все записи в указанный начальный день, указывайте время 5:00",
        ]);
        $user = TelegramUser::where('user_id', '=', $this->getUpdate()->message->from->id)->update(['status' => 'select_date']);
    }
}
