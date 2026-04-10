<div class="CdsTicket-message user-reply">
    <div class="CdsTicket-message-header">
        <div class="CdsTicket-avatar" style="background: {{ $reply->reply_type === 'admin' ? 'linear-gradient(135deg, #6366f1, #8b5cf6)' : 'linear-gradient(135deg, #10b981, #059669)' }};">
            {{ substr($reply->user->name ?? 'S', 0, 1) }}
        </div>
        <div class="CdsTicket-message-info">
            <div class="CdsTicket-message-author">
                {{ $reply->user->name ?? 'System' }}
                <span class="badge {{ $reply->reply_type_badge }} ms-2">{{ $reply->reply_type_text }}</span>
                @if($reply->is_internal)
                    <span class="badge bg-secondary ms-2">Internal</span>
                @endif
            </div>
            <div class="CdsTicket-message-time">{{ $reply->created_at->format('M d, Y \a\t g:i A') }}</div>
        </div>
    </div>
    <div class="CdsTicket-message-body">
        {!! $reply->formatted_message !!}
        @if($reply->attachments->count() > 0)
            <div class="CdsTicket-attachments mt-2">
                @foreach($reply->attachments as $attachment)
                    <a href="{{ $attachment->download_url }}" class="CdsTicket-attachment">
                        <i class="fa {{ $attachment->file_icon }}"></i>
                        {{ $attachment->original_name }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div> 