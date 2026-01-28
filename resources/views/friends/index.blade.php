<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Friends & Connections') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Pending Friend Requests -->
                @if($pendingRequests->count() > 0)
                    <div class="lg:col-span-3">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                    Pending Friend Requests ({{ $pendingRequests->count() }})
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($pendingRequests as $request)
                                        <div class="bg-gray-50 rounded-lg p-4 flex items-center space-x-4">
                                            @if($request->sender->profile && $request->sender->profile->profile_picture)
                                                <img src="{{ Storage::url($request->sender->profile->profile_picture['path']) }}"
                                                     alt="{{ $request->sender->name }}"
                                                     class="h-16 w-16 rounded-full object-cover">
                                            @else
                                                <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center text-xl font-bold text-gray-600">
                                                    {{ strtoupper(substr($request->sender->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div class="flex-1">
                                                <a href="{{ route('profile.show', $request->sender) }}" class="font-semibold text-gray-900 hover:text-indigo-600">
                                                    {{ $request->sender->name }}
                                                </a>
                                                <p class="text-xs text-gray-500">{{ $request->created_at->diffForHumans() }}</p>
                                                <div class="flex space-x-2 mt-2">
                                                    <form action="{{ route('friendships.accept', $request) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="px-3 py-1 bg-indigo-600 text-white text-xs font-semibold rounded hover:bg-indigo-700">
                                                            Accept
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('friendships.reject', $request) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="px-3 py-1 bg-gray-300 text-gray-700 text-xs font-semibold rounded hover:bg-gray-400">
                                                            Decline
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Friends List -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                My Friends ({{ $friends->count() }})
                            </h3>

                            @forelse($friends as $friend)
                                <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-0">
                                    <div class="flex items-center space-x-3">
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
                                            @if($friend->profile && $friend->profile->bio)
                                                <p class="text-sm text-gray-600 truncate max-w-xs">{{ Str::limit($friend->profile->bio, 50) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('profile.show', $friend) }}" class="px-3 py-1 bg-gray-100 text-gray-700 text-sm font-semibold rounded hover:bg-gray-200">
                                            View Profile
                                        </a>
                                        <form action="{{ route('friendships.remove', $friend) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this friend?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-100 text-red-700 text-sm font-semibold rounded hover:bg-red-200">
                                                Unfriend
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <!-- Show Suggested Friends when no friends -->
                                <div class="col-span-full">
                                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                        <div class="p-6">
                                            <div class="text-center mb-6">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                <h3 class="mt-2 text-lg font-semibold text-gray-900">No friends yet</h3>
                                                <p class="mt-1 text-sm text-gray-500">Start connecting with people to build your network.</p>
                                            </div>

                                            @if($suggestedFriends->isNotEmpty())
                                                <div class="border-t pt-6">
                                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">People you might know</h3>
                                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                        @foreach($suggestedFriends as $user)
                                                            <div class="bg-gray-50 rounded-lg p-4 flex flex-col items-center text-center">
                                                                <a href="{{ route('profile.show', $user) }}" class="block mb-2">
                                                                    @if($user->profile?->profile_picture)
                                                                        <img src="{{ Storage::url($user->profile->profile_picture['path']) }}"
                                                                             alt="{{ $user->name }}"
                                                                             class="w-16 h-16 rounded-full object-cover">
                                                                    @else
                                                                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl">
                                                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                                                        </div>
                                                                    @endif
                                                                </a>

                                                                <a href="{{ route('profile.show', $user) }}" class="font-semibold text-gray-900 hover:text-blue-600 mb-1">
                                                                    {{ $user->name }}
                                                                </a>

                                                                @if($user->profile?->bio)
                                                                    <p class="text-xs text-gray-500 mb-3 line-clamp-2">{{ $user->profile->bio }}</p>
                                                                @endif

                                                                <form action="{{ route('friendships.send', $user) }}" method="POST" class="w-full">
                                                                    @csrf
                                                                    <button type="submit" class="w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                                        Add Friend
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Sent Friend Requests -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                Sent Requests ({{ $sentRequests->count() }})
                            </h3>

                            @forelse($sentRequests as $request)
                                <div class="flex items-center space-x-3 mb-4 pb-4 border-b border-gray-200 last:border-0">
                                    @if($request->receiver->profile && $request->receiver->profile->profile_picture)
                                        <img src="{{ Storage::url($request->receiver->profile->profile_picture['path']) }}"
                                             alt="{{ $request->receiver->name }}"
                                             class="h-10 w-10 rounded-full object-cover">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center text-sm font-bold text-gray-600">
                                            {{ strtoupper(substr($request->receiver->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <a href="{{ route('profile.show', $request->receiver) }}" class="font-semibold text-sm text-gray-900 hover:text-indigo-600">
                                            {{ $request->receiver->name }}
                                        </a>
                                        <p class="text-xs text-gray-500">Sent {{ $request->created_at->diffForHumans() }}</p>
                                        <form action="{{ route('friendships.cancel', $request) }}" method="POST" class="mt-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:text-red-700">
                                                Cancel Request
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-600 text-center py-4">No pending sent requests.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
