<?php

namespace App\Telegram\Handlers;

use App\Models\TelegramUser;
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

        if($command) {
            $commandService = $this->commandFactory->getServiceFromCommand($command);

            if($commandService) {
                $commandService->start();
            } else {
                print_r('Command not found');
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
            }
        }
    }

    public function getException()
    {
        // TODO: Implement getException() method.
    }
}
