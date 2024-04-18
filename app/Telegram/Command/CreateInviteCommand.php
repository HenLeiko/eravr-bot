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
    public function handle(): int|bool
    {
        $reply_markup = Keyboard::make()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->row([
                Keyboard::button('Молодёжная')
            ])
            ->row([
                Keyboard::button('Беляево')
            ])
            ->row([
                Keyboard::button('Селигерская')
            ]);

        $this->replyWithMessage([
            'text' => 'Чтобы создать приглашение выберите клуб в меню',
            'reply_markup' => $reply_markup,
        ]);
        return TelegramUser::where('user_ud', '=', Telegram::getWebhookUpdate()->message->from->id)->update(['status' => 'select_club']);
    }
}
