<?php

namespace App\Http\Controllers;

use App\Models\TelegramUser;
use App\Telegram\Models\CountEventModel;
use App\Telegram\Models\CreateCertModel;
use App\Telegram\Models\CreateInviteModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
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
        @$userData = Telegram::getWebhookUpdate()->message;
        @$userId = $userData->id;
        @$user = TelegramUser::where('user_id' , '=', $userId)->first();
//        @$user = DB::table('telegram_users')->where('user_id', '=', $userId)->first();
        $this->botsManager->bot()->commandsHandler(true);
        $updates = Telegram::getWebhookUpdate();

        $invite = new CreateInviteModel($this->botsManager);
        $certificate = new CreateCertModel($this->botsManager);
        $counter = new CountEventModel($this->botsManager);
        $telegramUser = TelegramUser::get()->where('user_id', '=', Telegram::getWebhookUpdate()->message->from->id)->first();

        // dialog command handler
        switch (@Telegram::getWebhookUpdate()->message->text) {
            case 'Создать сертификат':
                Telegram::getCommandBus()->execute('create_cert', $updates, []);
                break;
            case 'Создать абонемент':
                Telegram::getCommandBus()->execute('create_ticket', $updates, []);
                break;
            case 'Создать приглашение':
                Telegram::getCommandBus()->execute('create_invite', $updates, []);
                break;
            case 'Подсчёт созданых записей админами':
                Telegram::getCommandBus()->execute('count_events', $updates, []);
            default:
        }

        // dialog command params
        switch (@$telegramUser->status) {
            case 'select_club':
                $club = $this->botsManager->bot()->getWebhookUpdate()->message->text;
                if ($club == 'Беляево' || $club == 'Молодёжная' || $club == 'Селигерская') {
                    $invite->selectClub();
                }
                break;
            case 'add_invite_title':
                $invite->setTitle();
                break;
            case 'add_invite_code':
                $invite->setCode();
                break;
            case 'add_cert_value':
                is_numeric($this->botsManager->bot()->getWebhookUpdate()->message->text) ? $certificate->setValue() : $certificate->getException();
                break;
            case 'set_cert_code':
                $certificate->setCode();
                break;
            case 'select_date':
                $counter->eventCounter();
            default:
        }
        return response(null, 200);
    }

}
