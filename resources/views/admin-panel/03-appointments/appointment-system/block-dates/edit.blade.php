@extends('admin-panel.layouts.app')

@section('content')
 <div class="ch-action">
                    <a href="{{ baseUrl('appointments/appointment-booking/calendar') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-left fa-solid me-1"></i>
                        Back
                    </a>
                </div>
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">

--------------
			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <form id="mark-leave-form" class="js-validate mt-3" method="post"
                            action="{{baseUrl('appointments/block-dates/update/'.$record->unique_id)}}">
                            @csrf

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="js-form-message">
                                        @foreach($companyLocations as $key => $loc_record)
                                        <!-- Address Item 1 -->
                                        <div class="address-item appointment-location"
                                            data-mode="{{ $loc_record->type }}"
                                            id="personal-address-div-{{$loc_record->id}}">

                                            <div class="address-header">
                                                <div class="map-thumbnail">
                                                    <i class="fa-sharp fa-solid fa-location-dot"
                                                        style="color:#000000;"></i>
                                                </div>
                                                <div class="address-details render-address">
                                                    <div class="address-name">
                                                        <div class="company-name"
                                                            data-value="{{$loc_record->company->company_name ?? ''}}">
                                                            {{$loc_record->company->company_name ?? ''}}</div>
                                                    </div>

                                                    <div class="address-text">
                                                        <div class="address-1"
                                                            data-value="{{$loc_record->address_1 ?? ''}}">
                                                            {{$loc_record->address_1 ?? ''}}</div>
                                                        <div class="address-2"
                                                            data-value="{{$loc_record->address_2 ?? ''}}">
                                                            {{$loc_record->address_2 ?? ''}}</div>
                                                    </div>
                                                    <div class="address-text">
                                                        <div class="state" data-value="{{$loc_record->state ?? ''}}">
                                                            {{$loc_record->state ?? ''}}</div>
                                                        <div class="city" data-value="{{$loc_record->city ?? ''}}">
                                                            {{$loc_record->city ?? ''}}</div>

                                                        <div class="pincode"
                                                            data-value="{{$loc_record->pincode ?? ''}}">
                                                            {{$loc_record->pincode ?? ''}}</div>
                                                        <div class="country"
                                                            data-value="{{$loc_record->country ?? ''}}">
                                                            {{$loc_record->country ?? ''}}</div>

                                                    </div>

                                                </div>
                                                <div class="radio">
                                                    <input type="radio" name="location_id" value="{{$loc_record->id}}"
                                                        {{$loc_record->id==$record->location_id?'checked':''}}
                                                        onclick="window.setLocation(this.value)" />
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <!-- <div class="CDSComponents-Calender-inline01-container CDSComponents-Calender-inline01-popup"
                                    id="popupCalendar"></div> -->

                                    {!! FormHelper::formInputText(['name'=>"leave_date","readonly"=>true,"id" =>
                                    "calendarInput","label"=>"Select dates","required"=>true,
                                    'value'=>$record->leave_date]) !!}
                                </div>
                                <div class="col-xl-12">
                                    {!! FormHelper::formTextarea([
                                    'name'=>"reason",
                                    "id" => "reason",
                                    "required"=>true,
                                    "textarea_class" => "custom_editor",
                                    'value'=>$record->reason ?? '',
                                  
                                    ]) !!}
                                    <!-- <input type="hidden" name="markedLeaves" id="markedLeaves" value="{{$record->leave_date }}"> -->

                                </div>
                            </div>
                            <div class="text-start">
                                <button type="submit" class="CdsTYButton-btn-primary add-btn">Save</button>
                            </div>
                        </form>
                   
			</div>
	
	</div>
  </div>
</div>				
				

@endsection

@section('javascript')
<script src="{{url('assets/js/custom-editor.js')}}"></script>
<script src="{{url('assets/js/custom-datepicker.js')}}"></script>
<link href="{{ url('assets/css/custom-datepicker.css') }}" rel="stylesheet" />
<script>
    $(document).ready(function () {
        CustomCalendarWidget.initialize("calendarInput");
        CustomEditor.init(".custom_editor");

        $("#mark-leave-form").submit(function (e) {
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
                beforeSend: function () {
                    showLoader();
                },
                success: function (response) {
                    hideLoader();
                    if (response.status == true) {
                        successMessage(response.message);
                     
                         redirect(response.redirect_back);
                    } else {
                        errorMessage(response.message);
                        // validation(response.message);
                    }
                },
                error: function (xhr) {
                    internalError();
                                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.error_type === 'validation') {
            validation(xhr.responseJSON.message);
        } else {
            errorMessage('An unexpected error occurred. Please try again.');
        }
                }
            });

            });
    });
   

</script>

@endsection
