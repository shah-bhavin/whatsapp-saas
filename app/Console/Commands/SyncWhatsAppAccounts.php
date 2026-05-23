<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\WhatsAppAccount;
use App\Services\MetaWhatsAppService;

#[Signature('app:sync-whats-app-accounts')]
#[Description('Command description')]
class SyncWhatsAppAccounts extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service =  new MetaWhatsAppService();
        $accounts = WhatsAppAccount::all();

        foreach ($accounts as $account) {
            try {
                /*
                VALIDATE TOKEN
                */
                $tokenValid = $service->validateToken($account->access_token);

                /*
                GET STATUS
                */
                $status = $service->getPhoneStatus(
                    $account->phone_number_id,
                    $account->access_token
                );

                $account->update([
                    'token_valid'     => $tokenValid,
                    'quality_rating'  => $status['quality_rating'] ?? null,
                    'messaging_limit' => $status['messaging_limit_tier'] ?? null,
                    'last_synced_at'  => now(),
                    'last_error'      => null,
                ]);

            } catch (\Exception $e) {
                $account->update([
                    'last_error' => $e->getMessage()
                ]);
            }
        }

        $this->info('WhatsApp Accounts Synced');

    }
}
