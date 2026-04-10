<!-- System Message -->
<div class="group-messages-message system" id="message-{{ $group_message->unique_id }}">
    <div class="group-messages-system-message">
        <div class="group-messages-system-icon">
            <i class="fa-solid fa-info-circle"></i>
        </div>
        <div class="group-messages-system-content">
            <span class="group-messages-system-text">{!! makeBoldBetweenAsterisks($group_message->message) !!}</span>
            <span class="group-messages-system-time">
                @php 
                    $timezone = getUserTimezone(); 
                    $checkTimezone = isValidTimezone($timezone); 
                @endphp
                @if($checkTimezone)
                    {{ $group_message->created_at->timezone($timezone)->format('H:i') }}
                @else
                    {{ date('H:i', strtotime($group_message->created_at)) }}
                @endif
            </span>
        </div>
    </div>
</div>
