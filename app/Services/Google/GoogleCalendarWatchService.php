<?php

namespace App\Services\Google;

use App\Models\GoogleCalendarSync;
use Carbon\Carbon;
use Exception;
use Google_Service_Calendar;
use Google_Service_Calendar_Channel;
use Illuminate\Support\Str;
use Log;

class GoogleCalendarWatchService
{
    private array $calendarIdList = [
        'eravrr@gmail.com',
        'ou86tdr91vgt3orsdtsnbu31r8@group.calendar.google.com',
        '56vb14ndiolefkv6b88h4urrfc@group.calendar.google.com'
    ];

    /**
     * @throws \Google\Service\Exception
     */
    public function initSelectedCalendars(): void
    {
        $client = app(GoogleCalendarClient::class)->client();
        $service = new Google_Service_Calendar($client);

        $calendars = $service->calendarList->listCalendarList();

        foreach ($calendars->getItems() as $calendar) {
            $calendarId = $calendar->getId();

            if (!in_array($calendarId, $this->calendarIdList)) {
                continue;
            }

            $this->watchCalendar($service, $calendarId);
        }
    }

    private function watchCalendar(Google_Service_Calendar $service, string $calendarId): void
    {
        try {
            $channel = new Google_Service_Calendar_Channel([
                'id' => (string) Str::uuid(),
                'type' => 'web_hook',
                'address' => secure_url(route('google.calendar.webhook', [], false)),
            ]);

            $response = $service->events->watch($calendarId, $channel);

            $googleSync = GoogleCalendarSync::updateOrCreate(
                ['calendar_id' => $calendarId],
                [
                    'channel_id' => $response->id,
                    'resource_id' => $response->resourceId,
                    'channel_expiration' => isset($response->expiration)
                        ? Carbon::createFromTimestampMs($response->expiration)
                        : null,
                ]
            );

            $events = $service->events->listEvents($calendarId, [
                'singleEvents' => true,
                'orderBy' => 'updated',
            ]);

            $googleSync->update([
                'sync_token' => $events->getNextSyncToken(),
                'last_sync_at' => Carbon::now(),
            ]);

            Log::info("Начато отслеживание календаря: {$calendarId}");


        } catch (Exception $e) {
            Log::error("Ошибка при инициализации watch для календаря {$calendarId}: " . $e->getMessage());
        }
    }

//    public function initAll()
//    {
//        $client = app(GoogleCalendarClient::class)->client();
//        $service = new Google_Service_Calendar($client);
//
//        $calendars = $service->calendarList->listCalendarList();
//
//        foreach ($calendars->getItems() as $calendar) {
//            $calendarId = $calendar->getId();
//            $channel = new Google_Service_Calendar_Channel([
//                'id' => (string) Str::uuid(),
//                'type' => 'web_hook',
//                'address' => secure_url(route('google.calendar.webhook', [], false)),
//            ]);
//
//            $response = $service->events->watch($calendarId, $channel);
//
//            $googleSync = GoogleCalendarSync::updateOrCreate(
//                ['calendar_id' => $calendarId],
//                [
//                    'channel_id' => $response->id,
//                    'resource_id' => $response->resourceId,
//                    'channel_expiration' => isset($response->expiration) ? Carbon::createFromTimestampMs($response->expiration) : null,
//                ]
//            );
//
//            $events = $service->events->listEvents($calendarId, [
//                'singleEvents' => true,
//                'orderBy' => 'updated',
//                'timeMin' => Carbon::now()->toRfc3339String(),
//            ]);
//
//            $googleSync->update([
//                'sync_token' => $events->getNextSyncToken(),
//                'last_sync_at' => Carbon::now(),
//            ]);
//        }
//    }
}
