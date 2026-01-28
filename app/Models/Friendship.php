<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Friendship extends Model
{
    /** @use HasFactory<\Database\Factories\FriendshipFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    /**
     * Get the sender of the friendship request.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the friendship request.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Accept the friendship request.
     */
    public function accept(): void
    {
        $this->update(['status' => 'accepted']);
    }

    /**
     * Reject the friendship request.
     */
    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }

    /**
     * Block the friendship.
     */
    public function block(): void
    {
        $this->update(['status' => 'blocked']);
    }

    /**
     * Scope a query to only include pending friendships.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include accepted friendships (friends).
     */
    public function scopeFriends($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Scope a query to get friendships for a specific user.
     */
    public function scopeFriendsOf($query, User $user)
    {
        return $query->where('status', 'accepted')
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            });
    }

    /**
     * Scope a query to get pending requests for a specific user (as receiver).
     */
    public function scopePendingRequestsFor($query, User $user)
    {
        return $query->where('status', 'pending')
            ->where('receiver_id', $user->id);
    }

    /**
     * Scope a query to get sent requests by a specific user (as sender).
     */
    public function scopeSentRequestsBy($query, User $user)
    {
        return $query->where('status', 'pending')
            ->where('sender_id', $user->id);
    }
}
