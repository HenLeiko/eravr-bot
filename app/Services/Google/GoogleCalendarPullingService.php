<?php

namespace App\Services\Google;


use App\Models\GoogleCalendarSync;
use App\Models\PaymentHistroy;
use App\Payment\PaymentClientService;
use Carbon\Carbon;
use Exception;
use Google_Service_Calendar;
use Google_Service_Exception;
use Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class GoogleCalendarPullingService
{
    private GoogleCalendarClient $calendarClient;

    public function __construct(GoogleCalendarClient $calendarClient)
    {
        $this->calendarClient = $calendarClient;
    }

    public function sync(GoogleCalendarSync $channel)
    {

        $client = $this->calendarClient->client();
        $service = new Google_Service_Calendar($client);

//        Log::info('Sync started', [
//            'calendar' => $channel->calendar_id,
//            'has_token' => !empty($channel->sync_token),
//        ]);

        try {
            $params = [];

            if (!empty($channel->sync_token)) {
                $params = ['syncToken' => $channel->sync_token];
//                Log::debug('Using syncToken', ['token' => substr($channel->sync_token, 0, 20) . '...']);
            } else {
                $params = [
                    'singleEvents' => true,
                    'orderBy' => 'updated',
                    'timeMin' => Carbon::now()->toRfc3339String(),
                    'maxResults' => 2500,
                ];
//                Log::debug('Using full sync params');
            }

            $totalEvents = 0;
            $pageToken = null;

            do {
                if ($pageToken) {
                    $params['pageToken'] = $pageToken;
                }

                $events = $service->events->listEvents($channel->calendar_id, $params);
                $eventItems = $events->getItems();
                $totalEvents += count($eventItems);

//                Log::info('Events page received', [
//                    'calendar' => $channel->calendar_id,
//                    'page_count' => count($eventItems),
//                    'total_so_far' => $totalEvents,
//                    'has_next_page' => !empty($events->getNextPageToken()),
//                ]);

                foreach ($eventItems as $event) {
//                    Log::info('Event found', [
//                        'calendar' => $channel->calendar_id,
//                        'id' => $event->getId(),
//                        'title' => $event->getSummary() ?? 'Untitled',
//                    ]);


                    $eventTitle = $event->getSummary() ?? 'Untitled';

                    $pattern = '/^тест\s+(\d+)(?:\s+вр\s+(\d+)\s*шл)?(?:\s+(\d+(?:\.\d+)?)\s*(?:час|часа|часов|ч))?(.*?)([^+\d]+?)?\s*(\+\d{11})?$/u';
                    if (preg_match($pattern, $eventTitle, $matches)) {


                        $summary = $event->getSummary() ?? 'Чёта пусто в название';
                        $eventDesc = $event->getLocation();
//                        Log::info('Event description', [$event->getLocation()]);
                        if ($eventDesc === null) {
                            $amount = (int)$matches[1] * 100;
                            $vrCounter = $matches[2] ?? '<не указано>';
                            $timeCounter = $matches[3] ?? '<не указано>';
                            $name = trim($matches[5] ?? '<не указано>');
                            $phone = trim($matches[6] ?? '<не указан>');
                            $paymentClient = app(PaymentClientService::class);

                            $paymentResult = $paymentClient->paymentInit($amount, 'Бронирование в клубе ЭраVR');

                            $paymentLink = $paymentResult['payment_url'];
                            $paymentHistory = PaymentHistroy::createFromTinkoffResponse(
                                [
                                    'PaymentId' => $paymentResult['payment_id'],
                                    'OrderId' => $paymentResult['order_id'],
                                    'Amount' => $paymentResult['amount'],
                                    'PaymentURL' => $paymentResult['payment_url'],
                                ],
                                [
                                    'calendar' => [
                                        'event_id' => $event->getId(),
                                        'calendar_id' => $channel->calendar_id
                                    ],

                                    'client' => [
                                        'name' => $name,
                                        'phone' => $phone
                                    ],

                                    'booking' => [
                                        'vr_headsets' => $vrCounter,
                                        'hours' => $timeCounter,
                                        'club' => $this->getClubNameByCalendarId($channel->calendar_id)
                                    ]
                                ]
                            );

                            $event->setLocation($paymentLink);
                            $service->events->update($channel->calendar_id, $event->getId(), $event);
                            $summary = $event->getSummary() ?? 'Чёта пусто в название';

                            $dateTimeStrStart = $event->getStart()?->getDateTime();
                            $dateTimeStrEnd = $event->getEnd()?->getDateTime();

                            Telegram::bot('eravrsmm')->sendMessage([
                                'chat_id' => '-1002402724986',
                                'message_thread_id' => '3',
                                'text' => 'Вы забронировали время в ' .
                                    $this->getClubNameByCalendarId($channel->calendar_id) . ' ' .
                                    Carbon::parse($dateTimeStrStart)->format('d.m.Y') . ' с ' .
                                    Carbon::parse($dateTimeStrStart)->format('H:i') . ' по ' .
                                    Carbon::parse($dateTimeStrEnd)->format('H:i') . ' (' .
                                    $vrCounter . ' ' . trans_choice('helmets', $vrCounter) . '). ' . 'Необходимо внести предоплату в размере ' .
                                    $matches[1] . ' р.' . ' Ссылка действительна в течение 1 часа. Возьмите с собой сменную обувь. При возникновении любых вопросов, звоните по номеру +7(991)312-17-11' .
                                    PHP_EOL .  PHP_EOL .  'Т-Банк: ' . $paymentResult['payment_url']
                            ]);


                        }
                    }

                }

                $pageToken = $events->getNextPageToken();

            } while ($pageToken);

//            Log::info('All pages processed', [
//                'calendar' => $channel->calendar_id,
//                'total_events' => $totalEvents,
//            ]);

            if ($nextSyncToken = $events->getNextSyncToken()) {
                $channel->update([
                    'sync_token' => $nextSyncToken,
                    'last_sync_at' => Carbon::now(),
                ]);

//                Log::info('Sync token updated', [
//                    'calendar' => $channel->calendar_id,
//                    'new_token' => substr($nextSyncToken, 0, 20) . '...',
//                    'total_events' => $totalEvents,
//                ]);
            } else {
//                Log::warning('No sync token in response after processing all pages', [
//                    'calendar' => $channel->calendar_id,
//                    'total_events' => $totalEvents,
//                ]);

                $this->tryAlternativeSyncTokenMethod($service, $channel);
            }

//            Log::info('Sync completed', [
//                'calendar' => $channel->calendar_id,
//                'total_events' => $totalEvents,
//            ]);

        } catch (Google_Service_Exception $e) {
        } catch (Exception $e) {
            Log::error('Unexpected error in sync', [
                'calendar' => $channel->calendar_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function tryAlternativeSyncTokenMethod(Google_Service_Calendar $service, GoogleCalendarSync $channel)
    {
        try {
//            Log::info('Trying alternative method for sync token', [
//                'calendar' => $channel->calendar_id,
//            ]);

            $params = [
                'singleEvents' => true,
                'orderBy' => 'updated',
                'timeMin' => Carbon::now()->toRfc3339String(),
                'timeMax' => Carbon::now()->addMonths(3)->toRfc3339String(),
                'maxResults' => 100,
            ];

            $events = $service->events->listEvents($channel->calendar_id, $params);

            if ($nextSyncToken = $events->getNextSyncToken()) {
                $channel->update([
                    'sync_token' => $nextSyncToken,
                    'last_sync_at' => Carbon::now(),
                ]);

//                Log::info('Alternative method worked!', [
//                    'calendar' => $channel->calendar_id,
//                    'new_token' => substr($nextSyncToken, 0, 20) . '...',
//                ]);
            } else {
                $params = [
                    'maxResults' => 1,
                    'orderBy' => 'updated',
                ];

                $events = $service->events->listEvents($channel->calendar_id, $params);

                if ($nextSyncToken = $events->getNextSyncToken()) {
                    $channel->update([
                        'sync_token' => $nextSyncToken,
                        'last_sync_at' => Carbon::now(),
                    ]);

//                    Log::info('Got sync token with single event', [
//                        'calendar' => $channel->calendar_id,
//                    ]);
                } else {
                    Log::warning('Still no sync token after alternative methods', [
                        'calendar' => $channel->calendar_id,
                    ]);
                }
            }

        } catch (Exception $e) {
            Log::error('Alternative sync token method failed', [
                'calendar' => $channel->calendar_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function fullResync(Google_Service_Calendar $service, GoogleCalendarSync $channel)
    {
        $events = $service->events->listEvents($channel->calendar_id, [
            'singleEvents' => true,
        ]);

        foreach ($events->getItems() as $event) {
            print_r('full resync');
        }

        $channel->update([
            'sync_token' => $events->getNextSyncToken(),
            'last_sync_at' => Carbon::now(),
            'timeMin' => Carbon::now()->toRfc3339String(),
        ]);

    }

    private function getClubNameByCalendarId(string $calendarId): string
    {
        $clubs = [
            'ou86tdr91vgt3orsdtsnbu31r8@group.calendar.google.com' => 'Эра VR Молодежная',
            '56vb14ndiolefkv6b88h4urrfc@group.calendar.google.com' => 'Эра VR Лианозово',
            'eravrr@gmail.com' => 'Эра VR Беляево',
            // Добавьте свои calendar_id
        ];

        return $clubs[$calendarId] ?? 'Эра VR';
    }
}
