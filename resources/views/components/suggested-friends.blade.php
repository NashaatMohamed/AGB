@props(['suggestedFriends'])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
            </svg>
            Suggested Friends
        </h3>
    </div>

    @if($suggestedFriends->isNotEmpty())
        <div class="space-y-3">
            @foreach($suggestedFriends as $user)
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <!-- User Info -->
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <a href="{{ route('profile.show', $user) }}" class="flex-shrink-0">
                            @if($user->profile?->profile_picture)
                                <img src="{{ Storage::url($user->profile->profile_picture['path']) }}"
                                     alt="{{ $user->name }}"
                                     class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </a>

                        <div class="flex-1 min-w-0">
                            <a href="{{ route('profile.show', $user) }}" class="block font-medium text-sm text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 truncate">
                                {{ $user->name }}
                            </a>
                            @if($user->profile?->bio)
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $user->profile->bio }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Add Friend Button -->
                    <form action="{{ route('friendships.send', $user) }}" method="POST" class="ml-2 flex-shrink-0">
                        @csrf
                        <button type="submit"
                                class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">
                            Add
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <a href="{{ route('friends.index') }}" class="block mt-4 text-center text-sm text-blue-600 hover:text-blue-800 dark:hover:text-blue-400 font-medium">
            View All Suggestions →
        </a>
    @else
        <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">You've connected with everyone!</p>
        </div>
    @endif
</div>
