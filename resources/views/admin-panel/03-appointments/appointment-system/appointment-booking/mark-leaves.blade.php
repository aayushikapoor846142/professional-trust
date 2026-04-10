@extends('admin-panel.layouts.app')

@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">

                        <div class="cdsTYMainsite-login-form-container-header p-0">
                            <span>Filter By Appointment Location</span>
                        </div> 
                        <div class="dropdown">
                            <a class="CdsTYButton-btn-primary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Filter Location</a>
                            <ul class="dropdown-menu">
                                <li>
                                    <div class="js-form-message">
                                        @foreach($companyLocations as $key => $record)
                                        <!-- Address Item 1 -->
                                        <div class="address-item appointment-location" data-mode="{{ $record->type }}" id="personal-address-div-{{$record->id}}">
                                            <div class="address-header">
                                                <div class="map-thumbnail">
                                                    <i class="fa-sharp fa-solid fa-location-dot" style="color: #000000;"></i>
                                                </div>
                                                <div class="address-details render-address">
                                                    <!-- <span class="badge main-house">
                                                    </span> -->
                                                    <div class="address-name">
                                                        <div class="company-name" data-value="{{$record->company->company_name ?? ''}}">{{$record->company->company_name ?? ''}}</div>
                                                    </div>
                                                    <div class="address-text">
                                                        <div class="address-1" data-value="{{$record->address_1 ?? ''}}">{{$record->address_1 ?? ''}}</div>
                                                        <div class="address-2" data-value="{{$record->address_2 ?? ''}}">{{$record->address_2 ?? ''}}</div>
                                                    </div>
                                                    <div class="address-text">
                                                        <div class="state" data-value="{{$record->state ?? ''}}">{{$record->state ?? ''}}</div>
                                                        <div class="city" data-value="{{$record->city ?? ''}}">{{$record->city ?? ''}}</div>
                                                        <div class="pincode" data-value="{{$record->pincode ?? ''}}">{{$record->pincode ?? ''}}</div>
                                                        <div class="country" data-value="{{$record->country ?? ''}}">{{$record->country ?? ''}}</div>
                                                    </div>
                                                </div>
                                                <div class="radio">
                                                    <input type="radio" name="location_id" value="{{$record->id}}" onclick="setLocation(this.value)" />
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </li>
                            </ul>
                        </div>
                    
                

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <div class="cds-register-address-list cds-markLeaves">
                        <form id="mark-leave-form" method="post" action="{{baseUrl('appointments/appointment-booking/save-leaves')}}">                        
                        <div class="p-3">
                            <input type="text" name="markedLeaves" id="markedLeaves" value="{{implode(',', $leaves) }}" />
                            <h2>Mark Leave Calendar</h2>
                            @csrf
                            <button type="submit" class="CdsTYButton-btn-primary">Save Leaves</button>
                            <div class="js-form-message">
                                <input type="text" id="calendarInput" name="leave_dates" readonly placeholder="Select dates" />
                                <div class="CDSComponents-Calender-inline01-container CDSComponents-Calender-inline01-popup" id="popupCalendar"></div>
                            </div>
                        </div>
                    </form>
                </div>
			</div>
	
	</div>
  </div>
</div>


<!-- End Content -->
@endsection

@section('javascript')
<script src="{{url('assets/js/mark-leaves-custom-event.js')}}"></script>
<link href="{{ url('assets/css/mark-leaves-custom-event.css') }}" rel="stylesheet" />
<script>
      
    $("#mark-leave-form").submit(function(e) {

        e.preventDefault();
        var is_valid = formValidation("form");
        if (!is_valid) {
            return false;
        }
        var formData = new FormData($(this)[0]);
        var url = $("#mark-leave-form").attr('action');

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
                   // redirect(response.redirect_back);
                } else {
                    errorMessage(response.message);
                }
            },
            error: function() {
                internalError();
            }
        });

        });

</script>

@endsection