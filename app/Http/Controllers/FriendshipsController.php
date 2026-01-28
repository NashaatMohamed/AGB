<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Models\User;
use App\Services\FriendshipService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FriendshipsController extends Controller
{
    public function __construct(private FriendshipService $friendshipService) {}

    /**
     * Display friends list and pending friend requests.
     */
    public function index(): View
    {
        $user = auth()->user();
        $data = $this->friendshipService->getUserFriendships($user);
        $suggestedFriends = $this->friendshipService->getSuggestedFriends(12);

        return view('friends.index', array_merge($data, compact('suggestedFriends')));
    }

    /**
     * Send a friend request to another user.
     */
    public function sendRequest(User $user): RedirectResponse
    {
        $result = $this->friendshipService->sendFriendRequest($user);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Accept a friend request.
     */
    public function acceptRequest(Friendship $friendship): RedirectResponse
    {
        $result = $this->friendshipService->acceptFriendRequest($friendship);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Reject a friend request.
     */
    public function rejectRequest(Friendship $friendship): RedirectResponse
    {
        $result = $this->friendshipService->rejectFriendRequest($friendship);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Cancel a sent friend request.
     */
    public function cancelRequest(Friendship $friendship): RedirectResponse
    {
        $result = $this->friendshipService->cancelFriendRequest($friendship);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Remove a friend (unfriend).
     */
    public function removeFriend(User $friend): RedirectResponse
    {
        $result = $this->friendshipService->removeFriend($friend);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Block a user.
     */
    public function blockUser(User $user): RedirectResponse
    {
        $result = $this->friendshipService->blockUser($user);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
