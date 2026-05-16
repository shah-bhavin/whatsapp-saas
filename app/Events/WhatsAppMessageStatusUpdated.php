<?php

namespace App\Events;

use App\Models\Message;

use Illuminate\Broadcasting\Channel;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use Illuminate\Foundation\Events\Dispatchable;

use Illuminate\Queue\SerializesModels;

class WhatsAppMessageStatusUpdated
implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        return [

            new Channel(
                'whatsapp-chat'
            )

        ];
    }
}