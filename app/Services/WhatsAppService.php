<?php namespace App\Services;

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
}
