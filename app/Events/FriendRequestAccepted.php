<?php

namespace App\Events;

use App\Models\Friendship;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FriendRequestAccepted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Friendship $friendship,
        public User $accepter
    ) {
        // Create notification for the request sender
        $this->notification = Notification::create([
            'user_id' => $friendship->sender_id,
            'type' => 'friend_request_accepted',
            'message' => "{$accepter->name} accepted your friend request",
            'data' => [
                'friendship_id' => $friendship->id,
                'accepter_id' => $accepter->id,
                'accepter_name' => $accepter->name,
                'accepter_profile_picture' => $accepter->profile?->profile_picture,
                'redirect_url' => route('profile.show', $accepter->id),
            ],
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->friendship->sender_id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'notification' => $this->notification->toArray(),
        ];
    }
}
