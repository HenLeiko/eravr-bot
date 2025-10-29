<?php

namespace App\Telegram\Handlers;

use App\Models\TelegramChannelMember;
use App\Models\UserState;
use App\Services\CommandFactoryService;
use App\Telegram\Interfaces\HandlerInterface;
use Carbon\Carbon;
use Telegram\Bot\Api;

class MessageHandler implements HandlerInterface
{
    protected CommandFactoryService $commandFactory;
    protected Api $bot;
    protected string $botName;

    public function __construct(Api $bot, string $botName)
    {
        $this->commandFactory = new CommandFactoryService();
        $this->botName = $botName;
        $this->bot = $bot;
    }

    public function handle(): void
    {
        $message = $this->bot->getWebhookUpdate()->message;
        $update = $this->bot->getWebhookUpdate();

//        Обработка событий пользователей в канале/чате
        if ($update->isType('chat_member')) {
            $this->channelHandel($update);
        }

//        Обработка callback query
        if ($update->isType('callback_query')) {
            $this->callbackHandler($update);
        }

//        Обработка команд/сообщений в личных сообщениях
        if ($update->isType('text') || $update->isType('message')) {
            $command = $this->getCommandFromMapping($message);
            $userId = $message->from->id;
            $chatId = $message->chat->id;
            print_r($this->botName);
            if ($command) {
                $commandService = $this->commandFactory->getServiceFromCommand($command);

                if ($commandService) {
                    $commandService->start($this->bot, $userId, $chatId, $message);
                } else {
                    $this->getResponse($chatId, 'Команда не найдена');
                }
            }

            if (!$command) {
                $state = UserState::where('user_id', $userId)->first();
                if ($state && $state->state !== 'done') {
                    $stateCommand = $state->command;
                    $service = $this->commandFactory->getServiceFromCommand($stateCommand);
                    if ($service) {
                        $service->handle($this->bot, $userId, $chatId, $message);
                    }
                } else {
                    $this->getResponse($chatId, 'Команды не существует и никаких шагов не запущенно');
                }
            }
        }
    }

    private function channelHandel($update)
    {
            $newChatMember = $update->chatMember->newChatMember;
            $this->bot->sendMessage([
                'chat_id' => '-1002650080708',
                'text' => 'Пользователь ' . $update->chatMember->newChatMember->user->firstName .
                ' теперь имеет статус ' . $update->chatMember->newChatMember->status,
            ]);
            $telegramChannelMember = TelegramChannelMember::where('user_id', '=', $update->chatMember->from->id)->first();
            if ($telegramChannelMember && $newChatMember->status == 'left' && $telegramChannelMember->is_participating) {
                $telegramChannelMember->status = $newChatMember->status;

                $isNoReffer = <<<HTML
👋 Здравствуйте. <b>Вы отписались от канала во время проведения конкурса.</b> Это означает, что вы больше <b>не участвуете в розыгрыше трёх призов</b>, которые распределяются случайным образом среди всех.
<br>
Если вы ранее приглашали участников по своей реферальной ссылке или кто-то продолжает переходить по ней сейчас, они будут отображаться в вашей статистике, но <b>вы не получите за них бонус</b>.
<br>
➡️ <i>Хотите всё исправить? Просто подпишитесь на наш Telegram-канал снова.</i>
HTML;

                $this->bot->sendMessage([
                    'chat_id' => $update->chatMember->from->id,
                    'text' => $isNoReffer
                ]);
            }

            $telegramChannelMember->is_participating = false;
            $telegramChannelMember->save();
    }

    private function callbackHandler($update)
    {
        $callbackQuery = $update->callbackQuery->data;
            $user = TelegramChannelMember::where('user_id', '=', $update->callbackQuery->from->id)->first();
            list($action, $type, $id) = explode(':', $callbackQuery);
            if ($action == 'get') {
                print_r('action is ready ');
                if ($type == 'referrals') {
                    print_r('method is ready');
                    $refCount = $user->referrals->count();
                    $this->bot->sendMessage([
                        'chat_id' => $update->callbackQuery->message->chat->id,
                        'text' => 'Вы пригласили ' . $refCount . ' человек!'
                    ]);
                    return;
                }
            }
    }
    protected function getCommandFromMapping($message)
    {
        $command_list = config("telegram.bots.{$this->botName}.command_mapping", []);
        foreach ($command_list as $key => $command) {
            if (mb_stripos($message->text, $key) !== false) {
                return $command;
            }
        }
        return null;
    }
    private function getResponse(int $chatId, String $text): void
    {
        $this->bot->sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
        ]);
    }

    public function getException()
    {
        // TODO: Implement getException() method.
    }
}
