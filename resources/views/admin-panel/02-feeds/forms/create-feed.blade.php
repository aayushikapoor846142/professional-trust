@extends('components.custom-popup',['modalTitle'=>$pageTitle ?? 'Create New Feed'])
@section('custom-popup-content')
<div class="container">
    <div class="row">
        <div class="col-xl-12">
            <div class="cds-ty-dashboard-box">
                <div class="CDSFeed-form-content">
                    <form id="createFeedForm" action="{{ baseUrl('my-feeds/save') }}" method ="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="CDSFeed-form-group js-form-message">
                            <textarea class="CDSFeed-form-textarea" id="postBox" name="post" placeholder="Write your content here..."></textarea>
                            <div
                                class="cdsTYDashboard-discussion-panel-view-editor-custom-container-action-buttons">
                                <div class="message-emoji-icon emoji-icon">
                                    <i class="fa-sharp fa-solid fa-face-smile"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- File Upload Section with unique ID -->
                        <div class="CDSFeed-form-group">
                            <label class="CDSFeed-form-label">Media Files</label>
                            <div class="CDSFeed-upload-container" id="feedMediaUpload">
                                <div class="CDSFeed-upload-area">
                                    <input type="file" class="CDSFeed-file-input" multiple accept="image/*,.pdf,.doc,.docx" style="display: none;">
                                    <div class="CDSFeed-upload-icon">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="7 10 12 15 17 10"></polyline>
                                            <line x1="12" y1="15" x2="12" y2="3"></line>
                                        </svg>
                                    </div>
                                    <p class="CDSFeed-upload-text">Drag and drop files here or click to browse</p>
                                    <p class="CDSFeed-upload-hint">Supports: JPG, PNG, GIF, PDF, DOC, DOCX (Max 10MB per file)</p>
                                </div>
                                
                                <!-- File Preview Area -->
                                <div class="CDSFeed-file-list" style="display: none;">
                                    <!-- Files will be dynamically added here -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- Schedule Publishing with Checkbox -->
                        <div class="CDSFeed-form-group">
                            <div class="CDSFeed-schedule-wrapper">
                                <label class="CDSFeed-checkbox-label">
                                    <input type="checkbox" id="scheduleCheckbox" name="is_scheduled" class="CDSFeed-checkbox">
                                    <span class="CDSFeed-checkbox-text">Schedule Publishing</span>
                                </label>
                                <div class="CDSFeed-schedule-input-wrapper" id="CDSFeed-schedule" style="display:none">
                                {!! FormHelper::formDatepicker([
                                        'label' => 'Schedule Date',
                                        'name' => 'schedule_date',
                                        'id' => 'scheduleDateTime',
                                        'disabled'=>'disabled',
                                        'required' => false
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('custom-popup-footer')
<div class="CDSFeed-form-actions">
    <button type="button" class="CDSFeed-btn CDSFeed-btn-secondary" onclick="hideCreateModal()">Cancel</button>
    <button type="button" class="CDSFeed-btn CDSFeed-btn-secondary" onclick="saveAsDraft()">Save as Draft</button>
    <button form="createFeedForm" type="submit" class="CDSFeed-btn CDSFeed-btn-primary">Publish</button>
</div>
    
@endsection 
<script src="{{url('assets/js/custom-file-upload.js')}}"></script>
<link rel="stylesheet" href="{{ url('assets/css/custom-file-upload.css') }}">

<script>
// Store instances in a dedicated namespace
window.feedComponents = {
    editor: null,
    emojiPicker: null,
    uploader: null
};

let feedUploader;
// Initialize all components
function initializeFeedComponents() {
    // Clean up existing instances first
    cleanupFeedComponents();
    window.feedComponents.editor  = initEditor("postBox")
    // Initialize editor
    // window.feedComponents.editor = CustomEditor.init(".CDSFeed-form-textarea");
    
    // Initialize emoji picker
    window.feedComponents.emojiPicker = new EmojiPicker(".message-emoji-icon", {
        targetElement: "#postBox",
        onEmojiSelect: function(emoji) {
            if (window.feedComponents.editor) {
                window.feedComponents.editor.appendText(emoji);
            }
        }
    });
    
    // Initialize date picker (assuming this doesn't need instance tracking)
    feedDatePicker("scheduleDateTime");
    
    // Initialize file upload manager
    window.feedComponents.uploader = new FileUploadManager('#feedMediaUpload', {
        maxFileSize: 10 * 1024 * 1024,
        maxFiles: 5,
        allowedTypes: ['image/jpeg', 'image/png', 'image/gif'],
        onFileAdded: function(fileData) {
            console.log('File added:', fileData.name);
        },
        onFileRemoved: function(fileData) {
            console.log('File removed:', fileData.name);
        },
        onError: function(message) {
            showNotification(message, 'error');
        }
    });
    window.feedComponents.uploader.init();
}

// Clean up components
function cleanupFeedComponents() {
    // Clean up editor
    if (window.feedComponents.editor && typeof window.feedComponents.editor.destroy === 'function') {
        window.feedComponents.editor.destroy();
    }
    
    // Clean up emoji picker
    if (window.feedComponents.emojiPicker && typeof window.feedComponents.emojiPicker.destroy === 'function') {
        window.feedComponents.emojiPicker.destroy();
    }
    
    // Clean up uploader
    if (window.feedComponents.uploader && typeof window.feedComponents.uploader.destroy === 'function') {
        window.feedComponents.uploader.destroy();
    }
    
    // Reset references
    window.feedComponents.editor = null;
    window.feedComponents.emojiPicker = null;
    window.feedComponents.uploader = null;
}

// Modal event handlers
// $('#popupModal')
//     .on('shown.bs.modal', initializeFeedComponents)
//     .on('hidden.bs.modal', cleanupFeedComponents);
initializeFeedComponents();
// Manual close function
function hideCreateModal() {
    $('#popupModal').modal('hide');
}
    // Schedule checkbox functionality
    const scheduleCheckbox = document.getElementById('scheduleCheckbox');
    const scheduleWrapper = document.getElementById('CDSFeed-schedule');
    const scheduleDateTime = document.getElementById('scheduleDateTime');
    
    // Handle checkbox change
    scheduleCheckbox.addEventListener('change', function() {
        if (this.checked) {
            scheduleDateTime.disabled = false;
            scheduleDateTime.required = true;
            // Focus on datetime input
            scheduleWrapper.style.display = 'block';
            scheduleDateTime.focus();
        } else {
            scheduleDateTime.disabled = true;
            scheduleDateTime.required = false;
            scheduleWrapper.style.display = 'none';
            scheduleDateTime.value = '';
        }
    });
    
    // Form submission
    document.getElementById('createFeedForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validate schedule date if checkbox is checked
        if (scheduleCheckbox.checked && !scheduleDateTime.value) {
            errorMessage('Please select a schedule date and time');
            scheduleDateTime.focus();
            return;
        }
        
        const formData = new FormData(this);
        
        // Remove scheduled_at if not scheduled
        if (!scheduleCheckbox.checked) {
            formData.delete('scheduled_at');
            formData.delete('is_scheduled');
        }
        
        // Add files from uploader
     if (window.feedComponents.uploader) {
        try {
            const files = window.feedComponents.uploader.getFiles();
            files.forEach((file, index) => {
                formData.append(`media[${index}]`, file);
            });
        } catch (error) {
            console.error('Error getting files:', error);
            errorMessage('Failed to process uploaded files');
            return;
        }
    } else {
        errorMessage('File uploader not initialized');
        return;
    }
        // Add status based on scheduling
        if (scheduleCheckbox.checked) {
            formData.append('status', 'scheduled');
        } else {
            formData.append('status', 'published');
        }
        
        try {
            $.ajax({
                url: $(this).attr("action"),
                type: "post",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function() {
                    showLoader();
                    $(".CDSFeed-btn").attr("disabled","disabled");
                },
                
                success: function(response) {
                    
                    $(".CDSFeed-btn").removeAttr("disabled");
                    if (response.status == true) {
                        hideLoader();
                        successMessage(response.message);
                    try {
                    // Use the globally stored instances
                    if (window.feedComponents?.editor && typeof window.feedComponents.editor.reset === 'function') {
                        window.feedComponents.editor.reset();
                    }
                    if (window.feedComponents?.uploader && typeof window.feedComponents.uploader.reset === 'function') {
                        window.feedComponents.uploader.reset();
                    }
                } catch (resetError) {
                    console.error("Error resetting components:", resetError);
                }
                        loadData(1);
                        closeModal();
                        location.reload();
                    } else {
                        hideLoader();
                        validation(response.message);
                    }
                },
                error: function() {
                    $(".CDSFeed-btn").removeAttr("disabled");
                    hideLoader();
                    internalError();
                }
            });
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        }
    });
    
    // Save as draft function
    async function saveAsDraft() {
        const form = document.getElementById('createFeedForm');
        const formData = new FormData(form);
        
        // Add status as draft
        formData.append('status', 'draft');
        
        // Remove scheduled_at if saving as draft
        formData.delete('scheduled_at');
        formData.delete('is_scheduled');
        
        // Add files
    if (window.feedComponents.uploader) {
        try {
            const files = window.feedComponents.uploader.getFiles();
            files.forEach((file, index) => {
                formData.append(`media[${index}]`, file);
            });
        } catch (error) {
            console.error('Error getting files:', error);
            errorMessage('Failed to process uploaded files');
            return;
        }
    } else {
        errorMessage('File uploader not initialized');
        return;
    }
        
        try {
            $.ajax({
                url: $("#createFeedForm").attr('action'),
                type: "post",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function() {
                    showLoader();
                    $(".CDSFeed-btn").attr("disabled","disabled");
                },
                success: function(response) {
                    
                    $(".CDSFeed-btn").removeAttr("disabled");
                    if (response.status == true) {
                        hideLoader();
                        successMessage(response.message);
                        editor.reset();
                        feedUploader.reset();
                        loadData(1);
                             location.reload();
                    } else {
                        validation(response.message);
                        hideLoader();
                    }
                },
                error: function() {
                    $(".CDSFeed-btn").removeAttr("disabled");
                    hideLoader();
                    internalError();
                }
            });
        } catch (error) {
            console.error('Error saving draft:', error);
            alert('Error saving draft. Please try again.');
        }
    }
    
    // Optional: Custom notification function
    function showNotification(message, type = 'info') {
        // You can implement your own notification system here
        console.log(`${type}: ${message}`);
        alert(message);
    }

       // Save as draft function
    async function saveAsDraft() {
        const form = document.getElementById('createFeedForm');
        const formData = new FormData(form);
        
        // Add status as draft
        formData.append('status', 'draft');
        
        // Remove scheduled_at if saving as draft
        formData.delete('scheduled_at');
        formData.delete('is_scheduled');
        
        // Add files
    if (window.feedComponents.uploader) {
        try {
            const files = window.feedComponents.uploader.getFiles();
            files.forEach((file, index) => {
                formData.append(`media[${index}]`, file);
            });
        } catch (error) {
            console.error('Error getting files:', error);
            errorMessage('Failed to process uploaded files');
            return;
        }
    } else {
        errorMessage('File uploader not initialized');
        return;
    }
        
        try {
            $.ajax({
                url: $("#createFeedForm").attr('action'),
                type: "post",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function() {
                    showLoader();
                    $(".CDSFeed-btn").attr("disabled","disabled");
                },
                success: function(response) {
                    
                    $(".CDSFeed-btn").removeAttr("disabled");
                    if (response.status == true) {
                        hideLoader();
                        successMessage(response.message);
                        editor.reset();
                        feedUploader.reset();
                        loadData(1);
                        closeModal();
                    } else {
                        validation(response.message);
                        hideLoader();
                    }
                },
                error: function() {
                    $(".CDSFeed-btn").removeAttr("disabled");
                    hideLoader();
                    internalError();
                }
            });
        } catch (error) {
            console.error('Error saving draft:', error);
            alert('Error saving draft. Please try again.');
        }
    }
</script> 
@endsection
