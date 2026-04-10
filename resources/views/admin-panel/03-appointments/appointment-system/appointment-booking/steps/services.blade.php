<div class="CdsAppointmentSystem-header">
    <h1 class="CdsAppointmentSystem-title">Select Service</h1>
</div>

<form id="service-form" class="js-validate" action="{{ baseUrl('appointments/appointment-booking/save') }}" method="post">
    @csrf
    <input type="hidden" name="professional_id" value="{{$professional_id}}">
    <input type="hidden" name="type" value="services">
    <input type="hidden" name="booking_id" value="{{$booking_id}}">
    
    @foreach ($groupedServices as $parent => $subServices)
    <div class="CdsAppointmentSystem-form-group">
        <label class="CdsAppointmentSystem-label">{{ $parent }}</label>
        <div class="CdsAppointmentSystem-radio-group">
            @foreach($subServices as $service)
            <div class="CdsAppointmentSystem-radio-item" onclick="selectService({{$service['id']}})">
                <div class="CdsAppointmentSystem-radio @if(($appointment_data->professional_service_id ?? '') == $service['id']) checked @endif" id="service-{{$service['id']}}"></div>
                <span class="ps-1">{{$service['name']}}</span>
                <input type="radio" name="service" value="{{$service['id']}}" style="display: none;" @if(($appointment_data->professional_service_id ?? '') == $service['id']) checked @endif />
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    <div class="CdsAppointmentSystem-form-group">
        <label class="CdsAppointmentSystem-label">Appointment Types</label>
        <div class="CdsAppointmentSystem-service-grid">
            @foreach($appointmentTypes as $key => $type)
            <div class="CdsAppointmentSystem-service-card" onclick="selectAppointmentType({{$type->id}})">
                <h4>{{$type->name}}</h4>
                <p class="mb-0">{{optional($type->timeDuration)->name}}</p>
                <div class="CdsAppointmentSystem-service-price">
                    {{currencySymbol($type->currency).' '.$type->price}}
                </div>
                <button type="button" style="background: #e2e8f0; border: none; padding: 8px 16px; border-radius: 6px; margin-top: 12px;">Select Appointment Type</button>
                <input type="radio" name="appointment_type" value="{{$type->id}}" style="display: none;" @if($appointment_data->appointment_type_id == $type->id) checked @endif />
            </div>
            @endforeach
        </div>
    </div>
    
    <div class="CdsAppointmentSystem-btn-group">
        <button type="button" class="CdsAppointmentSystem-btn CdsAppointmentSystem-btn-secondary previous">Previous</button>   
        <button type="submit" class="CdsAppointmentSystem-btn CdsAppointmentSystem-btn-primary">Next</button>
    </div>
</form>

<script>
function selectService(id) {
    // Remove checked class from all service radios
    document.querySelectorAll('[id^="service-"]').forEach(radio => {
        radio.classList.remove('checked');
        radio.closest('.CdsAppointmentSystem-radio-item').querySelector('input[type="radio"]').checked = false;
    });
    
    // Add checked class to selected radio
    const selectedRadio = document.getElementById('service-' + id);
    selectedRadio.classList.add('checked');
    selectedRadio.closest('.CdsAppointmentSystem-radio-item').querySelector('input[type="radio"]').checked = true;
}

function selectAppointmentType(id) {
    // Remove selection from all service cards
    document.querySelectorAll('.CdsAppointmentSystem-service-card').forEach(card => {
        card.classList.remove('selected');
        card.querySelector('input[type="radio"]').checked = false;
    });
    
    // Add selection to clicked card
    const selectedCard = event.currentTarget;
    selectedCard.classList.add('selected');
    selectedCard.querySelector('input[type="radio"]').checked = true;
}

// Initialize selected states
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.CdsAppointmentSystem-service-card').forEach(card => {
        const radio = card.querySelector('input[type="radio"]');
        if (radio && radio.checked) {
            card.classList.add('selected');
        }
    });
});
</script>
@push('scripts')
<script>
$(document).ready(function() {
    $("#service-form").submit(function(e) {
        e.preventDefault();
        var is_valid = formValidation("form");
        if (!is_valid) {
            return false;
        }
        var formData = new FormData($(this)[0]);
        var url = $("#service-form").attr('action');

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