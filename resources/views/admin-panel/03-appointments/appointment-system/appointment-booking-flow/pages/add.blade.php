@extends('admin-panel.layouts.app')
@if(checkPrivilege([
                        'route_prefix' => 'panel.appointment-booking-flow',
                        'module' => 'professional-appointment-booking-flow',
                        'action' => 'add'
                    ]))
                    @php
                    $canAddAppointmentBookingFlow=true;
                    @endphp
@else
                    @php
                    $canAddAppointmentBookingFlow=false;
                    @endphp
@endif
@php 
$page_arr = [
    'page_title' => 'Add Appointment Booking Flow ',
    'page_description' => 'Add new appointment booking flow.',
    'page_type' => 'add-appointment-booking-flow',
    'canAddAppointmentBookingFlow' => $canAddAppointmentBookingFlow,
    'appointmentBookingFlowFeatureStatus' => $appointmentBookingFlowFeatureStatus ?? null,
];
@endphp
@section('page-submenu')
{!! pageSubMenu('appointment-system',$page_arr) !!}
@endsection
@section('styles')
<link rel="stylesheet" href="{{url('assets/css/42-CDS-booking-flow.css')}}">
@endsection
@section('content')
            
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
                @if(isset($appointmentBookingFlowFeatureStatus))
                    @if(!$canAddAppointmentBookingFlow)
                        <div class="alert alert-danger mb-3">
                            <strong>⚠ Appointment Booking Flow Management</strong><br>
                            {{ $appointmentBookingFlowFeatureStatus['message']  }}
                           
                        </div>
                    @else
                        <div class="alert alert-warning mb-3">
                                <strong>⚠ Appointment Booking Flow Management</strong><br>
                           
                            {{ $appointmentBookingFlowFeatureStatus['message'] }}
                        </div>
                    @endif
                @endif
                <div class="CDSBookingsFlow02status-bar">
        <span>Your flow status is</span>
        <span class="CDSBookingsFlow02status-badge">
            @if($appointmentBookingFlow && $appointmentBookingFlow->status == 'draft')
                Draft
            @elseif($appointmentBookingFlow && $appointmentBookingFlow->status == 'pending')
                Pending
            @else
                New
            @endif
        </span>
    </div>

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
                @if($canAddAppointmentBookingFlow)
                <div class="CDSBookingsFlow02form-content">
        <form id="save-workflow-form" name="save-workflow-form" class="js-validate" action="{{ baseUrl('appointments/appointment-booking-flow/save') }}" method="post">
            @csrf
            <input type="hidden" name="booking_flow_id" id="booking_flow_id" value="{{$appointmentBookingFlow->unique_id ?? ''}}">
            
            <div class="CDSBookingsFlow02form-group">
                <label for="title">Enter Title</label>
                <input type="text" id="title" name="title" placeholder="Enter your title here" value="{{$appointmentBookingFlow->title ?? ''}}" required>
            </div>
            
            <div class="CDSBookingsFlow02form-group">
                <label for="description">Enter Description</label>
                <textarea id="description" name="description" placeholder="Describe your content in detail" required>{{$appointmentBookingFlow->description ?? ''}}</textarea>
            </div>
            
            <div class="CDSBookingsFlow02form-group">
                <label>Mode Selection</label>
                <div class="CDSBookingsFlow02mode-selector">
                    <label class="CDSBookingsFlow02radio-wrapper">
                        <input type="radio" name="appointment_mode" value="onsite" {{ (optional($appointmentBookingFlow)->appointment_mode ?? '') == 'onsite' ? 'checked' : '' }}>
                        <span class="CDSBookingsFlow02radio-custom"></span>
                        <span class="CDSBookingsFlow02radio-label">Onsite</span>
                    </label>
                    <label class="CDSBookingsFlow02radio-wrapper">
                        <input type="radio" name="appointment_mode" value="online" {{ (optional($appointmentBookingFlow)->appointment_mode ?? '') == 'online' ? 'checked' : '' }}>
                        <span class="CDSBookingsFlow02radio-custom"></span>
                        <span class="CDSBookingsFlow02radio-label">Online</span>
                    </label>
                </div>
                <div class="CDSBookingsFlow02warning-note">
                    <strong>Note:</strong> If you change appointment mode, you will need to add location again
                </div>
            </div>
            
            <button type="button" form="save-workflow-form" class="CDSBookingsFlow02save-btn save-btn" name="save">Save Changes</button>
        </form>
        
        <div class="CDSBookingsFlow02grid-container" @if($appointmentBookingFlow == "") style="display: none;" @endif>
            <div class="CDSBookingsFlow02card">
                <h3 class="CDSBookingsFlow02card-title">Time Duration</h3>
                <p class="CDSBookingsFlow02card-description">Set the duration for your appointment. This helps manage scheduling effectively.</p>
                <button class="CDSBookingsFlow02add-btn" onclick="openCustomPopup(this)" data-href="{{ baseUrl('appointments/appointment-booking-flow/add-time-duration/' . ($appointmentBookingFlow?->unique_id ?? '')) }}">
                    {{ $appointmentBookingFlow != '' && $appointmentBookingFlow->time_duration_id == 0 ? 'Add Duration' : 'Edit Duration' }}
                </button>
                @if(!empty($appointmentBookingFlow->timeDuration))
                <div class="CDSBookingsFlow02selected-items">
                    <strong>Your selected duration:</strong><br>
                    <strong>Name:</strong> {{$appointmentBookingFlow->timeDuration->name}}<br>
                    <strong>Duration:</strong> {{$appointmentBookingFlow->timeDuration->duration}}<br>
                    <strong>Type:</strong> {{$appointmentBookingFlow->timeDuration->type}}<br>
                    <strong>Break time:</strong> {{$appointmentBookingFlow->timeDuration->break_time}}
                </div>
                @endif
            </div>
            
            <div class="CDSBookingsFlow02card">
                <h3 class="CDSBookingsFlow02card-title">Types</h3>
                <p class="CDSBookingsFlow02card-description">Define different types of appointments or services you offer.</p>
                <button class="CDSBookingsFlow02add-btn" onclick="openCustomPopup(this)" data-href="{{ baseUrl('appointments/appointment-booking-flow/add-appointment-type/' . ($appointmentBookingFlow?->unique_id ?? '')) }}">
                    {{ $appointmentBookingFlow != '' && $appointmentBookingFlow->appointment_type_id == 0 ? 'Add Type' : 'Edit Type' }}
                </button>
                @if(!empty($appointmentBookingFlow->appointmentType))
                <div class="CDSBookingsFlow02selected-items">
                    <strong>Your selected Appointment type:</strong><br>
                    <strong>Name:</strong> {{$appointmentBookingFlow->appointmentType->name}}<br>
                    <strong>Price:</strong> {{$appointmentBookingFlow->appointmentType->price}}<br>
                    <strong>Currency:</strong> {{$appointmentBookingFlow->appointmentType->currency}}<br>
                    <strong>Duration:</strong> {{$appointmentBookingFlow->appointmentType->timeDuration->name}}
                </div>
                @endif
            </div>
            
            <div class="CDSBookingsFlow02card">
                <h3 class="CDSBookingsFlow02card-title">Location</h3>
                <p class="CDSBookingsFlow02card-description">Specify where the appointment will take place.</p>
                <button class="CDSBookingsFlow02add-btn" onclick="openCustomPopup(this)" data-href="{{ baseUrl('appointments/appointment-booking-flow/add-location/' . ($appointmentBookingFlow?->unique_id ?? '')) }}">
                    {{ $appointmentBookingFlow != '' && $appointmentBookingFlow->location_id == 0 ? 'Add Location' : 'Edit Location' }}
                </button>
                @if(!empty($appointmentBookingFlow->location))
                <div class="CDSBookingsFlow02selected-items">
                    <strong>Your selected location:</strong><br>
                    <strong>Address 1:</strong> {{$appointmentBookingFlow->location->address_1}}<br>
                    <strong>Address 2:</strong> {{$appointmentBookingFlow->location->address_2}}<br>
                    <strong>City:</strong> {{$appointmentBookingFlow->location->city}}<br>
                    <strong>State:</strong> {{$appointmentBookingFlow->location->state}}<br>
                    <strong>Country:</strong> {{$appointmentBookingFlow->location->country}}<br>
                    <strong>Pincode:</strong> {{$appointmentBookingFlow->location->pincode}}
                </div>
                @endif
            </div>
            
            <div class="CDSBookingsFlow02card">
                <h3 class="CDSBookingsFlow02card-title">Services</h3>
                <p class="CDSBookingsFlow02card-description">List the services available for this appointment.</p>
                <button class="CDSBookingsFlow02add-btn" onclick="openCustomPopup(this)" data-href="{{ baseUrl('appointments/appointment-booking-flow/add-service/' . ($appointmentBookingFlow?->unique_id ?? '')) }}">
                    {{ $appointmentBookingFlow != '' && $appointmentBookingFlow->service_id == '' ? 'Add Service' : 'Edit Service' }}
                </button>
                @if(!empty(getServices($appointmentBookingFlow->service_id ?? '')))
                <div class="CDSBookingsFlow02selected-items">
                    <strong>Your selected services:</strong><br>
                    @foreach(getServices($appointmentBookingFlow->service_id ?? '') as $value)
                        <strong>Name:</strong> {{$value->ImmigrationServices->name}}<br>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        
        @if(!empty($appointmentBookingFlow->timeDuration) && !empty($appointmentBookingFlow->appointmentType) && !empty($appointmentBookingFlow->location) && !empty(getServices($appointmentBookingFlow->service_id ?? '')))
        <div class="mt-4 text-start">
            <button type="button" class="CDSBookingsFlow02save-btn save-and-complete" name="save_and_complete">Save & Complete</button>
        </div>
        @endif
    </div>
                @else
                    <div class="text-center text-muted">
                        <p>You don't have permission to add appointment booking flow.</p>
                    </div>
                @endif

			</div>
	
	</div>
  </div>
