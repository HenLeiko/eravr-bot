<?php

namespace App\Telegram\Command;

use App\Models\TelegramUser;
use Illuminate\Support\Facades\DB;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Запуск / перезагрузка бота';
    protected TelegramUser $telegramUser;

    public function __construct(TelegramUser $telegramUser)
    {
        $this->telegramUser = $telegramUser;
    }

    public function handle(): void
    {
        $userData = $this->getUpdate()->message->from;
        $userId = $userData->id;
        $user = DB::table('telegram_users')->where('user_id', '=', $userId)->first();
        if (!$user) {
            $this->createNewUser($userData);
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

    protected function createNewUser($userData): void
    {
        DB::table('telegram_users')->insert([
            'user_id' => $userData->id,
            'is_bot' => $userData->is_bot,
            'first_name' => $userData->first_name,
            'last_name' => $userData->last_name,
            'username' => $userData->username,
            'language_code' => $userData->language_code,
        ]);
    }
}
