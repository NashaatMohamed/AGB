@props(['user'])

@php
    $currentUser = auth()->user();
    $friendship = null;
    $buttonText = 'Add Friend';
    $buttonClass = 'bg-indigo-600 text-white hover:bg-indigo-700';
    $disabled = false;

    if ($currentUser->id === $user->id) {
        // Viewing own profile
        $buttonText = 'Your Profile';
        $disabled = true;
        $buttonClass = 'bg-gray-300 text-gray-500 cursor-not-allowed';
    } else {
        // Check friendship status
        $friendship = App\Models\Friendship::where(function ($query) use ($currentUser, $user) {
            $query->where('sender_id', $currentUser->id)
                ->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($currentUser, $user) {
            $query->where('sender_id', $user->id)
                ->where('receiver_id', $currentUser->id);
        })->first();

        if ($friendship) {
            if ($friendship->status === 'accepted') {
                $buttonText = 'Friends ✓';
                $buttonClass = 'bg-green-100 text-green-700 cursor-default';
                $disabled = true;
            } elseif ($friendship->status === 'pending') {
                if ($friendship->sender_id === $currentUser->id) {
                    $buttonText = 'Request Sent';
                    $buttonClass = 'bg-gray-300 text-gray-700 cursor-default';
                    $disabled = true;
                } else {
                    $buttonText = 'Accept Request';
                    $buttonClass = 'bg-indigo-600 text-white hover:bg-indigo-700';
                }
            } elseif ($friendship->status === 'blocked') {
                $buttonText = 'Unavailable';
                $buttonClass = 'bg-gray-300 text-gray-500 cursor-not-allowed';
                $disabled = true;
            }
        }
    }
@endphp

@if(!$disabled && $friendship && $friendship->status === 'pending' && $friendship->receiver_id === $currentUser->id)
    <!-- Accept friend request button -->
    <form action="{{ route('friendships.accept', $friendship) }}" method="POST" class="inline">
        @csrf
        @method('PUT')
        <button type="submit" class="inline-flex items-center px-4 py-2 {{ $buttonClass }} border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            {{ $buttonText }}
        </button>
    </form>
@elseif(!$disabled && !$friendship)
    <!-- Send friend request button -->
    <form action="{{ route('friendships.send', $user) }}" method="POST" class="inline">
        @csrf
        <button type="submit" class="inline-flex items-center px-4 py-2 {{ $buttonClass }} border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            {{ $buttonText }}
        </button>
    </form>
@else
    <!-- Disabled state -->
    <button disabled class="inline-flex items-center px-4 py-2 {{ $buttonClass }} border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none transition ease-in-out duration-150">
        {{ $buttonText }}
    </button>
@endif
