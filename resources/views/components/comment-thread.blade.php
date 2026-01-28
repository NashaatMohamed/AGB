@props(['comments', 'post'])

<div class="space-y-4">
    @forelse($comments as $comment)
        <div class="flex gap-3" id="comment-{{ $comment->id }}">
            <!-- Avatar -->
            <a href="{{ route('profile.show', $comment->user) }}" class="flex-shrink-0">
                @if($comment->user->profile?->profile_picture)
                    <img src="{{ Storage::url($comment->user->profile->profile_picture['path']) }}"
                         alt="{{ $comment->user->name }}"
                         class="w-8 h-8 rounded-full object-cover">
                @else
                    <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-semibold text-xs">
                        {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                    </div>
                @endif
            </a>

            <!-- Comment Content -->
            <div class="flex-1">
                <div class="bg-gray-100 rounded-2xl px-4 py-2">
                    <a href="{{ route('profile.show', $comment->user) }}" class="font-semibold text-sm text-gray-900 hover:underline">
                        {{ $comment->user->name }}
                    </a>
                    <p class="text-sm text-gray-800 mt-1 whitespace-pre-wrap">{{ $comment->content }}</p>
                </div>

                <!-- Comment Meta -->
                <div class="flex items-center gap-3 mt-1 px-4">
                    <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>

                    @if(auth()->id() === $comment->user_id)
                        <button type="button"
                                onclick="document.getElementById('edit-form-{{ $comment->id }}').classList.toggle('hidden')"
                                class="text-xs text-blue-600 hover:text-blue-800">
                            Edit
                        </button>
                        <form method="POST" action="{{ route('comments.destroy', $comment) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('Delete this comment?')"
                                    class="text-xs text-red-600 hover:text-red-800">
                                Delete
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Edit Form (hidden by default) -->
                @if(auth()->id() === $comment->user_id)
                    <form id="edit-form-{{ $comment->id }}"
                          method="POST"
                          action="{{ route('comments.update', $comment) }}"
                          class="hidden mt-2">
                        @csrf
                        @method('PUT')
                        <textarea name="content"
                                  rows="2"
                                  class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                                  required>{{ $comment->content }}</textarea>
                        <div class="flex gap-2 mt-2">
                            <button type="submit"
                                    class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                Save
                            </button>
                            <button type="button"
                                    onclick="document.getElementById('edit-form-{{ $comment->id }}').classList.add('hidden')"
                                    class="px-3 py-1 bg-gray-200 text-gray-700 text-xs rounded hover:bg-gray-300">
                                Cancel
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <p class="text-center text-gray-500 py-8">No comments yet. Be the first to comment!</p>
    @endforelse
</div>
