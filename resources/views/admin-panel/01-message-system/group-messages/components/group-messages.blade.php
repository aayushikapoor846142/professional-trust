@if(count($group_messages) > 0)
    @foreach($group_messages as $group_message)
        @php
            $created_date = $group_message->created_at->format('Y-m-d');
            $check_prev_msg = $group_message->checkBeforeMessage($group_message->id, $group_message->group_id, $created_date, auth()->user()->id);
            $formattedDate = '';

            // Check if the message is from today and if "Today" has not been displayed yet
            if ($group_message->created_at->isToday() && $check_prev_msg == 0) {
                $formattedDate = 'Today';
            } elseif ($group_message->created_at->isYesterday() && $check_prev_msg == 0) {
                $formattedDate = 'Yesterday';
            } elseif ($check_prev_msg == 0) {
                $formattedDate = $group_message->created_at->format('F d, Y');
            }
        @endphp

        @if(!empty($group_message))
            @if($formattedDate != '')
                <div class="group-messages-date-separator" data-date="{{ date('Y-m-d', strtotime($group_message->created_at)) }}" id="date-{{ $created_date }}">
                    <div class="group-messages-date-label">
                        {{ $formattedDate }}
                    </div>
                </div>
            @endif

            @if($group_message->user_id == 0)
                @include('admin-panel.01-message-system.group-messages.components.system-message', [
                    'group_message' => $group_message
                ])
            @elseif($group_message->user_id != auth()->user()->id)
                <!-- Received Message -->
                @if(checkGroupPermission('can_see_messages', $group_message->group_id))
                    @include('admin-panel.01-message-system.group-messages.components.received-message', [
                        'group_message' => $group_message,
                        'formattedDate' => $formattedDate
                    ])
                @endif
            @else
                <!-- Sent Message -->
                @include('admin-panel.01-message-system.group-messages.components.sent-message', [
                    'group_message' => $group_message,
                    'formattedDate' => $formattedDate
                ])
            @endif
        @endif
    @endforeach
@else
    <!-- No Messages -->
    <div class="group-messages-welcome-chat">
    <div class="group-messages-welcome-content">
        <div class="group-messages-welcome-icon">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
        </div>
        
        <h1 class="group-messages-welcome-title">No Message Available</h1>
        
    </div>
</div>

@endif