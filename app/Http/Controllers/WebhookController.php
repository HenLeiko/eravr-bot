<?php

namespace App\Http\Controllers;

use App\Telegram\Handlers\MessageHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Telegram\Bot\BotsManager;
use Telegram\Bot\Laravel\Facades\Telegram;

class WebhookController extends Controller
{
    protected BotsManager $botsManager;
    protected MessageHandler $messageHandler;
    public function __construct(BotsManager $botsManager)
    {
        $this->botsManager = $botsManager;
        $this->messageHandler = new MessageHandler();
    }

    /**
     * Ожидание входящего запроса
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $update = Telegram::getWebhookUpdate();
        Telegram::commandsHandler(true);
        $this->messageHandler->handle($update);
        return response(null, 200);
    }

}
