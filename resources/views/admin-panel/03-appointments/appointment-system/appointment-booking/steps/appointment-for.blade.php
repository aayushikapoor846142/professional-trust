<div class="CdsAppointmentSystem-header">
    <h1 class="CdsAppointmentSystem-title">Appointment With Professional</h1>
</div>

<div id="timezone_notif">
</div>

<form id="add-appointment-form" class="js-validate" action="{{ baseUrl('appointments/appointment-booking/save') }}" method="post">
    @csrf
    
    <input type="hidden" name="booking_id" value="{{$booking_id}}">
    <input type="hidden" name="professional_id" value="{{$professional_id}}">
    <input type="hidden" name="type" value="appointment_for">
    
    <div class="CdsAppointmentSystem-form-group">
        {!! FormHelper::formSelect([
        'name' => 'appointment_for',
        'id' => 'user_id',
        'label' => 'Select Client',
        'required' => true,
        'options' => $clients,
        'value_column' => 'id',
        'label_column' => 'full_name',
                    'selected' => $appointment_data->user_id ?? '',
        'is_multiple' => false
        ]) !!}
    </div>
    
    @if(count($appointmentBookingFlow)>0)
    <div class="CdsAppointmentSystem-form-group">
        <label class="CdsAppointmentSystem-label">Appointment Booking Type</label>
        <div class="js-form-message">
            <div class="CdsAppointmentSystem-radio-group">
                <div class="CdsAppointmentSystem-radio-item" onclick="selectBookingType('booking_flow')">
                    <div class="CdsAppointmentSystem-radio @if(($appointment_data->booking_type ?? '') == 'booking_flow' || Session::get('predefined_booking_flow')) checked @endif" id="booking-type-booking_flow"></div>
                    <span>Predefined Booking Flow</span>
                    <input type="radio" name="booking_type" value="booking_flow" style="display: none;" @if(($appointment_data->booking_type ?? '') == 'booking_flow' || Session::get('predefined_booking_flow')) checked @endif />
                </div>
                <div class="CdsAppointmentSystem-radio-item" onclick="selectBookingType('general')">
                    <div class="CdsAppointmentSystem-radio @if(($appointment_data->booking_type ?? '') == 'general' && !Session::get('predefined_booking_flow')) checked @endif" id="booking-type-general"></div>
                    <span>General</span>
                    <input type="radio" name="booking_type" value="general" style="display: none;" @if(($appointment_data->booking_type ?? '') == 'general' && !Session::get('predefined_booking_flow')) checked @endif />
                </div>
            </div>
            <!-- Validation error display area for booking type -->
            <div id="booking_type-error" class="invalid-feedback" style="display: none;"></div>
        </div>
    </div>
    @else
    <input type="hidden" name="booking_type" value="general">
    @endif
    <div class="CdsAppointmentSystem-form-group" style="display:{{ (optional($appointment_data)->booking_type ?? '') == 'booking_flow' || Session::get('predefined_booking_flow') ? 'block' : 'none' }}" id="show-booking-flows">
        <label class="CdsAppointmentSystem-label">Predefined Booking Flow</label>
        <div class="js-form-message" >
            @foreach($appointmentBookingFlow as $key => $record)
            <div class="CdsAppointmentSystem-location-card appointment-booking-flow @if(($appointment_data->appointment_type_id ?? '') == $record->appointment_type_id || Session::get('predefined_booking_flow') == $record->id) selected @endif" id="booking-flow-div-{{$record->id}}" style="display:{{ (optional($appointment_data)->booking_type ?? '') == 'booking_flow' || Session::get('predefined_booking_flow') ? 'block' : 'none' }}">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div class="render-booking-flow">
                        <div style="margin-bottom: 8px;">
                            <strong>Booking Flow:</strong> {{$record->title ?? ''}}
                        </div>
                        <div style="margin-bottom: 4px;">
                            <strong>Appointment Time Duration:</strong> {{optional($record->timeDuration)->name ?? ''}}
                        </div>
                        <div style="margin-bottom: 4px;">
                            <strong>Appointment Type:</strong> {{optional($record->appointmentType)->name ?? ''}}
                        </div>
                        <div>
                            <strong>Appointment Mode:</strong> {{$record->appointment_mode ?? ''}}
                        </div>
                    </div>
                    <div class="CdsAppointmentSystem-radio-item">
                        <div class="CdsAppointmentSystem-radio @if(($appointment_data->appointment_type_id ?? '') == $record->appointment_type_id || Session::get('predefined_booking_flow') == $record->id) checked @endif" onclick="selectBookingFlow({{$record->id}})"></div>
                        <input type="radio" name="booking_flow_radio" value="{{$record->id}}" style="display:none;" @if(($appointment_data->appointment_type_id ?? '') == $record->appointment_type_id || Session::get('predefined_booking_flow') == $record->id) checked @endif />
                    </div>
                </div>
            </div>
            @endforeach
            <input type="hidden" name="predefined_booking_flow" id="predefined_booking_flow" value="{{ Session::get('predefined_booking_flow') ?? ($appointment_data->appointment_type_id ?? '') }}" />

        </div>
        <!-- Validation error display area for predefined booking flow -->
        <div id="predefined_booking_flow-error" class="invalid-feedback" style="display: none;"></div>
    </div>

    <div id="location-mode" style="display:{{ (optional($appointment_data)->booking_type ?? '') == 'booking_flow' || Session::get('predefined_booking_flow') ? 'none' : 'block' }}">
        <div class="CdsAppointmentSystem-form-group">
            <label class="CdsAppointmentSystem-label">Appointment Mode</label>
            <div class="js-form-message">
                <div class="CdsAppointmentSystem-radio-group">
                    <div class="CdsAppointmentSystem-radio-item" onclick="selectAppointmentMode('onsite')">
                        <div class="CdsAppointmentSystem-radio @if(($appointment_data->appointment_mode ?? '') == 'onsite') checked @endif" id="mode-onsite"></div>
                        <span class="ps-1">Onsite</span>
                        <input type="radio" name="appointment_mode" value="onsite" style="display: none;" @if(($appointment_data->appointment_mode ?? '') == 'onsite') checked @endif />
                    </div>
                    <div class="CdsAppointmentSystem-radio-item" onclick="selectAppointmentMode('online')">
                        <div class="CdsAppointmentSystem-radio @if(($appointment_data->appointment_mode ?? '') == 'online') checked @endif" id="mode-online"></div>
                        <span class="ps-1">Online</span>
                        <input type="radio" name="appointment_mode" value="online" style="display: none;" @if(($appointment_data->appointment_mode ?? '') == 'online') checked @endif />
                    </div>
                </div>
                <!-- Validation error display area for appointment mode -->
                <div id="appointment_mode-error" class="invalid-feedback" style="display: none;"></div>
            </div>
        </div>
        
        <div class="CdsAppointmentSystem-form-group">
            <label class="CdsAppointmentSystem-label">Appointment Location</label>
            <div class="js-form-message">
                @if(!empty($companyLocations))
                    @foreach($companyLocations as $key => $record)
                    <div class="CdsAppointmentSystem-location-card @if(($appointment_data->location_id ?? '') == $record->id) selected @endif" 
                         data-location-type="{{$record->type ?? 'onsite'}}" 
                         onclick="selectLocation({{$record->id}})">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <span style="font-size: 20px;">📍</span>
                            <div>
                                <div style="font-weight: 600; margin-bottom: 4px;">{{$record->company->company_name ?? ''}}</div>
                                <p class="mb-0 font14">
                                    {{$record->address_1 ?? ''}}<br>
                                    {{$record->address_2 ?? ''}}<br>
                                    {{$record->city ?? ''}}, {{$record->state ?? ''}} {{$record->pincode ?? ''}}<br>
                                    {{$record->country ?? ''}}
                                </p>
                            </div>
                        </div>
                        <input type="radio" name="location_id" value="{{$record->id}}" style="display: none;" @if(($appointment_data->location_id ?? '') == $record->id) checked @endif />
                    </div>
                    @endforeach
                @else 
                    <div class="CdsAppointmentSystem-alert">
                        <span>⚠️</span>
                        <div>There is no location available with working hours</div>
                    </div>
                @endif
                <!-- Validation error display area for location -->
                <div id="location_id-error" class="invalid-feedback" style="display: none;"></div>
            </div>
        </div>
    </div>
    
    <div class="CdsAppointmentSystem-btn-group">
        <button type="submit" class="CdsAppointmentSystem-btn CdsAppointmentSystem-btn-primary">Next</button>
    </div>
