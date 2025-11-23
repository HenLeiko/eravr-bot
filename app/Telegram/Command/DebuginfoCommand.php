<?php

namespace App\Telegram\Command;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class DebuginfoCommand extends Command
{
    protected string $name = 'debuginfo';
    protected string $description = 'Command for debuginfo';

    public function handle(): void
    {
        $update = Telegram::getWebhookUpdate();

        if (isset($update->message->message_thread_id)) {
            Telegram::sendMessage([
                'chat_id' => $update->getMessage()->chat->id,
                'message_thread_id' => $update->getMessage()->message_thread_id,
                'text' => 'chat id: ' . $update->getMessage()->chat->id. "\n" .
                    'user id: ' . $update->getMessage()->from->id . "\n" .
                    'thread id: ' . $update->getMessage()->message_thread_id . "\n" .
                    'chat title: ' . $update->getMessage()->reply_to_message->forum_topic_created->name. "\n" .
                    'user name: ' . $update->getMessage()->from->username . "\n"
            ]);
        } else {
            $this->replyWithMessage([
                'text' => 'chat id: ' . $update->getMessage()->chat->id .
                'user id: ' . $update->getMessage()->from->id . "\n" .
                'user name: ' . $update->getMessage()->from->username . "\n",
            ]);
        }
    }
}
