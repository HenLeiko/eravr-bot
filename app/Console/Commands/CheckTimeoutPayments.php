<?php

namespace App\Console\Commands;

use App\Jobs\CheckUnpaidRecords;
use Illuminate\Console\Command;

class CheckTimeoutPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:check_timeout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for checking if payment is timeout';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        CheckUnpaidRecords::dispatch();

        $this->info('Payment check job dispatched successfully.');
        return 0;
    }
}
