<?php

namespace App\Console\Commands;

use App\Models\GoogleCalendarSync;
use App\Services\Google\GoogleCalendarClient;
use Exception;
use Google_Service_Calendar;
use Google_Service_Calendar_Channel;
use Google_Service_Exception;
use Illuminate\Console\Command;

class StopGoogleWatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:stop-watch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $client = app(GoogleCalendarClient::class)->client();
        $service = new Google_Service_Calendar($client);

        $watches = GoogleCalendarSync::all();

        foreach ($watches as $watch) {
            try {

                $channel = new Google_Service_Calendar_Channel([
                    'id' => $watch->channel_id,
                    'resourceId' => $watch->resource_id,
                ]);

                echo "Stopping watch for: {$watch->calendar_id}... ";

                $service->channels->stop($channel);

                $watch->delete();
                echo "✓ Success\n";

            } catch (Google_Service_Exception $e) {
                if ($e->getCode() == 404) {
                    $watch->delete();
                    echo "✓ Already expired, removed from DB\n";
                } else {
                    echo "✗ Error: {$e->getMessage()}\n";
                }
            } catch (Exception $e) {
                echo "✗ Error: {$e->getMessage()}\n";
            }

            // Пауза между запросами
            sleep(1);
        }
    }
}
