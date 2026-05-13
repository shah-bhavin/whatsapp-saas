<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Contact;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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

        logger(
            'Message Sent To: ' .
            $this->contact->mobile
        );
    }
}
