<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function sendMessage($mobile)
    {

        $token = env('WHATSAPP_ACCESS_TOKEN');

        $phoneNumberId = env(
            'WHATSAPP_PHONE_NUMBER_ID'
        );

        $url =
            "https://graph.facebook.com/v25.0/" .
            $phoneNumberId .
            "/messages";

        $response = Http::withToken($token)

            ->post($url, [

                'messaging_product' => 'whatsapp',

                'to' => $mobile,

                'type' => 'template',

                'template' => [

                    'name' => 'hello_world',

                    'language' => [
                        'code' => 'en_US'
                    ]

                ]

        ]);

        Log::info('WhatsApp API Response');

        Log::info($response->body());

        return $response;
    }
}