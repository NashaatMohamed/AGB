<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\UpdateProfileBioRequest;
use App\Http\Requests\UpdateProfilePictureRequest;
use App\Http\Requests\DeleteAccountRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private UserService $userService) {}

    /**
     * Display the user's profile form.
     */
    public function edit(): View
    {
        $user = auth()->user();
        $profile = $user->profile ?? $user->profile()->create([]);

        return view('profile.edit', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Prepare data for service
        $data = ['name' => $validated['name'], 'email' => $validated['email']];
        if ($request->has('bio')) {
            $data['bio'] = $request->input('bio');
        }
        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture');
        }

        $this->userService->updateProfile($user, $data);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Display the specified user's profile.
     */
    public function show(User $user): View
    {
        $profile = $user->profile ?? $user->profile()->create([]);

        // Load relationships
        $user->load(['posts' => function ($query) {
            $query->latest()->with('comments.user')->take(10);
        }]);

        // Get friends list
        $friends = $user->friends()->slice(0, 6)->map(fn($friend) => $friend->load('profile'));

        // Get counts
        $postsCount = $user->posts()->count();
        $friendsCount = $user->friends()->count();

        return view('profile.show', compact('user', 'profile', 'friends', 'postsCount', 'friendsCount'));
    }

    /**
     * Display user profile by username.
     */
    public function showByUsername(string $username): View|RedirectResponse
    {
        $user = User::query()->where('name', $username)->firstOrFail();
        return $this->show($user);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(DeleteAccountRequest $request): RedirectResponse
    {

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
