<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{chat_id}', function ($user, $chat_id) {
    return true; // Replace with proper authorization logic
});
Broadcast::channel('individual-chat.{chat_id}', function ($user, $chat_id) {
    return true; // Replace with proper authorization logic
});
Broadcast::channel('individual-chat-reaction.{chat_id}', function ($user, $chat_id) {
    return true; // Replace with proper authorization logic
});
Broadcast::channel('user-individual-chat.{user_id}', function ($user) {
    return true; // Replace with proper authorization logic
});
Broadcast::channel('user-notif.{user_id}', function ($user, $user_id) {
    return true; // Replace with proper authorization logic
});

Broadcast::channel('user.{user_id}', function ($user, $user_id) {
    return true; // Replace with proper authorization logic
});

Broadcast::channel('feed-content.{feed_id}', function ($user, $feed_id) {
    return true; // Replace with proper authorization logic
});

Broadcast::channel('group-chat.{group_id}', function ($user, $group_id) {
    return true; // Replace with proper authorization logic
});
Broadcast::channel('group-message-socket.{user_id}', function ($user) {
    return true; // Replace with proper authorization logic
});
Broadcast::channel('group-message.{group_id}', function ($user, $group_id) {
    return true; // Replace with proper authorization logic
});
Broadcast::channel('message_reaction', function ($user) {
    return true; // Replace with proper authorization logic
});

Broadcast::channel('groupMessageReaction.{group_id}', function ($user) {
    return true; // Replace with proper authorization logic
});
Broadcast::channel('chat_blocked.{chat_id}', function ($user,$chatId) {
    return true; // Replace with proper authorization logic
});

Broadcast::channel('chatBot.{chat_id}', function ($user, $user_id) {
    return true; // Replace with proper authorization logic
});

Broadcast::channel('chatMessageReaction.{chat_id}', function ($user, $user_id) {
    return true; // Replace with proper authorization logic
});

Broadcast::channel('chat_blocked.{chat_id}', function ($user,$chatId) {
    return true; // Replace with proper authorization logic
});

Broadcast::channel('discussion-content.{discussion_id}', function ($user, $discussion_id) {
    return true; // Replace with proper authorization logic
});


Broadcast::channel('discussion-threads.{discussion_id}', function ($user, $discussion_id) {
    return true; // Replace with proper authorization logic
});


Broadcast::channel('groupChatBot.{group_id}', function ($user, $user_id) {
    return true; // Replace with proper authorization logic
});

Broadcast::channel('groupChatBot.MessageReaction.{group_id}', function ($user, $user_id) {
    return true; // Replace with proper authorization logic
});


// Add this presence channel for typing indicators
Broadcast::channel('presence-chat.{chatId}', function ($user, $chatId) {
    $chat = \App\Models\Chat::where('id', $chatId)
        ->where(function($query) use ($user) {
            $query->where('user1_id', $user->id)
                  ->orWhere('user2_id', $user->id);
        })
        ->first();
    
    if ($chat) {
        return [
            'id' => $user->id,
            'name' => $user->first_name." ".$user->last_name
        ];
    }
    
    return false;
});

Broadcast::channel('presence-group.{groupId}', function ($user, $groupId) {
    $group = \App\Models\GroupMembers::where('group_id', $groupId)
        ->where(function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->first();
    
    if ($group) {
        return [
            'id' => $user->id,
            'name' => $user->first_name." ".$user->last_name
        ];
    }
    
    return false;
});


Broadcast::channel('global-notif.{user_id}', function ($user, $user_id) {
    return true; // Replace with proper authorization logic
});

Broadcast::channel('ticket-user.{user_id}', function ($user, $user_id) {
    return true; // Replace with proper authorization logic
});