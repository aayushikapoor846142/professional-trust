<div class="CDSFeed-form-content">
    <div class="CDSFeed-form-header">
        <h2 class="CDSFeed-form-title">Edit Feed</h2>
    </div>
    <form id="editFeedForm" action="{{ baseUrl('my-feeds/update/'.$record->unique_id) }}" enctype="multipart/form-data">
        @csrf
        <div class="CDSFeed-form-group js-form-message">
            <textarea class="CDSFeed-form-textarea" name="post" id="editPostBox" placeholder="Write your content here...">{!! $record->post !!}</textarea>
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
            <div class="CDSFeed-upload-container" id="editFeedMediaUpload">
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
                <div class="CDSFeed-file-list"></div>
                <div class="CDSFeed-prev-file-list">
                    @if($record->media != '')
                        @php 
                        $medias  = explode(",",$record->media);
                        @endphp
                        @foreach($medias as $media)
                        <div class="CDSFeed-file-item">
                            <input type="hidden" name="prev_files[]" value="{{ $media }}" />
                            <div class="CDSFeed-file-preview">
                                <img src="{{ feedDirUrl(trim($media), 's') }}">
                            </div>
                            <div class="CDSFeed-file-info">
                                <div class="CDSFeed-file-name">{{ $media }}</div>
                            </div>
                            <button type="button" class="CDSFeed-file-remove file-remove">
                                Remove
                            </button>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Schedule Publishing with Checkbox -->
        <div class="CDSFeed-form-group">
           @if(
            (
                ($record->status == "post" || $record->status == "scheduled") 
                && $record->schedule_date != ""
            ) 
            || 
            (
                $record->status == "draft" && $record->schedule_date == ""
            )
        )
            <div class="CDSFeed-schedule-wrapper">
                <label class="CDSFeed-checkbox-label">
                    <input type="checkbox" id="scheduleCheckbox" name="is_scheduled" class="CDSFeed-checkbox" {{ $record->status == 'scheduled' ? 'checked' : '' }} {{ ($record->status == 'post' && $record->schedule_date != '') ? 'disabled' : '' }}>
                    <span class="CDSFeed-checkbox-text">Schedule Publishing</span>
                </label>
                <div class="CDSFeed-schedule-input-wrapper" id="CDSFeed-schedule" style="{{ ($record->status == 'scheduled' || $record->schedule_date != '') ? '' : 'display:none' }}">
                    {!! FormHelper::formDatepicker([
                        'label' => 'Schedule Date',
                        'name' => 'schedule_date',
                        'id' => 'scheduleDateTime',
                        'required' => false,
                        'value' => $record->schedule_date,
                        'disabled' => ($record->status == 'post' && $record->schedule_date != '') ? 'disabled' : ''
                    ]) !!}
                    <!-- <input type="datetime-local" id="scheduleDateTime" name="scheduled_at" class="CDSFeed-form-input CDSFeed-schedule-input" disabled> -->
                </div>
            </div>
            @endif
        </div>
        
        <div class="CDSFeed-form-actions">
            <button type="button" class="CDSFeed-btn CDSFeed-btn-secondary" onclick="hideCreateModal()">Cancel</button>
            <button type="button" class="CDSFeed-btn CDSFeed-btn-secondary" onclick="saveAsDraft()">Save as Draft</button>
            <button type="submit" class="CDSFeed-btn CDSFeed-btn-primary">Publish</button>
        </div>
    </form>
</div>

<link rel="stylesheet" href="{{ url('assets/css/custom-file-upload.css') }}">

<script>
    function hideCreateModal() {
    const feedEdit = document.querySelector('.CDSFeed-edit');
    if (feedEdit) {
        feedEdit.innerHTML = ''; // clears the form
        // OR feedEdit.classList.remove('active'); // if you're toggling with class
    }
}

    // Initialize editor
    var editor = CustomEditor.init(".CDSFeed-form-textarea");
    new EmojiPicker(".message-emoji-icon", {
        targetElement: "#editPostBox",
        onEmojiSelect:function(emoji){
            if (editor) {
                editor.appendText(emoji);
            }
        }
    });
    // CustomCalendarWidget.initialize("scheduleDateTime",{
    //     minDate: new Date(),
    // });
    // Initialize file upload manager by ID
    const editFeedUploader = new FileUploadManager('#editFeedMediaUpload', {
        maxFileSize: 10 * 1024 * 1024, // 10MB
        maxFiles: 5, // Maximum 5 files
        allowedTypes: [
            'image/jpeg', 
            'image/png', 
            'image/gif', 
        ],
        onFileAdded: function(fileData) {
            console.log('File added:', fileData.name);
        },
        onFileRemoved: function(fileData) {
            console.log('File removed:', fileData.name);
        },
        onError: function(message) {
            // Custom error handling
            showNotification(message, 'error');
        }
    });
    
    // Initialize the uploader
    editFeedUploader.init();
    
    // Schedule checkbox functionality
    const scheduleCheckbox = document.getElementById('scheduleCheckbox');
    const scheduleWrapper = document.getElementById('CDSFeed-schedule');
    const scheduleDateTime = document.getElementById('scheduleDateTime');
    
    // Handle checkbox change
    if (scheduleCheckbox && scheduleDateTime && scheduleWrapper) {
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
                // scheduleDateTime.value = '';
            }
        });
    }
  
    // Form submission
    document.getElementById('editFeedForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        // Validate schedule date if checkbox is checked
        if (scheduleCheckbox && scheduleDateTime){
            if (scheduleCheckbox.checked && !scheduleDateTime.value) {
                errorMessage('Please select a schedule date and time');
                scheduleDateTime.focus();
                return;
            }
        }
        
        
        const formData = new FormData(this);
        
        // Remove scheduled_at if not scheduled
        // if (!scheduleCheckbox.checked) {
        //     formData.delete('scheduled_at');
        //     formData.delete('is_scheduled');
        // }
        
        // Add files from uploader
        const files = editFeedUploader.getFiles();
        files.forEach((file, index) => {
            formData.append(`media[${index}]`, file);
        });
        formData.append('scheduled_at',$('#scheduleDateTime').val());
        // Add status based on scheduling
        if (scheduleCheckbox){
            if (scheduleCheckbox.checked) {
                formData.append('status', 'scheduled');
            } else {
                formData.append('status', 'published');
            }
        }else{
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
                        editor.reset();
                        editFeedUploader.reset();
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
        const form = document.getElementById('editFeedForm');
        const formData = new FormData(form);
        
        // Add status as draft
        formData.append('status', 'draft');
        
        // Remove scheduled_at if saving as draft
        formData.delete('scheduled_at');
        formData.delete('is_scheduled');
        
        // Add files
        const files = editFeedUploader.getFiles();
        files.forEach((file, index) => {
            formData.append(`media[${index}]`, file);
        });
       
        try {
            $.ajax({
                url: $("#editFeedForm").attr('action'),
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
                        editFeedUploader.reset();
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

    $(document).on("click",".file-remove",function(){
        $(this).parents(".CDSFeed-file-item").remove();
    });
</script>