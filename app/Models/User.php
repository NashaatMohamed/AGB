<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's profile.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Get the posts for the user.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the comments for the user.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the posts the user has liked.
     */
    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'likes')
            ->withTimestamps();
    }

    /**
     * Get the user's friends (accepted friendships) - sent by this user.
     */
    public function friendsOfMine(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'sender_id', 'receiver_id')
            ->wherePivot('status', 'accepted')
            ->withPivot('status', 'created_at')
            ->withTimestamps();
    }

    /**
     * Get the user's friends (accepted friendships) - received by this user.
     */
    public function friendOf(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'receiver_id', 'sender_id')
            ->wherePivot('status', 'accepted')
            ->withPivot('status', 'created_at')
            ->withTimestamps();
    }

    /**
     * Get all friends (combining both directions).
     */
    public function friends()
    {
        return $this->friendsOfMine->merge($this->friendOf);
    }

    /**
     * Get the friendship requests sent by the user.
     */
    public function sentFriendshipRequests(): HasMany
    {
        return $this->hasMany(Friendship::class, 'sender_id');
    }

    /**
     * Get the friendship requests received by the user.
     */
    public function receivedFriendshipRequests(): HasMany
    {
        return $this->hasMany(Friendship::class, 'receiver_id');
    }

    /**
     * Get the pending friendship requests.
     */
    public function pendingFriendshipRequests(): HasMany
    {
        return $this->receivedFriendshipRequests()->where('status', 'pending');
    }

    /**
     * Get the count of friends.
     */
    public function getFriendsCountAttribute(): int
    {
        return $this->friends()->count();
    }

    /**
     * Get the count of posts.
     */
    public function getPostsCountAttribute(): int
    {
        return $this->posts()->count();
    }

    /**
     * Check if the user is friends with another user.
     */
    public function isFriendsWith(User $user): bool
    {
        return $this->friends()->where('users.id', $user->id)->exists();
    }

    /**
     * Send a friendship request to another user.
     */
    public function sendFriendshipRequest(User $user): Friendship
    {
        return Friendship::create([
            'sender_id' => $this->id,
            'receiver_id' => $user->id,
            'status' => 'pending',
        ]);
    }
}
