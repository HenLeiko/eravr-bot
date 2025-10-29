<?php

namespace App\Services;

use App\Models\UserState;

class CreateContestService
{
    public function start($bot, $userId, $chatId, $message)
    {
        $bot->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Для создания конкурса вам нужно указать текст конкурса в формате HTML, без тега br',
        ]);

        $userState = UserState::updateOrCreate([
            'user_id' => $userId,
        ], [
            'user_id' => $userId,
            'command' => 'create_contest',
            'state' => 'main_text',
            'data' => [
                'text' => '',
                'end_at' => '',
                'img' => '',
                'rewards' => [
                    'rand_rewards' => [],
                    'ref_rewards' => [],
                ]
            ]
        ]);
    }

    public function handle($bot, $userId, $chatId, $message)
    {
        $userState = UserState::where('user_id', $userId)->first();
        $state = $userState->state;

        if (!$userState) {
            return;
        }

        switch ($state) {
            case 'main_text':

                break;

            case 'end_at':

                break;

            case 'rand_rewards':

                break;

            case 'ref_rewards':

                break;
            default: return;
        }

    }
}
