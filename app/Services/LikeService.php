<?php

namespace App\Services;

use App\Events\PostLiked;
use App\Models\Like;
use App\Models\Post;

class LikeService
{
    /**
     * Toggle like on a post (like/unlike).
     */
    public function toggleLike(Post $post, int $userId): array
    {
        $like = Like::where('post_id', $post->id)
            ->where('user_id', $userId)
            ->first();

        if ($like) {
            // Unlike
            $like->delete();
            $liked = false;
            $message = 'Post unliked!';
        } else {
            // Like
            Like::create([
                'post_id' => $post->id,
                'user_id' => $userId,
            ]);
            $liked = true;
            $message = 'Post liked!';

            // Dispatch broadcast event
            broadcast(new PostLiked($post, auth()->user()))->toOthers();
        }

        // Refresh the post to get updated counts
        $post->refresh();

        return [
            'liked' => $liked,
            'likes_count' => $post->likes_count,
            'message' => $message,
        ];
    }

    /**
     * Get users who liked a post.
     */
    public function getLikedBy(Post $post, int $perPage = 20)
    {
        return $post->likes()
            ->with('user.profile')
            ->latest()
            ->paginate($perPage);
    }
}
