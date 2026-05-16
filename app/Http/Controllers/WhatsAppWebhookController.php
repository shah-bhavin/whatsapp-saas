<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Message;

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

        if (!$messageData) {
            return response()->json(['success' => true]);
        }

        // 3. Log Incoming Message
        $mobile = $messageData['from'];
        $contact = Contact::where('mobile', $mobile)->first();

        Message::create([
            'contact_id' => $contact?->id,
            'mobile'     => $mobile,
            'message'    => $messageData['text']['body'] ?? '',
            'direction'  => 'incoming',
            'status'     => 'received',
        ]);

        return response()->json(['success' => true]);
    }
}
