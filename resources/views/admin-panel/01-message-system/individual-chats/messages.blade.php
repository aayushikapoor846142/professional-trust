@php
$lastDisplayedDate = null;
$displayedToday = false;
$displayedYesterday = false;
@endphp
@if(count($chat_messages) > 0)
    @foreach($chat_messages as $message)
    @php
    $created_date = $message->created_at->format('Y-m-d');
    $check_prev_msg = $message->checkBeforeMessage($message->id, $message->chat_id, $created_date, auth()->user()->id);
    $formattedDate = '';
    // Check if the message is from today and if "Today" has not been displayed yet
    if ($message->created_at->isToday() && $check_prev_msg == 0) {
        $formattedDate = 'Today';
        $displayedToday = true;
    } elseif ($message->created_at->isYesterday() && $check_prev_msg == 0) {
        $formattedDate = 'Yesterday';
        $displayedYesterday = true;
    } else if($check_prev_msg == 0) {
        $formattedDate = $message->created_at->format('F d, Y');
    }
    @endphp

    @if($formattedDate != '')
    <div class="CdsIndividualChat-date-separator" data-date="{{ date('Y-m-d', strtotime($message->created_at)) }}" id="date-{{ $created_date }}">
        <div class="CdsIndividualChat-date-label">
            {{ $formattedDate ?? '' }}
        </div>
    </div>
    @endif

    @if($message->sent_by == auth()->user()->id)
    <!-- Sent Message -->
    @include('admin-panel.01-message-system.individual-chats.sent-message')
    @else
    <!-- Received Message -->
    @include('admin-panel.01-message-system.individual-chats.receive-message')
    @endif
    @endforeach
@else
    <!-- No messages yet -->
    <div class="CdsIndividualChat-no-messages">
        <div class="CdsIndividualChat-no-messages-text">
            No messages yet. Start the conversation!
        </div>
    </div>
@endif