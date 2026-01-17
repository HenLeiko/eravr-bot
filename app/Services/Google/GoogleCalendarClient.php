<?php

namespace App\Services\Google;

use App\Models\GoogleTokens;
use Carbon\Carbon;
use Google_Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GoogleCalendarClient
{
    public function client()
    {
        $client = new Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect_uri'));

        $client->setScopes([
            'https://www.googleapis.com/auth/calendar.events'
        ]);

        $client->setAccessType('offline');
        $client->setPrompt('consent'); // возможны ошибки и желательно убрать
        $token = $this->getTokenFromDb();
        $client->setAccessToken([
            'access_token' => $token->access_token,
            'created' => Carbon::parse($token->expires_at)->subSeconds(3600)->timestamp,
            'expires_in' => 3600,
        ]);

        if ($client->isAccessTokenExpired()) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($token->refresh_token);
            if (!isset($newToken['access_token'])) {
                throw new \RuntimeException(
                    'Google Client access token could not be generated. ' . json_encode($newToken)
                );
            }
            $token->update([
                'access_token' => $newToken['access_token'],
                'expires_at' => Carbon::now()->addSeconds($newToken['expires_in']),
            ]);

            $client->setAccessToken($newToken);
        }
        return $client;
    }

    private function getTokenFromDb(): Model|GoogleTokens|Builder
    {
        return GoogleTokens::latest()->firstOrFail();
    }
}
