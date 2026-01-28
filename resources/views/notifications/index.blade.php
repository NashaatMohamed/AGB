<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Notifications') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($notifications->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <p class="mt-4 text-gray-500">No notifications yet</p>
                        </div>
                    @else
                        <div class="space-y-1">
                            @foreach($notifications as $notification)
                                <div class="flex items-start gap-4 p-4 hover:bg-gray-50 rounded-lg {{ !$notification->read ? 'bg-blue-50' : '' }}">
                                    <!-- Icon based on notification type -->
                                    <div class="flex-shrink-0">
                                        @if($notification->type === 'friend_request')
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                                </svg>
                                            </div>
                                        @elseif($notification->type === 'friend_request_accepted')
                                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                            </div>
                                        @elseif($notification->type === 'post_liked')
                                            <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                                </svg>
                                            </div>
                                        @elseif($notification->type === 'new_comment')
                                            <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Notification Content -->
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $notification->message }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>

                                        <!-- Action buttons based on notification type -->
                                        @if($notification->type === 'friend_request' && isset($notification->data['friendship_id']))
                                            <div class="mt-2 flex gap-2">
                                                <form method="POST" action="{{ route('friendships.accept', $notification->data['friendship_id']) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="text-xs px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                                                        Accept
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('friendships.reject', $notification->data['friendship_id']) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="text-xs px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                                                        Decline
                                                    </button>
                                                </form>
                                            </div>
                                        @elseif(($notification->type === 'post_liked' || $notification->type === 'new_comment') && isset($notification->data['post_id']))
                                            <a href="{{ route('posts.show', $notification->data['post_id']) }}" class="inline-block mt-2 text-xs text-blue-600 hover:text-blue-800">
                                                View Post →
                                            </a>
                                        @endif
                                    </div>

                                    <!-- Mark as read / delete buttons -->
                                    <div class="flex-shrink-0 flex gap-2">
                                        @if(!$notification->read)
                                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-blue-600 hover:text-blue-800" title="Mark as read">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
