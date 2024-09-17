<?php

namespace App\Telegram\Contracts;

use Telegram\Bot\Objects\Message;

interface HandleCommanInterface
{
    public function handle(Message $message, string $data, array $context = []): void;
}
