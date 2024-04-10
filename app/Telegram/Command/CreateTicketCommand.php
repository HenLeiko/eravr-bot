<?php

namespace App\Telegram\Command;

use Telegram\Bot\Commands\Command;

class CreateTicketCommand extends Command
{
    protected string $name = 'create_ticket';
    protected string $description = 'Создать абонемент';
    public function handle()
    {
        // TODO: Implement handle() method.
    }
}
