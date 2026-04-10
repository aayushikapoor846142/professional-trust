@extends('admin-panel.layouts.app')

@section('content')
@php
$active_step = 1;
@endphp

<link rel="stylesheet" href="{{ asset('assets/css/CdsAppointmentSystem.css') }}">

<div class="CdsAppointmentSystem-container">
    <!-- Sidebar with Steps -->
    <div class="CdsAppointmentSystem-sidebar">
        <div class="CdsAppointmentSystem-step @if($completed_step == 0) active @elseif($completed_step >= 1) completed @endif" data-step="1">
            <div class="CdsAppointmentSystem-step-number">1</div>
            <div class="CdsAppointmentSystem-step-content">
                <h3>Book Appointment With</h3>
                <p>Select Professional</p>
            </div>
        </div>

        <div class="CdsAppointmentSystem-step @if($completed_step == 1) active @elseif($completed_step >= 2) completed @endif" data-step="2">
            <div class="CdsAppointmentSystem-step-number">2</div>
            <div class="CdsAppointmentSystem-step-content">
                <h3>Services</h3>
                <p>Choose Service Type</p>
            </div>
        </div>

        <div class="CdsAppointmentSystem-step @if($completed_step == 2) active @elseif($completed_step >= 3) completed @endif" data-step="3">
            <div class="CdsAppointmentSystem-step-number">3</div>
            <div class="CdsAppointmentSystem-step-content">
                <h3>Appointment Date</h3>
                <p>Select Date & Time</p>
            </div>
        </div>

        <div class="CdsAppointmentSystem-step @if($completed_step == 3) active @elseif($completed_step >= 4) completed @endif" data-step="4">
            <div class="CdsAppointmentSystem-step-number">4</div>
            <div class="CdsAppointmentSystem-step-content">
                <h3>Additional Information</h3>
                <p>Extra Details</p>
            </div>
        </div>

        <div class="CdsAppointmentSystem-step @if($completed_step == 4) active @elseif($completed_step >= 5) completed @endif" data-step="5">
            <div class="CdsAppointmentSystem-step-number">5</div>
            <div class="CdsAppointmentSystem-step-content">
                <h3>Preview</h3>
                <p>Review Booking</p>
            </div>
        </div>

        <div class="CdsAppointmentSystem-step @if($completed_step == 5) active @elseif($completed_step >= 6) completed @endif" data-step="6">
            <div class="CdsAppointmentSystem-step-number">6</div>
            <div class="CdsAppointmentSystem-step-content">
                <h3>Payment</h3>
                <p>Complete Booking</p>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="CdsAppointmentSystem-main">
        @if($appointment_data && $appointment_data->status=="awaiting")
        <div class="CdsAppointmentSystem-payment-notice">
            <strong>Important Payment Notice:</strong> Please complete your payment <strong>within 15 minutes</strong> to confirm your booking. If payment is not received, your booking will be <strong>automatically cancelled</strong>.
        </div>
        @endif

        @if($completed_step >= 0)
        <div class="CdsAppointmentSystem-step-content-area {{ $completed_step == 0?'active':'' }}" id="step-1">
            @include('admin-panel.03-appointments.appointment-system.appointment-booking.steps.appointment-for')
        </div>
        @endif

        @if($completed_step >= 1)
        <div class="CdsAppointmentSystem-step-content-area {{ $completed_step == 1?'active':'' }}" id="step-2">
            @include('admin-panel.03-appointments.appointment-system.appointment-booking.steps.services')
        </div>
        @endif

        @if($completed_step >= 2)
        <div class="CdsAppointmentSystem-step-content-area {{ $completed_step == 2?'active':'' }}" id="step-3">
            @include('admin-panel.03-appointments.appointment-system.appointment-booking.steps.appointment-date')
        </div>
        @endif

        @if($completed_step >= 3)
        <div class="CdsAppointmentSystem-step-content-area {{ $completed_step == 3?'active':'' }}" id="step-4">
            @include('admin-panel.03-appointments.appointment-system.appointment-booking.steps.additional-info')
        </div>
        @endif

        @if($completed_step >= 4)
        <div class="CdsAppointmentSystem-step-content-area {{ $completed_step == 4?'active':'' }}" id="step-5">
            @include('admin-panel.03-appointments.appointment-system.appointment-booking.steps.appointment-preview')
        </div>
        @endif

        @if($completed_step >= 5)
        <div class="CdsAppointmentSystem-step-content-area {{ $completed_step == 5?'active':'' }}" id="step-6">
            @include('admin-panel.03-appointments.appointment-system.appointment-booking.steps.payment')
        </div>
        @endif
    </div>
</div>
@endsection

