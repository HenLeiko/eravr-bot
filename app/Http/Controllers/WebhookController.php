<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Telegram\Bot\BotsManager;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Laravel\Facades\Telegram;

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
        switch ($request['message']['text']) {
            case 'Создать сертификат':
                Telegram::getCommandBus()->execute('create_cert', $this->botsManager->bot()->getWebhookUpdate(), []);
                break;
            case 'Создать абонемент':
                Telegram::getCommandBus()->execute('create_ticket', $this->botsManager->bot()->getWebhookUpdate(), []);
                break;
            case 'Создать приглашение на мероприятие':
                Telegram::getCommandBus()->execute('create_invite', $this->botsManager->bot()->getWebhookUpdate(), []);
                break;
        }
        return response(null, 200);
    }

}
