<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Chat;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('notification-channel{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat-conversation.{conversationId}', function ($user, $conversationId) {
    Log::info('hit channel', ['user' => $user ? $user->id : null, 'conversation_id' => $conversationId]);

    if (! $user) {
        return false;
    }

    $role = strtolower(trim((string) $user->role));
    if (in_array($role, ['admin', 'superadmin'], true) || $user->hasAnyRole(['admin', 'superadmin'])) {
        return true;
    }

    $participantIds = array_values(array_filter(array_map('intval', preg_split('/-/', (string) $conversationId))));
    if (count($participantIds) === 2) {
        return in_array((int) $user->id, $participantIds, true);
    }

    return Chat::where('conversation_id', $conversationId)
        ->where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                ->orWhere('receiver_id', $user->id);
        })->exists();
});

Broadcast::channel('shop.{shopId}', function ($user, $shopId) {

    return (int) $user->id === (int) $shopId && $user->hasRole('shop');
});

Broadcast::channel('user.{id}', function ($user, $id) {

    return (int) $user->id === (int) $id;
});
