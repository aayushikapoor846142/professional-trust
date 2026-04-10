@extends('components.custom-popup',['modalTitle'=>$pageTitle])
@section('custom-popup-content')

<div class="CdsDashboardCustomPopup-modal-form-wrapper">
    <div class="CdsDashboardCustomPopup-modal-form-grid">
        <div class="CdsDashboardCustomPopup-modal-left-column">
            
            <form id="popup-form" action="{{ baseUrl('/companies/save-company-address/'.$id.'/'.$company->unique_id) }}" method="post">
                @csrf
                <input type="hidden" name="address_id" value="{{ $id }}" />
                <input type="hidden" name="type_label" value="company" />
                
                <div class="CdsDashboardCustomPopup-modal-form-header">
                    <h3 class="CdsDashboardCustomPopup-modal-title">{{$pageTitle}}</h3>
                    <p class="CdsDashboardCustomPopup-modal-subtitle">Add or update your company address information</p>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">Location Name</label>
                    <input type="text" 
                           name="location_name" 
                           class="CdsDashboardCustomPopup-modal-input" 
                           value="{{ $adddressInfo->location_name ?? '' }}" 
                           id="location_name">
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">Type</label>
                    <div class="CdsDashboardCustomPopup-modal-radio-group">
                        @foreach(FormHelper::selectAppointmentMode() as $option)
                        <label class="CdsDashboardCustomPopup-modal-radio-label">
                            <input type="radio" 
                                   name="type" 
                                   value="{{ $option['value'] }}" 
                                   class="CdsDashboardCustomPopup-modal-radio-input"
                                   {{ ($adddressInfo->type ?? '') == $option['value'] ? 'checked' : '' }}>
                            <div class="CdsDashboardCustomPopup-modal-radio-content">
                                <div class="CdsDashboardCustomPopup-modal-radio-title">{{ $option['label'] }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">Status</label>
                    <div class="CdsDashboardCustomPopup-modal-radio-group">
                        @foreach(FormHelper::getLocationStatus() as $option)
                        <label class="CdsDashboardCustomPopup-modal-radio-label">
                            <input type="radio" 
                                   name="status" 
                                   value="{{ $option['value'] }}" 
                                   class="CdsDashboardCustomPopup-modal-radio-input"
                                   {{ ($adddressInfo->status ?? '') == $option['value'] ? 'checked' : '' }}>
                            <div class="CdsDashboardCustomPopup-modal-radio-content">
                                <div class="CdsDashboardCustomPopup-modal-radio-title">{{ $option['label'] }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">
                        Address 1 <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <input type="text" 
                           name="address1" 
                           class="CdsDashboardCustomPopup-modal-input google-address" 
                           value="{{ $adddressInfo->address_1 ?? '' }}" 
                           id="company_address" 
                           required>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">Address 2</label>
                    <input type="text" 
                           name="address2" 
                           class="CdsDashboardCustomPopup-modal-input ga-address2" 
                           value="{{ $adddressInfo->address_2 ?? '' }}" 
                           id="address_2">
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">
                        Select Country <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <select name="country" 
                            class="CdsDashboardCustomPopup-modal-select select2-input ga-country" 
                            id="country" 
                            required>
                        <option value="">Select Country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->name }}" {{ ($adddressInfo->country ?? '') == $country->name ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">
                        State <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <input type="text" 
                           name="state" 
                           class="CdsDashboardCustomPopup-modal-input ga-state" 
                           value="{{ $adddressInfo->state ?? '' }}" 
                           oninput="validateName(this)" 
                           required>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">
                        City <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <input type="text" 
                           name="city" 
                           class="CdsDashboardCustomPopup-modal-input ga-city" 
                           value="{{ $adddressInfo->city ?? '' }}" 
                           oninput="validateName(this)" 
                           required>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">
                        Pincode <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <input type="number" 
                           name="pincode" 
                           class="CdsDashboardCustomPopup-modal-input ga-pincode" 
                           value="{{ $adddressInfo->pincode ?? '' }}" 
                           oninput="validateZipCode(this)" 
                           onblur="validateZipCode(this)" 
                           required>
                </div>
            </form>
        </div>
    </div>
    
    <div class="CdsDashboardCustomPopup-modal-submit-section">
        <button type="submit" form="popup-form" class="CdsDashboardCustomPopup-modal-submit-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20">
                <path d="M5 13l4 4L19 7"></path>
            </svg>
            <span>Save Address</span>
        </button>
    </div>
</div>

<script>
$(document).ready(function(){
    initGoogleAddress();
    
    $("#popup-form").submit(function(e) {
        e.preventDefault();
        var is_valid = formValidation("popup-form");
        if(!is_valid){
            return false;
        }
        
        $(this).find(".CdsDashboardCustomPopup-modal-submit-btn").attr("disabled","disabled");
        var formData = new FormData($(this)[0]);
        var url = $("#popup-form").attr('action');
        
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
                $("#popup-form").find(".CdsDashboardCustomPopup-modal-submit-btn").removeAttr("disabled");
                if (response.status == true) {
                    successMessage(response.message);
                    loadCompanyAddress();
                    closeCustomPopup();
                } else {
                    validation(response.message);
                }
            },
            error: function(xhr) {
                hideLoader();
                $("#popup-form").find(".CdsDashboardCustomPopup-modal-submit-btn").removeAttr("disabled");
                internalError();
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.error_type === 'validation') {
                    validation(xhr.responseJSON.message);
                } else {
                    errorMessage('An unexpected error occurred. Please try again.');
                }
            }
        });
    });
    
    // Initialize radio button styling
    document.querySelectorAll('.CdsDashboardCustomPopup-modal-radio-input').forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove selected class from all radio labels in this group
            const name = this.getAttribute('name');
            document.querySelectorAll(`input[name="${name}"]`).forEach(input => {
                input.closest('.CdsDashboardCustomPopup-modal-radio-label').classList.remove('CdsDashboardCustomPopup-modal-selected');
            });
            // Add selected class to current radio label
            this.closest('.CdsDashboardCustomPopup-modal-radio-label').classList.add('CdsDashboardCustomPopup-modal-selected');
        });
    });
});
</script>

@endsection