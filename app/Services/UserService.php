<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserService
{
    /**
     * Update user profile with image handling.
     */
    public function updateProfile(User $user, array $data): User
    {
        $profile = $user->profile ?? $user->profile()->create([]);

        // Update user name/email
        if (isset($data['name']) || isset($data['email'])) {
            $updateData = array_filter([
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
            ]);

            if ($updateData) {
                $user->update($updateData);
            }
        }

        // Update profile bio
        if (isset($data['bio'])) {
            $profile->update(['bio' => $data['bio']]);
        }

        // Handle profile picture upload
        if (isset($data['profile_picture']) && $data['profile_picture']) {
            if ($profile->profile_picture && isset($profile->profile_picture['path'])) {
                Storage::disk('public')->delete($profile->profile_picture['path']);
            }

            $image = $data['profile_picture'];
            $filename = time() . '_' . $user->id . '.' . $image->getClientOriginalExtension();
            $path = 'profiles/' . $filename;

            Storage::disk('public')->putFileAs('profiles', $image, $filename);

            $profile->update([
                'profile_picture' => [
                    'path' => $path,
                    'url' => Storage::disk('public')->url($path),
                ],
            ]);
        }

        return $user->refresh();
    }
}
