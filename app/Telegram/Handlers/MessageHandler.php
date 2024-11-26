<?php

namespace App\Telegram\Handlers;

use App\Models\TelegramUser;
use App\Services\CertService;
use App\Telegram\Interfaces\HandlerInterface;
use Telegram\Bot\Laravel\Facades\Telegram;

class MessageHandler implements HandlerInterface
{
    protected CertService $certService;
    protected $user;
    public function __construct()
    {
        $user = TelegramUser::where('user_id', '=', Telegram::getWebhookUpdate()->message->from->id)->first();
        if ($user || !isset(Telegram::getWebhookUpdate()->message->message_thread_id)) {
            $this->user = $user;
        }
        $this->certService = new CertService();
    }

    public function handle(): void
    {
        // TODO: Implement handle() method.
    }

    public function getException()
    {
        // TODO: Implement getException() method.
    }
}
