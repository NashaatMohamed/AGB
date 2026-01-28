<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('posts.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Feed
                </a>
            </div>

            <!-- Post Card -->
            <x-post-card :post="$post" />

            <!-- Comments Section -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-4">
                    Comments ({{ $post->comments_count }})
                </h3>

                <!-- Add Comment Form -->
                <form method="POST" action="{{ route('comments.store', $post) }}" class="mb-6" x-data="commentForm()">
                    @csrf
                    <div class="flex gap-3">
                        @if(auth()->user()->profile?->profile_picture)
                            <img src="{{ Storage::url(auth()->user()->profile->profile_picture['path']) }}"
                                 alt="{{ auth()->user()->name }}"
                                 class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-semibold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif

                        <div class="flex-1">
                            <textarea
                                name="content"
                                x-model="content"
                                @input="autoResize($event)"
                                rows="1"
                                placeholder="Write a comment..."
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 resize-none"
                                required></textarea>

                            <div class="mt-2 flex justify-end">
                                <button
                                    type="submit"
                                    @click="submit()"
                                    :disabled="submitting || !content.trim()"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span x-show="!submitting">Post Comment</span>
                                    <span x-show="submitting">Posting...</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    @error('content')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </form>

                <!-- Comments List -->
                <x-comment-thread :comments="$post->comments" :post="$post" />
            </div>
        </div>
    </div>
</x-app-layout>
