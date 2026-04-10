@extends('components.custom-popup',['modalTitle'=>$pageTitle ?? 'Edit Discussion Thread'])
@section('custom-popup-content')

<div class="CdsDashboardCustomPopup-modal-form-wrapper">
    <div class="CdsDashboardCustomPopup-modal-form-grid">
        <div class="CdsDashboardCustomPopup-modal-left-column">
            
            <form id="editDiscussionForm" action="{{ baseUrl('manage-discussion-threads/update/'.$record->unique_id) }}" method="post" enctype="multipart/form-data">
                @csrf
                
                <div class="CdsDashboardCustomPopup-modal-form-header">
                    <h3 class="CdsDashboardCustomPopup-modal-title">Edit Discussion Thread</h3>
                    <p class="CdsDashboardCustomPopup-modal-subtitle">Update your discussion thread information and settings</p>
                    </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label" for="edit_topic_title">
                        Topic Title <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="edit_topic_title"
                        name="topic_title"
                        class="CdsDashboardCustomPopup-modal-input" 
                        placeholder="Enter topic title"
                        value="{{ $record->topic_title }}"
                        required
                    >
                    </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label" for="edit_short_description">
                        Short Description <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <textarea 
                        id="edit_short_description"
                        name="short_description"
                        class="CdsDashboardCustomPopup-modal-textarea" 
                        placeholder="Enter short description"
                        required
                    >{{ html_entity_decode($record->short_description) }}</textarea>
                    </div>

                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label" for="edit_description">
                        Description <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <textarea 
                        id="edit_description"
                        name="description"
                        class="CdsDashboardCustomPopup-modal-textarea cds-texteditor" 
                        placeholder="Enter description"
                        required
                    >{{ html_entity_decode($record->description) }}</textarea>
                        </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label" for="edit_discussion_category">
                        Discussion Category <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <select 
                        id="edit_discussion_category"
                        name="discussion_category"
                        class="CdsDashboardCustomPopup-modal-select select2-input" 
                        required
                    >
                        <option value="">Select Discussion Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $record->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    </div>

                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label" for="edit_type">
                        Discussion Type <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <select 
                        id="edit_type"
                        name="type"
                        class="CdsDashboardCustomPopup-modal-select select2-input" 
                        required
                    >
                        <option value="">Select Discussion Type</option>
                        @foreach(FormHelper::groupType() as $type)
                            <option value="{{ $type['value'] }}" {{ $record->type == $type['value'] ? 'selected' : '' }}>{{ $type['label'] }}</option>
                        @endforeach
                    </select>
                    </div>
                    
                <div class="CdsDashboardCustomPopup-modal-form-group members-area" style="display:{{ $record->type == 'private' ? 'block' : 'none' }}">
                    <label class="CdsDashboardCustomPopup-modal-label" for="edit_selected_members">
                        Select Members
                    </label>
                    <select 
                        id="edit_selected_members"
                        name="selected_members[]"
                        class="CdsDashboardCustomPopup-modal-select select2-input cds-multiselect add-multi" 
                        multiple
                    >
                        @foreach($members ?? [] as $member)
                            <option value="{{ $member['id'] }}" {{ in_array($member['id'], $record->members->pluck('member_id')->toArray()) ? 'selected' : '' }}>{{ $member['name'] }}</option>
                        @endforeach
                    </select>
                            <span class="text-danger selected_members"></span>
                    </div>

                <div class="CdsDashboardCustomPopup-modal-form-group allow-join-member" style="display:{{ $record->type == 'private' ? 'block' : 'none' }}">
                    <div class="CdsDashboardCustomPopup-modal-checkbox-group">
                        <label class="CdsDashboardCustomPopup-modal-checkbox-label">
                            <input type="checkbox" 
                                   name="allow_join_request" 
                                   value="1" 
                                   id="edit_allow_join_request"
                                   {{ $record->allow_join_request == 1 ? 'checked' : '' }}
                                   class="CdsDashboardCustomPopup-modal-checkbox-input">
                            <span class="CdsDashboardCustomPopup-modal-checkbox-custom"></span>
                            <span class="CdsDashboardCustomPopup-modal-checkbox-text">Allow Member to Join</span>
                        </label>
                    </div>
                </div>



                <!-- Current Files Display -->
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">
                        <i class="fas fa-paperclip"></i> Current Files 
                        <span class="file-count-badge">({{ $record->files && !empty(trim($record->files)) ? count(array_filter(explode(',', $record->files))) : 0 }})</span>
                    </label>
                    @if($record->files && !empty(trim($record->files)))
                        <div class="CdsDashboardCustomPopup-modal-prev-file-list current-files-section">
                            @foreach(explode(',', $record->files) as $index => $file)
                                @if($file && !empty(trim($file)))
                                    <div class="CdsDashboardCustomPopup-modal-file-item current-file-item">
                                        <div class="CdsDashboardCustomPopup-modal-file-preview">
                                            @if(in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']))
                                                <img src="{{ discussionDirUrl($file, 's') }}" alt="File {{ $index + 1 }}" class="file-thumbnail">
                                            @else
                                                <div class="file-icon-placeholder">
                                                    @php
                                                        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                        $iconClass = 'fas fa-file';
                                                        if (in_array($extension, ['pdf'])) {
                                                            $iconClass = 'fas fa-file-pdf';
                                                        } elseif (in_array($extension, ['doc', 'docx'])) {
                                                            $iconClass = 'fas fa-file-word';
                                                        } elseif (in_array($extension, ['xls', 'xlsx'])) {
                                                            $iconClass = 'fas fa-file-excel';
                                                        } elseif (in_array($extension, ['ppt', 'pptx'])) {
                                                            $iconClass = 'fas fa-file-powerpoint';
                                                        } elseif (in_array($extension, ['zip', 'rar', '7z'])) {
                                                            $iconClass = 'fas fa-file-archive';
                                                        } elseif (in_array($extension, ['txt'])) {
                                                            $iconClass = 'fas fa-file-text';
                                                        }
                                                    @endphp
                                                    <i class="{{ $iconClass }} fa-2x"></i>
                                                    <small class="d-block">{{ strtoupper($extension) }}</small>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="CdsDashboardCustomPopup-modal-file-info">
                                            <div class="CdsDashboardCustomPopup-modal-file-name">{{ $file }}</div>
                                            <div class="file-status">
                                                <span class="status-badge status-uploaded">
                                                    <i class="fas fa-check-circle"></i> Uploaded
                                                </span>
                                            </div>
                                        </div>
                                        <div class="file-actions">
                                            <a href="{{ discussionDirUrl($file) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="View/Download">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCurrentFile('{{ $file }}', {{ $index }})" title="Remove">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="current-files-section no-files-section">
                            <div class="no-files-message">
                                <i class="fas fa-inbox fa-3x text-muted"></i>
                                <p class="text-muted mt-2">No files uploaded yet</p>
                                <small class="text-muted">Debug: Record files = "{{ $record->files }}"</small>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">Add New Media Files</label>
                    <div class="CdsDashboardCustomPopup-modal-upload-container" id="editDiscussionMediaUpload">
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
                
                <input type="hidden" name="file" id="edit-file-modal" />
                <input type="hidden" name="updated_files" id="edit-updated-files" value="{{ $record->files }}" />
            </form>
        </div>
            </div>
    
    <div class="CdsDashboardCustomPopup-modal-submit-section">
        <button type="submit" form="editDiscussionForm" class="CdsDashboardCustomPopup-modal-submit-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20">
                <path d="M5 13l4 4L19 7"></path>
            </svg>
            <span>Update Discussion</span>
        </button>
    </div>
</div>

<script>
$(document).ready(function(){
    // Initialize select2 for the edit modal
    setTimeout(() => {
        initSelect();
    }, 500);

    // Initialize text editor for description only
    setTimeout(() => {
        initEditor("edit_description");
    }, 1000);

    // Initialize custom file uploader
    setTimeout(() => {
        // Simple file upload implementation
        const uploadContainer = document.getElementById('editDiscussionMediaUpload');
        const fileInput = uploadContainer.querySelector('input[type="file"]');
        const uploadArea = uploadContainer.querySelector('.CdsDashboardCustomPopup-modal-upload-area');
        const fileList = uploadContainer.querySelector('.CdsDashboardCustomPopup-modal-file-list');
        
        if (fileInput && uploadArea) {
            // Make upload area clickable
            uploadArea.addEventListener('click', function() {
                fileInput.click();
            });
            
            // Handle file selection
            fileInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                
                files.forEach(file => {
                    
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
                        if (response.status) {
                            // Add to hidden input for form submission
                            const currentFiles = $('#edit-file-modal').val();
                            const newFiles = currentFiles ? currentFiles + ',' + response.filename : response.filename;
                            $('#edit-file-modal').val(newFiles);
                            
                            // Update file item with success state
                            fileItem.classList.add('upload-success');
                            progressBar.style.background = '#28a745';
                        } else {
                            errorMessage(response.message || 'Upload failed');
                            fileItem.remove();
                        }
                    },
                    error: function(xhr, status, error) {
                        errorMessage('Upload failed: ' + error);
                        fileItem.remove();
                    }
                });
            }
        } else {
            // File input or upload area not found
        }
    }, 1000);

    // Handle edit discussion type change
    $(document).on("change","#edit_type",function(){
        if($(this).val() == 'private'){
            $(".members-area").show();
            $(".allow-join-member").show();
        }else{
            $(".members-area").hide();
            $(".allow-join-member").hide();
        }
    });

    // Handle edit form submission
    $("#editDiscussionForm").submit(function(e) {
        e.preventDefault();
        
        var isValid = formValidation("editDiscussionForm");
        if (!isValid) {
            return false;
        }
       
        // Combine existing files and new files
        var existingFiles = $('#edit-updated-files').val();
        var newFiles = $('#edit-file-modal').val();
        
        // Combine files
        var allFiles = '';
        if (existingFiles && newFiles) {
            allFiles = existingFiles + ',' + newFiles;
        } else if (existingFiles) {
            allFiles = existingFiles;
        } else if (newFiles) {
            allFiles = newFiles;
        }
        
        // Update the updated_files input with combined files
        $('#edit-updated-files').val(allFiles);
        
        var formData = new FormData($(this)[0]);
        var url = $("#editDiscussionForm").attr('action');
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

// Function to remove current files
function removeCurrentFile(filename, index) {
    if (confirm('Are you sure you want to remove this file?')) {
        const currentFiles = $('#edit-updated-files').val();
        
        const filesArray = currentFiles.split(',').filter(file => file.trim() !== filename);
        const updatedFiles = filesArray.join(',');
        
        $('#edit-updated-files').val(updatedFiles);
        
        // Remove the file item from DOM
        const fileItem = $(`.current-file-item:eq(${index})`);
        fileItem.fadeOut(300, function() {
            $(this).remove();
            updateFileCount();
        });
        
        successMessage('File removed successfully');
    }
}

// Function to update file count badge
function updateFileCount() {
    const currentFiles = $('.current-file-item').length;
    const newFiles = $('.CdsDashboardCustomPopup-modal-file-item:not(.current-file-item)').length;
    const totalFiles = currentFiles + newFiles;
    
    // Update current files count
    $('.file-count-badge').text(`(${currentFiles})`);
    
    // Show/hide current files section if empty
    if (currentFiles === 0) {
        $('.current-files-section').fadeOut(300);
    }
}

// Function to remove newly uploaded files
function removeNewFile(button) {
    if (confirm('Are you sure you want to remove this file?')) {
        const fileItem = button.closest('.CdsDashboardCustomPopup-modal-file-item');
        const fileName = fileItem.querySelector('.CdsDashboardCustomPopup-modal-file-name').textContent;
        
        // Remove from hidden input
        const currentFiles = $('#edit-file-modal').val();
        const filesArray = currentFiles.split(',').filter(file => file.trim() !== fileName);
        const updatedFiles = filesArray.join(',');
        
        $('#edit-file-modal').val(updatedFiles);
        
        // Remove the file item from DOM with animation
        $(fileItem).fadeOut(300, function() {
            $(this).remove();
            updateFileCount();
        });
        
        successMessage('File removed successfully');
    }
}
</script> 

@endsection
