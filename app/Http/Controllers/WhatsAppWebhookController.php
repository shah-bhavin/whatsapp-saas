<?php

namespace App\Http\Controllers;

use App\Events\NewWhatsAppMessageReceived;
use App\Events\WhatsAppMessageStatusUpdated;
use App\Models\AutomationRule;
use App\Models\Contact;
use App\Models\Message;
use App\Services\AIService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WhatsAppWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $verifyToken = 'AntrixWebhook7574';

        // 1. Meta Webhook Verification
        if ($request->isMethod('GET')) {
            if ($request->get('hub_verify_token') === $verifyToken) {
                return response($request->get('hub_challenge'), 200);
            }
            return response('Invalid token', 403);
        }

        // 2. Extract Incoming Message Data
        $messageData = $request->input('entry.0.changes.0.value.messages.0');
        $entry = $request->input('entry');
        $statusData = $entry[0]['changes'][0]['value']['statuses'][0] ?? null;

        if ($statusData) {
            $messageId = $statusData['id'];
            $status = $statusData['status'];
            $message = Message::where('whatsapp_message_id', $messageId)->first();

            if ($message) {
                $message->update(['status' => $status]);
                broadcast(new WhatsAppMessageStatusUpdated($message));
            }

            return response()->json(['success' => true]);
        }

        if (!$messageData) {
            return response()->json(['success' => true]);
        }

        // 3. Log Incoming Message
        $mobile = $messageData['from'];
        $contact = Contact::where('mobile', $mobile)->first();
        
        $message = Message::create([
            'contact_id' => $contact?->id,
            'mobile' => $mobile,
            'message' => $messageData['text']['body'] ?? '',
            'direction' => 'incoming',
            'status' => 'received',
        ]);

        broadcast(new NewWhatsAppMessageReceived($message));

        // 4. Automation Rule Check
        $rule = AutomationRule::query()
            ->where('is_active', true)
            ->where('keyword', Str::lower($messageData['text']['body'] ?? ''))
            ->first();

        if ($rule) {
            if ($rule->use_ai) {

    $ai =
        new AIService();

    $reply =
        $ai->ask($messageData['text']['body']);

} else {

    $reply =
        $rule->reply_message;
}


            $service = new WhatsAppService();
            $response = $service->sendTextMessage($mobile, $reply);

            if ($response->successful()) {
                $data = $response->json();

                Message::create([
                    'contact_id' => $contact?->id,
                    'mobile' => $mobile,
                    'message' => $reply,
                    'direction' => 'outgoing',
                    'status' => 'sent',
                    'whatsapp_message_id' => $data['messages'][0]['id'] ?? null
                ]);
            }
        }

        return response()->json(['success' => true]);
    }
}
