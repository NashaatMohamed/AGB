<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

class CommentService
{
    /**
     * Get paginated comments for a post.
     */
    public function getPostComments(Post $post, int $perPage = 20)
    {
        return $post->comments()
            ->with('user.profile')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new comment on a post.
     */
    public function createComment(Post $post, array $data): Comment
    {
        return Comment::create([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
            'content' => $data['content'],
        ]);
    }

    /**
     * Update an existing comment.
     */
    public function updateComment(Comment $comment, array $data): Comment
    {
        $comment->update([
            'content' => $data['content'],
        ]);

        return $comment;
    }

    /**
     * Delete a comment.
     */
    public function deleteComment(Comment $comment): bool
    {
        return $comment->delete();
    }
}
