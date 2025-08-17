<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $receiverId;
    public $auctionId;

    public function __construct($message, $receiverId, $auctionId)
    {
        $this->message = $message;
        $this->receiverId = $receiverId;
        $this->auctionId = $auctionId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('messages.' . $this->receiverId);
    }
}
