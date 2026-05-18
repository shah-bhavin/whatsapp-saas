<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\CampaignProgressUpdated;
use App\Services\WhatsAppService;

class SendWhatsAppMessageJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public $campaign;
    public $contact;

    /**
     * Create a new job instance.
     */
    public function __construct(Campaign $campaign, Contact $contact)
    {
        $this->campaign = $campaign;
        $this->contact = $contact;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Create outbound database log
        Message::create([
            'contact_id' => $this->contact->id,
            //'campaign_id' => $this->campaign->id,
            'mobile'     => $this->contact->mobile,
            'message'    => $this->campaign->message,
            'direction'  => 'outgoing',
            'status'     => 'sent',
        ]);
        // 2. Dispatch via WhatsApp Service
        $service = new WhatsAppService();

        if ($this->campaign->type === 'template') {

            $response = $service->sendTemplateMessage($this->contact->mobile, $this->campaign->template->template_name, [$this->contact->name]);

        } else {
            $response = $service->sendTextMessage($this->contact->mobile, $this->campaign->message);
        }
        

        // 3. Process API responses and update pivot tables
        if ($response->successful()) {
            $this->campaign->contacts()->updateExistingPivot($this->contact->id, [
                'status'  => 'sent',
                'sent_at' => now(),
            ]);
        } else {
            $this->campaign->contacts()->updateExistingPivot($this->contact->id, [
                'status'        => 'failed',
                'error_message' => $response->body(),
            ]);
        }

        // 4. Calculate progress aggregates
        $sentCount = $this->campaign->contacts()->wherePivot('status', 'sent')->count();
        $pendingCount = $this->campaign->contacts()->wherePivot('status', 'pending')->count();

        // 5. Broadcast live metrics & log results
        broadcast(new CampaignProgressUpdated($this->campaign->id, $sentCount, $pendingCount));
        
        //logger('Message Processed for: ' . $this->contact->mobile);
    }
}