</form>
@push('scripts')

<style>
/* Custom validation styles for location cards */
.CdsAppointmentSystem-location-card.is-invalid {
    border: 2px solid #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.CdsAppointmentSystem-location-card.is-invalid:hover {
    border-color: #dc3545 !important;
}

/* Validation error styling */
.invalid-feedback {
    display: block !important;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
    font-weight: 500;
}

.invalid-feedback.d-block {
    display: block !important;
}

/* Enhanced validation styling for radio groups */
.CdsAppointmentSystem-radio-group.has-error {
    border: 1px solid #dc3545;
    border-radius: 4px;
    padding: 8px;
    background-color: rgba(220, 53, 69, 0.05);
}

/* Alert styling for no location message */
.CdsAppointmentSystem-alert.no-location-message {
    background-color: #fff3cd;
    border: 1px solid #ffeaa7;
    color: #856404;
    padding: 12px;
    border-radius: 4px;
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.CdsAppointmentSystem-alert.no-location-message span {
    font-size: 16px;
}
</style>

<script>
$(document).ready(function() {
    $("#add-appointment-form").on('submit', function(e) {
        e.preventDefault();
        
        // Clear previous validation errors
        $(".invalid-feedback").remove();
        $(".form-control").removeClass("is-invalid");
        $(".CdsAppointmentSystem-location-card").removeClass("is-invalid");
        
        // Ensure proper booking type is set if options are hidden
        var bookingTypeOptions = $('.CdsAppointmentSystem-radio-group');
        var hasBookingTypeOptions = bookingTypeOptions.length > 0 && bookingTypeOptions.is(':visible');
        var hasBookingFlows = {{ count($appointmentBookingFlow) }};
        
        // If no booking type options are visible or no booking flows exist, set to general
        if ((!hasBookingTypeOptions || hasBookingFlows === 0) && !$('input[name="booking_type"]:checked').val()) {
            $('input[name="booking_type"][value="general"]').prop('checked', true);
        }
        
        // Custom validation for this form
        var isValid = validateAppointmentForm();
        if (!isValid) {
            return false;
        }
        
        // Get form data
        var formData = new FormData(this);
        console.log(formData);
        var url = $("#add-appointment-form").attr('action');

        // Disable submit button to prevent double submission
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Submitting...');
        
        $.ajax({
            url: url,
            type: "POST",
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
                submitBtn.prop('disabled', false).text(originalText);
                
                if (response.status == true) {
                    successMessage(response.message);
                    if (response.redirect_back) {
                        redirect(response.redirect_back);
                    }
                } else {
                    validation(response.message);
                }
            },
            error: function(xhr, status, error) {
                hideLoader();
                submitBtn.prop('disabled', false).text(originalText);
                
                console.error('AJAX Error:', status, error);
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    validation(xhr.responseJSON.message);
                } else {
                    internalError();
                }
            }
        });
    });
});

