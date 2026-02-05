<?php

namespace App\Services;

use App\Models\UserState;

class StartSmmService
{
    public function start($bot, $userId, $chatId, $message)
    {
        $userState = UserState::updateOrCreate([
            'user_id' => $userId,
        ], [
            'user_id' => $userId,
            'username' => 'null',
            'access_level_id' => 0
        ]);
    }
}
