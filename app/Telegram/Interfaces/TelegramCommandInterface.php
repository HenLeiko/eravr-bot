<?php

namespace App\Telegram\Interfaces;

interface TelegramCommandInterface
{
    function start($userId, $chatId, $message);
    function handle($userId, $chatId, $message);
    function getResponse(int $chatId, String $text);
}
