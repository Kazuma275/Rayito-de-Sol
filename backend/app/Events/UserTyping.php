<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;

class UserTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $fromUserId;
    public $toUserId;
    public $conversationId;

    public function __construct($fromUserId, $toUserId, $conversationId)
    {
        $this->fromUserId = $fromUserId;
        $this->toUserId = $toUserId;
        $this->conversationId = $conversationId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel("chat.{$this->conversationId}");
    }

    public function broadcastWith()
    {
        return [
            'from_user_id' => $this->fromUserId,
        ];
    }

    public function broadcastAs()
    {
        return 'user.typing';
    }
}
