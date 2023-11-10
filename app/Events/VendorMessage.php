<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VendorMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $message, $vendor_id, $user, $chat_id, $image;

    public function __construct($message, $vendor_id, $user, $chat_id, $image)
    {
        $this->message = $message;
        $this->vendor_id = $vendor_id;
        $this->user = $user;
        $this->chat_id = $chat_id;
        $this->image = $image;
    }

    public function broadcastAs(): string
    {
        return 'vendor-message'.$this->vendor_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('vendor-chat'.$this->vendor_id),
        ];
    }
}
