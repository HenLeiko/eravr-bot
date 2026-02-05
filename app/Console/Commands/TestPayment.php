<?php

namespace App\Console\Commands;

use App\Payment\PaymentClientService;
use Illuminate\Console\Command;

class TestPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test for generate payment link';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $paymentService = new PaymentClientService();
        $result = $paymentService->paymentInit(1000, 'Тестовая предоплата');

        if ($result['success']) {
            echo 'Победа! Ссылка на оплату: ' . $result['payment_url'];
        } else {
            echo 'У нас проблемс :((  Воть ошибка: ' . $result['error_message'];
        }
    }
}