</div>


@endsection
<!-- End Content -->
@push('scripts')
    <script>
        $(document).ready(function() {
            
            $(".save-btn").click(function(){
                submit_type = 'save';
                $("#save-workflow-form").submit();
            });
            $(".save-and-complete").click(function(){
                submit_type = 'save_and_complete';
                $("#save-workflow-form").submit();
            });
            $("#save-workflow-form").submit(function(e) {
                e.preventDefault();
                var is_valid = formValidation("save-workflow-form");
                if(!is_valid){
                    return false;
                }
                var formData = new FormData($(this)[0]);
                formData.append("submit_type",submit_type);
                var url = $("#save-workflow-form").attr('action');
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
                            if(response.error_type == 'validation'){
                                validation(response.message);
                            }else{
                                errorMessage(response.message);
                            }
                        }
                    },
                    error: function(xhr) {
                        internalError();
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.error_type === 'validation') {
                            validation(xhr.responseJSON.message);
                        } else {
                            errorMessage('An unexpected error occurred. Please try again.');
                        }
                    }
                });
            });

            // Add interactivity for the new design
            $('.CDSBookingsFlow02add-btn').on('click', function() {
                const card = $(this).closest('.CDSBookingsFlow02card');
                const title = card.find('.CDSBookingsFlow02card-title').text();
                
                // Animation feedback
                $(this).css('transform', 'scale(0.95)');
                setTimeout(() => {
                    $(this).css('transform', '');
                }, 200);
            });
            
            // Save button animation
            $('.CDSBookingsFlow02save-btn').on('click', function() {
                const originalText = $(this).text();
                $(this).text('Saving...');
                $(this).css('background', 'linear-gradient(135deg, #48bb78 0%, #38a169 100%)');
                
                setTimeout(() => {
                    $(this).text('Saved!');
                    setTimeout(() => {
                        $(this).text(originalText);
                        $(this).css('background', '');
                    }, 2000);
                }, 1000);
            });
            
            // Radio button change handler
            $('input[name="appointment_mode"]').on('change', function() {
                if ($(this).val() === 'online') {
                    $('.CDSBookingsFlow02warning-note').show();
                }
            });
        });
    </script>
@endpush