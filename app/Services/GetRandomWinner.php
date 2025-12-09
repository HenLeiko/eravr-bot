<?php

namespace App\Services;

use App\Models\TelegramChannelMember;
use App\Models\UserState;

class GetRandomWinner
{
    public function start($bot, $userId, $chatId, $message)
    {
        $bot->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Укажите кол-во победителей'
        ]);
        $userState = UserState::updateOrCreate([
            'user_id' => $userId,
        ],
            [
                'user_id' => $userId,
                'command' => 'rand_winner',
                'state' => 'set_count',
                'data' => [],
            ]
        );
    }

    public function handle($bot, $userId, $chatId, $message)
    {
        $userState = UserState::where('user_id', $userId)->first();
    $count = (int) substr($message, -1); // приводим к числу

    if (!$userState) {
        return; // защита от null
    }

    switch ($userState->state) {
        case 'set_count':
            // используем scopeCanParticipate через query()
            $users = TelegramChannelMember::query()
                ->canParticipate()
                ->inRandomOrder()
                ->limit($count)
                ->get();
print_r($users);
            foreach ($users as $channelMember) {
                $member = $bot->getChatMember([
                    'chat_id' => '@era_vr',
                    'user_id' => $channelMember->user_id,
                ]);

                if ($member) {
                    $user = $member->get('user');

                    $username = $user['username'] ?? null;
                    $firstName = $user['first_name'] ?? null;
                    $lastName = $user['last_name'] ?? null;

                    $bot->sendMessage([
                        'chat_id' => $chatId,
                        'text' => 'В конкурсе победил(а): ' . trim("$firstName $lastName") . ($username ? " (@$username)" : ""),
                    ]);
                }
            }
            break;
    }
    }
}
