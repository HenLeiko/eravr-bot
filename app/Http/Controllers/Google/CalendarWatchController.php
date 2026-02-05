<?php

namespace App\Http\Controllers\Google;

use App\Http\Controllers\Controller;
use App\Services\Google\GoogleCalendarWatchService;

class CalendarWatchController extends Controller
{
    public function init(GoogleCalendarWatchService $service)
    {
        $service->initSelectedCalendars();
        return response()->json(['status' => 'success_init']);
    }
}
