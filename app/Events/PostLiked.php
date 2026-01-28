<?php

namespace App\Events;

use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostLiked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Post $post,
        public User $liker
    ) {
        // Create notification for post owner (if not liking own post)
        if ($post->user_id !== $liker->id) {
            $this->notification = Notification::create([
                'user_id' => $post->user_id,
                'type' => 'post_liked',
                'message' => "{$liker->name} liked your post",
                'data' => [
                    'post_id' => $post->id,
                    'liker_id' => $liker->id,
                    'liker_name' => $liker->name,
                    'liker_profile_picture' => $liker->profile?->profile_picture,
                    'redirect_url' => route('posts.show', $post->id),
                ],
            ]);
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            new Channel('posts.' . $this->post->id . '.likes'),
        ];

        // Also broadcast to post owner's private channel
        if ($this->notification) {
            $channels[] = new PrivateChannel('user.' . $this->post->user_id);
        }

        return $channels;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'post_id' => $this->post->id,
            'likes_count' => $this->post->likes_count,
            'liker' => [
                'id' => $this->liker->id,
                'name' => $this->liker->name,
                'profile_picture' => $this->liker->profile?->profile_picture,
            ],
            'notification' => $this->notification?->toArray(),
        ];
    }
}
