<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left Sidebar (Profile Summary) -->
                <aside class="hidden lg:block lg:col-span-3">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-20">
                        <!-- Profile Card -->
                        <div class="relative">
                            <!-- Cover Photo -->
                            @if(auth()->user()->profile?->cover_photo)
                                <img src="{{ Storage::url(auth()->user()->profile->cover_photo['path']) }}"
                                     alt="Cover"
                                     class="w-full h-24 object-cover">
                            @else
                                <div class="w-full h-24 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500"></div>
                            @endif

                            <!-- Profile Picture Overlay -->
                            <div class="absolute -bottom-8 left-1/2 transform -translate-x-1/2">
                                <a href="{{ route('profile.show', auth()->user()) }}" class="block">
                                    @if(auth()->user()->profile?->profile_picture)
                                        <img src="{{ Storage::url(auth()->user()->profile->profile_picture['path']) }}"
                                             alt="{{ auth()->user()->name }}"
                                             class="w-16 h-16 rounded-full border-4 border-white dark:border-gray-800 object-cover shadow-lg">
                                    @else
                                        <div class="w-16 h-16 rounded-full border-4 border-white dark:border-gray-800 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </a>
                            </div>
                        </div>

                        <!-- Profile Info -->
                        <div class="pt-10 px-4 pb-4 text-center">
                            <a href="{{ route('profile.show', auth()->user()) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</h3>
                            </a>
                            @if(auth()->user()->profile?->bio)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ auth()->user()->profile->bio }}</p>
                            @endif
                        </div>

                        <!-- Stats -->
                        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex justify-around text-center">
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ auth()->user()->posts_count }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Posts</p>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ auth()->user()->friends_count }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Friends</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Main Feed -->
                <main class="lg:col-span-6">
                    <!-- Create Post Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
                        <div class="flex gap-3">
                            @if(auth()->user()->profile?->profile_picture)
                                <img src="{{ Storage::url(auth()->user()->profile->profile_picture['path']) }}"
                                     alt="{{ auth()->user()->name }}"
                                     class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold flex-shrink-0">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif

                            <a href="{{ route('posts.create') }}"
                               class="flex-1 px-4 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-full text-gray-500 dark:text-gray-400 transition-colors cursor-pointer">
                                What's on your mind, {{ auth()->user()->name }}?
                            </a>
                        </div>

                        <div class="flex gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('posts.create') }}"
                               class="flex-1 flex items-center justify-center gap-2 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Photo</span>
                            </a>
                        </div>
                    </div>

                    <!-- Posts Feed -->
                    <div class="space-y-6">
                        @forelse($posts as $post)
                            <x-post-card :post="$post" />
                        @empty
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                </svg>
                                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No posts yet</h3>
                                <p class="mt-2 text-gray-500 dark:text-gray-400">Start by creating your first post or add some friends!</p>
                                <div class="mt-6 flex justify-center gap-4">
                                    <a href="{{ route('posts.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        Create Post
                                    </a>
                                    <a href="{{ route('friends.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                                        Find Friends
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($posts->hasPages())
                        <div class="mt-6">
                            {{ $posts->links() }}
                        </div>
                    @endif
                </main>

                <!-- Right Sidebar (Friends List / Suggestions) -->
                <aside class="hidden lg:block lg:col-span-3">
                    @if(auth()->user()->friends_count > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sticky top-20">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-gray-900 dark:text-white">Friends</h3>
                                <a href="{{ route('friends.index') }}" class="text-sm text-blue-600 hover:text-blue-800">See all</a>
                            </div>

                            <x-friends-list :friends="auth()->user()->friends()->take(6)" />
                        </div>
                    @else
                        <div class="sticky top-20">
                            <x-suggested-friends :suggestedFriends="$suggestedFriends" />
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
