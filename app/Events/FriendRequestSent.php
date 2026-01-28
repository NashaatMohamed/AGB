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

class FriendRequestSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Friendship $friendship,
        public User $sender
    ) {
        // Create notification
        $this->notification = Notification::create([
            'user_id' => $friendship->receiver_id,
            'type' => 'friend_request',
            'message' => "{$sender->name} sent you a friend request",
            'data' => [
                'friendship_id' => $friendship->id,
                'sender_id' => $sender->id,
                'sender_name' => $sender->name,
                'sender_profile_picture' => $sender->profile?->profile_picture,
                'redirect_url' => route('friends.index'),
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
            new PrivateChannel('user.' . $this->friendship->receiver_id),
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
