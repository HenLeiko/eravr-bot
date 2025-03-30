<?php

namespace App\Telegram\Handlers;

use App\Models\UserState;
use App\Services\CommandFactoryService;
use App\Telegram\Interfaces\HandlerInterface;
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
        if ($message == '') {
            $this->bot->sendMessage([
                'chat_id' => '948709856',
                'text' => 'Что-то с каналом'
            ]);
            return;
        }
        $command = $this->getCommandFromMapping($message);
        $userId = $message->from->id;
        $chatId = $message->chat->id;

        if($command) {
            $commandService = $this->commandFactory->getServiceFromCommand($command);

            if($commandService) {
                $commandService->start($this->bot, $userId, $chatId, $message);
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
                    $service->handle($this->bot, $userId, $chatId, $message);
                }
            } else {
                $this->getResponse($chatId, 'Команды не существует и никаких шагов не запущенно');
            }
        }
    }

    protected function getCommandFromMapping($message)
    {
        $commands = config("telegram.bots.{$this->botName}.command_mapping", []);

        foreach ($commands as $key => $command) {
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
