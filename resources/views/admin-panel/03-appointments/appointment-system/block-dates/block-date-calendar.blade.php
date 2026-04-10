<div class="cds-ty-dashboard-box-body">
    <form id="mark-leave-form" class="js-validate mt-3" method="post"
        action="{{baseUrl('appointments/block-dates/save')}}">
        @csrf
        <div class="cds-register-address-list">
            <div class="cdsTYMainsite-login-form-container-header ps-0">
                <span>Filter By Appointment Location</span>
            </div>
            <div class="js-form-message">
                @foreach($companyLocations as $key => $record)
                <!-- Address Item 1 -->
                <div class="address-item appointment-location" data-mode="{{ $record->type }}"
                    id="personal-address-div-{{$record->id}}">

                    <div class="address-header">
                        <div class="map-thumbnail">
                            <i class="fa-sharp fa-solid fa-location-dot" style="color:#000000;"></i>
                        </div>
                        <div class="address-details render-address">
                            <!-- <span class="badge main-house">
                        </span> -->
                            <div class="address-name">
                                <div class="company-name"
                                    data-value="{{$record->company->company_name ?? ''}}">
                                    {{$record->company->company_name ?? ''}}</div>
                            </div>

                            <div class="address-text">
                                <div class="address-1" data-value="{{$record->address_1 ?? ''}}">
                                    {{$record->address_1 ?? ''}}</div>
                                <div class="address-2" data-value="{{$record->address_2 ?? ''}}">
                                    {{$record->address_2 ?? ''}}</div>
                            </div>
                            <div class="address-text">
                                <div class="state" data-value="{{$record->state ?? ''}}">
                                    {{$record->state ?? ''}}</div>
                                <div class="city" data-value="{{$record->city ?? ''}}">
                                    {{$record->city ?? ''}}</div>

                                <div class="pincode" data-value="{{$record->pincode ?? ''}}">
                                    {{$record->pincode ?? ''}}</div>
                                <div class="country" data-value="{{$record->country ?? ''}}">
                                    {{$record->country ?? ''}}</div>

                            </div>

                        </div>
                        <div class="radio">
                            <input type="radio" name="location_id" value="{{$record->id}}" onclick="window.setLocation(this.value)" />
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                        <div id="leaveCalendar" class="calendar-wrapper"></div>
            </div>
            <div class="js-form-message">
                <input type="hidden" name="leave_dates" id="leave_dates"  value=""/>
            </div>

            {{--- <div class="col-xl-6">
                <!-- <div class="CDSComponents-Calender-inline01-container CDSComponents-Calender-inline01-popup"
                    id="popupCalendar"></div> -->

                <!-- {!! FormHelper::formInputText(['name'=>"leave_dates","readonly"=>true,"id" =>
                "calendarInput","label"=>"Select dates","required"=>true]) !!} -->
            </div> --}}
            <div class="col-xl-12 mt-3">
                {!! FormHelper::formTextarea(['name'=>"reason","id" => "reason","label"=>"Enter
                Reason","required"=>true, 
                ]) !!}
            </div>
        </div>
        <div class="text-start">
            <button type="submit" class="CdsTYButton-btn-primary add-btn">Save</button>
        </div>
    </form>
