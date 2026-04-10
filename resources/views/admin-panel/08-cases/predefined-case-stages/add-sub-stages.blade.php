@extends('components.custom-popup',['modalTitle'=>$pageTitle ?? 'Generate Via AI'])
@section('custom-popup-content')

<div class="CdsDashboardCustomPopup-modal-form-wrapper">
    <div class="CdsDashboardCustomPopup-modal-form-grid">
        <div class="CdsDashboardCustomPopup-modal-left-column">
            
            <form id="add-edit-sub-stage-form" name="add-edit-sub-stage-form" class="js-validate" action="{{ baseUrl('predefined-case-sub-stages/save/') }}" method="post">
                @csrf
                <input type="hidden" name="stage_id" value="{{ $stage_id }}" />
                <input type="hidden" name="sub_stage_id" value="{{ $record->id??'0' }}" />
                
                <div class="CdsDashboardCustomPopup-modal-form-header">
                    <h3 class="CdsDashboardCustomPopup-modal-title">Add Sub Stage</h3>
                    <p class="CdsDashboardCustomPopup-modal-subtitle">Create a new sub stage for your case workflow</p>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">
                        Sub Stage Name <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <input type="text" name="name" id="name" class="CdsDashboardCustomPopup-modal-input" 
                           placeholder="Enter sub stage name" required>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">
                        Sort Order <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <input type="number" name="sort_order" id="sort_order" class="CdsDashboardCustomPopup-modal-input" 
                           placeholder="Enter sort order" required>
                    <small class="form-helper-text">Order in which this sub stage appears</small>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">
                        Stage Type <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <select name="stage_type" id="stage_type" class="CdsDashboardCustomPopup-modal-input">
                        <option value="">Select Stage Type</option>
                        @foreach(FormHelper::subStageType() as $option)
                            <option value="{{ $option['value'] }}" {{ old('stage_type') == $option['value'] ? 'selected' : '' }}>
                                {{ $option['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group form-div" style="display:none;">
                    <label class="CdsDashboardCustomPopup-modal-label">Select Form</label>
                    <select name="form_id" id="form_id" class="CdsDashboardCustomPopup-modal-input">
                        <option value="">Choose Form</option>
                        @foreach($forms as $form)
                            <option value="{{ $form->id }}" {{ old('form_id') == $form->id ? 'selected' : '' }}>
                                {{ $form->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-helper-text">Form to be filled during this stage</small>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group default-document-div" style="display:none;">
                    <label class="CdsDashboardCustomPopup-modal-label">Default Documents</label>
                    <select name="default_documents[]" id="default_documents" class="CdsDashboardCustomPopup-modal-input" multiple>
                        @foreach($default_documents as $document)
                            <option value="{{ $document->id }}" {{ old('default_documents') && in_array($document->id, old('default_documents')) ? 'selected' : '' }}>
                                {{ $document->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-helper-text">Documents required for this stage</small>
                    <div class="text-danger default_documents_error"></div>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-submit-section">
                    <button type="submit" class="CdsDashboardCustomPopup-modal-submit-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20">
                            <path d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Save & Publish</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    initSelect();
    
    $("#add-edit-sub-stage-form").submit(function(e) {
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        var url = $("#add-edit-sub-stage-form").attr('action');
        
        var is_valid = formValidation("add-edit-sub-stage-form");
        
        if (!is_valid) {
            return false;
        }
        
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
                    $.each(response.message, function (index, value) {
                        if(index == 'default_documents'){
                            $('.default_documents_error').html(value);
                        }
                    });
                    validation(response.message);
                }
            },
            error: function() {
                internalError();
            }
        });
    });
    
    $('#stage_type').change(function() {
        var selectedValue = $(this).val();
        if(selectedValue == 'fill-form'){
            $('.form-div').show();
            $('.default-document-div').hide();
            $('.custom-document-div').hide();
        }else{
            $('.default-document-div').show();
            $('.custom-document-div').show();
            $('.form-div').hide();
        }
    });
});
</script>
@endsection