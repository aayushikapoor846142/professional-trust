@extends('components.custom-popup',['modalTitle'=>'Locations'])
@section('custom-popup-content')
<div class="container">
    <div class="row">
        <div class="col-xl-12">
            <h5>Locations</h5>
        </div>
        <div class="col-xl-12">
            <div class="card rounded">
                <div class="card-body">
                    <div class="cds-form-container mb-0">
                        {{-- Select Duration Form --}}
                        @if($companyLocations->isNotEmpty())
                        <form id="select-location-form" class="js-validate" action="{{baseUrl('appointments/appointment-booking-flow/save-locations')}}" method="post" >
                            @csrf
                            <input type="hidden" name="duration_booking_flow_id"  value="{{$appointmentBookingFlow->unique_id ?? ''}}">
                            <div class="row">
                                <div class="col-xl-12">
                                <div class="cds-register-address-list">
                                    <div class="js-form-message">
                                        @foreach($companyLocations as $key => $record)
                                        <!-- Address Item 1 -->
                                        <div class="address-item appointment-location"  data-mode="{{ $record->type }}" style="display:{{ ($appointmentBookingFlow->appointment_mode??'') == $record->type?'block':'none' }}" id="personal-address-div-{{$record->id}}">
                                            <div class="address-header">
                                                <div class="radio">
                                                    <input type="radio" name="location_id" value="{{ $record->id }}"
                                                    @if($appointmentBookingFlow != '' && $appointmentBookingFlow->location_id == $record->id) checked @endif />
                                                </div>
                                                <div class="">
                                                    <span class="bg-warning text-white px-3 py-2 mb-2 d-block">{{ $record->type }}</span>
                                                    <div class="map-thumbnail  d-inline-block d-md-block">
                                                        <i class="fa-sharp fa-solid fa-location-dot" style="color:#000000;"></i>
                                                    </div>
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
                                                        <div class="state" data-value="{{$record->state ?? ''}}"> {{$record->state ?? ''}}</div>
                                                        <div class="city" data-value="{{$record->city ?? ''}}"> {{$record->city ?? ''}}</div>

                                                        <div class="pincode" data-value="{{$record->pincode ?? ''}}"> {{$record->pincode ?? ''}}</div>
                                                        <div class="country" data-value="{{$record->country ?? ''}}"> {{$record->country ?? ''}}</div>

                                                    </div>
                                                    <div class="address-action-btns mt-2">
                                                        @if(!in_array($record->id,$locationIdsWithWorkingHours))
                                                        <a href="javascript:;" onclick="showPopup('<?= baseUrl('appointments/appointment-booking-flow/working-hours-modal/'.$record->unique_id) ?>')" class="CdsTYButton-btn-primary CdsTYButton-border-thick btn-sm">
                                                            Add Working Hours
                                                        </a>
                                                        @else
                                                        <a href="javascript:;" onclick="showPopup('<?= baseUrl('appointments/appointment-booking-flow/working-hours-modal/'.$record->unique_id) ?>')" class="CdsTYButton-btn-primary CdsTYButton-border-thick btn-sm">
                                                            Edit Working Hours
                                                        </a>
                                                        @endif

                                                        </div>
                                                </div>                                                
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-start">
                                <a href="{{baseUrl('profile/company-detail')}}" class="btn add-CdsTYButton-btn-primary">Company Locations</a>

                                <button type="submit" class="CdsTYButton-btn-primary add-btn">Save</button>
                            </div>
                        </form>
                        @else 
                        <div class="alert alert-danger">No location available</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // function toggleLocationByMode() {
        //     const selectedMode = document.querySelector('input[name="appointment_mode"]:checked')?.value;
        //     const allLocations = document.querySelectorAll('.appointment-location');

        //     allLocations.forEach(function (el) {
        //         const locationMode = el.getAttribute('data-mode');
        //         if (selectedMode === locationMode) {
        //             el.style.display = 'block';
        //         } else {
        //             el.style.display = 'none';
        //         }
        //     });
        // }

        // Initial display on page load
      //  toggleLocationByMode();

        // Listen for changes in appointment mode
        
        
      
        // document.querySelectorAll('input[name="appointment_mode"]').forEach(function (radio) {
        //     radio.addEventListener('change', toggleLocationByMode);
        // });
    });
</script>
<script>
    $(document).ready(function() {
      
      
        $("#select-location-form").submit(function(e) {
            e.preventDefault();
            var is_valid = formValidation("select-location-form");
            if (!is_valid) return false;

            var formData = new FormData($(this)[0]);
            var url = $("#select-location-form").attr('action');

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
                        location.reload();
                    } else {
                        if (response.error_type == 'validation') {
                            validation(response.message);
                        } else {
                            errorMessage(response.message);
                        }
                    }
                },
                error: function() {
                    internalError();
                }
            });
        });
    });
</script>
@endsection