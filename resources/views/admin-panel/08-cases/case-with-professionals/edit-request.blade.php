@extends('admin-panel.08-cases.case-with-professionals.my-cases-master')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/custom-file-upload.css') }}">
@endsection


@section('case-container')                                                         

<style>
.existing-attachments-container {
    margin-bottom: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
    border: 1px solid #e9ecef;
}

.existing-attachments-container h6 {
    margin: 0 0 10px 0;
    color: #333;
    font-weight: 600;
}

.existing-attachments-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.existing-attachment-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.existing-attachment-item:hover {
    border-color: #007bff;
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.1);
}

.existing-attachment-item .file-name a {
    color: #007bff;
    text-decoration: none;
}

.existing-attachment-item .file-name a:hover {
    text-decoration: underline;
}
.existing-attachments-container {
    margin-bottom: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
    border: 1px solid #e9ecef;
}

.existing-attachments-container h6 {
    margin: 0 0 10px 0;
    color: #333;
    font-weight: 600;
}

.existing-attachments-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.existing-attachment-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.existing-attachment-item:hover {
    border-color: #007bff;
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.1);
}

.existing-attachment-item .file-name a {
    color: #007bff;
    text-decoration: none;
}

.existing-attachment-item .file-name a:hover {
    text-decoration: underline;
}
</style>                
<div class="submit-bid p-3 border rounded bg-light">
    <h5>Edit Request</h5>
    <form id="form" class="js-validate mt-3" action="{{ baseUrl('/case-with-professionals/update-request/'.$professional_case_request->unique_id) }}" method="POST">
        @csrf
        <input type="hidden" name="case_id" value="{{$case_id}}">
        <div class="row">
            <div class="mb-2 col-md-6">
                {!! FormHelper::formInputText([
                    'name'=>"title",
                    'required'=>true,
                    'id'=>"title",
                    'label'=>"Enter Title",
                    'value'=>$professional_case_request->title ?? ''
                ]) !!}
            </div>
            <div class="mb-2 col-md-6">
                <div class="cds-selectbox">
                    {!! FormHelper::formSelect([
                        'name' => 'status',
                        'id' => 'status',
                        'label' => 'Select Staus ',
                        'class' => 'select2-input',
                        'required' => true,
                        'options' => FormHelper::requestStatus(),
                        'value_column' => 'value',
                        'label_column' => 'label',
                        'selected' => $professional_case_request->status ?? '',
                        'is_multiple' => false,
                        'required' => true
                    ]) !!}
                </div>
            </div>
            <div class="mb-2 col-md-12">
                <div class="">
                    {!! FormHelper::formSelect([
                        'name' => 'request_type',
                        'id' => 'request_type',
                        'label' => 'Select Request Type ',
                        'class' => 'select2-input cds-multiselect add-multi',
                        'required' => true,
                        'options' => FormHelper::requestType(),
                        'value_column' => 'value',
                        'label_column' => 'label',
                        'selected' => $professional_case_request->request_type ?? '',
                        'is_multiple' => false,
                        'required' => true
                    ]) !!}
                </div>
            </div>
            <div class="mb-2 col-md-12 assesment-form-div" style="display:none;">
                <div class="cds-selectbox">
                    {!! FormHelper::formSelect([
                        'name' => 'assesment_form_id',
                        'id' => 'assesment_form_id',
                        'label' => 'Select Assesment Form ',
                        'class' => 'select2-input',
                        'required' => true,
                        'options' =>$forms,
                        'value_column' => 'id',
                        'label_column' => 'name',
                        'selected' => $professional_case_request->form_id ?? '',
                        'is_multiple' => false,
                        'required' => false
                    ]) !!}
                </div>
            </div>

            <div class="mb-2 col-md-12 document-div" style="display:none;">
                <div class="cds-selectbox">
                    {!! FormHelper::formSelect([
                        'name' => 'document_id[]',
                        'id' => 'document_id',
                        'label' => 'Select Document',
                        'class' => 'select2-input cds-multiselect add-multi',
                        'required' => true,
                        'options' =>$all_folders,
                        'value_column' => 'id',
                        'label_column' => 'name',
                        'is_multiple' => true,
                        'selected' => isset($professional_case_request->additional_detail) && $professional_case_request->request_type == 'document-request' ? explode(',', $professional_case_request->additional_detail) : [],
                        'required' => false
                    ]) !!}
                </div>
            </div>
            <div class="mb-2 col-md-12 information-request-div" style="display:none;">
                <label class="col-form-label input-label">Enter Information</label>
                {!! FormHelper::formTextarea([
                    'name'=>"information",
                    'id'=>"information",
                    'required'=>false,
                    'class'=>"cds-texteditor",
                    'textarea_class'=>"",
                    'value' => $professional_case_request->request_type == 'information-request' ? ($professional_case_request->additional_detail ?? '') : ''
                ]) !!}
               
            </div>
            <div class="mb-2 col-md-12">
                <label class="col-form-label input-label">Enter Message <span class="danger">*</span></label>
                {!! FormHelper::formTextarea([
                    'name'=>"message_body",
                    'id'=>"message_body",
                    'required'=>true,
                    'class'=>"cds-texteditor",
                    'textarea_class'=>"",
                    'value' => $professional_case_request->message_body ?? ''
                ]) !!}
            </div>
            <div class="mb-2 col-md-12">
                <div class="custom-file-upload-container">
                    <label for="custom-file-input" class="custom-file-label">Attachment</label>
                    
                    {{-- Existing Attachments --}}
                    @php
                        $existingAttachments = $professional_case_request->attachment ? explode(',', $professional_case_request->attachment) : [];
                    @endphp
                    @if(count($existingAttachments) > 0)
                    <div class="existing-attachments-container mb-3">
                        <h6>Existing Attachments:</h6>
                        <div class="existing-attachments-list">
                    @foreach($existingAttachments as $file)
                        @if(trim($file) != '')
                                <div class="existing-attachment-item" data-file="{{ $file }}">
                                    <div class="file-info">
                                        <svg class="file-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <div>
                                            <div class="file-name">
                                                <a href="{{ baseUrl('/case-with-professionals/download-request-attachment?file='.$file) }}" target="_blank">{{ $file }}</a>
                                            </div>
                                            <div class="file-size">Existing file</div>
                                        </div>
                                    </div>
                                    <button type="button" class="remove-file" onclick="removeExistingFile('{{ $file }}')">Remove</button>
                        </div>
                        @endif
                    @endforeach
                        </div>
                    </div>
                    @endif
                    
                    {{-- New File Upload Area --}}
                    <div class="custom-file-upload-area" id="file-upload-area">
                        <div class="upload-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 16L12 8M12 8L15 11M12 8L9 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3 15V16C3 18.8284 3 20.2426 3.87868 21.1213C4.75736 22 6.17157 22 9 22H15C17.8284 22 19.2426 22 20.1213 21.1213C21 20.2426 21 18.8284 21 16V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <p class="upload-text">Drag & drop files here or <span class="browse-link">browse</span></p>
                        <p class="upload-hint">Supported formats: JPG, JPEG, PNG (Max 6MB per file)</p>
                        <input type="file" id="custom-file-input" name="custom_attachment[]" multiple accept=".jpg,.jpeg,.png" style="display: none;">
                    </div>
                    <div class="file-preview-container" id="file-preview-container" style="display: none;">
                        <h6>New Files:</h6>
                        <div class="file-list" id="file-list"></div>
                    </div>
                </div>
                <input type="hidden" id="attachments" name="attachments" value="{{ $professional_case_request->attachment }}" />
            </div>
        </div>
        <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
    </form>
