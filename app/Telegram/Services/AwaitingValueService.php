<?php

namespace App\Telegram\Services;

use App\Telegram\Contracts\HandleCommanInterface;
use Telegram\Bot\Objects\Message;

class AwaitingValueService implements HandleCommanInterfacegT
{

    /**
     * @param Message $message
     * @param string $data
     * @param array $context
     * @return void
     */
    public function handle(Message $message, string $data, array $context = []): void
    {
        // TODO: Implement handle() method.
    }
}
