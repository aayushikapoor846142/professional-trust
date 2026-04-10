@extends('components.custom-popup',['modalTitle'=>$pageTitle ?? 'Add Discussion Thread'])
@section('custom-popup-content')

<div class="CdsDashboardCustomPopup-modal-form-wrapper">
    <div class="CdsDashboardCustomPopup-modal-form-grid">
        <div class="CdsDashboardCustomPopup-modal-left-column">
            
            <form id="discussionCreateFormModal" class="js-validate" action="{{ baseUrl('manage-discussion-threads/save/thread') }}" method="post" enctype="multipart/form-data">
                @csrf
                
                <div class="CdsDashboardCustomPopup-modal-form-header">
                    <h3 class="CdsDashboardCustomPopup-modal-title">Add Discussion Thread</h3>
                    <p class="CdsDashboardCustomPopup-modal-subtitle">Create a new discussion thread with topic, description, and category</p>
                    </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label" for="topic_title">
                        Topic Title <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="topic_title"
                        name="topic_title"
                        class="CdsDashboardCustomPopup-modal-input" 
                        placeholder="Enter topic title"
                        required
                    >
                    </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label" for="short_description">
                        Short Description <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <textarea 
                        id="short_description"
                        name="short_description"
                        class="CdsDashboardCustomPopup-modal-textarea" 
                        placeholder="Enter short description"
                        required
                    ></textarea>
                    </div>

                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label" for="description">
                        Description <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <textarea 
                        id="description"
                        name="description"
                        class="CdsDashboardCustomPopup-modal-textarea cds-texteditor" 
                        placeholder="Enter description"
                        required
                    ></textarea>
                        </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label" for="discussion_category">
                        Discussion Category <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <select 
                        id="discussion_category"
                        name="discussion_category"
                        class="CdsDashboardCustomPopup-modal-select select2-input" 
                        required
                    >
                        <option value="">Select Discussion Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    </div>

                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label" for="type">
                        Discussion Type <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <select 
                        id="type"
                        name="type"
                        class="CdsDashboardCustomPopup-modal-select select2-input" 
                        required
                    >
                        <option value="">Select Discussion Type</option>
                        @foreach(FormHelper::groupType() as $type)
                            <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                        @endforeach
                    </select>
                    </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group members-area" style="display:none">
                    <label class="CdsDashboardCustomPopup-modal-label" for="selected_members">
                        Select Members
                    </label>
                    <select 
                        id="selected_members"
                        name="selected_members[]"
                        class="CdsDashboardCustomPopup-modal-select select2-input cds-multiselect add-multi" 
                        multiple
                    >
                        @foreach($members ?? [] as $member)
                            <option value="{{ $member['id'] }}">{{ $member['name'] }}</option>
                        @endforeach
                    </select>
                            <span class="text-danger selected_members"></span>
                    </div>

                <div class="CdsDashboardCustomPopup-modal-form-group allow-join-member" style="display:none">
                    <div class="CdsDashboardCustomPopup-modal-checkbox-group">
                        <label class="CdsDashboardCustomPopup-modal-checkbox-label">
                            <input type="checkbox" 
                                   name="allow_join_request" 
                                   value="1" 
                                   id="allow_join_request"
                                   class="CdsDashboardCustomPopup-modal-checkbox-input">
                            <span class="CdsDashboardCustomPopup-modal-checkbox-custom"></span>
                            <span class="CdsDashboardCustomPopup-modal-checkbox-text">Allow Member to Join</span>
                        </label>
                    </div>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">Media Files</label>
                    <div class="CdsDashboardCustomPopup-modal-upload-container" id="addDiscussionMediaUpload">
                        <div class="CdsDashboardCustomPopup-modal-upload-area">
                            <input type="file" class="CdsDashboardCustomPopup-modal-file-input" multiple accept="image/*,.pdf,.doc,.docx" style="display: none;">
                            <div class="CdsDashboardCustomPopup-modal-upload-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                            </div>
                            <p class="CdsDashboardCustomPopup-modal-upload-text">Drag and drop files here or click to browse</p>
                            <p class="CdsDashboardCustomPopup-modal-upload-hint">Supports: JPG, PNG, GIF, PDF, DOC, DOCX (Max 10MB per file)</p>
                        </div>
                        
                        <!-- File Preview Area -->
                        <div class="CdsDashboardCustomPopup-modal-file-list" style="display: none;">
                            <!-- Files will be dynamically added here -->
                        </div>
                    </div>
                </div>

                <input type="hidden" name="file" id="file-modal" />
            </form>
        </div>
            </div>
    
    <div class="CdsDashboardCustomPopup-modal-submit-section">
        <button type="submit" form="discussionCreateFormModal" class="CdsDashboardCustomPopup-modal-submit-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20">
                <path d="M5 13l4 4L19 7"></path>
            </svg>
            <span>Post Discussion</span>
        </button>
    </div>
</div>

