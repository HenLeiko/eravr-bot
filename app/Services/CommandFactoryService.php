<?php

namespace App\Services;

use Illuminate\Contracts\Foundation\Application;

class CommandFactoryService
{
    /**
     * Простая фабрика для создания экземпляра сервиса команды
     *
     * @param string $command
     * @return string|Application|CertService|null
     */
    public function getServiceFromCommand(string $command): string|Application|null|CertService|InviteService|CreatePostService|StartGiftBotService|GetRandomWinner|WorkHandleService
    {
        return match ($command) {
            'start' => app(StartSmmService::class),
            'create_cert' => app(CertService::class),
            'create_invite' => app(InviteService::class),
            'create_post' => app(CreatePostService::class),
            'count_calendar_events' => app(CountCalendarEventsService::class),
            '/start' => app(StartGiftBotService::class),
            'rand_winner' => app(GetRandomWinner::class),
            'switch_status' => app(WorkHandleService::class),
//            'debuginfo' => app(DebuginfoService::class),
            default => null,
        };
    }
}
