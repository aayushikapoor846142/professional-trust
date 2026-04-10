@extends('components.custom-popup',['modalTitle'=>$pageTitle ?? 'Raise a Support Ticket'])
@section('custom-popup-content')

<div class="CdsDashboardCustomPopup-modal-form-wrapper">
    <div class="CdsDashboardCustomPopup-modal-form-grid">
        <div class="CdsDashboardCustomPopup-modal-left-column">
            
            <form id="popup-form" class="js-validate" action="{{ route('panel.tickets.store') }}" method="POST">
                @csrf
                
                <div class="CdsDashboardCustomPopup-modal-form-header">
                    <h3 class="CdsDashboardCustomPopup-modal-title">Raise a Support Ticket</h3>
                    <p class="CdsDashboardCustomPopup-modal-subtitle">Please provide details about your issue</p>
                    </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">
                        Subject <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <input type="text" 
                           name="subject" 
                           class="CdsDashboardCustomPopup-modal-input" 
                           value="{{ old('subject') }}" 
                           required>
                    </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">
                        Description <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <textarea name="description" 
                              class="CdsDashboardCustomPopup-modal-textarea cds-texteditor" 
                              required>{{ old('description') }}</textarea>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">
                        Category <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <select name="category_id" 
                            class="CdsDashboardCustomPopup-modal-select select2-input category_id" 
                            required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">
                        Priority <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <select name="priority" 
                            class="CdsDashboardCustomPopup-modal-select select2-input priority" 
                            required>
                        <option value="">Select Priority</option>
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
            </form>
        </div>
    </div>
    
    <div class="CdsDashboardCustomPopup-modal-submit-section">
        <button type="submit" form="popup-form" class="CdsDashboardCustomPopup-modal-submit-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20">
                <path d="M5 13l4 4L19 7"></path>
            </svg>
            <span>Submit Ticket</span>
        </button>
    </div>
</div>

<script>
$(document).ready(function() {
    initSelect();
    
    $("#popup-form").submit(function(e){
        e.preventDefault();
        var is_valid = formValidation('popup-form');
        if (!is_valid) {
            return false;
        }
        
        $(this).find(".CdsDashboardCustomPopup-modal-submit-btn").attr("disabled","disabled");
        var formData = new FormData($(this)[0])
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType:"json",
            beforeSend: function() {
                showLoader();
            },
            success: function (response) {
                hideLoader();
                $("#popup-form").find(".CdsDashboardCustomPopup-modal-submit-btn").removeAttr("disabled");
                if (response.status == true) {
                    successMessage(response.message);
                    closeCustomPopup();
                    // Refresh the tickets list if on tickets page
                    if (typeof refreshTicketsList === 'function') {
                        refreshTicketsList();
                    } else {
                        // Redirect to tickets page
                        window.location.href = "{{ route('panel.tickets.index') }}";
                    }
                } else {
                    if(response.error_type == 'validation'){
                        validation(response.message);
                    }else{
                        errorMessage(response.message);
                    }
                    $(".CdsDashboardCustomPopup-modal-submit-btn").removeAttr("disabled");
                }
            },
            error: function (xhr, status, error) {
                hideLoader();
                $("#popup-form").find(".CdsDashboardCustomPopup-modal-submit-btn").removeAttr("disabled");
                console.error('Ticket creation error:', xhr.responseText);
                
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.error_type === 'validation') {
                    validation(xhr.responseJSON.message);
                } else {
                    errorMessage('An error occurred while creating the ticket. Please try again.');
                }
            }
        });
    });
    
    // Initialize text editor if needed
    if (typeof $.fn.redactor !== 'undefined') {
        $('.cds-texteditor').redactor({
            buttons: ['bold', 'italic', 'underline', 'link', 'list'],
            minHeight: 150,
            maxHeight: 300,
            placeholder: 'Describe your issue in detail...'
        });
    }
});
</script> 

@endsection 