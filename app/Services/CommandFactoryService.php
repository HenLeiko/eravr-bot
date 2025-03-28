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
    public function getServiceFromCommand(string $command): string|Application|null|CertService|InviteService
    {
        return match ($command) {
            'create_cert' => app(CertService::class),
            'create_invite' => app(InviteService::class),
            default => null,
        };
    }
}
