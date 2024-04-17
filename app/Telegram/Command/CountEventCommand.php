<?php

namespace App\Telegram\Command;

use App\Models\TelegramUser;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class CountEventCommand extends Command
{
    protected string $name = 'count_events';
    protected string $description = 'Подсчитать кол-во записей созданных админами за определённый периуд';

    /**
     * @return void
     */
    public function handle(): void
    {
        $user = TelegramUser::get()->where('user_id', '=', Telegram::getWebhookUpdate()->message->from->id)->first();
        if ($user->role == 'admin') {
            $this->replyWithMessage([
                'text' => "Укажите дату и время с которых начинать сбор статистики, пример: 16.04.24 5:00 \n \nПримичание: чтобы отобразились все записи в указанный начальный день, указывайте время 5:00",
            ]);
            $user->update(['status' => 'select_date']);
        }
    }
}
