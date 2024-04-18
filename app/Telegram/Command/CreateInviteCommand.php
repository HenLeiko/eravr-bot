<?php

namespace App\Telegram\Command;

use Illuminate\Support\Facades\DB;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class CreateInviteCommand extends Command
{
    protected string $name = 'create_invite';
    protected string $description = 'Создать приглашение на мероприятие';
    public function handle(): void
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
        $response = DB::table('telegram_users')->where('user_id', '=', $this->getUpdate()->getMessage()->from->id)->update(['status' => 'select_club']);
    }
}