// Custom validation function for appointment form
function validateAppointmentForm() {
    var isValid = true;
    
    // Validate client selection
    var selectedClient = $('select[name="appointment_for"]').val();
    if (!selectedClient || selectedClient === '') {
        showFieldError('appointment_for', 'Please select a client');
        isValid = false;
    }
    
    // Check if booking type options are available (not hidden)
    var bookingTypeOptions = $('.CdsAppointmentSystem-radio-group');
    var hasBookingTypeOptions = bookingTypeOptions.length > 0 && bookingTypeOptions.is(':visible');
    
    // Check if there are predefined booking flows available
    var hasBookingFlows = {{ count($appointmentBookingFlow) }};
    
    console.log('Has booking type options:', hasBookingTypeOptions);
    console.log('Has booking flows:', hasBookingFlows);
    
    // Validate booking type only if options are visible and booking flows exist
    if (hasBookingTypeOptions && hasBookingFlows > 0) {
        var selectedBookingType = $('input[name="booking_type"]:checked').val();
        if (!selectedBookingType) {
            showFieldError('booking_type', 'Please select a booking type');
            isValid = false;
        } else {
            // Clear booking type error if valid
            $('#booking_type-error').remove();
        }
    }
    
    // Determine the current booking type
    var currentBookingType = $('input[name="booking_type"]:checked').val();
    
    // If no booking type is selected but options are hidden (no booking flows), assume it's general
    if (!currentBookingType && !hasBookingTypeOptions) {
        currentBookingType = 'general';
    }
    
    // If booking type is general OR if no booking type options are available (no booking flows), validate appointment mode and location
    if (currentBookingType === 'general' || !hasBookingTypeOptions || hasBookingFlows === 0) {
        console.log('Validating appointment mode and location');
        
        // Validate appointment mode
        var selectedAppointmentMode = $('input[name="appointment_mode"]:checked').val();
        if (!selectedAppointmentMode) {
            showFieldError('appointment_mode', 'Please select an appointment mode');
            isValid = false;
        } else {
            // Clear appointment mode error if valid
            $('#appointment_mode-error').remove();
        }
        
        // Validate location selection
        var selectedLocation = $('input[name="location_id"]:checked').val();
        if (!selectedLocation) {
            showFieldError('location_id', 'Please select a location');
            isValid = false;
        } else {
            // Clear location error if valid
            $('#location_id-error').remove();
            $('.CdsAppointmentSystem-location-card').removeClass('is-invalid');
        }
        
        // Additional validation: Check if there are any visible locations for the selected mode
        if (selectedAppointmentMode) {
            var visibleLocations = $('.CdsAppointmentSystem-location-card:visible');
            if (visibleLocations.length === 0) {
                showFieldError('appointment_mode', 'No locations available for the selected appointment mode');
                isValid = false;
            }
        }
    }
    
    // If booking type is booking_flow, validate predefined booking flow
    if (currentBookingType === 'booking_flow') {
        var selectedBookingFlow = $('input[name="booking_flow_radio"]:checked').val();
        if (!selectedBookingFlow) {
            showFieldError('predefined_booking_flow', 'Please select a booking flow');
            isValid = false;
        } else {
            // Clear predefined booking flow error if valid
            $('#predefined_booking_flow-error').remove();
        }
    }
    
    return isValid;
}

