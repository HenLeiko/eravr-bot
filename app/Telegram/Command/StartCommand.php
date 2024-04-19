<?php

namespace App\Telegram\Command;

use App\Models\TelegramUser;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Запуск / перезагрузка бота';

    public function handle(): void
    {
        $update = Telegram::getWebhookUpdate()->message;
        $user = TelegramUser::where('user_id', '=', $update->from->id)->first();
        if (!$user) {
            $this->createNewUser($update->from);
        }

        $keyboard = [
            ['Создать сертификат'],
            ['Создать абонемент'],
            ['Создать приглашение'],
            ['Подсчёт созданых записей админами'],
        ];

        $reply_markup = Keyboard::make([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);

        $this->replyWithMessage([
            'text' => 'Добро пожаловать в бота EraVR!',
            'reply_markup' => $reply_markup,
        ]);
    }

    /**
     * Запись пользователя в базу
     *
     * @param $update
     * @return void
     */
    protected function createNewUser($update): void
    {
        TelegramUser::updateOrCreate([
            'user_id' => $update->id,
            'is_bot' => $update->is_bot,
            'first_name' => $update->first_name,
            'last_name' => $update->last_name,
            'username' => $update->username,
            'status' => 'none',
            'language_code' => $update->language_code,
        ]);
    }
}
