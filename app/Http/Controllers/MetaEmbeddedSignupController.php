<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;

use App\Models\WhatsAppAccount;
use Illuminate\Support\Str;

class MetaEmbeddedSignupController extends Controller
{
    public function exchangeToken(Request $request)
    {
        try {
            $code = $request->code;

            // Exchange Token
            $tokenResponse = Http::asForm()->post('https://graph.facebook.com/v25.0/oauth/access_token', [
                'client_id'     => env('FACEBOOK_CLIENT_ID'),
                'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
                'grant_type'    => 'authorization_code',
                'code'          => $code,
            ]);

            $tokenData = $tokenResponse->json();
            logger($tokenData);

            // Check Error
            if (!isset($tokenData['access_token'])) {
                return response()->json([
                    'success'  => false,
                    'message'  => $tokenData['error']['message'] ?? 'Token failed',
                    'response' => $tokenData
                ], 500);
            }

            // Store Temp Token
            session(['meta_access_token' => $tokenData['access_token']]);

            return response()->json([
                'success'    => true,
                'message'    => 'Token Generated',
                'token_data' => $tokenData
            ]);

        } catch (\Exception $e) {
            logger($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function saveAccount(Request $request)
    {
        try {
            // Get Data
            $wabaId = $request->waba_id;
            $phoneNumberId = $request->phone_number_id;

            // Get Access Token
            $accessToken = session('meta_access_token');

            // Validation
            if (!$accessToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access token missing'
                ]);
            }

            // Fetch Phone Details
            $phoneResponse = Http::withToken($accessToken)->get("https://graph.facebook.com/v25.0/{$phoneNumberId}", [
                'fields' => 'display_phone_number,verified_name'
            ])->json();

            logger($phoneResponse);

            // Save Database
            $account = WhatsAppAccount::create([
                'vendor_id'           => auth()->user()->vendor_id,
                'name'                => $phoneResponse['verified_name'] ?? 'WhatsApp Business',
                'phone_number'        => $phoneResponse['display_phone_number'] ?? null,
                'phone_number_id'     => $phoneNumberId,
                'business_account_id' => $wabaId,
                'access_token'        => $accessToken,
                'verify_token'        => Str::random(40),
                'is_active'           => true,
            ]);

            $metaService = new \App\Services\MetaWhatsAppService();

            /*
            SUBSCRIBE WEBHOOK
            */
            $subscriptionResponse = $metaService->subscribeWebhook($wabaId, $accessToken);

            logger($subscriptionResponse);

            /*
            UPDATE STATUS
            */
            $account->update([
                'webhook_subscribed' => isset($subscriptionResponse['success']) || isset($subscriptionResponse['id'])
            ]);


            return response()->json([
                'success' => true,
                'message' => 'WhatsApp Account Saved',
                'account' => $account
            ]);

        } catch (\Exception $e) {
            logger($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }



}