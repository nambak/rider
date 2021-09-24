<?php

namespace App\Cafe24;

use App\Models\Cafe24Auth;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;

class Auth
{
    static private $baseUrl = 'https://tenminutesquad.cafe24api.com/api/v2';

    static public function getAccessToken()
    {
        $cafe24AuthInfo = Cafe24Auth::where('expired_at', '>', now())->latest()->first();

        if (is_null($cafe24AuthInfo)) {
            $cafe24AuthInfo = self::getRefreshToken();
        }

        return $cafe24AuthInfo->access_token;
    }

    static public function getRefreshToken()
    {
        $client = new Client();

        $refreshToken = Cafe24Auth::orderBy('expired_at', 'desc')->first()->refresh_token;

        $response = $client->request('POST', self::$baseUrl . '/oauth/token', [
            'headers'     => [
                'Authorization' => 'Basic ' . base64_encode(config('cafe24.clientID') . ':'
                        . config('cafe24.secretKey')),
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'grant_type'    => 'refresh_token',
                'refresh_token' => $refreshToken,
            ],
        ]);

        $results = json_decode($response->getBody());

        return Cafe24Auth::create([
            'access_token'  => $results->access_token,
            'refresh_token' => $results->refresh_token,
            'expired_at'    => self::covertDateTime($results->expires_at),
        ]);
    }

    static private function covertDateTime($dateTime)
    {
        return Carbon::create($dateTime)->format('Y-m-d H:i:s');
    }
}