</div>

@endsection

@push('scripts')
<script>
    let selectedFiles = []; // Make this global
    let existingFiles = []; // Track existing files

     $(document).ready(function() {
        const fileUploadArea = document.getElementById('file-upload-area');
        const fileInput = document.getElementById('custom-file-input');
        const filePreviewContainer = document.getElementById('file-preview-container');
        const fileList = document.getElementById('file-list');
        
        // Initialize existing files from hidden input
        const currentAttachments = $('#attachments').val();
        if (currentAttachments) {
            existingFiles = currentAttachments.split(',').filter(f => f.trim() !== '');
        }

        // Click to browse files
        fileUploadArea.addEventListener('click', () => {
            fileInput.click();
        });

        // Browse link click
        document.querySelector('.browse-link').addEventListener('click', (e) => {
            e.stopPropagation();
            fileInput.click();
        });

        // Drag and drop events
        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('dragover');
        });

        fileUploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('dragover');
        });

        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('dragover');
            const files = Array.from(e.dataTransfer.files);
            handleFiles(files);
        });

        // File input change
        fileInput.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            handleFiles(files);
        });

        function handleFiles(files) {
            files.forEach(file => {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    showError(`File "${file.name}" is not a supported format. Please use JPG, JPEG, or PNG files.`);
                    return;
                }

                // Validate file size (6MB)
                const maxSize = 6 * 1024 * 1024; // 6MB in bytes
                if (file.size > maxSize) {
                    showError(`File "${file.name}" is too large. Maximum size is 6MB.`);
                    return;
                }

                // Add file to selected files
                selectedFiles.push(file);
            });

            updateFilePreview();
        }

        window.updateFilePreview = function() {
            if (selectedFiles.length === 0) {
                filePreviewContainer.style.display = 'none';
                return;
            }

            filePreviewContainer.style.display = 'block';
            fileList.innerHTML = '';

            selectedFiles.forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                
                const fileSize = formatFileSize(file.size);
                
                fileItem.innerHTML = `
                    <div class="file-info">
                        <svg class="file-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div>
                            <div class="file-name">${file.name}</div>
                            <div class="file-size">${fileSize}</div>
                        </div>
                    </div>
                    <button type="button" class="remove-file" onclick="removeFile(${index})">Remove</button>
                `;
                
                fileList.appendChild(fileItem);
            });
        };

        window.removeFile = function(index) {
            selectedFiles.splice(index, 1);
            window.updateFilePreview();
        };

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function showError(message) {
            console.log('Error:', message);
            if (typeof errorMessage === 'function') {
                errorMessage(message);
            } else {
                alert(message);
            }
        }
       
        $('#request_type').change(function() {
            if($(this).val() == 'assesment-form-request'){
                $('.assesment-form-div').show();
                $('.document-div').hide();
                $('.information-request-div').hide();
            }else if($(this).val() == 'document-request'){
                $('.assesment-form-div').hide();
                $('.document-div').show();
                $('.information-request-div').hide();
            }else if($(this).val() == 'information-request'){
                $('.information-request-div').show();
                $('.document-div').hide();
                $('.assesment-form-div').hide();
            }
        });
        
        initEditor("message_body");
        initEditor("information");
        
        $("#form").submit(function(e) {
            e.preventDefault();
            console.log('Form submitted');
            
            // Check if required fields are filled
            var title = $('#title').val();
            var status = $('#status').val();
            var request_type = $('#request_type').val();
            var message_body = $('#message_body').val();
            
            console.log('Form values:', {
                title: title,
                status: status,
                request_type: request_type,
                message_body: message_body
            });
            
            var is_valid = formValidation("form");
            console.log('Form validation result:', is_valid);
            
            if (!is_valid) {
                console.log('Form validation failed');
                return false;
            }

            console.log('Form validation passed, checking files...');
            if (selectedFiles.length > 0) {
                console.log('Files selected, uploading...');
                uploadFiles();
            } else {
                console.log('No new files selected, submitting form directly...');
                submitForm();
            }
        });
    });

    // Function to remove existing files
    window.removeExistingFile = function(fileName) {
        existingFiles = existingFiles.filter(f => f !== fileName);
        updateAttachmentsInput();
        
        // Remove from DOM
        $(`.existing-attachment-item[data-file="${fileName}"]`).remove();
        
        // Hide container if no more existing files
        if (existingFiles.length === 0) {
            $('.existing-attachments-container').hide();
        }
    };

    function updateAttachmentsInput() {
        const allFiles = [...existingFiles];
        $('#attachments').val(allFiles.join(','));
    }

    function uploadFiles() {
        console.log('uploadFiles called, selectedFiles:', selectedFiles);
        const uploadedFiles = [];
        let uploadCount = 0;
        const totalFiles = selectedFiles.length;

        if (totalFiles === 0) {
            console.log('No files to upload, submitting form directly');
            submitForm();
            return;
        }

        function uploadNextFile() {
            if (uploadCount >= totalFiles) {
                // All files uploaded, submit form
                hideLoader();
                const newFiles = uploadedFiles.join(',');
                const existingFilesStr = existingFiles.join(',');
                const allFiles = existingFilesStr ? (existingFilesStr + ',' + newFiles) : newFiles;
                $('#attachments').val(allFiles);
                submitForm();
                return;
            }

            const file = selectedFiles[uploadCount];
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', csrf_token);

            $.ajax({
                url: BASEURL + "/case-with-professionals/upload-request-attachment",
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function() {
                    if (uploadCount === 0) {
                        showLoader();
                    }
                },
                success: function(response) {
                    if (response.status === true) {
                        uploadedFiles.push(response.filename);
                    } else {
                        showError(`Failed to upload ${file.name}: ${response.message || 'Upload failed'}`);
                    }
                    uploadCount++;
                    uploadNextFile();
                },
                error: function() {
                    showError(`Failed to upload ${file.name}`);
                    uploadCount++;
                    uploadNextFile();
                }
            });
        }

        uploadNextFile();
    }

     function submitForm() {
        console.log('submitForm called');
        var formData = new FormData($("#form")[0]);
        
        // Log form data for debugging
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
      
        var url = $("#form").attr('action');
        console.log('Submitting to URL:', url);
        
        $.ajax({
            url: url,
            type: "post",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            beforeSend: function() {
                console.log('Ajax request started');
                showLoader();
            },
            success: function(response) {
                console.log('Ajax response:', response);
                hideLoader();
                if (response.status == true) {
                    successMessage(response.message);
                    window.location.href=response.redirect_back;
                } else {
                    console.log('Response status false:', response.message);
                    $.each(response.message, function (index, value) {
                       if(index == "document_id"){
                            errorMessage(value);
                       }
                    });
                    validation(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log('Ajax error:', xhr, status, error);
                hideLoader();
                internalError();
            }
        });
    }
</script>
@endpush