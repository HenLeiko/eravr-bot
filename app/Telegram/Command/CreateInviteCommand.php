<?php

namespace App\Telegram\Command;

use Telegram\Bot\Commands\Command;

class CreateInviteCommand extends Command
{
    protected string $name = 'create_invite';
    protected string $description = 'Создать приглашение на мероприятие';
    public function handle()
    {
        // TODO: Implement handle() method.
    }
}
