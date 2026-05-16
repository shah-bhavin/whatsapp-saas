<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Message;
use App\Events\NewWhatsAppMessageReceived;
use App\Events\WhatsAppMessageStatusUpdated;

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
            $message = Message::where(
                'whatsapp_message_id',
                $messageId
            )->first();

            if ($message) {

                $message->update([

                    'status' => $status

                ]);

                broadcast(

                    new WhatsAppMessageStatusUpdated(
                        $message
                    )

                );
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

        broadcast(
            new NewWhatsAppMessageReceived(
                $message
            )
        );

        

        return response()->json(['success' => true]);
    }
}
