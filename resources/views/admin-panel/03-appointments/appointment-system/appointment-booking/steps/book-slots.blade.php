<div class="cdsTYMainsite-login-form-container-header">
    <span>Book Slot</span>
  
</div>

<div class="cdsTYMainsite-login-form-container-body">
    <form id="bookslot-form" class="js-validate" action="{{ baseUrl('appointments/appointment-booking/save') }}" method="post">
        @csrf
        <input type="hidden" name="professional_id" id="professional_id" value="{{$professional_id}}">
        <input type="hidden" name="type" value="book-slot">
        <input type="hidden" name="booking_id" id="booking_id" value="{{$booking_id}}">
        <input type="hidden" name="selected_date"  id="selected_date" value="{{$appointment_date}}">
        <input type="hidden" name="working_hours_id" id="working_hours_id"  value="{{$working_hours_id}}">
        <div class="cdsTYMainsite-form-floating">
        <div class="cds-bookSlots"></div>
            
        </div>
        <div class="cdsTYMainsite-form-floating-button cdsTYMainsite-form-floating-segment-end">
            @if($appointment_data->start_time && $appointment_data->end_time)
            <div>
            <span>Booked Slot</span>

            <div class="cds-value">{{ $appointment_data->start_time_converted.'-'.$appointment_data->end_time_converted }}</div>

            </div>
            @endif
            <button type="button" data-step="4" class="CdsTYButton-btn-primary previous"> Previous</button>
    
            <button type="submit" id="confirmBooking" class="btn add-CdsTYButton-btn-primary">Save</button>
        </div>
    </form>
</div>


<!-- End Content -->
 
@push('scripts') 

<script>
$(document).ready(function() {
    var selectedDate = $("#selected_date").val();
    var professional_id = $("#professional_id").val();
        // fetchAvailableSlots(selectedDate, professional_id);

    function fetchAvailableSlots(date, professional_id) {
        var url = "{{ baseUrl('appointments/appointment-booking/fetch-available-slots') }}";
        var working_hours_id = $("#working_hours_id").val(); // Assuming you store working hours ID
        
        var booking_id = $("#booking_id").val(); // Assuming you store working hours ID
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                professional_id: professional_id,
                date: date,
                booking_id: booking_id,
                working_hours_id: working_hours_id
            },
            beforeSend: function () {
                showLoader();
            },
            success: function (response) {
                hideLoader();
                if (response.available_slots.length > 0) {
                    renderTimeSlots(response.available_slots);
                } else {
                    $(".cds-bookSlots").html('<p class="text-danger">No slots available</p>');
                }
            },
            error: function () {
                hideLoader();
                $(".cds-bookSlots").html('<p class="text-danger">Error fetching slots</p>');
            }
        });
    }
    function renderTimeSlots(slots) {
    var slotsHtml = '<h5>Select a Time Slot:</h5>';
    slotsHtml += '<div class="slot-container">';
    
    slots.forEach(function (slot) {
        
    let isSelected = (slot.start_time_24 == slot.selected_start_time && slot.end_time_24 == slot.selected_end_time) ? 'checked' : '';
    if(slot.type=="break"){
        slotsHtml += `
            <label class="slot-option" style="color:red">
                ${slot.start_time_12} - ${slot.end_time_12}
            </label>
        `;
    }else{
        slotsHtml += `
            <label class="slot-option">
                <input type="radio" name="time_slot" value="${slot.start_time_24}-${slot.end_time_24}" ${isSelected}>
                ${slot.start_time_12} - ${slot.end_time_12}
            </label>
        `;
    }
  
       
    });


    slotsHtml += '</div>';
    $(".cds-bookSlots").html(slotsHtml);
}
    $(document).on("change", "input[name='time_slot']", function () {
    $("#confirmBooking").prop("disabled", false);
});

// $("#confirmBooking").on("click", function () {
//     var selectedDate = $("#selected_date").val();
//     var selectedSlot = $("input[name='time_slot']:checked").val();
//     var professional_id = $("#professional_id").val();

//     if (!selectedSlot) {
//         alert("Please select a time slot.");
//         return;
//     }

//     $.ajax({
//         url: "{{ baseUrl('appointments/appointment-booking/save') }}",
//         type: 'POST',
//         data: {
//                 _token: "{{ csrf_token() }}",
//                 professional_id: professional_id,
//                 date: selectedDate,
//                 time_slot: selectedSlot
//             },
//             beforeSend: function () {
//                 showLoader();
//             },
//             success: function (response) {
//                 hideLoader();
//                 alert("Appointment booked successfully!");
//                 $("#calendar").fullCalendar("refetchEvents");
//             },
//             error: function () {
//                 hideLoader();
//                 alert("Error booking appointment. Please try again.");
//             }
//         });
//     });

    $("#bookslot-form").submit(function(e) {

        e.preventDefault();
        var is_valid = formValidation("form");
        if (!is_valid) {
            return false;
        }
        var formData = new FormData($(this)[0]);
        var url = $("#bookslot-form").attr('action');

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
                    errorMessage('Please select a slot for Booking');
                    validation(response.message);
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