<?php

namespace App\Http\Controllers\Google;

use App\Http\Controllers\Controller;
use App\Models\GoogleTokens;
use Carbon\Carbon;
use Google_Client;
use Illuminate\Http\Request;

class GoogleOAuthController extends Controller
{
    public function redirectGoogle()
    {
        $client = new Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect')); //callback url
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setScopes(['https://www.googleapis.com/auth/calendar.events', 'https://www.googleapis.com/auth/calendar']);

        return redirect($client->createAuthUrl());
    }

    public function callbackGoogle(Request $request)
    {
        $code = $request->query('code');
        $client = new Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));

        $accessToken = $client->fetchAccessTokenWithAuthCode($code);

        GoogleTokens::create([
            'access_token' => $accessToken['access_token'],
            'refresh_token' => $accessToken['refresh_token'],
            'expires_at' => Carbon::now()->addSeconds($accessToken['expires_in'])
        ]);

        return redirect()->route('google.calendar.init');
    }
}