// Function to show field-specific errors
function showFieldError(fieldName, message) {
    // Remove existing error for this field
    $('#' + fieldName + '-error').remove();
    
    // Create error message
    var errorHtml = '<div id="' + fieldName + '-error" class="invalid-feedback d-block">' + message + '</div>';
    
    // Find the appropriate container and append error
    var field = $('[name="' + fieldName + '"]');
    if (field.length > 0) {
        var container = field.closest('.js-form-message');
        if (container.length > 0) {
            container.append(errorHtml);
            
            // Add visual indication for location cards
            if (fieldName === 'location_id') {
                $('.CdsAppointmentSystem-location-card').addClass('is-invalid');
            }
        }
    } else {
        // Handle special cases where the field might not be a direct input
        if (fieldName === 'appointment_mode') {
            var container = $('.CdsAppointmentSystem-form-group:has(input[name="appointment_mode"]) .js-form-message');
            if (container.length > 0) {
                container.append(errorHtml);
            }
        } else if (fieldName === 'location_id') {
            var container = $('.CdsAppointmentSystem-form-group:has(.CdsAppointmentSystem-location-card) .js-form-message');
            if (container.length > 0) {
                container.append(errorHtml);
                $('.CdsAppointmentSystem-location-card').addClass('is-invalid');
            }
        }
    }
}
</script>
@endpush
<script>
function selectBookingFlow(id) {
    // Remove selection from all booking flows
    document.querySelectorAll('.appointment-booking-flow').forEach(flow => {
        flow.classList.remove('selected');
        const radio = flow.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = false;
        }
    });
    
    // Add selection to clicked booking flow
    const selectedFlow = document.getElementById('booking-flow-div-' + id);
    document.getElementById('predefined_booking_flow').value=id;
    if (selectedFlow) {
        selectedFlow.classList.add('selected');
        const radio = selectedFlow.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = true;
        }
    }
    
    // Clear predefined booking flow validation error
    $('#predefined_booking_flow-error').remove();
}

