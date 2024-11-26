<?php

namespace App\Services;

use Illuminate\Contracts\Foundation\Application;

class CommandFactoryService
{
    /**
     * @param string $command
     * @return CertService|(CertService&Application)|Application|\Illuminate\Foundation\Application|mixed|null
     */
    public function getServiceFromCommand(string $command)
    {
        switch ($command) {
            case 'create cert':
                return app(CertService::class);

            default:
                return null;
        }
    }
}
