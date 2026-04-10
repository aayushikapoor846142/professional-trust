@if(count($todayEvents))
    <div class="CDSDashboardAppointmentEnhancedCalender-agenda-content">
        @foreach($todayEvents as $index => $event)
            <div class="CDSDashboardAppointmentEnhancedCalender-time-slot" data-index="{{ $index }}">
                <div class="CDSDashboardAppointmentEnhancedCalender-event-time">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8 0a8 8 0 100 16A8 8 0 008 0zM8 14a6 6 0 110-12 6 6 0 010 12zm.5-9.5v3.793l2.854 2.853a.5.5 0 01-.708.708l-3-3A.5.5 0 017.5 8V4.5a.5.5 0 011 0z"/>
                    </svg>
                    <strong>{{ $event['time'] }}</strong>
                </div>
                <div class="CDSDashboardAppointmentEnhancedCalender-event-title">
                    {{ $event['title'] }}
                </div>
                @if(!empty($event['description']))
                    <div class="CDSDashboardAppointmentEnhancedCalender-event-description">
                        {{ $event['description'] }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@else
    <div class="CDSDashboardAppointmentEnhancedCalender-agenda-empty">
         <div class="CDSDashboardAppointmentEnhancedCalender-empty-icon">📅</div>
        <p>No events scheduled for this day</p>
    </div>
@endif