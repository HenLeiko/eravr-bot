<?php

namespace App\Services;

class StartGiftBotService
{
    public function start($bot, $userId, $chatId, $message)
    {

        $bot->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Вы участвуете в конкурсе!',
        ]);
    }
}
