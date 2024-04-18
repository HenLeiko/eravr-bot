<?php

namespace App\Telegram\Command;

use App\Models\TelegramUser;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class CreatePassCommand extends Command
{
    protected string $name = 'create_pass';
    protected string $description = 'Создать абонемент на прохождение';
    public function handle(): void
    {
        $reply_markup = Keyboard::make()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->row([
                Keyboard::button('Half-Life: Alyx')
            ])
            ->row([
                Keyboard::button('Arizona Sunshine')
            ])
            ->row([
                Keyboard::button('Arizona Sunshine 2')
            ])
            ->row([
                Keyboard::button('Указать свою игру')
            ]);
        $this->replyWithMessage([
            'text' => 'Выберите игру для абонимента',
            'reply_markup' => $reply_markup,
        ]);
        TelegramUser::where('user_id', '=', $this->getUpdate()->getMessage()->from->id)->update(['status' => 'select_game']);
    }
}
