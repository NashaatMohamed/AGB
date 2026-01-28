<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private user channel for notifications
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Public channels for post likes and comments
Broadcast::channel('posts.{postId}.likes', function () {
    return true; // Public channel
});

Broadcast::channel('posts.{postId}.comments', function () {
    return true; // Public channel
});
