<?php

namespace App\Http\Controllers\Google;

use App\Http\Controllers\Controller;
use App\Models\GoogleCalendarSync;
use App\Services\Google\GoogleCalendarPullingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Log;

class CalendarWebhookController extends Controller
{
    public function handle(Request $request, GoogleCalendarPullingService $service)
    {
        $channelId = $request->header('x-goog-channel-id');
        $resourceState = $request->header('x-goog-resource-state');
        $resourceId = $request->header('x-goog-resource-id');
        $messageNumber = $request->header('x-goog-message-number');

        $duplicateKey = "webhook_{$channelId}_{$messageNumber}";

        if (Cache::has($duplicateKey)) {
            Log::warning('Duplicate webhook ignore', [
                'Channel ID' => $channelId,
                'Message number' => $messageNumber
            ]);
            return response('Ok - duplicate ignore', 200);
        }

        Cache::put($duplicateKey, true, 3600);
            Log::info('Webhook received', [
            'channel_id' => $channelId,
            'resource_state' => $resourceState,
            'resource_id' => $resourceId,
            'headers' => $request->headers->all(),
        ]);
        if ($resourceState === 'sync') {
            Log::info('Sync notification received');
            return response()->noContent(200);
        }

        if (!$channelId) {
            Log::warning('No channel ID in request');
            return response()->noContent(200);
        }

        $channel = GoogleCalendarSync::where('channel_id', $channelId)->first();

        if (!$channel) {
            Log::warning('Channel not found in DB', ['channel_id' => $channelId]);
            return response()->noContent(200);
        }

        if ($resourceState === 'not_exists') {
            Log::info('Channel expired or deleted', ['channel_id' => $channelId]);
            $channel->delete();
            return response()->noContent(200);
        }

        if ($resourceState === 'exists') {
            Log::info('Starting sync for calendar', [
                'calendar_id' => $channel->calendar_id,
                'channel_id' => $channelId,
            ]);

            $service->sync($channel);

            Log::info('Sync completed for calendar', [
                'calendar_id' => $channel->calendar_id,
            ]);
        }

        return response()->noContent(200);
    }
}