<script>
$(document).ready(function(){
    // Initialize select2 for the modal
    setTimeout(() => {
        initSelect();
    }, 500);

    // Initialize text editor for description only
    setTimeout(() => {
        initEditor("description");
    }, 1000);

    // Initialize custom file uploader
    setTimeout(() => {
        console.log('Initializing add discussion file uploader...');
        
        // Simple file upload implementation
        const uploadContainer = document.getElementById('addDiscussionMediaUpload');
        const fileInput = uploadContainer.querySelector('input[type="file"]');
        const uploadArea = uploadContainer.querySelector('.CdsDashboardCustomPopup-modal-upload-area');
        const fileList = uploadContainer.querySelector('.CdsDashboardCustomPopup-modal-file-list');
        
        console.log('Upload container:', uploadContainer);
        console.log('File input:', fileInput);
        
        if (fileInput && uploadArea) {
            // Make upload area clickable
            uploadArea.addEventListener('click', function() {
                fileInput.click();
            });
            
            // Handle file selection
            fileInput.addEventListener('change', function(e) {
                console.log('Files selected:', e.target.files);
                const files = Array.from(e.target.files);
                
                files.forEach(file => {
                    console.log('Processing file:', file.name);
                    
                    // Validate file size (10MB max)
                    if (file.size > 10 * 1024 * 1024) {
                        errorMessage(`File ${file.name} is too large. Maximum size is 10MB.`);
                        return;
                    }
                    
                    // Validate file type
                    const allowedTypes = [
                        'image/jpeg', 'image/png', 'image/gif', 'application/pdf',
                        'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    ];
                    
                    if (!allowedTypes.includes(file.type)) {
                        errorMessage(`File type ${file.type} is not allowed.`);
                        return;
                    }
                    
                    // Upload file
                    uploadFile(file);
                });
            });
            
            // Drag and drop functionality
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                uploadArea.style.borderColor = '#6366f1';
                uploadArea.style.background = '#f0f0ff';
            });
            
            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                uploadArea.style.borderColor = '#e9ecef';
                uploadArea.style.background = '#f8f9fa';
            });
            
            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                uploadArea.style.borderColor = '#e9ecef';
                uploadArea.style.background = '#f8f9fa';
                
                const files = Array.from(e.dataTransfer.files);
                files.forEach(file => {
                    uploadFile(file);
                });
            });
            
            function uploadFile(file) {
                console.log('Uploading file:', file.name);
                
                // Create file preview item
                const fileItem = document.createElement('div');
                fileItem.className = 'CdsDashboardCustomPopup-modal-file-item';
                fileItem.innerHTML = `
                    <div class="CdsDashboardCustomPopup-modal-file-preview">
                        <div class="file-icon-placeholder">
                            <i class="fas fa-file fa-2x text-muted"></i>
                            <small class="d-block">${file.name.split('.').pop()}</small>
                        </div>
                    </div>
                    <div class="CdsDashboardCustomPopup-modal-file-info">
                        <div class="CdsDashboardCustomPopup-modal-file-name">${file.name}</div>
                        <div class="CdsDashboardCustomPopup-modal-upload-progress">
                            <div class="CdsDashboardCustomPopup-modal-upload-progress-bar" style="width: 0%"></div>
                        </div>
                    </div>
                    <button type="button" class="CdsDashboardCustomPopup-modal-file-remove" onclick="removeNewFile(this)">
                        Remove
                    </button>
                `;
                
                fileList.appendChild(fileItem);
                fileList.style.display = 'block';
                
                // Upload file to server
                const formData = new FormData();
                formData.append('file', file);
                formData.append('_token', csrf_token);
                formData.append('timestamp', Date.now());
                formData.append('document_type', 'file');
                
                const progressBar = fileItem.querySelector('.CdsDashboardCustomPopup-modal-upload-progress-bar');
                
                $.ajax({
                    url: BASEURL + '/manage-discussion-threads/upload-file',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        const xhr = new XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                const percent = Math.round((e.loaded / e.total) * 100);
                                progressBar.style.width = percent + '%';
                            }
                        });
                        return xhr;
                    },
                    success: function(response) {
                        console.log('Upload success response:', response);
                        if (response.status) {
                            // Add to hidden input for form submission
                            const currentFiles = $('#file-modal').val();
                            const newFiles = currentFiles ? currentFiles + ',' + response.filename : response.filename;
                            $('#file-modal').val(newFiles);
                            console.log('Updated file input value:', newFiles);
                            
                            // Update file item with success state
                            fileItem.classList.add('upload-success');
                            progressBar.style.background = '#28a745';
                        } else {
                            console.error('Upload failed:', response.message);
                            errorMessage(response.message || 'Upload failed');
                            fileItem.remove();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Upload AJAX error:', {xhr, status, error});
                        errorMessage('Upload failed: ' + error);
                        fileItem.remove();
                    }
                });
            }
            
            console.log('Add discussion file uploader initialized successfully');
        } else {
            console.error('File input or upload area not found');
        }
    }, 1000);

    // Handle discussion type change
    $(document).on("change","#type",function(){
        if($(this).val() == 'private'){
            $(".members-area").show();
            $(".allow-join-member").show();
        }else{
            $(".members-area").hide();
            $(".allow-join-member").hide();
        }
    });

    // Handle form submission
    $("#discussionCreateFormModal").submit(function(e) {
        e.preventDefault();
        var is_valid = formValidation("discussionCreateFormModal");
        if (!is_valid) {
            return false;
        }
       
        var formData = new FormData($(this)[0]);
        var url = $("#discussionCreateFormModal").attr('action');
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
                    closeCustomPopup();
                    // Reload the discussion threads list
                    if (typeof loadData === 'function') {
                        loadData(1);
                    } else {
                        location.reload();
                    }
                } else {
                    validation(response.message);
                }
            },
            error: function(xhr) {
                hideLoader();
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
