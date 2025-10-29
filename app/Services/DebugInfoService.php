<?php

namespace App\Services;

class DebugInfoService
{
    public function start($bot, $userId, $chatId, $message)
    {
        $bot->sendMessage([
            'chat_id' => $chatId,
            'text' => $message,
        ]);
    }
}
