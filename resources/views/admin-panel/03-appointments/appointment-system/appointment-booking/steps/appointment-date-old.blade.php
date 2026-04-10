
<div class="cdsTYMainsite-login-form-container-header">
    <span>Select Appointment Date</span>
</div>
<div class="cdsTYMainsite-login-form-container-body">
   <p>Appointment Date-: <b>{{ $appointment_data->appointment_date??''}}</b> </p>
    <!-- <button id="load-calendar-btn" class="btn btn-outline-primary d-block m-auto mb-4"><i class="fa-regular fa-calendar-range fa-lg me-1"></i> Load Calendar</button> -->

    <form id="date-form" class="js-validate" action="{{ baseUrl('appointments/appointment-booking/save') }}" method="post">
        @csrf
        <input type="hidden" name="time_type" value="" id="time_type">
        <input type="hidden" name="professional_id" id="professional_id" value="{{$professional_id}}">
        <input type="hidden" name="type" value="appointment-date">
        <input type="hidden" name="booking_id" id="booking_id" value="{{$booking_id}}">
        <input type="hidden" name="selected_date" value="{{ $appointment_data->appointment_date??''}}" id="selected_date">
        <input type="hidden" name="working_hours_id" value="{{ $appointment_data->working_hours_id??''}}" id="working_hours_id">
        <div class="row">
            <div class="col-md-7">
                <div id="calendar"></div>
            </div>
            <div class="col-md-5">
                <div class="cds-bookSlots"></div>
            </div>
        </div>
        
        <div class="cdsTYMainsite-form-floating-button cdsTYMainsite-form-floating-segment-end">
            <button type="button" data-step="3" class="CdsTYButton-btn-primary previous"> Previous</button>
            <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
        </div>
    </form>
</div>


<!-- End Content -->
@push('scripts')
<script src="{{url('assets/vendor/moment/moment-with-locales.min.js')}}"></script>
<script src="{{url('assets/vendor/fullcalendar-latest/dist/index.global.min.js')}}"></script>


<script>
$(document).ready(function() {
    // $("#load-calendar-btn").on('click', function() {
    //     loadCalendar();
        
    // });
    var selectedDate = $("#selected_date").val();
    var professional_id = $("#professional_id").val();
    fetchAvailableSlots(selectedDate, professional_id);
    loadCalendar();
    $("#date-form").submit(function(e) {

        e.preventDefault();
        var is_valid = formValidation("form");
        if (!is_valid) {
            return false;
        }
        var formData = new FormData($(this)[0]);
        var url = $("#date-form").attr('action');

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
                    errorMessage(response.message);
                }
            },
            error: function() {
                internalError();
            }
        });

        });
});
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
var calendar = null;
function loadCalendar() {
    var professional_id = $("#professional_id").val();
    var booking_id = $("#booking_id").val();
    if ($('#calendar').data('fullCalendar')) {
        $('#calendar').fullCalendar('destroy'); // destroy if initialized
    }
    var calendarEl = document.getElementById('calendar');

    // Destroy existing calendar instance if it exists
    if (calendar !== null) {
        calendar.destroy();
        calendar = null; // Optional: reset reference
    }

    
    calendar = new FullCalendar.Calendar(calendarEl, {
        rerenderDelay: 10,
        initialView: "dayGridMonth", // Show the month view
      //  selectable: true, // Allow user selection
        validRange: {
            start: new Date(), // Disable past dates
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            $.ajax({
                url: "{{ baseUrl('appointments/appointment-booking/fetch-hours') }}",
                type: "GET",
                data: {
                    professional_id: professional_id,
                    booking_id: booking_id,
                    start: fetchInfo.startStr, // Fetch start date
                    end: fetchInfo.endStr, // Fetch end date
                },
                beforeSend: function () {
                    showLoader();
                },
                success: function (response) {
                    hideLoader();
                    successCallback(response);
                },
                error: function() {
                    failureCallback();
                }
            });
        },
        select: function(info) {
            var selectedStartDate = moment(info.start).format("YYYY-MM-DD");
            var selectedEndDate = moment(info.end).format("YYYY-MM-DD"); // If it's a range selection
            var today = moment().format("YYYY-MM-DD");

            // Prevent booking past dates
            if (selectedStartDate < today) {
                errorMessage("Not allowed to book for past date");
                return false;
            }

            $("#selected_date").val(selectedStartDate); // You can choose to show just the start or both start and end if it's a range

        },
         eventClick: function(info) {
            var selectedDate = moment(info.event.start).format("YYYY-MM-DD");
            var today = moment().format("YYYY-MM-DD");

            // Prevent booking past dates
            if (selectedDate < today) {
                errorMessage("Not allowed to book for past date");
                return false;
            }
            $("#selected_date").val(selectedDate);
            // Prevent booking on a day off
            if (info.event.extendedProps.time_type === "day_off") {
                errorMessage("This day is unavailable for booking.");
                
                // Clear the selected date value if it's a day off
                $("#selected_date").val(""); // Clear the value
                return false;
            }
            $('.fc-daygrid-day').removeClass('selectedDate');

            // Get local date properly
            var eventDate = info.event.start;
            var year = eventDate.getFullYear();
            var month = ('0' + (eventDate.getMonth() + 1)).slice(-2);
            var day = ('0' + eventDate.getDate()).slice(-2);

            var dateStr = year + '-' + month + '-' + day;

            // Add class to the correct cell
            $('.fc-daygrid-day[data-date="' + dateStr + '"]').addClass('selectedDate');
            $("#working_hours_id").val(info.event.id);
            fetchAvailableSlots(dateStr,'{{$professional_id}}');
            successMessage('Date Selected');
            // If it's not a day off, populate the fields
            $("#time_type").val(info.event.extendedProps.time_type);
           
            
        }
    });

    setTimeout(function () {
        calendar.render();
    }, 1500);
}
 
    
</script>
@endpush