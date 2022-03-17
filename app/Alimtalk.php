<?php

namespace App;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Exception;

class Alimtalk
{
    public static function send($templateCode, $phoneNumber, array $replaces = [])
    {
        $client = new Client();

        $url = 'http://api.apistore.co.kr/kko/1/msg/' . config('alimtalk.client_id');
        $template = self::getTemplate($templateCode);
        $message = self::replaceTemplate($template['message'], $replaces);


        try {
            $client->request('POST', $url, [
                'headers'     => ['x-waple-authorization' => config('alimtalk.store_key')],
                'form_params' => [
                    'PHONE'          => str_replace('-', '', $phoneNumber),
                    'CALLBACK'       => '07042787541',
                    'MSG'            => $message,
                    'TEMPLATE_CODE'  => $templateCode,
                    'FAILED_TYPE'    => 'sms',
                    'FAILED_SUBJECT' => $template['title'],
                    'FAILED_MSG'     => $message,
                ],
            ]);
        } catch (Exception $exception) {
            return response($exception->getMessage(), 500);
        }
    }

    private static function getTemplate($code)
    {
        $client = new Client();

        $url = 'http://api.apistore.co.kr/kko/1/template/list/' . config('alimtalk.client_id');

        $response = $client->request('GET', $url, [
            'headers' => ['x-waple-authorization' => config('alimtalk.store_key')],
            'query'   => [
                'template_code' => $code,
            ],
        ]);

        $template = json_decode($response->getBody());

        return [
            'message' => $template->templateList[0]->template_msg,
            'title'   =>   $template->templateList[0]->template_name,
        ];
    }

    private static function replaceTemplate($templateMessage, array $replaceText = [])
    {
        foreach ($replaceText as $key => $text) {
            $templateMessage = str_replace($key, $text, $templateMessage);
        }

        return $templateMessage;
    }
}
