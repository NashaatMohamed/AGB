<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Friendship;
use App\Models\Like;
use App\Models\Post;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class SocialNetworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 users with profiles
        $users = User::factory(10)->create();

        $users->each(function (User $user) use ($users) {
            // Create a profile for each user
            Profile::factory()->create([
                'user_id' => $user->id,
            ]);

            // Create 3-5 posts for each user
            $posts = Post::factory(rand(3, 5))->create([
                'user_id' => $user->id,
            ]);

            // Add comments to each post
            $posts->each(function (Post $post) use ($users) {
                Comment::factory(rand(1, 4))->create([
                    'post_id' => $post->id,
                    'user_id' => $users->random()->id,
                ]);
            });
        });

        // Create likes (each user likes random posts)
        $users->each(function (User $user) use ($users) {
            $randomPosts = Post::whereNot('user_id', $user->id)
                ->inRandomOrder()
                ->limit(rand(5, 15))
                ->get();

            foreach ($randomPosts as $post) {
                Like::create([
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                ]);
            }
        });

        // Create friendships (random accepted friendships)
        $users->each(function (User $sender) use ($users) {
            $potentialFriends = $users->whereNotIn('id', [$sender->id])->random(rand(2, 5));

            foreach ($potentialFriends as $receiver) {
                // Check if friendship already exists
                $exists = Friendship::query()->where(function ($query) use ($sender, $receiver) {
                    $query->where('sender_id', $sender->id)
                        ->where('receiver_id', $receiver->id);
                })->orWhere(function ($query) use ($sender, $receiver) {
                    $query->where('sender_id', $receiver->id)
                        ->where('receiver_id', $sender->id);
                })->exists();

                if (!$exists) {
                    Friendship::query()->create([
                        'sender_id' => $sender->id,
                        'receiver_id' => $receiver->id,
                        'status' => collect(['accepted', 'pending', 'accepted', 'accepted'])->random(),
                    ]);
                }
            }
        });

        $this->command->info('Social network data seeded successfully!');
        $this->command->info('Users: ' . User::count());
        $this->command->info('Posts: ' . Post::count());
        $this->command->info('Comments: ' . Comment::count());
        $this->command->info('Likes: ' . Like::count());
        $this->command->info('Friendships: ' . Friendship::count());
    }
}
