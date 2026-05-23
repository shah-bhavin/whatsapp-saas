<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\WhatsAppAccount;
use App\Models\WhatsAppTemplate;

#[Signature('app:sync-whats-app-templates')]
#[Description('Command description')]
class SyncWhatsAppTemplates extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Template Sync...');

        /*
        GET ALL ACTIVE ACCOUNTS
        */
        $accounts = WhatsAppAccount::where('is_active', true)->get();

         foreach ($accounts as $account) {
            $this->info('Checking Account: ' . $account->phone_number);

            try {
                /*
                FETCH TEMPLATES
                */
                $response = Http::withToken($account->access_token)
                    ->get("https://graph.facebook.com/v25.0/{$account->business_account_id}/message_templates");

                $data = $response->json();
                logger($data);

                /*
                CHECK DATA
                */
                if (!isset($data['data'])) {
                    $this->error('No templates found');
                    continue;
                }

                /*
                LOOP META TEMPLATES
                */
                foreach ($data['data'] as $metaTemplate) {
                    /*
                    FIND EXISTING
                    */
                    $template = WhatsAppTemplate::where('meta_template_id', $metaTemplate['id'])->first();

                    /*
                    UPDATE EXISTING
                    */
                    if ($template) {
                        $template->update([
                            'status'   => $metaTemplate['status'] ?? 'UNKNOWN',
                            'category' => $metaTemplate['category'] ?? null,
                            'language' => $metaTemplate['language'] ?? 'en',
                        ]);

                        $this->info('Updated: ' . $template->template_name);
                    }
                    /*
                    IMPORT NEW TEMPLATE
                    */
                    else {
                        WhatsAppTemplate::create([
                            'vendor_id'            => $account->vendor_id,
                            'whats_app_account_id' => $account->id,
                            'template_name'        => $metaTemplate['name'] ?? 'template',
                            'meta_template_id'     => $metaTemplate['id'] ?? null,
                            'category'             => $metaTemplate['category'] ?? 'MARKETING',
                            'language'             => $metaTemplate['language'] ?? 'en',
                            'status'               => $metaTemplate['status'] ?? 'UNKNOWN',
                            'body_text'            => 'Imported from Meta',
                        ]);

                        $this->info('Imported: ' . $metaTemplate['name']);
                    }
                }

            } catch (\Exception $e) {
                logger($e->getMessage());
                $this->error($e->getMessage());
            }
        }

        $this->info('Template Sync Completed');
    }
}
