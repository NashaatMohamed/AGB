<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\LikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LikeController extends Controller
{
    public function __construct(private LikeService $likeService) {}

    /**
     * Toggle like on a post.
     */
    public function toggle(Post $post): RedirectResponse|JsonResponse
    {
        $result = $this->likeService->toggleLike($post, auth()->id());

        // Return JSON for AJAX requests
        if (request()->wantsJson() || request()->header('Accept') === 'application/json') {
            return response()->success([
                'liked' => $result['liked'],
                'likes_count' => $result['likes_count'],
            ], $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    /**
     * Get users who liked a post.
     */
    public function likedBy(Post $post): View
    {
        $users = $this->likeService->getLikedBy($post);

        return view('posts.liked-by', compact('post', 'users'));
    }
}
