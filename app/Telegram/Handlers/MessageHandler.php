<?php

namespace App\Telegram\Handlers;

use App\Models\UserState;
use App\Services\CommandFactoryService;
use App\Telegram\Interfaces\HandlerInterface;
use Telegram\Bot\Laravel\Facades\Telegram;

class MessageHandler implements HandlerInterface
{
    protected CommandFactoryService $commandFactory;

    public function __construct()
    {
        $this->commandFactory = new CommandFactoryService();
    }

    public function handle(): void
    {
        $message = Telegram::getWebhookUpdate()->message;
        $command = $this->getCommandFromMapping($message);
        $userId = $message->from->id;
        $chatId = $message->chat->id;

        if($command) {
            $commandService = $this->commandFactory->getServiceFromCommand($command);

            if($commandService) {
                $commandService->start($userId, $chatId, $message);
            } else {
                $this->getResponse($chatId, 'Команда не найдена');
            }
        }

        if(!$command) {
            $state = UserState::where('user_id', $userId)->first();
            if($state && $state->state !== 'done') {
                $stateCommand = $state->command;
                $service = $this->commandFactory->getServiceFromCommand($stateCommand);
                if($service) {
                    $service->handle($userId, $chatId, $message);
                }
            } else {
                $this->getResponse($chatId, 'Команды не существует и никаких шагов не запущенно');
            }
        }
    }

    protected function getCommandFromMapping($message)
    {
        $commands = [
            'создать приглашение' => 'create_invite',
            'создать сертификат' => 'create_cert'
        ];

        foreach ($commands as $key => $command) {
            if (mb_stripos($message->text, $key) !== false) {
                return $command;
            } else {
            }
        }
    }
    private function getResponse(int $chatId, String $text): void
    {
        $result = Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $text
        ]);
    }

    public function getException()
    {
        // TODO: Implement getException() method.
    }
}
