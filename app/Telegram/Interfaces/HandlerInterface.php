<?php

namespace App\Telegram\Interfaces;

interface HandlerInterface
{
    public function handle();
    public function getException();
}