function selectLocation(id) {
    // Remove selection from all locations
    document.querySelectorAll('.CdsAppointmentSystem-location-card').forEach(card => {
        card.classList.remove('selected');
        card.classList.remove('is-invalid'); // Clear validation styling
        const radio = card.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = false;
        }
    });
    
    // Add selection to clicked location
    const selectedCard = document.querySelector(`[onclick="selectLocation(${id})"]`);
    if (selectedCard) {
        selectedCard.classList.add('selected');
        const radio = selectedCard.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = true;
        }
    }
    
    // Clear location validation error
    $('#location_id-error').remove();
    
    // Trigger form validation to update any dependent validations
    if (typeof validateAppointmentForm === 'function') {
        validateAppointmentForm();
    }
}

function selectAppointmentMode(mode) {
    console.log('selectAppointmentMode called with mode:', mode);
    
    // Remove checked class from all appointment mode radios
    document.querySelectorAll('input[name="appointment_mode"]').forEach(input => {
        input.checked = false;
        const radioDiv = input.closest('.CdsAppointmentSystem-radio-item')?.querySelector('.CdsAppointmentSystem-radio');
        if (radioDiv) {
            radioDiv.classList.remove('checked');
            console.log('Unchecked radio:', input.value);
        }
    });
    
    // Add checked class to selected radio
    const targetInput = document.querySelector(`input[name="appointment_mode"][value="${mode}"]`);
    if (targetInput) {
        targetInput.checked = true;
        const radioDiv = targetInput.closest('.CdsAppointmentSystem-radio-item')?.querySelector('.CdsAppointmentSystem-radio');
        if (radioDiv) {
            radioDiv.classList.add('checked');
            console.log('Checked radio:', mode);
        }
        
        // Filter locations based on appointment mode
        filterLocationsByMode(mode);
        
        // Trigger change event for form validation
        targetInput.dispatchEvent(new Event('change', { bubbles: true }));
        console.log('Appointment mode updated to:', mode);
        
        // Clear appointment mode validation error
        $('#appointment_mode-error').remove();
        
        // Clear location validation error since mode changed
        $('#location_id-error').remove();
        $('.CdsAppointmentSystem-location-card').removeClass('is-invalid');
        
        // Trigger form validation to update any dependent validations
        if (typeof validateAppointmentForm === 'function') {
            validateAppointmentForm();
        }
    } else {
        console.log('Radio button not found for mode:', mode);
    }
}

function filterLocationsByMode(mode) {
    console.log('Filtering locations for mode:', mode);
    
    // Get all location cards
    const locationCards = document.querySelectorAll('.CdsAppointmentSystem-location-card');
    let visibleCount = 0;
    
    locationCards.forEach(card => {
        const locationType = card.getAttribute('data-location-type');
        console.log('Location type:', locationType, 'for mode:', mode);
        
        if (locationType === mode) {
            // Show location card
            card.style.display = 'block';
            visibleCount++;
            console.log('Showing location card with type:', locationType);
        } else {
            // Hide location card
            card.style.display = 'none';
            console.log('Hiding location card with type:', locationType);
        }
    });
    
    console.log('Total visible locations:', visibleCount);
    
    // Show message if no locations are available for the selected mode
    const locationContainer = document.querySelector('.CdsAppointmentSystem-form-group:has(.CdsAppointmentSystem-location-card)');
    if (locationContainer) {
        let noLocationMessage = locationContainer.querySelector('.no-location-message');
        
        if (visibleCount === 0) {
            if (!noLocationMessage) {
                noLocationMessage = document.createElement('div');
                noLocationMessage.className = 'CdsAppointmentSystem-alert no-location-message';
                noLocationMessage.innerHTML = `
                    <span>⚠️</span>
                    <div>No ${mode} locations available with working hours</div>
                `;
                locationContainer.appendChild(noLocationMessage);
            }
            
            // Clear any selected location since none are available
            $('input[name="location_id"]').prop('checked', false);
            $('.CdsAppointmentSystem-location-card').removeClass('selected');
        } else {
            if (noLocationMessage) {
                noLocationMessage.remove();
            }
        }
    }
}

