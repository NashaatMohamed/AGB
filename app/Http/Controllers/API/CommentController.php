<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function __construct(private CommentService $commentService) {}

    /**
     * Display comments for a post.
     */
    public function index(Post $post): JsonResponse
    {
        $comments = $this->commentService->getPostComments($post);

        return response()->success($comments);
    }

    /**
     * Store a newly created comment.
     */
    public function store(StoreCommentRequest $request, Post $post): JsonResponse
    {
        $validated = $request->validated();
        $comment = $this->commentService->createComment($post, $validated);
        $comment->load('user.profile');

        return response()->created($comment, 'Comment added successfully');
    }

    /**
     * Update the specified comment.
     */
    public function update(UpdateCommentRequest $request, Comment $comment): JsonResponse
    {
        $validated = $request->validated();
        $this->commentService->updateComment($comment, $validated);
        $comment->load('user.profile');

        return response()->success($comment, 'Comment updated successfully');
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $this->commentService->deleteComment($comment);

        return response()->success(null, 'Comment deleted successfully');
    }
}
