<div class="chat-list chat-notification">
    <div class="chat-title">
        <h2>Chat Notifications</h2>
    </div>
    <div class="recent-chats">
        <h3>Recent</h3>
        <div id="chat-notifications" class="chat-notification-list">
            @if($chat_notifications->count() > 0)
            @foreach($chat_notifications as $notification)
            <a href="@if($notification->redirect_link){{ baseUrl($notification->redirect_link) }}@else{{ 'javascript:;' }} @endif" class="chat-item {{ $notification->is_read == 0 ? 'unread-notification' : '' }}"
                    data-id="{{ $notification->unique_id }}" onclick="markAsRead(this)"
                    id="notification-{{ $notification->unique_id }}">
                    
                
                    <div class="chat-avatar">
                        @if(optional($notification->sender)->profile_image != '')
                        <img src="{{ optional($notification->sender)->profile_image ? userDirUrl(optional($notification->sender)->profile_image, 't') : asset('assets/images/default.jpg') }}"
                            alt="User">

                        @else
                        <div class="group-icon" data-initial="{{ userInitial($notification->sender) }}"></div>

                        @endif
                    </div>
                    <div class="chat-info">
                        <p class="accepted">
                            {!! makeBoldBetweenAsterisks($notification->comment) !!}
                        </p>
                        <span class="notification-time">
                            {{ $notification->created_at->format('d M, Y h:i A') }}
                        </span>
                    </div> 
                </a>
            
        
            @endforeach
            @else
            <div class="empty-chat-request">
                <h5>No Notifications Available</h5>
            </div>
            @endif
        </div>
    </div>

    @if($chat_notifications->hasMorePages())
    <button id="load-more-notifications" data-page="1" class="CdsTYButton-btn-primary">Load More</button>
    @endif

    <div id="loading-spinner" style="display: none;">
        {{--<i class="fa fa-spinner fa-spin"></i> Loading...--}}
        @include('components.skelenton-loader.chatlistloder-skeleton')
    </div>
</div>
<script>
    function markAsRead(element) {
    const notificationId = element.getAttribute('data-id');
    // AJAX Request
    $.ajax({
        url: "{{ baseUrl('group/notifications/mark-as-read') }}",
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            notificationId: notificationId
        },
        success: function(response) {
            if (response.status == true) {
                // successMessage(response.message); 
                $('#notification-' + response.unique_id).removeClass('unread-notification');
                $('.notification-count').html(response.count);
                // location.reload();

            } else {
                console.error(response.message);
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
        }
    });
}
</script>