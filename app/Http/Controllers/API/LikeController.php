<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Toggle like on a post.
     */
    public function toggle(Request $request, Post $post): JsonResponse
    {
        $userId = $request->user()->id;

        $like = $post->likes()->where('user_id', $userId)->first();

        if ($like) {
            $like->delete();
            $liked = false;
            $message = 'Post unliked';
        } else {
            $post->likes()->create(['user_id' => $userId]);
            $liked = true;
            $message = 'Post liked';
        }

        $post->refresh();

        return response()->success([
            'liked' => $liked,
            'likes_count' => $post->likes_count,
        ], $message);
    }

    /**
     * Get users who liked a post.
     */
    public function likedBy(Post $post): JsonResponse
    {
        $users = $post->likes()
            ->with('profile')
            ->latest()
            ->paginate(20);

        return response()->success($users);
    }
}
