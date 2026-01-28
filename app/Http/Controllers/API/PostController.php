<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(private PostService $postService) {}

    /**
     * Display a listing of posts (News Feed).
     */
    public function index(Request $request): JsonResponse
    {
        $posts = $this->postService->getUserFeed($request->user()->id);

        return response()->success($posts, 'Posts retrieved successfully');
    }

    /**
     * Store a newly created post.
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $post = $this->postService->createPost($validated);
        $post->load('user.profile');

        return response()->created($post, 'Post created successfully');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post): JsonResponse
    {
        $post->load(['user.profile', 'comments.user.profile']);

        return response()->success($post);
    }

    /**
     * Update the specified post.
     */
    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $validated = $request->validated();
        $this->postService->updatePost($post, $validated);
        $post->load('user.profile');

        return response()->success($post, 'Post updated successfully');
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Post $post): JsonResponse
    {
        $this->postService->deletePost($post);

        return response()->success(null, 'Post deleted successfully');
    }
}
