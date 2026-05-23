<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\WhatsAppTemplate;

class MetaTemplateService
{
    /*
    SUBMIT TEMPLATE
    */
    public function submit(WhatsAppTemplate $template)
    {
        /*
        ACCOUNT
        */
        $account = $template->whatsAppAccount;

        /*
        TOKEN
        */
        $token = $account->access_token;

        /*
        WABA ID
        */
        $wabaId = $account->business_account_id;

        /*
        COMPONENTS
        */
        $components = [];

        /*
        HEADER
        */
        if ($template->header_text) {
            $components[] = [
                'type'   => 'HEADER',
                'format' => $template->has_media ? $template->media_type : 'TEXT',
                'text'   => $template->header_text,
            ];
        }

        /*
        BODY
        */
        $components[] = [
            'type' => 'BODY',
            'text' => $template->body_text,
        ];

        /*
        FOOTER
        */
        if ($template->footer_text) {
            $components[] = [
                'type' => 'FOOTER',
                'text' => $template->footer_text,
            ];
        }

        /*
        SEND TO META
        */
        $response = Http::withToken($token)
            ->post("https://graph.facebook.com/v25.0/{$wabaId}/message_templates", [
                'name'       => $template->template_name,
                'language'   => $template->language,
                'category'   => $template->category,
                'components' => $components,
            ]);

        $data = $response->json();
        logger($data);

        /*
        SUCCESS
        */
        if (isset($data['id'])) {
            $template->update([
                'meta_template_id' => $data['id'],
                'status'           => 'PENDING',
            ]);

            return [
                'success' => true,
                'data'    => $data,
            ];
        }

        /*
        ERROR
        */
        return [
            'success' => false,
            'data'    => $data,
        ];
    }
}
