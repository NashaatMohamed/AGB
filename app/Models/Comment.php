<?php

namespace App\Models;

use App\Events\NewCommentAdded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'post_id',
        'user_id',
        'content',
    ];

    /**
     * Get the post that owns the comment.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the user that owns the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::created(function (Comment $comment) {
            // Increment post comment count
            $comment->post->incrementComments();

            // Load relationships for the event
            $comment->load('user.profile', 'post');

            // Dispatch event to notify post owner
            NewCommentAdded::dispatch($comment);
        });

        static::deleted(function (Comment $comment) {
            $comment->post->decrementComments();
        });
    }
}
