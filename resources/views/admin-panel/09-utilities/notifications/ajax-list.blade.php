@if($records->isNotEmpty())
    @foreach($records as $value)
        <div class="cds-notification-item bg-light border">
                <div class="cds-notification-icon">📩</div>
                <div class="cds-notification-content ">
                    <a href="{{baseUrl('notification/redirect/'.$value->unique_id)}}">
                        <div class="cds-notification-title @if($value->is_read == 1) text-muted @endif">{{$value->comment}}</div>
                        <div class="cds-notification-time">{{ getChatNotificationTime($value->created_at) }}</div>
                    </a>
                </div>
                @if($value->is_read == 0)
                    <i class="cds-notification-close-btn fa-solid fa-bell text-primary" ></i>
                @else
                    <i class="cds-notification-close-btn fa-solid fa-check-circle text-primary" ></i>
                @endif
        </div>
    @endforeach
@else
    <div class="cds-notification-item">
        <div class="cds-notification-content">
            <div class="cds-notification-title">No notifications available.</div>
        </div>
    </div>
@endif

@if(!empty($records))
	@if($current_page < $last_page)
	<div class="notification-view-more-link text-center mt-3">
		<a href="javascript:;" class="CdsTYButton-btn-primary" onclick="loadData({{ $next_page }})">View More </a>
	</div>
	@endif
@endif
