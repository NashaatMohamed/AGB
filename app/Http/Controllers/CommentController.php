<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function __construct(private CommentService $commentService) {}

    /**
     * Store a newly created comment in storage.
     */
    public function store(StoreCommentRequest $request, Post $post): RedirectResponse
    {
        $validated = $request->validated();
        $comment = $this->commentService->createComment($post, $validated);
        $comment->load('user.profile', 'post');

        return back()->with('success', 'Comment added successfully!');
    }

    /**
     * Update the specified comment in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment): RedirectResponse
    {
        $validated = $request->validated();
        $this->commentService->updateComment($comment, $validated);

        return back()->with('success', 'Comment updated successfully!');
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $this->commentService->deleteComment($comment);

        return back()->with('success', 'Comment deleted successfully!');
    }
}
