<!-- No Chat Available State -->
@if(!$chat_users)
<div class="CdsIndividualChat-no-chat-state" id="noChatState">
    <div class="CdsIndividualChat-no-chat-icon">
        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
    </div>
    <div class="CdsIndividualChat-no-chat-title">No Conversations Yet</div>
    <div class="CdsIndividualChat-no-chat-subtitle">Start a new conversation to begin messaging</div>
    <div class="CdsIndividualChat-no-chat-actions">
        <button class="CdsIndividualChat-new-chat-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            New Chat
        </button>
    </div>
</div>
@else
@foreach($chat_users as $chat)
    @if(isset($chat->addedBy) && $chat->addedBy->id!=auth()->user()->id)
        @php
        $chat_with=$chat->addedBy;
        @endphp
    @else
        @php
        $chat_with=$chat->chatWith;
        @endphp
    @endif

    @php
    $timezone=getUserTimezone();
    $checkTimezone = isValidTimezone($timezone);
    $lastMessageTime = optional($chat->lastMessage)->created_at;
    $formattedTime = $lastMessageTime ? $lastMessageTime->format('H:i') : '';

    @endphp
<!-- Chat Items (Hidden when no chats) -->
<div class="CdsIndividualChat-chat-items">
    <div class="CdsIndividualChat-chat-item chat-user-item {{$chat->unique_id == $chat_id ? 'active' : ''}}" data-chat-id="{{$chat->id}}" data-chat-unique-id="{{$chat->unique_id}}">
        <div class="CdsIndividualChat-avatar">
            {!! getProfileImage($chat_with->unique_id,'s',52) !!}
            @if(loginStatus($chat_with) == 1)
            <span class="status-online login-status chatOnlineStatus{{$chat->id}}">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                </svg>
            </span>
            @else
            <span class="status-offline login-status chatOnlineStatus{{$chat->id}}">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                </svg>
            </span>
            @endif
            <!-- <div class="CdsIndividualChat-status-indicator"></div> -->
        </div>
        <div class="CdsIndividualChat-chat-info">
            <div class="CdsIndividualChat-chat-name">{{$chat_with->first_name." ".$chat_with->last_name}}</div>
            <div class="CdsIndividualChat-chat-preview">
            @if($chat->lastMessage)
                @if($chat->lastMessage->message != null)
                    {{$chat->lastMessage->message}}
                @elseif($chat->lastMessage->attachment != null)
                    <i><i class="fa fa-paperclip"></i> Attachment is sent</i>
                @else
                    No messages yet
                @endif
            @else
                No messages yet
            @endif
            </div>
        </div>
        <div class="CdsIndividualChat-chat-time">
            @if($lastMessageTime)
                @if($checkTimezone)
                {{$lastMessageTime->timezone($timezone)->format('H:i');}}
                @else
                {{$formattedTime}}
                @endif
            @endif
            @if($chat->unreadMessage($chat->id,auth()->user()->id) > 0)
                @php
                $unreadCount = $chat->unreadMessage($chat->id, auth()->user()->id);
                //$totalUnread += $unreadCount;
                @endphp

                <div>
                    <span class="unread-message">{{$chat->unreadMessage($chat->id,auth()->user()->id)}}</span>
                </div>
            @endif
        </div>
    </div>
</div>
@endforeach
@endif