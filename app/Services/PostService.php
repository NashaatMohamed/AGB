<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class PostService
{
    /**
     * Get paginated posts from friends and user.
     */
    public function getUserFeed(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        $user = auth()->user();
        $friendIds = $user->friends()->pluck('id')->toArray();

        return Post::with(['user.profile', 'comments.user'])
            ->whereIn('user_id', array_merge($friendIds, [$userId]))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new post with image upload support.
     */
    public function createPost(array $data): Post
    {
        $postData = [
            'user_id' => auth()->id(),
            'content' => $data['content'],
        ];

        // Handle image upload
        if (isset($data['image']) && $data['image']) {
            $image = $data['image'];
            $filename = time() . '_' . auth()->id() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = 'posts/' . $filename;

            Storage::disk('public')->putFileAs('posts', $image, $filename);

            $postData['image'] = [
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
            ];
        }

        return Post::create($postData);
    }

    /**
     * Update an existing post.
     */
    public function updatePost(Post $post, array $data): Post
    {
        $updateData = [
            'content' => $data['content'],
        ];

        // Handle image upload
        if (isset($data['image']) && $data['image']) {
            // Delete old image if exists
            if ($post->image && isset($post->image['path'])) {
                Storage::disk('public')->delete($post->image['path']);
            }

            $image = $data['image'];
            $filename = time() . '_' . auth()->id() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = 'posts/' . $filename;

            Storage::disk('public')->putFileAs('posts', $image, $filename);

            $updateData['image'] = [
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
            ];
        }

        $post->update($updateData);

        return $post;
    }

    /**
     * Delete a post and its image.
     */
    public function deletePost(Post $post): bool
    {
        // Delete image if exists
        if ($post->image && isset($post->image['path'])) {
            Storage::disk('public')->delete($post->image['path']);
        }

        return $post->delete();
    }

    /**
     * Check if user has liked a post.
     */
    public function userHasLiked(Post $post, int $userId): bool
    {
        return $post->likes()->where('user_id', $userId)->exists();
    }
}
