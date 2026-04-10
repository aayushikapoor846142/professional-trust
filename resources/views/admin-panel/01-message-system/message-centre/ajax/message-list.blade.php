@php
$lastDisplayedDate = null;
$displayedToday = false;
$displayedYesterday = false;
@endphp

@if(count($chat_messages)>0)
    @foreach($chat_messages as $chat_msg)
    @php
    $created_date = $chat_msg->created_at->format('Y-m-d');
    $check_prev_msg = $chat_msg->checkBeforeMessage($chat_msg->id,$chat_msg->chat_id,$created_date,auth()->user()->id);
    $formattedDate = '';
    // Check if the message is from today and if "Today" has not been displayed yet
    if ($chat_msg->created_at->isToday() && $check_prev_msg == 0) {
    $formattedDate = 'Today';
    $displayedToday = true;
    } elseif ($chat_msg->created_at->isYesterday() && $check_prev_msg == 0){
    $formattedDate = 'Yesterday';
    $displayedToday = true;
    }else if($check_prev_msg == 0){
    $formattedDate = $chat_msg->created_at->format('F d, Y');
    }
    @endphp

    @if(!empty($chat_msg))
    @if($formattedDate != '')
    <div class="datenotification msg-notification" data-date="{{ date("Y-m-d",strtotime($chat_msg->created_at)) }}" id="date-{{ $created_date }}">
        <div class="datenotification-label">
            {{ $formattedDate ?? '' }}
        </div>
    </div>
    @endif
    @if(isset($unread_from_id) && $unread_from_id == $chat_msg->unique_id)
    <div class="unread-message-from msg-notification">
        Unread Message
    </div>
    @endif
    @if($chat_msg->sent_by != auth()->user()->id)
        @include('admin-panel.01-message-system.message-centre.components.message.received-message', ['chat_msg' => $chat_msg])
    @else
        @include('admin-panel.01-message-system.message-centre.components.message.sent-message', ['chat_msg' => $chat_msg])
    @endif
    @endif
    @endforeach
@else
    <div class="no-messages">
        <p>No messages yet</p>
    </div>
@endif 