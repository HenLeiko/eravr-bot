<?php

namespace App\Telegram\Command;

use Telegram\Bot\Commands\Command;

class SecondStartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Start a telegram bot';

    public function handle()
    {
        $this->replyWithMessage([
            'text' => 'You are about to start a telegram bot',
        ]);
        // TODO: Implement handle() method.
    }
}
