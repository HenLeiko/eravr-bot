<?php

namespace App\Services;

use App\Models\TelegramUser;
use Telegram\Bot\Laravel\Facades\Telegram;

class CertService
{
    protected $update;

    public function setValue()
    {
        $certValue = [
            'user_id' => $this->user->id,
        ];
    }
}
