<?php

namespace App\Services;

use Illuminate\Contracts\Foundation\Application;

class CommandFactoryService
{
    /**
     * @param string $command
     * @return string|Application|null
     */
    public function getServiceFromCommand(string $command): string|Application|null|CertService
    {
        return match ($command) {
            'create_cert' => app(CertService::class),
            'create_invite' => 'lorem',
            default => null,
        };
    }
}
