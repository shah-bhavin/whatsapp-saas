<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MetaWhatsAppService
{
    /*
    SUBSCRIBE WEBHOOK
    */
    public function subscribeWebhook($wabaId, $accessToken)
    {
        return Http::withToken($accessToken)
            ->post("https://graph.facebook.com/v25.0/{$wabaId}/subscribed_apps", [])
            ->json();
    }

    /*
    GET PHONE QUALITY
    */
    public function getPhoneStatus($phoneNumberId, $accessToken)
    {
        return Http::withToken($accessToken)
            ->get("https://graph.facebook.com/v25.0/{$phoneNumberId}", [
                'fields' => 'verified_name,quality_rating,messaging_limit_tier'
            ])
            ->json();
    }

    /*
    VALIDATE TOKEN
    */
    public function validateToken($accessToken)
    {
        return Http::withToken($accessToken)
            ->get('https://graph.facebook.com/v25.0/me')
            ->successful();
    }
}
