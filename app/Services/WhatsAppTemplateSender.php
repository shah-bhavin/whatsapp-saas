<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\WhatsAppAccount;

class WhatsAppTemplateSender
{
    public function send(
        WhatsAppAccount $account,
        string $to,
        string $templateName,
        array $variables = [],
        string $language = 'en',
        ?string $mediaUrl = null,
        ?string $mediaType = null
    ) {
        /*
        BUILD PARAMETERS
        */
        $parameters = [];

        foreach ($variables as $variable) {
            $parameters[] = [
                'type' => 'text',
                'text' => $variable,
            ];
        }

        /*
        TEMPLATE COMPONENTS
        */
        $components = [];

        /*
        HEADER MEDIA
        */
        if (!empty($mediaUrl)) {
            $components[] = [
                'type'       => 'header',
                'parameters' => [
                    [
                        'type'                 => strtolower($mediaType),
                        strtolower($mediaType) => [
                            'link' => $mediaUrl
                        ]
                    ]
                ]
            ];
        }


        if (count($parameters)) {
            $components[] = [
                'type'       => 'body',
                'parameters' => $parameters,
            ];
        }

        /*
        SEND MESSAGE
        */
        $response = Http::withToken($account->access_token)
            ->post("https://graph.facebook.com/v25.0/{$account->phone_number_id}/messages", [
                'messaging_product' => 'whatsapp',
                'to'                => $to,
                'type'              => 'template',
                'template'          => [
                    'name'     => $templateName,
                    'language' => [
                        'code' => $language,
                    ],
                    'components' => $components,
                ],
            ]);

        $data = $response->json();
        logger($data);

        return $data;
    }
}
