<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FriendshipController extends Controller
{
    /**
     * Get friends list and pending requests.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $friendships = Friendship::friendsOf($user)
            ->with(['sender.profile', 'receiver.profile'])
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

        return response()->success([
            'friends' => $friends,
            'pending_requests' => $pendingRequests,
            'sent_requests' => $sentRequests,
        ]);
    }

    /**
     * Send a friend request.
     */
    public function sendRequest(Request $request, User $receiver): JsonResponse
    {
        $sender = $request->user();

        if ($sender->id === $receiver->id) {
            return response()->error('You cannot send a friend request to yourself', 400);
        }

        $existingFriendship = Friendship::where(function ($query) use ($sender, $receiver) {
            $query->where('sender_id', $sender->id)->where('receiver_id', $receiver->id);
        })->orWhere(function ($query) use ($sender, $receiver) {
            $query->where('sender_id', $receiver->id)->where('receiver_id', $sender->id);
        })->first();

        if ($existingFriendship) {
            if ($existingFriendship->status === 'accepted') {
                return response()->error('You are already friends with this user', 400);
            }
            return response()->error('Friend request already exists', 400);
        }

        $friendship = Friendship::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'status' => 'pending',
        ]);

        $friendship->load(['sender.profile', 'receiver.profile']);

        return response()->created($friendship, 'Friend request sent successfully');
    }

    /**
     * Accept a friend request.
     */
    public function accept(Request $request, Friendship $friendship): JsonResponse
    {
        if ($friendship->receiver_id !== $request->user()->id) {
            return response()->forbidden();
        }

        if ($friendship->status !== 'pending') {
            return response()->error('This friend request is no longer pending', 400);
        }

        $friendship->accept();
        $friendship->load(['sender.profile', 'receiver.profile']);

        return response()->success($friendship, 'Friend request accepted');
    }

    /**
     * Reject a friend request.
     */
    public function reject(Request $request, Friendship $friendship): JsonResponse
    {
        if ($friendship->receiver_id !== $request->user()->id) {
            return response()->forbidden();
        }

        if ($friendship->status !== 'pending') {
            return response()->error('This friend request is no longer pending', 400);
        }

        $friendship->reject();

        return response()->success(null, 'Friend request rejected');
    }

    /**
     * Remove a friend.
     */
    public function remove(Request $request, User $friend): JsonResponse
    {
        $user = $request->user();

        $friendship = Friendship::where('status', 'accepted')
            ->where(function ($query) use ($user, $friend) {
                $query->where(function ($q) use ($user, $friend) {
                    $q->where('sender_id', $user->id)->where('receiver_id', $friend->id);
                })->orWhere(function ($q) use ($user, $friend) {
                    $q->where('sender_id', $friend->id)->where('receiver_id', $user->id);
                });
            })->first();

        if (!$friendship) {
            return response()->notFound();
        }

        $friendship->delete();

        return response()->success(null, 'Friend removed successfully');
    }
}
