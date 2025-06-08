<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('online', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});

Broadcast::channel('chat', function ($user) {
    return true;
});

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    return true;
});
