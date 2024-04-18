<?php

namespace App\Telegram\Models;

use App\Models\TelegramUser;
use Illuminate\Database\Eloquent\Builder;
use Telegram\Bot\BotsManager;
use Telegram\Bot\Objects\Update;

class CreatePassModel
{

    protected BotsManager $botManager;
    protected Update $webhookUpdate;
    protected Builder|TelegramUser $user;

    public function __construct(BotsManager $botsManager)
    {
        $this->botManager = $botsManager;
        $this->webhookUpdate = $botsManager->getWebhookUpdate();
        $this->user = TelegramUser::where('user_id', '=', $this->webhookUpdate->message->from->id);
    }

    public function setGame()
    {

    }
}
