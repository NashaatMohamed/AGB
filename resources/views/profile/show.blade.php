<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $user->name }}'s Profile
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Profile Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-start space-x-6">
                        <!-- Profile Picture -->
                        <div class="flex-shrink-0">
                            @if($profile->profile_picture)
                                <img src="{{ Storage::url($profile->profile_picture['path']) }}"
                                     alt="{{ $user->name }}'s profile picture"
                                     class="h-32 w-32 rounded-full object-cover border-4 border-gray-200">
                            @else
                                <div class="h-32 w-32 rounded-full bg-gray-300 flex items-center justify-center text-3xl font-bold text-gray-600 border-4 border-gray-200">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <!-- Profile Info -->
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
                            <p class="text-gray-600 mt-1">{{ $user->email }}</p>

                            @if($profile->bio)
                                <p class="mt-4 text-gray-700">{{ $profile->bio }}</p>
                            @endif

                            <!-- Stats -->
                            <div class="flex space-x-8 mt-6">
                                <div>
                                    <span class="text-2xl font-bold text-gray-900">{{ $postsCount }}</span>
                                    <span class="text-gray-600 ml-2">Posts</span>
                                </div>
                                <div>
                                    <span class="text-2xl font-bold text-gray-900">{{ $friendsCount }}</span>
                                    <span class="text-gray-600 ml-2">Friends</span>
                                </div>
                            </div>

                            <!-- Edit Profile Button (if viewing own profile) -->
                            @auth
                                @if(auth()->id() === $user->id)
                                    <div class="mt-6">
                                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Edit Profile
                                        </a>
                                    </div>
                                @else
                                    <div class="mt-6">
                                        <x-send-friend-request-button :user="$user" />
                                    </div>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Posts Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Recent Posts</h3>

                            @forelse($user->posts as $post)
                                <div class="mb-6 pb-6 border-b border-gray-200 last:border-0">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-1">
                                            <p class="text-gray-900">{{ $post->content }}</p>

                                            @if($post->image)
                                                <img src="{{ Storage::url($post->image['path']) }}"
                                                     alt="Post image"
                                                     class="mt-3 rounded-lg max-w-full h-auto">
                                            @endif

                                            <div class="flex items-center space-x-4 mt-3 text-sm text-gray-600">
                                                <span>{{ $post->likes_count }} likes</span>
                                                <span>{{ $post->comments_count }} comments</span>
                                                <span>{{ $post->created_at->diffForHumans() }}</span>
                                            </div>

                                            <!-- Comments Preview -->
                                            @if($post->comments->count() > 0)
                                                <div class="mt-3 space-y-2">
                                                    @foreach($post->comments->take(3) as $comment)
                                                        <div class="text-sm">
                                                            <span class="font-semibold text-gray-900">{{ $comment->user->name }}</span>
                                                            <span class="text-gray-700">{{ $comment->content }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-600 text-center py-8">No posts yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Friends Section -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Friends ({{ $friendsCount }})</h3>

                            @forelse($friends as $friend)
                                <div class="flex items-center space-x-3 mb-4">
                                    @if($friend->profile && $friend->profile->profile_picture)
                                        <img src="{{ Storage::url($friend->profile->profile_picture['path']) }}"
                                             alt="{{ $friend->name }}"
                                             class="h-12 w-12 rounded-full object-cover">
                                    @else
                                        <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center text-lg font-bold text-gray-600">
                                            {{ strtoupper(substr($friend->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('profile.show', $friend) }}" class="font-semibold text-gray-900 hover:text-indigo-600">
                                            {{ $friend->name }}
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-600 text-center py-4">No friends yet.</p>
                            @endforelse

                            @if($friendsCount > 6)
                                <div class="mt-4 text-center">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-700 font-semibold text-sm">
                                        View All Friends
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
