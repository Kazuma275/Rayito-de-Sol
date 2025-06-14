<?php
// UserTyping.php
namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $fromUserId;
    public $conversationId;

    public function __construct($fromUserId, $conversationId)
    {
        $this->fromUserId = $fromUserId;
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
            'conversation_id' => $this->conversationId,
            'timestamp' => now()->toISOString()
        ];
    }

    public function broadcastAs()
    {
        return 'user.typing';
    }
}
