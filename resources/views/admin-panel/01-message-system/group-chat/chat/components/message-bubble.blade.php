@props(['message', 'isOwn' => false])

<div class="message-bubble {{ $isOwn ? 'own-message' : 'other-message' }}" 
     data-message-id="{{ $message->id }}">
    
    @if(!$isOwn)
        <div class="message-sender">
            <span class="sender-name">{{ $message->sentBy->first_name }}</span>
        </div>
    @endif
    
    <div class="message-content">
        @if($message->reply_to)
            @include('admin-panel.01-message-system.group-chat.chat.components.reply-message', 
                     ['replyMessage' => $message->replyTo])
        @endif
        
        <div class="message-text">
            {!! nl2br(e($message->message)) !!}
        </div>
        
        @if($message->attachment)
            @include('admin-panel.01-message-system.group-chat.chat.components.file-preview', 
                     ['attachments' => $message->attachment])
        @endif
    </div>
    
    <div class="message-footer">
        <span class="message-time">{{ $message->created_at->format('H:i') }}</span>
        
        @if($isOwn)
            <div class="message-status">
                @if($message->is_read)
                    <i class="fa-solid fa-check-double read"></i>
                @else
                    <i class="fa-solid fa-check"></i>
                @endif
            </div>
        @endif
        
        @include('admin-panel.01-message-system.group-chat.chat.components.message-reactions', 
                 ['message' => $message])
    </div>
</div> 