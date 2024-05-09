<?php

namespace App\Telegram\Command;

use Telegram\Bot\Commands\Command;

class HelpCommand extends Command
{
    protected string $name = 'help';
    protected array $aliases = ['listcommands'];
    protected string $description = 'Получить информацию о боте и список доступных команд';


    /**
     * @return void
     */
    public function handle(): void
    {
        $commands = $this->telegram->getCommandBus()->getCommands();

        $text = 'Бот ЭраВР для облегчения работы с клиентами и администраторами' . PHP_EOL . PHP_EOL;

        foreach ($commands as $name => $handler) {
            $text .= sprintf('/%s - %s'.PHP_EOL, $name, $handler->getDescription());
        }

        $this->replyWithMessage([
            'text' => $text,
        ]);
    }
}
