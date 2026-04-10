@extends('admin-panel.layouts.app')


@section('content')
<!-- Content -->
<div class="container-fluid">
    <section class="cds-ty-dashboard-breadcrumb-container">
        <div class="cds-main-layout-header">
            <div class="breadcrumb-conatiner">
                <ol class="breadcrumb">
                    <i class="fa-grid-2 fa-regular"></i>
                    <li class="breadcrumb-item"><a class="breadcrumb-link" href="{{ baseUrl('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a class="breadcrumb-link" href="{{ baseUrl('associates') }}">Associates</a></li>
                    <li class="active breadcrumb-item" aria-current="page">{{$pageTitle}}</li>
                </ol>
            </div>
            <div class="cds-heading">
                <div class="cds-heading-icon">
                    <i class="fa-light fa-file-contract"></i>
                </div>
                <h1>{{$pageTitle}}</h1>
            </div>
        </div>
    </section>

    <!-- Agreement Form Section -->
    <div class="cds-ty-dashboard-box">
        <div class="cds-ty-dashboard-box-body">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Create Agreement for {{ $associate->first_name }} {{ $associate->last_name }}</h5>
                            <p class="card-text">Create a customized agreement for this associate using the template below.</p>
                        </div>
                        <div class="card-body">
                            <form id="agreement-form" class="js-validate" action="{{ baseUrl('agreement/' . $template->unique_id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="associate_id" value="{{ $associate->unique_id }}">
                                
                                                                 <div class="row mb-3">
                                     <div class="col-xl-12">
                                         {!! FormHelper::formInputNumber(['name'=>"sharing_fees_percentage","id" => "sharing_fees_percentage","label"=>"Enter sharing fees percentage","required"=>true]) !!}
                                     </div>
                                    <div class="col-md-12">
                                        <label for="agreement_content" class="form-label">Agreement Content <span class="text-danger">*</span></label>
                                        <textarea class="form-control cds-texteditor" id="agreement_content" name="agreement_content" 
                                                  rows="10" required placeholder="Enter your agreement content here...">{{ $displayContent ?? '' }}</textarea>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">Create Agreement</button>
                                        <a href="{{ baseUrl('associates') }}" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Content -->
@endsection

@section('javascript')

<script>
$(document).ready(function() {
    // Initialize the text editor
    var customEditorInstance = initEditor("agreement_content");
    
    // Store the editor instance globally
    window.editorInstance = customEditorInstance;
    
    // Store the original template content to avoid multiple replacements
    var originalTemplateContent = '';
    
    // Wait for editor to be fully initialized
    setTimeout(function() {
        // Get and store the original template content
        if (window.editorInstance && typeof window.editorInstance.getContent === 'function') {
            try {
                originalTemplateContent = window.editorInstance.getContent();
              
            } catch (e) {
            
                originalTemplateContent = $('#agreement_content').val();
            }
        } else {
            originalTemplateContent = $('#agreement_content').val();
        }
        
        // Function to update sharing fees placeholder in the editor
        function updateSharingFeesPlaceholder() {
            var sharingFeesPercentage = $('#sharing_fees_percentage').val();
            if (sharingFeesPercentage) {
                // Always start with the original template content to avoid multiple replacements
                var editorContent = originalTemplateContent;
              
                
                // Replace all instances of {SHARING_FEES} with the percentage value
                var updatedContent = editorContent.replace(/\{SHARING_FEES\}/g, sharingFeesPercentage + '%');
              
                
                // Update both the textarea and the rich text editor
                $('#agreement_content').val(updatedContent);
                
                // Try multiple ways to update the rich text editor
                if (window.editorInstance && typeof window.editorInstance.setContent === 'function') {
                    try {
                        window.editorInstance.setContent(updatedContent);
                       
                        
                        // Also try to update the editor element directly if available
                        if (window.editorInstance.getEditor && typeof window.editorInstance.getEditor === 'function') {
                            var editorElement = window.editorInstance.getEditor();
                            if (editorElement) {
                                editorElement.innerHTML = updatedContent;
                               
                            }
                        }
                    } catch (e) {
                       
                        
                        // Fallback: try to update the editor element directly
                        if (window.editorInstance.getEditor && typeof window.editorInstance.getEditor === 'function') {
                            var editorElement = window.editorInstance.getEditor();
                            if (editorElement) {
                                editorElement.innerHTML = updatedContent;
                               
                            }
                        }
                    }
                } else if (typeof tinymce !== 'undefined' && tinymce.get('agreement_content')) {
                    tinymce.get('agreement_content').setContent(updatedContent);
                  
                } else if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.agreement_content) {
                    CKEDITOR.instances.agreement_content.setData(updatedContent);
                    
                }
                
                
                // Last resort: try to find and update the editor element in the DOM
                setTimeout(function() {
                    var editorElements = document.querySelectorAll('.cds-editor-content');
                    if (editorElements.length > 0) {
                        editorElements.forEach(function(element) {
                            if (element.innerHTML !== updatedContent) {
                                element.innerHTML = updatedContent;
                            }
                        });
                    }
                }, 100);
            }
        }

        // Listen for changes in sharing fees percentage input
        $(document).on('input', '#sharing_fees_percentage', function() {
            updateSharingFeesPlaceholder();
        });
        
                 // Also listen for keyup and change events for better compatibility
         $(document).on('keyup change', '#sharing_fees_percentage', function() {
             updateSharingFeesPlaceholder();
         });
         

         
     }, 1000); // Wait 1 second for editor to initialize

    // Handle form submission
    $("#agreement-form").submit(function(e) {
        e.preventDefault();
        
        var is_valid = formValidation("agreement-form");
        if (!is_valid) {
            return false;
        }
        
        var formData = new FormData($("#agreement-form")[0]);
        var url = $("#agreement-form").attr('action');
        
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
                    if (response.redirect_back) {
                        setTimeout(function() {
                            window.location.href = response.redirect_back;
                        }, 1500);
                    }
                } else {
                    validation(response.message);
                }
            },
            error: function() {
                hideLoader();
                internalError();
            }
        });
    });
});
</script>
@endsection
