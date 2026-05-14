<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Contact;
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
        $service = new WhatsAppService();

        $response = $service->sendMessage(

            $this->contact->mobile,

            $this->campaign->message

        );

        if ($response->successful()) {

            $this->campaign->contacts()
                ->updateExistingPivot(

                    $this->contact->id,

                    [
                        'status' => 'sent',

                        'sent_at' => now(),
                    ]
                );

        } else {

            $this->campaign->contacts()
                ->updateExistingPivot(

                    $this->contact->id,

                    [
                        'status' => 'failed',

                        'error_message' =>
                            $response->body(),
                    ]
                );

        }

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
