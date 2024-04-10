<?php

namespace App\Telegram\Command;

use App\Models\TelegramUser;
use Telegram\Bot\Commands\Command;

class CreateCertCommand extends Command
{
    protected string $name = 'create_cert';
    protected string $description = 'Создать сертификат на определённую сумму';
    public function handle()
    {
        $this->replyWithMessage([
            'text' => 'Укажите номинал сертификата, пример: "2000"'
        ]);
        TelegramUser::where('user_id', '=', $this->getUpdate()->message->from->id)->update(['status' => 'add_cert_value']);

        // TODO: Implement handle() method.
    }
}
