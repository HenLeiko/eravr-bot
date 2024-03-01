<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Telegram\Bot\BotsManager;
use Telegram\Bot\Exceptions\TelegramSDKException;

class WebhookController extends Controller
{
    protected BotsManager $botsManager;
    public function __construct(BotsManager $botsManager)
    {
        $this->botsManager = $botsManager;
    }

    /**
     * Ожидание входящего запроса
     *
     * @param Request $request
     * @return Response
     * @throws TelegramSDKException
     */
    public function __invoke(Request $request): Response
    {
        $this->botsManager->bot()->commandsHandler(true);
        return response(null, 200);
    }

}
