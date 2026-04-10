@foreach($appointmentTypes as $key => $type)
    <label class="CdsDashboardCustomPopup-modal-radio-label @if($selected == $type->id) selected @endif">
        <input type="radio" 
               name="appointment_type_id" 
               value="{{ $type->id }}" 
               id="appointment_type_{{ $type->id }}"
               @if($selected == $type->id) checked @endif
               class="CdsDashboardCustomPopup-modal-radio-input"
               required>
        <div class="CdsDashboardCustomPopup-modal-radio-content">
            <div class="CdsDashboardCustomPopup-modal-radio-title">{{ $type->name }}</div>
            <div class="CdsDashboardCustomPopup-modal-radio-details">
                @if($type->duration)
                    Duration: {{ $type->duration->name??'' }} | 
                @endif
                Price: {{ $type->currency ?? 'USD' }} {{ $type->price }}
            </div>
        </div>
    </label>
@endforeach