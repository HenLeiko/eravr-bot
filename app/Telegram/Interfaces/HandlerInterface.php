<?php

namespace App\Telegram\Interfaces;

interface HandlerInterface
{
    public function __invoke();
    public function getException();
}