@section("javascript")
<script>
    $(document).ready(function(){
        const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

        $.ajax({
            url: "{{baseUrl('/save-timezone')}}",
            method: 'POST',
            data: {
                timezone: timezone,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Timezone saved:', response);
            },
            error: function(xhr) {
                console.error('Failed to save timezone:', xhr.responseText);
            }
        });
    });

    $(".previous").click(function() {
        var step = $(this).parents(".CdsAppointmentSystem-step-content-area").attr('id').replace('step-', '');
        var prev_step = parseInt(step) - 1;
        console.log('Current step:', step);
        console.log('Previous step:', prev_step);
        
        // Remove active class from all step content areas
        $(".CdsAppointmentSystem-step-content-area").removeClass("active");
        
        // Remove active class from all sidebar steps
        $(".CdsAppointmentSystem-step").removeClass("active");
        
        // Add active class to the previous step content area using id selector
        $("#step-" + prev_step).addClass("active");
        
        // Update sidebar step states
        $(".CdsAppointmentSystem-step[data-step="+prev_step+"]").removeClass("completed");
        $(".CdsAppointmentSystem-step[data-step="+prev_step+"]").addClass("active");
        
        // Scroll to top of the container
        $("html, body").animate({
            scrollTop: $(".CdsAppointmentSystem-container").offset().top
        }, 800);
        
        // Special handling for step 3 (appointment date) to reload calendar
        if(prev_step == 3){
            setTimeout(() => {
                if(typeof loadCalendar === 'function') {
                    loadCalendar();
                }
            }, 1300);
        }
    });

    // Global function to handle radio button selection
    window.selectRadioButton = function(name, value) {
        // Remove checked from all radios in same group
        document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
            r.checked = false;
            const radioDiv = r.closest('.CdsAppointmentSystem-radio-item')?.querySelector('.CdsAppointmentSystem-radio');
            if (radioDiv) {
                radioDiv.classList.remove('checked');
            }
        });
        
        // Check the selected radio
        const targetInput = document.querySelector(`input[name="${name}"][value="${value}"]`);
        if (targetInput) {
            targetInput.checked = true;
            const radioDiv = targetInput.closest('.CdsAppointmentSystem-radio-item')?.querySelector('.CdsAppointmentSystem-radio');
            if (radioDiv) {
                radioDiv.classList.add('checked');
            }
            // Trigger change event
            targetInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
    };

    // Global function to handle appointment mode selection
    window.selectAppointmentMode = function(mode) {
        console.log('Global selectAppointmentMode called with mode:', mode);
        
        // Remove checked from all appointment mode radios
        document.querySelectorAll('input[name="appointment_mode"]').forEach(input => {
            input.checked = false;
            const radioDiv = input.closest('.CdsAppointmentSystem-radio-item')?.querySelector('.CdsAppointmentSystem-radio');
            if (radioDiv) {
                radioDiv.classList.remove('checked');
            }
        });
        
        // Check the selected radio
        const targetInput = document.querySelector(`input[name="appointment_mode"][value="${mode}"]`);
        if (targetInput) {
            targetInput.checked = true;
            const radioDiv = targetInput.closest('.CdsAppointmentSystem-radio-item')?.querySelector('.CdsAppointmentSystem-radio');
            if (radioDiv) {
                radioDiv.classList.add('checked');
            }
            
            // Filter locations based on appointment mode
            if (typeof filterLocationsByMode === 'function') {
                filterLocationsByMode(mode);
            }
            
            // Trigger change event
            targetInput.dispatchEvent(new Event('change', { bubbles: true }));
            console.log('Appointment mode updated to:', mode);
        } else {
            console.log('Appointment mode radio not found for:', mode);
        }
    };

    // Initialize radio button functionality
    function initializeRadioButtons() {
        // Initialize all radio buttons with modern styling
        document.querySelectorAll('.CdsAppointmentSystem-radio-item').forEach(radioItem => {
            const radio = radioItem.querySelector('.CdsAppointmentSystem-radio');
            const input = radioItem.querySelector('input[type="radio"]');
            
            if (radio && input) {
                // Set initial state
                if (input.checked) {
                    radio.classList.add('checked');
                }
                
                // Skip appointment_mode radios as they have custom handlers
                if (input.name === 'appointment_mode') {
                    return;
                }
                
                // Add click handler
                radioItem.addEventListener('click', function(e) {
                    const name = input.name;
                    
                    // Remove checked from all radios in same group
                    document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                        r.checked = false;
                        const radioDiv = r.closest('.CdsAppointmentSystem-radio-item')?.querySelector('.CdsAppointmentSystem-radio');
                        if (radioDiv) {
                            radioDiv.classList.remove('checked');
                        }
                    });
                    
                    // Check the clicked radio
                    input.checked = true;
                    radio.classList.add('checked');
                    
                    // Trigger change event for form validation
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                });
            }
        });
    }

    // Initialize on page load
    $(document).ready(function() {
        initializeRadioButtons();
    });

    // Re-initialize when steps change or content is loaded
    $(document).on('DOMContentLoaded', function() {
        initializeRadioButtons();
    });

    // Re-initialize after AJAX content loads
    $(document).on('ajaxComplete', function() {
        setTimeout(initializeRadioButtons, 100);
    });

    // Re-initialize when step content becomes active
    $(document).on('click', '.CdsAppointmentSystem-step', function() {
        setTimeout(initializeRadioButtons, 100);
    });
</script>
@endsection