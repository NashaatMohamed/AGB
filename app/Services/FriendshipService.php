<?php

namespace App\Services;

use App\Events\FriendRequestAccepted;
use App\Events\FriendRequestSent;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class FriendshipService
{
    /**
     * Get user's friends and pending requests.
     */
    public function getUserFriendships(User $user): array
    {
        $friendships = Friendship::friendsOf($user)
            ->with(['sender', 'receiver'])
            ->get();

        $friends = $friendships->map(function ($friendship) use ($user) {
            return $friendship->sender_id === $user->id
                ? $friendship->receiver
                : $friendship->sender;
        });

        $pendingRequests = Friendship::pendingRequestsFor($user)
            ->with('sender.profile')
            ->latest()
            ->get();

        $sentRequests = Friendship::sentRequestsBy($user)
            ->with('receiver.profile')
            ->latest()
            ->get();

        return compact('friends', 'pendingRequests', 'sentRequests');
    }

    /**
     * Send a friend request.
     */
    public function sendFriendRequest(User $receiver): array
    {
        $sender = auth()->user();

        // Can't send request to yourself
        if ($sender->id === $receiver->id) {
            return ['success' => false, 'message' => 'You cannot send a friend request to yourself.'];
        }

        // Check if friendship already exists
        $existingFriendship = Friendship::where(function ($query) use ($sender, $receiver) {
            $query->where('sender_id', $sender->id)
                ->where('receiver_id', $receiver->id);
        })->orWhere(function ($query) use ($sender, $receiver) {
            $query->where('sender_id', $receiver->id)
                ->where('receiver_id', $sender->id);
        })->first();

        if ($existingFriendship) {
            if ($existingFriendship->status === 'accepted') {
                return ['success' => false, 'message' => 'You are already friends with this user.'];
            } elseif ($existingFriendship->status === 'pending') {
                return ['success' => false, 'message' => 'Friend request already sent.'];
            } elseif ($existingFriendship->status === 'blocked') {
                return ['success' => false, 'message' => 'Cannot send friend request to this user.'];
            }
        }

        // Create new friend request
        $friendship = Friendship::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'status' => 'pending',
        ]);

        // Dispatch broadcast event
        broadcast(new FriendRequestSent($friendship, $sender))->toOthers();

        return ['success' => true, 'message' => 'Friend request sent successfully!', 'data' => $friendship];
    }

    /**
     * Accept a friend request.
     */
    public function acceptFriendRequest(Friendship $friendship): array
    {
        if ($friendship->status !== 'pending') {
            return ['success' => false, 'message' => 'This friend request is no longer pending.'];
        }

        $friendship->accept();

        // Dispatch broadcast event
        broadcast(new FriendRequestAccepted($friendship, auth()->user()))->toOthers();

        return ['success' => true, 'message' => 'Friend request accepted!', 'data' => $friendship];
    }

    /**
     * Reject a friend request.
     */
    public function rejectFriendRequest(Friendship $friendship): array
    {
        if ($friendship->status !== 'pending') {
            return ['success' => false, 'message' => 'This friend request is no longer pending.'];
        }

        $friendship->reject();

        return ['success' => true, 'message' => 'Friend request rejected.'];
    }

    /**
     * Cancel a sent friend request.
     */
    public function cancelFriendRequest(Friendship $friendship): array
    {
        if ($friendship->status !== 'pending') {
            return ['success' => false, 'message' => 'This friend request is no longer pending.'];
        }

        $friendship->delete();

        return ['success' => true, 'message' => 'Friend request cancelled.'];
    }

    /**
     * Remove a friend (unfriend).
     */
    public function removeFriend(User $friend): array
    {
        $user = auth()->user();

        $friendship = Friendship::where('status', 'accepted')
            ->where(function ($query) use ($user, $friend) {
                $query->where(function ($q) use ($user, $friend) {
                    $q->where('sender_id', $user->id)
                        ->where('receiver_id', $friend->id);
                })->orWhere(function ($q) use ($user, $friend) {
                    $q->where('sender_id', $friend->id)
                        ->where('receiver_id', $user->id);
                });
            })->first();

        if (!$friendship) {
            return ['success' => false, 'message' => 'Friendship not found.'];
        }

        $friendship->delete();

        return ['success' => true, 'message' => 'Friend removed successfully.'];
    }

    /**
     * Block a user.
     */
    public function blockUser(User $user): array
    {
        $currentUser = auth()->user();

        $friendship = Friendship::where(function ($query) use ($currentUser, $user) {
            $query->where('sender_id', $currentUser->id)
                ->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($currentUser, $user) {
            $query->where('sender_id', $user->id)
                ->where('receiver_id', $currentUser->id);
        })->first();

        if ($friendship) {
            $friendship->block();
        } else {
            Friendship::create([
                'sender_id' => $currentUser->id,
                'receiver_id' => $user->id,
                'status' => 'blocked',
            ]);
        }

        return ['success' => true, 'message' => 'User blocked successfully.'];
    }

    /**
     * Get suggested friends (users not yet connected with).
     */
    public function getSuggestedFriends(int $limit = 6): Collection
    {
        $user = auth()->user();

        // Get IDs of users already connected with (sent, received, or blocked)
        $connectedUserIds = Friendship::where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                ->orWhere('receiver_id', $user->id);
        })->pluck('sender_id', 'receiver_id')
            ->merge(Friendship::where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })->pluck('receiver_id', 'sender_id'))
            ->unique()
            ->values();

        // Get users who are not the current user and not already connected
        return User::where('id', '!=', $user->id)
            ->whereNotIn('id', $connectedUserIds)
            ->with('profile')
            ->limit($limit)
            ->get();
    }
}