</div>
@push("scripts")
<link href="{{ url('assets/css/28-cds-enhance-calendar.css') }}" rel="stylesheet" />
<script src="{{url('assets/js/cds-enhance-calendar.js')}}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    let leaveCalendarInstance = null;

  function initializeLeaveCalendar(config = {}) {
    // Destroy existing calendar if any
    const container = document.getElementById("leaveCalendar");
    if (container) {
        container.innerHTML = '';
    }

    const calendarConfig = {
        title: 'Leave Calendar',
        events: {},
        startDate: new Date(),
    disabledDays: config.disabledDays || [],
         disabledDates: config.disabledDates || [],
        multiDateSelect: true,
        onDateSelect: function(selectedDates) {
    // 1. Convert to array if single date is selected
    const datesArray = Array.isArray(selectedDates) ? selectedDates : [selectedDates];
    
    // 2. Filter out disabled dates (Tuesday-Sunday)
    const enabledDates = datesArray.filter(date => {
        // Skip if not a valid date
        if (!(date instanceof Date)) return false;
        
        const dayOfWeek = date.getDay(); // 0 (Sunday) to 6 (Saturday)
        const dateStr = date.toISOString().split('T')[0]; // YYYY-MM-DD format
        
        // Only allow dates that aren't in disabledDays (Tuesday-Sunday)
        // and aren't in disabledDates (specific blocked dates)
        return !config.disabledDays.includes(dayOfWeek) && 
               !config.disabledDates.includes(dateStr);
    });

    const formatDate = (date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const formattedDates = enabledDates.map(date => formatDate(date));

$("#leave_dates").val(formattedDates.join(","));
console.log("Selected valid dates:", formattedDates);
}
    };

    leaveCalendarInstance = new EnhancedCalendar('#leaveCalendar', calendarConfig);
}


   function setLocation(location_id) {
    $.ajax({
        url: "{{ baseUrl('appointments/block-dates/fetch-location-leaves') }}",
        type: "POST",
        data: {
            _token: csrf_token,
            location_id: location_id
        },
        dataType: "json",
        beforeSend: showLoader,
        success: function (response) {
            hideLoader();
           // Convert day names to numbers (0=Sunday to 6=Saturday)
            const dayNameToNumber = {
                'sunday': 0,
                'monday': 1,
                'tuesday': 2,
                'wednesday': 3,
                'thursday': 4,
                'friday': 5,
                'saturday': 6
            };

            // Normalize day names to lowercase and map to numbers
            const disabledDays = (response.disabled_days || [])
                .map(day => day.toLowerCase())
                .map(day => dayNameToNumber[day])
                .filter(num => num !== undefined);

            console.log("Disabled day numbers:", disabledDays); // Should log [2,3,4,5,6,0]

            const config = {
                disabledDates: response.leave_dates || [],
                disabledDays: disabledDays // This will disable Tuesday-Sunday
            };
            
            initializeLeaveCalendar(config);
        },
        error: function (xhr) {
            hideLoader();
            if (xhr.status === 422) {
                validation(xhr.responseJSON.message);
            } else {
                errorMessage('Failed to load location data');
            }
        }
    });
}

    // Expose setLocation globally
    window.setLocation = setLocation;

    // Form submission
    $("#mark-leave-form").submit(function (e) {
        e.preventDefault();
        const is_valid = formValidation("form");
        if (!is_valid) return;
         const leaveDates = $("#leave_dates").val();

      const formData = new FormData(this);
         formData.set('leave_dates', leaveDates);
        
     
        const url = $(this).attr("action");

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            beforeSend: showLoader,
            success: function (response) {
                hideLoader();
                if (response.status === true) {
                    successMessage(response.message);
                    redirect(response.redirect_back);
                } else {
                    validation(response.message);
                }
            },
            error: function (xhr) {
                hideLoader();
                internalError();
                if (xhr.status === 422 && xhr.responseJSON?.error_type === 'validation') {
                    validation(xhr.responseJSON.message);
                } else {
                    errorMessage('An unexpected error occurred. Please try again.');
                }
            }
        });
    });
});

// $(document).ready(function () {
//     $("#mark-leave-form").submit(function (e) {
//         e.preventDefault();
//         var is_valid = formValidation("form");
//         if (!is_valid) {
//             return false;
//         }
//         var formData = new FormData($(this)[0]);
//         var url = $("#mark-leave-form").attr('action');

//         $.ajax({
//             url: url,
//             type: "post",
//             data: formData,
//             cache: false,
//             contentType: false,
//             processData: false,
//             dataType: "json",
//             beforeSend: function () {
//                 showLoader();
//             },
//             success: function (response) {
//                 hideLoader();
//                 if (response.status == true) {
//                     successMessage(response.message);
//                     // window.location.reload();
//                     redirect(response.redirect_back);
//                 } else {
//                     // errorMessage(response.message);
//                     validation(response.message);
//                 }
//             },
//             error: function (xhr) {
//                 internalError();
//                 if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.error_type === 'validation') {
//                     validation(xhr.responseJSON.message);
//                 } else {
//                     errorMessage('An unexpected error occurred. Please try again.');
//                 }
//             }
//         });

//     });

// })
// function initializeCalendar(param = {}){
//     console.log(param.disabledDays,'days');
//     EventCalendarWidget.initialize("leaveCalendar", {
//         multiDateSelect: true,
//         disabledDays: param.disabledDays !== undefined?param.disabledDays:[],
//         disabledDates: param.disabledDates !== undefined?param.disabledDates:[],
//         onDateSelect: function (selectedDates) {
//             $("#leave_dates").val(selectedDates.join(","));
//         }
//     });
// }
// function setLocation(location_id){
//     $.ajax({
//         url: "{{ baseUrl('appointments/block-dates/fetch-location-leaves') }}",
//         type: "post",
//         data: {
//             _token:csrf_token,
//             location_id:location_id
//         },
//         dataType: "json",
//         beforeSend: function () {
//             showLoader();
//         },
//         success: function (response) {
//             hideLoader();
//             if (response.status == true) {
//                 if(response.leave_dates.length > 0){
//                     var param = {
//                         disabledDates:response.leave_dates,
//                         disabledDays:response.disabled_days
//                     };
//                     initializeCalendar(param);
//                 }else{
//                     var param = {
//                         disabledDays:response.disabled_days
//                     };
//                     initializeCalendar(param);
//                 }
//             }
//         },
//         error: function (xhr) {
//             internalError();
//             if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.error_type === 'validation') {
//                 validation(xhr.responseJSON.message);
//             } else {
//                 errorMessage('An unexpected error occurred. Please try again.');
//             }
//         }
//     });
// }
</script>
@endpush