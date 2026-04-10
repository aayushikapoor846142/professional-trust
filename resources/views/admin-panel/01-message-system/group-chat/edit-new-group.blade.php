@extends('components.custom-popup',['modalTitle'=>$pageTitle ?? 'Edit Group'])
@section('custom-popup-content')

<div class="CdsDashboardCustomPopup-modal-form-wrapper">
    <div class="CdsDashboardCustomPopup-modal-form-grid">
        <div class="CdsDashboardCustomPopup-modal-left-column">
            
            <form id="edit-group-form" enctype="multipart/form-data"
                  action="{{ baseUrl('/group/update-new-group/' . $record->unique_id) }}" method="POST">
                @csrf
                
                <div class="CdsDashboardCustomPopup-modal-form-header">
                    <h3 class="CdsDashboardCustomPopup-modal-title">Edit Group</h3>
                    <p class="CdsDashboardCustomPopup-modal-subtitle">Update your group information and manage members</p>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-icon-upload-section">
                    <div class="CdsDashboardCustomPopup-modal-icon-circle" id="groupIcon" onclick="triggerGroupIcon()">
                        @if($record->group_image)
                            <img src="{{ groupChatDirUrl($record->group_image, 't') }}" alt="Group Icon">
                        @else
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2196f3" stroke-width="2">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                        @endif
                    </div>
                    <div class="CdsDashboardCustomPopup-modal-icon-info">
                        <h3>Group Icon</h3>
                        <p>Click to upload</p>
                    </div>
                    <input type="file" name="group_image" id="groupImageInput" accept="image/*" style="display:none;" onchange="previewGroupImage(this)">
                </div>

                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label" for="group-name">
                        Group Name <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="group-name"
                        name="name"
                        class="CdsDashboardCustomPopup-modal-input" 
                        placeholder="Enter group name"
                        value="{{ $record->name }}"
                        required
                    >
                </div>

                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">
                        Category <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <div class="CdsDashboardCustomPopup-modal-radio-group">
                        @foreach(FormHelper::groupType() as $type)
                        <label class="CdsDashboardCustomPopup-modal-radio-label">
                            <input type="radio" 
                                   name="group_type" 
                                   class="CdsDashboardCustomPopup-modal-radio-input" 
                                   value="{{ $type['value'] }}"
                                   {{ $record->type == $type['value'] ? 'checked' : '' }}
                                   required>
                            <div class="CdsDashboardCustomPopup-modal-radio-content">
                                <div class="CdsDashboardCustomPopup-modal-radio-title">{{ $type['label'] }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">Media Files</label>
                    <div class="CdsDashboardCustomPopup-modal-upload-container" id="grpMediaUpload">
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
                        <div class="CdsDashboardCustomPopup-modal-prev-file-list">
                            <div class="CdsDashboardCustomPopup-modal-file-item">
                                <div class="CdsDashboardCustomPopup-modal-file-preview">
                                    <img src="{{ groupChatDirUrl($record->banner_image, 't') }}">
                                </div>
                                <div class="CdsDashboardCustomPopup-modal-file-info">
                                    <div class="CdsDashboardCustomPopup-modal-file-name">{{ $record->banner_image }}</div>
                                </div>
                                <button type="button" class="CdsDashboardCustomPopup-modal-file-remove file-remove">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label" for="edit_description">
                        Description <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <textarea 
                        id="edit_description"
                        name="description"
                        class="CdsDashboardCustomPopup-modal-textarea" 
                        placeholder="Enter group description"
                        required
                    >{{ html_entity_decode($record->description) }}</textarea>
                </div>
            </form>
        </div>
        
        <div class="CdsDashboardCustomPopup-modal-right-column">
            <div class="CdsDashboardCustomPopup-modal-members-section">
                <h3 class="CdsDashboardCustomPopup-modal-members-header">
                    Group Members <span class="CdsDashboardCustomPopup-modal-required">*</span>
                </h3>
                <div class="gerror text-danger"></div>
                <div class="CdsDashboardCustomPopup-modal-search-wrapper">
                    <svg class="CdsDashboardCustomPopup-modal-search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" 
                           class="CdsDashboardCustomPopup-modal-search-box" 
                           id="searchMembersInput" 
                           placeholder="Search Members..." 
                           onkeyup="filterMembers()">
                </div>
                
                <!-- Current Members Section -->
                <div class="CdsDashboardCustomPopup-modal-members-subsection">
                    <h4 class="CdsDashboardCustomPopup-modal-subsection-title">Current Members</h4>
                    <div class="CdsDashboardCustomPopup-modal-member-list" id="currentMembersList">
                        @foreach($record->members->where('id','!=',auth()->user()->id) as $member)
                        <div class="CdsDashboardCustomPopup-modal-member-item member-item CdsDashboardCustomPopup-modal-selected" 
                             data-name="{{ strtolower($member->first_name.' '.$member->last_name) }}" 
                             for="member-list-{{$member->id}}">
                            <input type="checkbox" 
                                   id="member-list-{{$member->id}}" 
                                   class="CdsDashboardCustomPopup-modal-member-checkbox g-members" 
                                   value="{{$member->id}}" 
                                   name="member_id[]"
                                   checked>
                            <div class="CdsDashboardCustomPopup-modal-member-avatar">
                                @if($member->profile_image)
                                    <img src="{{ userDirUrl($member->profile_image, 'm') }}" alt="{{ $member->first_name }} {{ $member->last_name }}">
                                @else
                                    @php
                                        $initial = strtoupper(substr($member->first_name, 0, 1)) . strtoupper(substr($member->last_name, 0, 1));
                                    @endphp
                                    <div class="group-icon">
                                        {{ $initial }}
                                    </div>
                                @endif
                                @if($member->is_login)
                                    <span class="status-online"></span>
                                @else
                                    <span class="status-offline"></span>
                                @endif
                            </div>
                            <div class="CdsDashboardCustomPopup-modal-member-info">
                                <p class="CdsDashboardCustomPopup-modal-member-name">{{$member->first_name." ".$member->last_name}}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Available Members Section -->
                @if(isset($members) && $members->count() > 0)
                <div class="CdsDashboardCustomPopup-modal-members-subsection">
                    <h4 class="CdsDashboardCustomPopup-modal-subsection-title">Add New Members</h4>
                    <div class="CdsDashboardCustomPopup-modal-member-list" id="availableMembersList">
                        @foreach($members as $member)
                        @php
                            $isAlreadyMember = $record->members->contains('id', $member->id);
                        @endphp
                        @if(!$isAlreadyMember)
                        <div class="CdsDashboardCustomPopup-modal-member-item member-item" 
                             data-name="{{ strtolower($member->first_name.' '.$member->last_name) }}" 
                             for="member-list-{{$member->id}}">
                            <input type="checkbox" 
                                   id="member-list-{{$member->id}}" 
                                   class="CdsDashboardCustomPopup-modal-member-checkbox g-members" 
                                   value="{{$member->id}}" 
                                   name="member_id[]">
                            <div class="CdsDashboardCustomPopup-modal-member-avatar">
                                @if($member->profile_image)
                                    <img src="{{ userDirUrl($member->profile_image, 'm') }}" alt="{{ $member->first_name }} {{ $member->last_name }}">
                                @else
                                    @php
                                        $initial = strtoupper(substr($member->first_name, 0, 1)) . strtoupper(substr($member->last_name, 0, 1));
                                    @endphp
                                    <div class="group-icon">
                                        {{ $initial }}
                                    </div>
                                @endif
                                @if($member->is_login)
                                    <span class="status-online"></span>
                                @else
                                    <span class="status-offline"></span>
                                @endif
                            </div>
                            <div class="CdsDashboardCustomPopup-modal-member-info">
                                <p class="CdsDashboardCustomPopup-modal-member-name">{{$member->first_name." ".$member->last_name}}</p>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif
                
                @if(!isset($members) || $members->count() == 0)
                <div class="CdsDashboardCustomPopup-modal-connection-hint">
                    <p>No additional members available to add. Please <a href="{{ baseUrl('connections/connect') }}" target="_blank">add connections</a> by clicking on the link.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="CdsDashboardCustomPopup-modal-submit-section">
        <button type="submit" form="edit-group-form" class="CdsDashboardCustomPopup-modal-submit-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20">
                <path d="M5 13l4 4L19 7"></path>
            </svg>
            <span>Save Changes</span>
        </button>
    </div>
</div>

<script src="{{url('assets/js/custom-file-upload.js')}}"></script>
<link rel="stylesheet" href="{{ url('assets/css/custom-file-upload.css') }}">

<script>
    $(document).ready(function() {
        // Toggle selected member UI
        $(".g-members").change(function(){
            const item = $(this).closest('.CdsDashboardCustomPopup-modal-member-item');
            if(this.checked) {
                item.addClass('CdsDashboardCustomPopup-modal-selected');
            } else {
                item.removeClass('CdsDashboardCustomPopup-modal-selected');
                $(".gerror").text("You are going to remove the members from group");
            }
        });
        
        // Check if FileUploadManager is available
        if (typeof FileUploadManager === 'undefined') {
            console.error('FileUploadManager is not loaded');
            return;
        }
        
        const grpUploader = new FileUploadManager('#grpMediaUpload', {
            maxFileSize: 10 * 1024 * 1024, // 10MB
            maxFiles: 5, // Maximum 5 files
            allowedTypes: [
                'image/jpeg', 
                'image/png', 
                'image/gif',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
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
        const initResult = grpUploader.init();
        if (!initResult) {
            console.error('Failed to initialize FileUploadManager for #grpMediaUpload');
        } else {
            console.log('FileUploadManager initialized successfully for #grpMediaUpload');
        }

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
        $(this).parents(".CdsDashboardCustomPopup-modal-file-item").remove();
    });

    // Icon triggers
    function triggerGroupIcon() { 
        $("#groupImageInput").click(); 
    }
    
    function previewGroupImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => $("#groupIcon").html(`<img src="${e.target.result}" alt="Group Icon"/>`);
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    // Search functionality
    function filterMembers() {
        const searchInput = document.getElementById("searchMembersInput").value.toLowerCase();
        const currentMembersList = document.getElementById("currentMembersList");
        const availableMembersList = document.getElementById("availableMembersList");
        
        // Filter current members
        if (currentMembersList) {
            const currentMembers = currentMembersList.getElementsByClassName("member-item");
            for (let member of currentMembers) {
                const memberName = member.getAttribute("data-name");
                if (memberName.includes(searchInput)) {
                    member.style.display = "flex"; // Show matching members
                } else {
                    member.style.display = "none"; // Hide non-matching members
                }
            }
        }
        
        // Filter available members
        if (availableMembersList) {
            const availableMembers = availableMembersList.getElementsByClassName("member-item");
            for (let member of availableMembers) {
                const memberName = member.getAttribute("data-name");
                if (memberName.includes(searchInput)) {
                    member.style.display = "flex"; // Show matching members
                } else {
                    member.style.display = "none"; // Hide non-matching members
                }
            }
        }
    }
</script>
@endsection
