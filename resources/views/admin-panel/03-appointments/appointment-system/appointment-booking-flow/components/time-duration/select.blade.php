@foreach($durations as $key => $duration)
    <label class="CdsDashboardCustomPopup-modal-radio-label @if($selected == $duration->id) selected @endif">
        <input type="radio" 
               name="time_duration_id" 
               value="{{ $duration->id }}" 
               id="time_duration_{{ $duration->id }}"
               @if($selected == $duration->id) checked @endif
               class="CdsDashboardCustomPopup-modal-radio-input"
               required>
        <div class="CdsDashboardCustomPopup-modal-radio-content">
            <div class="CdsDashboardCustomPopup-modal-radio-title">{{ $duration->name }}</div>
            <div class="CdsDashboardCustomPopup-modal-radio-details">
                Duration: {{ $duration->duration }} minutes | Break: {{ $duration->break_time }} minutes
            </div>
        </div>
    </label>
@endforeach