<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected $token;
    protected $phoneNumberId;

    public function __construct()
    {
        $this->token = env('WHATSAPP_ACCESS_TOKEN');
        $this->phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');
    }

    /**
     * Send a text message via WhatsApp.
     *
     * @param string $mobile
     * @param string $message
     * @return \Illuminate\Http\Client\Response
     */
    public function sendTextMessage($mobile, $message)
    {
        $url = "https://graph.facebook.com/v25.0/" . $this->phoneNumberId . "/messages";
        
        return Http::withToken($this->token)
            ->post($url, [
                'messaging_product' => 'whatsapp',
                'to'                => $mobile,
                'type'              => 'text',
                'text'              => [
                    'body' => $message
                ]
            ]);
    }

    public function sendTemplateMessage($mobile, $templateName, $parameters = [])
    {
        $url = "https://graph.facebook.com/v25.0/{$this->phoneNumberId}/messages";
        $components = [];

        if (count($parameters)) {
            $components[] = [
                'type'       => 'body',
                'parameters' => collect($parameters)
                    ->map(fn($value) => [
                        'type' => 'text',
                        'text' => $value,
                    ])
                    ->toArray(),
            ];
        }

        return Http::withToken($this->token)
            ->post($url, [
                'messaging_product' => 'whatsapp',
                'to'                => $mobile,
                'type'              => 'template',
                'template'          => [
                    'name'     => $templateName,
                    'language' => [
                        'code' => 'en',
                    ],
                    'components' => $components,
                ],
            ]);
    }

    public function uploadMedia($filePath, $mimeType)
    {

        $url =
            "https://graph.facebook.com/v25.0/" .
            $this->phoneNumberId .
            "/media";

        return Http::withToken(
            $this->token
        )

            ->attach(
                'file',
                fopen($filePath, 'r'),
                basename($filePath)
            )

            ->post($url, [

                'messaging_product' =>
                'whatsapp',

                'type' => $mimeType
            ]);
    }

    public function sendImageMessage($mobile, $mediaId, $caption = null) {

        $url =
            "https://graph.facebook.com/v25.0/" .
            $this->phoneNumberId .
            "/messages";

        return Http::withToken(
            $this->token
        )

            ->post($url, [

                'messaging_product' =>
                'whatsapp',

                'to' => $mobile,

                'type' => 'image',

                'image' => [

                    'id' => $mediaId,

                    'caption' => $caption

                ]

            ]);
    }
}
