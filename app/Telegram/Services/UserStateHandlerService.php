<?php

namespace App\Telegram\Services;

use App\Models\UserState;
use App\Telegram\Contracts\StateHandlerInterface;
use Telegram\Bot\Objects\Message;

class UserStateHandlerService implements StateHandlerInterface
{
    /**
     * Возвращает стейт пользователя по его ID
     *
     * @param int $chatId
     * @return UserState|null
     */
    public function getUserState(int $chatId): ?UserState
    {
        return UserState::where('chat_id', $chatId)->first();
    }

    /**
     * Обновляет стейт юзера по его ID
     *
     * @param int $chatId
     * @param string $state
     * @param array $context
     * @return void
     */
    public function updateUserState(int $chatId, string $state, array $context = []): void
    {
        $userState = UserState::updateOrCreate(
            ['chat_id' => $chatId],
            ['state' => $state, 'data' => json_encode($context)]
        );

        $userState->save();
    }

    /**
     * Обнуляет стейт пользователя по его ID
     *
     * @param int $chatId
     * @return void
     */
    public function resetUserState(int $chatId): void
    {
        UserState::where('chat_id', $chatId)->delete();
    }

    /**
     * @param Message $message
     * @param UserState $userState
     * @param array $context
     * @return void
     */
    public function handle(Message $message, UserState $userState, array $context): void
    {
        // TODO: Implement handle() method.
    }
}
