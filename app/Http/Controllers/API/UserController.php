<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    /**
     * Get authenticated user's profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user()->load(['profile', 'posts', 'friends']);

        return response()->success($user);
    }

    /**
     * Update authenticated user's profile.
     */
    public function updateProfile(UpdateUserProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $this->userService->updateProfile($user, $validated);

        return response()->success($user->load('profile'), 'Profile updated successfully');
    }

    /**
     * Get a specific user's profile.
     */
    public function show(User $user): JsonResponse
    {
        $user->load(['profile', 'posts' => function ($query) {
            $query->latest()->take(10);
        }]);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    /**
     * Search users.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q');

        $users = User::with('profile')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }
}