function selectBookingType(type) {
    console.log('selectBookingType called with type:', type);
    
    // Remove checked class from all booking type radios
    document.querySelectorAll('[id^="booking-type-"]').forEach(radio => {
        radio.classList.remove('checked');
        const radioItem = radio.closest('.CdsAppointmentSystem-radio-item');
        if (radioItem) {
            const input = radioItem.querySelector('input[type="radio"]');
            if (input) {
                input.checked = false;
            }
        }
    });
    const selectedRadio = document.getElementById('booking-type-' + type);
    if (selectedRadio) {
        selectedRadio.classList.add('checked');
        const radioItem = selectedRadio.closest('.CdsAppointmentSystem-radio-item');
        if (radioItem) {
            const input = radioItem.querySelector('input[type="radio"]');
            if (input) {
                input.checked = true;
            }
        }
        console.log('Radio button updated for type:', type);
    } else {
        console.log('Radio button not found for type:', type);
    }
    
    // Show/hide booking flows and location mode
    console.log('Calling showBookingFlows with type:', type);
    showBookingFlows(type);
    
    // Clear booking type validation error
    $('#booking_type-error').remove();
}

function showBookingFlows(value) {
    const bookingFlowsDiv = document.getElementById('show-booking-flows');
    const locationModeDiv = document.getElementById('location-mode');
    
    console.log('showBookingFlows called with value:', value);
    console.log('Value type:', typeof value);
    console.log('Value === "booking_flow":', value === 'booking_flow');
    
    if (value === 'booking_flow') {
        // Show booking flows, hide location mode
        console.log('Showing booking flows');
        if (bookingFlowsDiv) {
            $('.appointment-booking-flow').show();    // Add checked class to selected radio

            bookingFlowsDiv.style.display = 'block';
            console.log('Booking flows div found and shown');
        } else {
            console.log('Booking flows div not found');
        }
        if (locationModeDiv) {
            locationModeDiv.style.display = 'none';
            console.log('Location mode div hidden');
        } else {
            console.log('Location mode div not found');
        }
    } else {
        // Hide booking flows, show location mode
        console.log('Showing location mode');
        if (bookingFlowsDiv) {
            bookingFlowsDiv.style.display = 'none';
            console.log('Booking flows div hidden');
        }
        if (locationModeDiv) {
            locationModeDiv.style.display = 'block';
            console.log('Location mode div shown');
        }
    }
    
    // Update validation state after changing visibility
    updateValidationState();
}

// Function to update validation state when form sections change
function updateValidationState() {
    // Clear all validation errors when switching between booking types
    $('.invalid-feedback').remove();
    $('.CdsAppointmentSystem-location-card').removeClass('is-invalid');
    
    // Check current booking type and ensure proper validation is active
    var currentBookingType = $('input[name="booking_type"]:checked').val();
    var bookingTypeOptions = $('.CdsAppointmentSystem-radio-group');
    var hasBookingTypeOptions = bookingTypeOptions.length > 0 && bookingTypeOptions.is(':visible');
    var hasBookingFlows = {{ count($appointmentBookingFlow) }};
    
    // If no booking type options are visible or no booking flows exist, ensure general validation is active
    if ((!hasBookingTypeOptions || hasBookingFlows === 0) && !currentBookingType) {
        $('input[name="booking_type"][value="general"]').prop('checked', true);
    }
}

