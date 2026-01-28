<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Public profile routes (authenticated users can view)
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/@{username}', [ProfileController::class, 'showByUsername'])->name('profile.username');

    // Posts routes
    Route::resource('posts', \App\Http\Controllers\PostController::class);

    // Comments routes
    Route::post('/posts/{post}/comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy');

    // Likes routes
    Route::post('/posts/{post}/likes', [\App\Http\Controllers\LikeController::class, 'toggle'])->name('likes.toggle');
    Route::get('/posts/{post}/likes', [\App\Http\Controllers\LikeController::class, 'likedBy'])->name('likes.show');

    // Friendships routes
    Route::get('/friends', [\App\Http\Controllers\FriendshipsController::class, 'index'])->name('friends.index');
    Route::post('/friends/{user}/request', [\App\Http\Controllers\FriendshipsController::class, 'sendRequest'])->name('friendships.send');
    Route::put('/friendships/{friendship}/accept', [\App\Http\Controllers\FriendshipsController::class, 'acceptRequest'])->name('friendships.accept');
    Route::put('/friendships/{friendship}/reject', [\App\Http\Controllers\FriendshipsController::class, 'rejectRequest'])->name('friendships.reject');
    Route::delete('/friendships/{friendship}/cancel', [\App\Http\Controllers\FriendshipsController::class, 'cancelRequest'])->name('friendships.cancel');
    Route::delete('/friends/{friend}', [\App\Http\Controllers\FriendshipsController::class, 'removeFriend'])->name('friendships.remove');
    Route::post('/users/{user}/block', [\App\Http\Controllers\FriendshipsController::class, 'blockUser'])->name('users.block');

    // Notifications routes
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/clear-all', [\App\Http\Controllers\NotificationController::class, 'clearAll'])->name('notifications.clear-all');
    Route::delete('/notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
});

require __DIR__.'/auth.php';
