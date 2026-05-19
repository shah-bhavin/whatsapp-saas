<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

use App\Models\AiSetting;

class AIService
{
    public function ask($message)
    {
        $setting =
            AiSetting::where('is_active', true)->first();

        if (!$setting) {

            return 'AI not configured.';
        }

        return match(
            $setting->provider
        ) {

            'gemini' =>
                $this->askGemini(
                    $message,
                    $setting->api_key
                ),

            'groq' =>
                $this->askGroq(
                    $message,
                    $setting->api_key
                ),

            'openai' =>
                $this->askOpenAI(
                    $message,
                    $setting->api_key
                ),

            default =>
                'Unsupported AI provider'
        };
    }

    private function askGemini(
        $message,
        $apiKey
    ) {

        $url =
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        $response =
            Http::post($url, [

                'contents' => [[

                    'parts' => [[

                        'text' => $message

                    ]]

                ]]

            ]);


        return
            $response['candidates'][0]
            ['content']['parts'][0]
            ['text']

            ?? 'No response';
    }

    private function askGroq(
        $message,
        $apiKey
    ) {

        $response =
            Http::withToken($apiKey)

            ->post(

                'https://api.groq.com/openai/v1/chat/completions',

                [

                    'model' =>
                        'llama3-8b-8192',

                    'messages' => [

                        [

                            'role' =>
                                'user',

                            'content' =>
                                $message

                        ]

                    ]

                ]
            );

        return
            $response['choices'][0]
            ['message']['content']

            ?? 'No response';
    }

    private function askOpenAI(
        $message,
        $apiKey
    ) {

        $response =
            Http::withToken($apiKey)

            ->post(

                'https://api.openai.com/v1/chat/completions',

                [

                    'model' => 'gpt-4o-mini',

                    'messages' => [

                        [

                            'role' =>
                                'user',

                            'content' =>
                                $message

                        ]

                    ]

                ]
            );

        return
            $response['choices'][0]
            ['message']['content']

            ?? 'No response';
    }

}