<link href="{{ url('assets/css/18CDS-add-new-group-modal.css') }}" rel="stylesheet" />
<div class="modal-dialog modal-dialog-centered CdsDashboardMessagesGroup-create-group-model-modal">
    <div class="modal-content CdsDashboardMessagesGroup-create-group-model-container">
        <form id="edit-group-form" enctype="multipart/form-data"
              action="{{ baseUrl('/group-message/update-group/' . $record->unique_id) }}" method="POST">
            <div class="CdsDashboardMessagesGroup-create-group-model-form-wrapper">
                <div class="CdsDashboardMessagesGroup-create-group-model-header">
                    <h1 class="CdsDashboardMessagesGroup-create-group-model-title">{{ $pageTitle }}</h1>
                    <button type="button" class="CdsDashboardMessagesGroup-create-group-model-close-btn" data-bs-dismiss="modal" aria-label="Close">×</button>
                </div>
                
                @csrf
                <div class="CdsDashboardMessagesGroup-create-group-model-form-grid">
                    <div class="CdsDashboardMessagesGroup-create-group-model-left-column">
                        
                        <div class="CdsDashboardMessagesGroup-create-group-model-icon-upload-section">
                            <div class="CdsDashboardMessagesGroup-create-group-model-icon-circle" id="groupIcon" onclick="triggerGroupIcon()">
                                @if($record->group_image)
                                    <img src="{{ groupChatDirUrl($record->group_image, 't') }}" alt="Group Icon">
                                @else
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2196f3" stroke-width="2">
                                        <path d="M12 5v14M5 12h14"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="CdsDashboardMessagesGroup-create-group-model-icon-info">
                                <h3>Group Icon</h3>
                                <p>Click to upload</p>
                            </div>
                            <input type="file" name="group_image" id="groupImageInput" accept="image/*" style="display:none;" onchange="previewGroupImage(this)">
                        </div>

                        <div class="CdsDashboardMessagesGroup-create-group-model-form-group">
                            <label class="CdsDashboardMessagesGroup-create-group-model-label" for="group-name">
                                Edit Group Name <span class="CdsDashboardMessagesGroup-create-group-model-required">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="group-name"
                                name="name"
                                class="CdsDashboardMessagesGroup-create-group-model-input" 
                                placeholder="Enter group name"
                                value="{{ $record->name }}"
                                required
                            >
                        </div>

                        <div class="CdsDashboardMessagesGroup-create-group-model-form-group">
                            <label class="CdsDashboardMessagesGroup-create-group-model-label">
                                Category <span class="CdsDashboardMessagesGroup-create-group-model-required">*</span>
                            </label>
                            <div class="CdsDashboardMessagesGroup-create-group-model-radio-group">
                                @foreach(FormHelper::groupType() as $type)
                                <label class="CdsDashboardMessagesGroup-create-group-model-radio-label">
                                    <input type="radio" 
                                           name="group_type" 
                                           class="CdsDashboardMessagesGroup-create-group-model-radio-input" 
                                           value="{{ $type['value'] }}"
                                           {{ $record->type == $type['value'] ? 'checked' : '' }}
                                           required>
                                    {{ $type['label'] }}
                                </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="CDSFeed-form-group">
                            <label class="CDSFeed-form-label">Media Files</label>
                            <div class="CDSFeed-upload-container" id="grpMediaUpload">
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
                                <div class="CDSFeed-prev-file-list">
                                    <div class="CDSFeed-file-item">
                                        <div class="CDSFeed-file-preview">
                                            <img src="{{ groupChatDirUrl($record->banner_image, 't') }}">
                                        </div>
                                        <div class="CDSFeed-file-info">
                                            <div class="CDSFeed-file-name">{{ $record->banner_image }}</div>
                                        </div>
                                        <button type="button" class="CDSFeed-file-remove file-remove">
                                            Remove
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                        
                        <div class="CdsDashboardMessagesGroup-create-group-model-form-group">
                            <label class="CdsDashboardMessagesGroup-create-group-model-label" for="edit_description">
                                Enter Description <span class="CdsDashboardMessagesGroup-create-group-model-required">*</span>
                            </label>
                            <textarea 
                                id="edit_description"
                                name="description"
                                class="CdsDashboardMessagesGroup-create-group-model-textarea noval cds-texteditor" 
                                placeholder="Enter group description"
                                required
                            >{{ html_entity_decode($record->description) }}</textarea>
                        </div>
                    </div>

                    <div class="CdsDashboardMessagesGroup-create-group-model-members-section">
                        <h3 class="CdsDashboardMessagesGroup-create-group-model-members-header">
                            Select Members <span class="CdsDashboardMessagesGroup-create-group-model-required">*</span>
                        </h3>
                        <div class="gerror text-danger"></div>
                        <div class="CdsDashboardMessagesGroup-create-group-model-member-list" id="membersList">
                            @foreach($record->members->where('id','!=',auth()->user()->id) as $member)
                            <div class="CdsDashboardMessagesGroup-create-group-model-member-item member-item CdsDashboardMessagesGroup-create-group-model-selected" 
                                 data-name="{{ strtolower($member->first_name.' '.$member->last_name) }}" 
                                 for="member-list-{{$member->id}}">
                                <input type="checkbox" 
                                       id="member-list-{{$member->id}}" 
                                       class="CdsDashboardMessagesGroup-create-group-model-member-checkbox g-members" 
                                       value="{{$member->id}}" 
                                       name="member_id[]"
                                       checked>
                                <div class="CdsDashboardMessagesGroup-create-group-model-member-avatar">
                                    <img src="{{ $member->profile_image ? userDirUrl($member->profile_image, 'm') : 'assets/images/default.jpg' }}"
                                         alt="{{$member->first_name}} {{$member->last_name}}">
                                    <span class="status-online"></span>
                                </div>
                                <div class="CdsDashboardMessagesGroup-create-group-model-member-info">
                                    <p class="CdsDashboardMessagesGroup-create-group-model-member-name">{{$member->first_name." ".$member->last_name}}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="CdsDashboardMessagesGroup-create-group-model-submit-section">
                        <button type="submit" class="CdsDashboardMessagesCompose-submit-btn">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Toggle selected member UI
        $(".g-members").change(function(){
            const item = $(this).closest('.CdsDashboardMessagesGroup-create-group-model-member-item');
            if(this.checked) {
                item.addClass('CdsDashboardMessagesGroup-create-group-model-selected');
            } else {
                item.removeClass('CdsDashboardMessagesGroup-create-group-model-selected');
                $(".gerror").text("You are going to remove the members from group");
            }
        });
          const grpUploader = new FileUploadManager('#grpMediaUpload', {
            maxFileSize: 10 * 1024 * 1024, // 10MB
            maxFiles: 5, // Maximum 5 files
            allowedTypes: [
                'image/jpeg', 
                'image/png', 
                'image/gif', 
            ],
            onFileAdded: function(fileData) {
                $('.file-remove').click();
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
        grpUploader.init();

        // Ajax submit
        $("#edit-group-form").submit(function(e) {
            e.preventDefault();
            var is_valid = formValidation("edit-group-form");
            if (!is_valid) return false;

            var formData = new FormData(this);
            const files = grpUploader.getFiles();

            files.forEach((file, index) => {
                formData.append(`banner_image`, file);
            });
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: showLoader,
                success: function(response) {
                    hideLoader();
                    if (response.status) {
                        successMessage(response.message);
                        window.location.reload();
                    } else {
                        validation(response.message);
                    }
                },
                error: function(xhr) {
                    hideLoader();
                    if (xhr.status === 422 && xhr.responseJSON?.error_type === 'validation') {
                        validation(xhr.responseJSON.message);
                    } else {
                        errorMessage('An unexpected error occurred. Please try again.');
                    }
                }
            });
        });
    });
    $(document).on("click",".file-remove",function(){
        $(this).parents(".CDSFeed-file-item").remove();
    });

    // Icon triggers
    function triggerGroupIcon() { $("#groupImageInput").click(); }
    function previewGroupImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => $("#groupIcon").html(`<img src="${e.target.result}" alt="Group Icon"/>`);
            reader.readAsDataURL(input.files[0]);
        }
    }

    function triggerBannerImage() { $("#bannerImageInput").click(); }
    function previewBannerImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => $("#bannerImage").html(`<img src="${e.target.result}" alt="Banner Image"/>`);
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
