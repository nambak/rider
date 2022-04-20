<?php

namespace App;

use Exception;
use GuzzleHttp\Client;

class NcloudAPI
{
    static public function getGeoCodeData($address)
    {
        $client = new Client();

        try {
            $response = $client->request(
                'GET',
                'https://naveropenapi.apigw.ntruss.com/map-geocode/v2/geocode',
                [
                    'headers' => [
                        'X-NCP-APIGW-API-KEY-ID' => config('ncloud.clientID'),
                        'X-NCP-APIGW-API-KEY'    => config('ncloud.secretKey'),
                    ],
                    'query'   => [
                        'query' => $address,
                    ],
                ],

            );

            $result = json_decode($response->getBody())->addresses;

            return $result ?: '';
        } catch (Exception $e) {
            return response($e->getMessage(), 500);
        }
    }
}
