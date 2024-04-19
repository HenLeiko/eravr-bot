<?php

namespace App\Telegram\Command;

use App\Models\TelegramUser;
use Illuminate\Support\Facades\DB;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class CreateInviteCommand extends Command
{
    protected string $name = 'create_invite';
    protected string $description = 'Создать приглашение на мероприятие';
    public function handle(): void
    {
        $keyboard = [
            ['Молодёжная'],
            ['Беляево'],
            ['Селигерская'],
        ];

        $reply_markup = Keyboard::make([
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
            'keyboard' => $keyboard
        ]);

        $this->replyWithMessage([
            'text' => 'Чтобы создать приглашение выберите клуб в меню',
            'reply_markup' => $reply_markup,
        ]);
        TelegramUser::where('user_id', '=', Telegram::getWebhookUpdate()->message->from->id)->update(['status' => 'select_club']);
    }
}
