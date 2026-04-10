<style>
    .CdsAppointmentSystem-container {
        /* max-width: 1200px; */
        margin: 0 auto;
        padding: 20px;
        min-height: 100vh;
    }

    .CdsAppointmentSystem-main {
        background: white;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        min-height: 600px;
    }

    .CdsAppointmentSystem-header {
        margin-bottom: 40px;
    }

    .CdsAppointmentSystem-title {
        font-size: 28px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 8px;
    }

    .CdsAppointmentSystem-subtitle {
        font-size: 16px;
        color: #718096;
    }

    .CdsAppointmentSystem-alert {
        background: #fed7d7;
        border: 1px solid #feb2b2;
        color: #c53030;
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .CdsAppointmentSystem-alert a {
        color: #3182ce;
        text-decoration: none;
        font-weight: 500;
    }

    .CdsAppointmentSystem-form-group {
        margin-bottom: 24px;
    }

    .CdsAppointmentSystem-label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #2d3748;
    }

    .CdsAppointmentSystem-input {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .CdsAppointmentSystem-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .CdsAppointmentSystem-calendar {
        background: #f7fafc;
        border-radius: 12px;
        padding: 24px;
        margin-top: 20px;
    }

    .CdsAppointmentSystem-calendar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .CdsAppointmentSystem-nav-btn {
        background: #667eea;
        color: white;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .CdsAppointmentSystem-calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
    }

    .CdsAppointmentSystem-calendar-day {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .CdsAppointmentSystem-calendar-day:hover {
        background: #e2e8f0;
    }

    .CdsAppointmentSystem-calendar-day.selected {
        background: #667eea;
        color: white;
    }

    .CdsAppointmentSystem-calendar-day.disabled {
        color: #a0aec0;
        cursor: not-allowed;
    }

    .CdsAppointmentSystem-btn-group {
        display: flex;
        gap: 16px;
        justify-content: flex-end;
        margin-top: 40px;
    }

    .CdsAppointmentSystem-btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        min-width: 120px;
    }

    .CdsAppointmentSystem-btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .CdsAppointmentSystem-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    .CdsAppointmentSystem-btn-secondary {
        background: #e2e8f0;
        color: #4a5568;
    }

    .CdsAppointmentSystem-btn-secondary:hover {
        background: #cbd5e0;
    }

    .selectedDate{
        background:  #cde8db;
    }
    .disabled-class {
        background-color: #ddd;
        color: #aaa;  
        border: 1px solid #ccc; 
        cursor: not-allowed;
        opacity: 0.6;      
        pointer-events:none;
    }

    /* Responsive Design */
    @media (max-width: 991px) {
        .CdsAppointmentSystem-calendar {padding: 0;}
    }
    @media (max-width: 768px) {
        .CdsAppointmentSystem-container {
            padding: 15px;
        }

        .CdsAppointmentSystem-main {
            padding: 20px;
        }

        .CdsAppointmentSystem-btn-group {
            flex-direction: column;
        }

        .CdsAppointmentSystem-title {
            font-size: 24px;
        }

        .CdsAppointmentSystem-calendar-grid {
            gap: 4px;
        }
    }

    @media (max-width: 480px) {
        .CdsAppointmentSystem-container {
            padding: 10px;
        }

        .CdsAppointmentSystem-main {
            padding: 20px;
        }

        .CdsAppointmentSystem-calendar {
            padding: 16px;
        }
    }
</style>

<div class="CdsAppointmentSystem-container">
    <!-- Main Content Area -->
    <div class="CdsAppointmentSystem-main">
        <div class="CdsAppointmentSystem-header">
            <h1 class="CdsAppointmentSystem-title">Select Appointment Date</h1>
        </div>

        <div class="CdsAppointmentSystem-alert">
            <span>⚠️</span>
            <div>There is a difference in your and professional's timezone. So, dates may differ.</div>
        </div>

        <div class="CdsAppointmentSystem-form-group">
            <div><strong>Professional Timezone:</strong> {{ $profTz }}</div>
            <div><strong>Your Timezone:</strong> {{ $clientTz }}</div>
            <div><strong>Appointment Date:</strong> {{ $appointment_data->appointment_date??''}}</div>
        </div>

        <form id="date-form" class="js-validate" action="{{ baseUrl('appointments/appointment-booking/save') }}" method="post">
            @csrf
            <input type="hidden" name="time_type" value="" id="time_type">
            <input type="hidden" name="professional_id" id="professional_id" value="{{$professional_id}}">
            <input type="hidden" name="type" value="appointment-date">
            <input type="hidden" name="booking_id" id="booking_id" value="{{$booking_id}}">
            <input type="hidden" name="selected_date" value="{{ $appointment_data->appointment_date??''}}" id="selected_date">
            <input type="hidden" name="time_slot" value="{{ $start_end_time??''}}" id="selected_slot">
            <input type="hidden" name="working_hours_id" value="{{ $appointment_data->working_hours_id??''}}" id="working_hours_id">
            
            <div class="CdsAppointmentSystem-calendar">
                <div id="calendar"></div>
            </div>
            
            <div class="CdsAppointmentSystem-btn-group">
                <button type="button" class="CdsAppointmentSystem-btn CdsAppointmentSystem-btn-secondary previous">Previous</button>
                <button type="submit" class="CdsAppointmentSystem-btn CdsAppointmentSystem-btn-primary">Next</button>
            </div>
        </form>
    </div>
</div>

<!-- End Content -->
@push('scripts')
<!-- <script src="{{url('assets/vendor/moment/moment-with-locales.min.js')}}"></script>
<script src="{{url('assets/vendor/fullcalendar-latest/dist/index.global.min.js')}}"></script> -->
<script src="{{url('assets/js/custom-calendar.js')}}"></script>
<link href="{{ url('assets/css/custom-calendar.css') }}" rel="stylesheet" />

<script>
var selectedDate = $("#selected_date").val();
$(document).ready(function() {
    // $("#load-calendar-btn").on('click', function() {
    //     loadCalendar();
        
    // });
    
    var professional_id = $("#professional_id").val();
    loadCalendar();
    
    $(document).on("click",".CDSComponents-Calender-inline01-book-btn",function(){
        var time_slot = $(this).parents(".CDSComponents-Calender-inline01-slot-row").find(".time_slot").val();
        console.log("Time slot selected:", time_slot); // Debug log
        
        if (!time_slot || time_slot.trim() === '') {
            errorMessage("Invalid time slot. Please try again.");
            return false;
        }
        
        $("#selected_slot").val(time_slot);
        
        $(".CDSComponents-Calender-inline01-slot-row").removeClass("slot-selected");
        $(".CDSComponents-Calender-inline01-slot-row[data-date='"+time_slot+"']").addClass("slot-selected");
        
        console.log("Selected slot updated:", $("#selected_slot").val()); // Debug log
    });

    $("#date-form").submit(function(e) {
        e.preventDefault();
        
        // Debug: Log form data before submission
        var selectedDate = $("#selected_date").val();
        var timeSlot = $("#selected_slot").val();
        console.log("Selected Date:", selectedDate);
        console.log("Time Slot:", timeSlot);
        
        // Check if required fields are filled
        if (!selectedDate || selectedDate.trim() === '') {
            errorMessage("Please select an appointment date");
            return false;
        }
        
        if (!timeSlot || timeSlot.trim() === '') {
            errorMessage("Please select a time slot");
            return false;
        }
        
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
                console.log("Response:", response); // Debug response
                if (response.status == true) {
                    successMessage(response.message);
                    redirect(response.redirect_back);
                } else {
                    if (response.error_type === 'validation') {
                        // Handle validation errors
                        if (typeof response.message === 'object') {
                            var errorMsg = '';
                            for (var field in response.message) {
                                errorMsg += response.message[field] + '\n';
                            }
                            errorMessage(errorMsg);
                        } else {
                            errorMessage(response.message);
                        }
                    } else {
                        errorMessage(response.message);
                    }
                }
            },
            error: function(xhr, status, error) {
                hideLoader();
                console.log("AJAX Error:", xhr.responseText); // Debug error
                internalError();
            }
        });
    });
});
function fetchAvailableSlots(date) {
    // Check if date is provided and not empty
    if (!date || date.trim() === '') {
        $(".cds-bookSlots").html('<p class="text-muted">Please select a date to view available time slots</p>');
        return;
    }
    
    $("#selected_date").val(date);
    var url = "{{ baseUrl('appointments/appointment-booking/fetch-available-slots') }}";
    var working_hours_id = $("#working_hours_id").val(); // Assuming you store working hours ID
    var professional_id = "{{ $professional_id }}";
    var booking_id = $("#booking_id").val(); // Assuming you store working hours ID
    var location_id = $("input[name=location_id]:checked").val();
    $.ajax({
        url: url,
        type: 'POST',
        data: {
            _token: "{{ csrf_token() }}",
            professional_id: professional_id,
            date: date,
            booking_id: booking_id,
            location_id:location_id,
            // working_hours_id: working_hours_id
        },
        beforeSend: function () {
            showLoader();
        },
        success: function (response) {
            hideLoader();
            if (response.available_slots.length > 0) {
                renderBookingSlots(response.available_slots);
            } else {
                $(".cds-bookSlots").html('<p class="text-danger">No slots available for the selected date</p>');
            }
        },
        error: function () {
            hideLoader();
            $(".cds-bookSlots").html('<p class="text-danger">Error fetching slots</p>');
        }
    });
}
function renderBookingSlots(slots) {
    //var slotsHtml = '<h5>Select a Time Slot:</h5>';
    //slotsHtml += '<div class="slots">';
    var slotsHtml = '';
    slots.forEach(function (slot) {
        
    let isSelected = (slot.start_time_24 == slot.selected_start_time && slot.end_time_24 == slot.selected_end_time) ? 'slot-selected' : '';
    if(slot.type=="break"){
        slotsHtml += `
            <div class="CDSComponents-Calender-inline01-slot-row" style="color:red">
                ${slot.start_time_12} - ${slot.end_time_12}
            </div>
        `;
    }else{
        slotsHtml += `
            <div class="CDSComponents-Calender-inline01-slot-row ${isSelected}" data-date="${slot.start_time_24}-${slot.end_time_24}">
                <input type="hidden" class="time_slot" value="${slot.start_time_24}-${slot.end_time_24}" `+isSelected+` >
                <div>${slot.start_time_12} - ${slot.end_time_12}</div>
                <button type="button" class="CDSComponents-Calender-inline01-book-btn">Book</button>
            </div>
        `;
        // if(isSelected == 'checked'){
        //     var v = slot.start_time_24+'-'+slot.end_time_24;
        //     alert(v);
        //     $(".time_slot[value='"+v+"']").trigger('click');
        // }
    }
  
        
    });


    slotsHtml += '</div>';
    $(".cds-bookSlots").html(slotsHtml);
    // $("#modalSlots-"+index).html(slotsHtml);
    // $("#mobileSlots-"+index).html(slotsHtml);
    
}
var calendar = null;
function loadCalendar() {
    var professional_id = $("#professional_id").val();
    var booking_id = $("#booking_id").val();
    var selected_date = $("#selected_date").val();
    // initializeCalendar('calendar')
    var disabledDates= <?php echo json_encode($LeaveDays) ?>;
    var days = <?php echo json_encode($days) ?>;
    var params = {
        selectedDate: selected_date,
        days:days,
        disabledDates:disabledDates,
    };
    CalendarWidget.initialize("calendar",params);
   // CalendarWidget.initialize("calendar",selected_date,days);
    
    // Only fetch slots if a date is selected
    if (selected_date && selected_date.trim() !== '') {
        fetchAvailableSlots(selectedDate);
    } else {
        // Clear the slots area if no date is selected
        $(".cds-bookSlots").html('<p class="text-muted">Please select a date to view available time slots</p>');
    }
}
 
    
</script>
@endpush