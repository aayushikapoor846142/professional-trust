 @if(getChatNotification()->isNotEmpty())
    @foreach(getChatNotification() as $value)
        <div class="cds-case-notification-item @if($value->is_read == 0) bg-light border @endif">
                <div class="cds-case-icon">📩</div>
                <div class="cds-case-notification-content ">
                    <a href="{{baseUrl('notification/redirect/'.$value->unique_id)}}">
                        <div class="cds-case-notification-title @if($value->is_read == 1) text-muted @endif">{{$value->comment}}</div>
                        <div class="cds-case-notification-time">{{ getChatNotificationTime($value->created_at) }}</div>
                    </a>
                </div>
                @if($value->is_read == 0)
                    <i class="cds-case-close-btn fa-solid fa-bell text-primary" ></i>
                @else
                    <i class="cds-case-close-btn fa-solid fa-check-circle text-primary" ></i>
                @endif
        </div>
    @endforeach
@else
    <div class="cds-case-notification-item">
        <div class="cds-case-notification-content">
            <div class="cds-case-notification-title">No notifications available.</div>
        </div>
    </div>
@endif
