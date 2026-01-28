@props(['post'])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
    <!-- Post Header -->
    <div class="p-4 flex items-center gap-3">
        <!-- Profile Picture -->
        <a href="{{ route('profile.show', $post->user) }}" class="flex-shrink-0">
            @if($post->user->profile?->profile_picture)
                <img src="{{ Storage::url($post->user->profile->profile_picture['path']) }}"
                     alt="{{ $post->user->name }}"
                     class="w-10 h-10 rounded-full object-cover">
            @else
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr($post->user->name, 0, 1)) }}
                </div>
            @endif
        </a>

        <!-- User Info -->
        <div class="flex-1 min-w-0">
            <a href="{{ route('profile.show', $post->user) }}" class="font-semibold text-gray-900 dark:text-white hover:underline">
                {{ $post->user->name }}
            </a>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $post->created_at->diffForHumans() }}</p>
        </div>

        <!-- Edit/Delete Menu (if owner) -->
        @if(auth()->id() === $post->user_id)
            <div class="relative" id="postMenu-{{ $post->id }}">
                <button onclick="window.togglePostMenu(this)" type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                    </svg>
                </button>

                <div id="postMenuDropdown-{{ $post->id }}"
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 py-1 z-10"
                     style="display: none;">
                    <a href="{{ route('posts.edit', $post) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        ✏️ Edit Post
                    </a>
                    <form method="POST" action="{{ route('posts.destroy', $post) }}" class="block" onsubmit="return confirm('Are you sure you want to delete this post?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            🗑️ Delete Post
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <!-- Post Content -->
    <div class="px-4 pb-3">
        <p class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $post->content }}</p>
    </div>

    <!-- Post Image -->
    @if($post->image)
        <div class="bg-gray-50 dark:bg-gray-900">
            <a href="{{ route('posts.show', $post) }}" class="block">
                <img src="{{ Storage::url($post->image['path']) }}"
                     alt="Post image"
                     class="w-full h-auto max-h-[600px] object-cover">
            </a>
        </div>
    @endif

    <!-- Stats Bar -->
    <div class="px-4 py-2 flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700">
        <span>{{ $post->likes_count }} {{ Str::plural('like', $post->likes_count) }}</span>
        <span>{{ $post->comments_count }} {{ Str::plural('comment', $post->comments_count) }}</span>
    </div>

    <!-- Action Buttons -->
    <div class="px-4 py-2 flex items-center gap-2 border-t border-gray-100 dark:border-gray-700">
        <!-- Like Button -->
        @php
            $isLiked = $post->isLikedBy(auth()->user());
        @endphp
        <button type="button"
                onclick="window.toggleLike({{ $post->id }})"
                data-post-id="{{ $post->id }}"
                data-liked="{{ $isLiked ? 'true' : 'false' }}"
                class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-lg transition-colors {{ $isLiked ? 'text-red-600' : 'text-gray-600 dark:text-gray-300' }} hover:{{ $isLiked ? 'bg-red-50 dark:bg-red-900/20' : 'bg-gray-50 dark:hover:bg-gray-700' }}"
                id="like-button-{{ $post->id }}">
            <svg class="w-5 h-5 like-icon {{ $isLiked ? 'fill-current text-red-600' : 'fill-none' }}" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            <span class="font-medium text-sm">Like <span class="like-count">({{ $post->likes_count }})</span></span>
        </button>

        <!-- Comment Button -->
        <a href="{{ route('posts.show', $post) }}" class="flex-1">
            <button class="w-full flex items-center justify-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <span class="font-medium text-sm">Comment</span>
            </button>
        </a>

        <!-- Share Button -->
        <button type="button" onclick="window.sharePost('{{ route('posts.show', $post) }}', '{{ addslashes(Str::limit($post->content, 100)) }}')"
                class="flex-1 flex items-center justify-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
            </svg>
            <span class="font-medium text-sm">Share</span>
        </button>
    </div>

    <!-- Recent Comments Preview -->
    @if($post->comments->isNotEmpty())
        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-700">
            @foreach($post->comments->take(2) as $comment)
                <div class="mb-2 last:mb-0">
                    <a href="{{ route('profile.show', $comment->user) }}" class="font-semibold text-sm text-gray-900 dark:text-white hover:underline">
                        {{ $comment->user->name }}
                    </a>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ Str::limit($comment->content, 100) }}</span>
                </div>
            @endforeach

            @if($post->comments_count > 2)
                <a href="{{ route('posts.show', $post) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 mt-2 inline-block">
                    View all {{ $post->comments_count }} comments
                </a>
            @endif
        </div>
    @endif
</div>
