<?php

namespace App\Events;

use App\Models\Comment;
use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewCommentAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Comment $comment
    ) {
        // Create notification for post owner (if not commenting own post)
        if ($comment->post->user_id !== $comment->user_id) {
            $this->notification = Notification::create([
                'user_id' => $comment->post->user_id,
                'type' => 'new_comment',
                'message' => "{$comment->user->name} commented on your post",
                'data' => [
                    'post_id' => $comment->post_id,
                    'comment_id' => $comment->id,
                    'commenter_id' => $comment->user_id,
                    'commenter_name' => $comment->user->name,
                    'commenter_profile_picture' => $comment->user->profile?->profile_picture,
                    'comment_content' => substr($comment->content, 0, 100),
                    'redirect_url' => route('posts.show', $comment->post_id),
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
            new Channel('posts.' . $this->comment->post_id . '.comments'),
        ];

        // Also broadcast to post owner's private channel
        if ($this->notification) {
            $channels[] = new PrivateChannel('user.' . $this->comment->post->user_id);
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
            'comment' => [
                'id' => $this->comment->id,
                'content' => $this->comment->content,
                'created_at' => $this->comment->created_at->toISOString(),
                'user' => [
                    'id' => $this->comment->user->id,
                    'name' => $this->comment->user->name,
                    'profile_picture' => $this->comment->user->profile?->profile_picture,
                ],
            ],
            'notification' => $this->notification?->toArray(),
        ];
    }
}