// Initialize radio button styling
document.addEventListener('DOMContentLoaded', function() {
    // Check if there's a predefined booking flow in session and auto-select booking_flow type
    @if(Session::get('predefined_booking_flow'))
        // Auto-select booking_flow type
        selectBookingType('booking_flow');
        showBookingFlows('booking_flow');
        $('.appointment-booking-flow').show();    // Add checked class to selected radio

        // Auto-select the specific booking flow
        var predefinedFlowId = {{ Session::get('predefined_booking_flow') }};
        selectBookingFlow(predefinedFlowId);
    @endif
    
    // Style radio buttons
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        const radioItem = radio.closest('.CdsAppointmentSystem-radio-item');
        if (radioItem) {
            const radioDiv = radioItem.querySelector('.CdsAppointmentSystem-radio');
            if (radioDiv && radio.checked) {
                radioDiv.classList.add('checked');
            }
            
            radio.addEventListener('change', function() {
                // Remove checked class from all radios in the same group
                const name = this.name;
                document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                    const item = r.closest('.CdsAppointmentSystem-radio-item');
                    if (item) {
                        const div = item.querySelector('.CdsAppointmentSystem-radio');
                        if (div) {
                            div.classList.remove('checked');
                        }
                    }
                });
                
                // Add checked class to selected radio
                if (this.checked) {
                    const item = this.closest('.CdsAppointmentSystem-radio-item');
                    if (item) {
                        const div = item.querySelector('.CdsAppointmentSystem-radio');
                        if (div) {
                            div.classList.add('checked');
                        }
                    }
                }
            });
        }
    });
    
    // Initialize selected states for location cards
    document.querySelectorAll('.CdsAppointmentSystem-location-card.selected').forEach(card => {
        card.classList.add('selected');
    });
    
    // Initialize booking flows display based on current selection
    const selectedBookingType = document.querySelector('input[name="booking_type"]:checked');
    if (selectedBookingType) {
        showBookingFlows(selectedBookingType.value);
    }
    
    // Initialize location filtering based on current appointment mode
    const selectedAppointmentMode = document.querySelector('input[name="appointment_mode"]:checked');
    if (selectedAppointmentMode) {
        filterLocationsByMode(selectedAppointmentMode.value);
    }
    
    // Add click handlers for location cards if not already present
    document.querySelectorAll('.CdsAppointmentSystem-location-card').forEach(card => {
        if (!card.hasAttribute('onclick')) {
            const radio = card.querySelector('input[type="radio"]');
            if (radio) {
                card.addEventListener('click', function() {
                    selectLocation(radio.value);
                });
            }
        }
    });
    
    // Initialize validation state based on current form state
    initializeValidationState();
});

// Function to initialize validation state
function initializeValidationState() {
    // Check if booking type options are visible
    var bookingTypeOptions = $('.CdsAppointmentSystem-radio-group');
    var hasBookingTypeOptions = bookingTypeOptions.length > 0 && bookingTypeOptions.is(':visible');
    var hasBookingFlows = {{ count($appointmentBookingFlow) }};
    
    console.log('Initializing validation state');
    console.log('Has booking type options:', hasBookingTypeOptions);
    console.log('Has booking flows:', hasBookingFlows);
    
    // If no booking type options are visible or no booking flows exist, ensure general validation is active
    if (!hasBookingTypeOptions || hasBookingFlows === 0) {
        console.log('Setting up general validation');
        // Set default booking type to general if not set
        if (!$('input[name="booking_type"]:checked').val()) {
            $('input[name="booking_type"][value="general"]').prop('checked', true);
        }
        
        // Ensure location mode is visible
        $('#location-mode').show();
        
        // Initialize location filtering if appointment mode is selected
        var selectedAppointmentMode = $('input[name="appointment_mode"]:checked').val();
        if (selectedAppointmentMode) {
            filterLocationsByMode(selectedAppointmentMode);
        }
    } else {
        // If booking type options are visible, check current selection
        var currentBookingType = $('input[name="booking_type"]:checked').val();
        if (currentBookingType === 'general') {
            $('#location-mode').show();
        } else if (currentBookingType === 'booking_flow') {
            $('#location-mode').hide();
        }
    }
}
</script>