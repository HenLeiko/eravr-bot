<?php

namespace App\Telegram\Command;

use Telegram\Bot\Commands\Command;

class CreateCertCommand extends Command
{
    protected string $name = 'create_cert';
    protected string $description = 'Создать сертификат на определённую сумму';
    public function handle()
    {
        // TODO: Implement handle() method.
    }
}
