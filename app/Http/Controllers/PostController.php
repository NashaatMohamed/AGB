<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Services\PostService;
use App\Services\FriendshipService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(
        private PostService $postService,
        private FriendshipService $friendshipService
    ) {}

    /**
     * Display a listing of posts (News Feed).
     */
    public function index(): View
    {
        $posts = $this->postService->getUserFeed(auth()->id());
        $suggestedFriends = $this->friendshipService->getSuggestedFriends(6);

        return view('posts.index', compact('posts', 'suggestedFriends'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create(): View
    {
        return view('posts.create');
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $this->postService->createPost($validated);

        return redirect()->route('posts.index')
            ->with('success', 'Post created successfully!');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post): View
    {
        $post->load(['user.profile', 'comments.user.profile']);
        $hasLiked = $this->postService->userHasLiked($post, auth()->id());

        return view('posts.show', compact('post', 'hasLiked'));
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post): View
    {
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified post in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $validated = $request->validated();
        $this->postService->updatePost($post, $validated);

        return redirect()->route('posts.show', $post)
            ->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        // Check if user is the post owner
        if ($post->user_id !== auth()->id()) {
            abort(403, 'You can only delete your own posts.');
        }

        $this->postService->deletePost($post);

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully!');
    }
}
