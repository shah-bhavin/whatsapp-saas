<?php

namespace App\Events;

use App\Models\Campaign;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampaignProgressUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $campaignId;

    public $sentCount;

    public $pendingCount;

    public function __construct(
        $campaignId,
        $sentCount,
        $pendingCount
    ) {
        $this->campaignId = $campaignId;

        $this->sentCount = $sentCount;

        $this->pendingCount = $pendingCount;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('campaign-progress'),
        ];
    }
}