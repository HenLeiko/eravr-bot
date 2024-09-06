<?php

namespace App\Telegram\Contracts;

use App\Models\UserState;
use Telegram\Bot\Objects\Message;

interface StateHandlerInterface
{

    /**
     * Обработка сообщения и обновление статуса пользователя
     *
     * @param Message $message
     * @param UserState $userState
     * @param array $data
     * @return void
     */
    public function handle(Message $message, UserState $userState, array $data): void;
}
