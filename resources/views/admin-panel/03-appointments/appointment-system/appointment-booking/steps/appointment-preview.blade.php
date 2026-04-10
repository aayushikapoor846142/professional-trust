<div class="CdsAppointmentSystem-header">
    <h1 class="CdsAppointmentSystem-title">Appointment Preview</h1>
</div>

<form id="preview-form1" class="js-validate" action="{{ baseUrl('appointments/appointment-booking/save') }}" method="post">
    @csrf
    <input type="hidden" name="professional_id" value="{{$professional_id}}" />
    <input type="hidden" name="type" value="appointment-preview" />
    <input type="hidden" name="booking_id" value="{{$booking_id}}" />
    
    <div class="CdsAppointmentSystem-preview-card">
        <div class="CdsAppointmentSystem-preview-row">
            <div class="CdsAppointmentSystem-preview-label">Appointment Date/Time (As per location timezone):</div>
            <div class="CdsAppointmentSystem-preview-value">
                {{ dateFormat($appointment_data->appointment_date) ?? '' }}<br>
                {{ $appointment_data->start_time_converted.'-'.$appointment_data->end_time_converted}}
            </div>
        </div>
        
        <div class="CdsAppointmentSystem-preview-row">
            <div class="CdsAppointmentSystem-preview-label">Appointment Date/Time (As per your profile timezone):</div>
            <div class="CdsAppointmentSystem-preview-value">
                {{ dateFormat($appointment_data->appointment_date) ?? '' }}<br>
                {{ $appointment_data->profile_timezone_start_time.'-'.$appointment_data->profile_timezone_end_time}}
            </div>
        </div>
        
        <div class="CdsAppointmentSystem-preview-row">
            <div class="CdsAppointmentSystem-preview-label">Appointment Status:</div>
            <div class="CdsAppointmentSystem-preview-value">{{ $appointment_data->status ?? '' }}</div>
        </div>
        
        <div class="CdsAppointmentSystem-preview-row">
            <div class="CdsAppointmentSystem-preview-label">Service:</div>
            <div class="CdsAppointmentSystem-preview-value">{{$appointment_data->service->name ?? ''}}</div>
        </div>
        
        <div class="CdsAppointmentSystem-preview-row">
            <div class="CdsAppointmentSystem-preview-label">For Client:</div>
            <div class="CdsAppointmentSystem-preview-value">{{ ($appointment_data->client->first_name ?? '') .' '.($appointment_data->client->last_name ?? '') }}</div>
        </div>
        
        <div class="CdsAppointmentSystem-preview-row">
            <div class="CdsAppointmentSystem-preview-label">Payment Status:</div>
            <div class="CdsAppointmentSystem-preview-value">{{$appointment_data->payment_status}}</div>
        </div>
        
        <div class="CdsAppointmentSystem-preview-row">
            <div class="CdsAppointmentSystem-preview-label">Booking Price:</div>
            <div class="CdsAppointmentSystem-preview-value">{{currencySymbol($appointment_data->currency).' '.$appointment_data->price}}</div>
        </div>
        
        <div class="CdsAppointmentSystem-preview-row">
            <div class="CdsAppointmentSystem-preview-label">Appointment Mode:</div>
            <div class="CdsAppointmentSystem-preview-value">{{ $appointment_data->appointment_mode }}</div>
        </div>
        
        <div class="CdsAppointmentSystem-preview-row">
            <div class="CdsAppointmentSystem-preview-label">Professional's Selected Location:</div>
            <div class="CdsAppointmentSystem-preview-value">
                <div class="render-address">
                    <div style="font-weight: 600; margin-bottom: 4px;">{{$fetchLoctimezone->company->company_name ?? ''}}</div>
                    <p class="mb-0 font14">
                        <span class="d-block">{{$fetchLoctimezone->address_1 ?? ''}}</span>
                        <span class="d-block">{{$fetchLoctimezone->address_2 ?? ''}}</span>
                        <span class="d-block">{{$fetchLoctimezone->state ?? ''}}, {{$fetchLoctimezone->city ?? ''}} {{$fetchLoctimezone->pincode ?? ''}}</span>                        
                        {{$fetchLoctimezone->country ?? ''}}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="CdsAppointmentSystem-preview-row">
            <div class="CdsAppointmentSystem-preview-label">Additional Info:</div>
            <div class="CdsAppointmentSystem-preview-value">{{$appointment_data->additional_info}}</div>
        </div>
    </div>
    
    <div class="CdsAppointmentSystem-form-group">
        <div class="CdsAppointmentSystem-radio-item">
            {!! FormHelper::formCheckbox(['name' => 'mark_as_free', 'value' => 1, 'checkbox_class' => 'termsCheckbox', 'required' => true, 'checked' => false ,'id'=>'mark_as_free']) !!}
            <label class="CdsAppointmentSystem-label" for="mark_as_free">Mark Appointment As Free</label>
        </div>
    </div>
    
    <div class="CdsAppointmentSystem-btn-group">
        <button type="button" class="CdsAppointmentSystem-btn CdsAppointmentSystem-btn-secondary previous">Previous</button>
        <button type="submit" class="CdsAppointmentSystem-btn CdsAppointmentSystem-btn-primary">Confirm Booking</button>
    </div>
</form>
@push('scripts')

<script>
$(document).ready(function() {
    $("#preview-form1").submit(function(e) {
        e.preventDefault();
        var is_valid = formValidation("form");
        if (!is_valid) {
            return false;
        }
        var formData = new FormData($(this)[0]);
        var url = $("#preview-form1").attr('action');

        $.ajax({
            url: url,
            type: "post",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            beforeSend: function() {
                showLoader();
            },
            success: function(response) {
                hideLoader();
                if (response.status == true) {
                    successMessage(response.message);
                    redirect(response.redirect_back);
                } else {
                    $.each(response.message, function(key, val) {
                       errorMessage(val);
                    });
                }
            },
            error: function() {
                internalError();
            }
        });
    });
});
</script>
@endpush