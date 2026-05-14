<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Contact;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\CampaignProgressUpdated;

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
        sleep(2);

        $this->campaign->contacts()->updateExistingPivot(
            $this->contact->id,
            [
                'status' => 'sent',
                'sent_at' => now(),
            ]
        );

        $sentCount = $this->campaign
            ->contacts()
            ->wherePivot('status', 'sent')
            ->count();

        $pendingCount = $this->campaign
            ->contacts()
            ->wherePivot('status', 'pending')
            ->count();

        broadcast(new CampaignProgressUpdated(
            $this->campaign->id,
            $sentCount,
            $pendingCount
        ));

        logger(
            'Message Sent To: ' .
            $this->contact->mobile
        );
    }
}
