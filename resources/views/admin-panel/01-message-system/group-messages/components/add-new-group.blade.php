@extends('components.custom-popup', ['modalTitle' => $pageTitle ?? 'Create New Group'])

@section('custom-popup-content')
<link href="{{ url('assets/css/18CDS-add-new-group-modal.css') }}" rel="stylesheet" />

<form id="popup-form" enctype="multipart/form-data" action="{{ baseUrl('group-message/create-group') }}" method="post">
    <div class="CdsDashboardMessagesGroup-create-group-model-form-wrapper">
        @csrf
        <div class="CdsDashboardMessagesGroup-create-group-model-form-grid">
            <div class="CdsDashboardMessagesGroup-create-group-model-left-column">
                <div class="CdsDashboardMessagesGroup-create-group-model-icon-upload-section">
                    <div class="CdsDashboardMessagesGroup-create-group-model-icon-circle" id="groupIcon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2196f3" stroke-width="2">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                    </div>
                    <div class="CdsDashboardMessagesGroup-create-group-model-icon-info">
                        <h3>Group Icon</h3>
                        <p>Click to upload</p>
                    </div>
                    <!-- Hidden File Upload for Group Icon -->
                    <input type="file" id="fileUploads" accept="image/*" style="display: none;" onchange="previewImage(event,this.value)" />
                    <div class="d-none">
                        <input type="file" id="fileInput2" name="group_image">
                    </div>
                </div>
                
                <div class="CdsDashboardMessagesGroup-create-group-model-form-group">
                    <label class="CdsDashboardMessagesGroup-create-group-model-label" for="group-name">
                        Enter Group Name <span class="CdsDashboardMessagesGroup-create-group-model-required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="group-name"
                        name="name"
                        class="CdsDashboardMessagesGroup-create-group-model-input" 
                        placeholder="Enter group name"
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
                                   {{ $loop->first ? 'checked' : '' }}
                                   required>
                            {{ $type['label'] }}
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="CDSFeed-form-group">
                    <label class="CDSFeed-form-label">Media Files</label>
                    <div class="CDSFeed-upload-container" id="grpMediaUpload">
                        <div class="CDSFeed-upload-area" >
                            <input type="file" class="CDSFeed-file-input" multiple accept="image/*,.pdf,.doc,.docx" style="display: none;">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                            
                            <p class="CDSFeed-upload-text" onclick="document.querySelector('#grpMediaUpload .CDSFeed-file-input').click()">Drag and drop files here or click to browse</p>
                            <p class="CDSFeed-upload-hint" onclick="document.querySelector('#grpMediaUpload .CDSFeed-file-input').click()">Supports: JPG, PNG, GIF, PDF, DOC, DOCX (Max 10MB per file)</p>
                        </div>
                        
                        <!-- File Preview Area -->
                        <div class="CDSFeed-file-list" style="display: none;">
                            <!-- Files will be dynamically added here by FileUploadManager -->
                        </div>
                    </div>
                </div>

                <div class="CdsDashboardMessagesGroup-create-group-model-form-group">
                    <label class="CdsDashboardMessagesGroup-create-group-model-label" for="description">
                        Enter Description <span class="CdsDashboardMessagesGroup-create-group-model-required">*</span>
                    </label>
                    <textarea 
                        id="edit_description"
                        name="description"
                        class="CdsDashboardMessagesGroup-create-group-model-textarea noval cds-texteditor" 
                        placeholder="Enter group description"
                        required
                    ></textarea>
                </div>
            </div>
            
            <div class="CdsDashboardMessagesGroup-create-group-model-members-section">
                        <h3 class="CdsDashboardMessagesGroup-create-group-model-members-header">
                            Select Members <span class="CdsDashboardMessagesGroup-create-group-model-required">*</span>
                        </h3>
                        <div class="CdsDashboardMessagesGroup-create-group-model-search-wrapper">
                            <svg class="CdsDashboardMessagesGroup-create-group-model-search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                            <input type="text" 
                                   class="CdsDashboardMessagesGroup-create-group-model-search-box" 
                                   id="searchMembersInput" 
                                   placeholder="Search Members..." 
                                   onkeyup="filterMembers()">
                        </div>
                        @if($members->count() == 0)
                        <div class="CdsDashboardMessagesGroup-create-group-model-connection-hint">
                            <p>No members available in the list. Please <a href="{{ baseUrl('connections/connect') }}" target="_blank">add connections</a> by clicking on the link.</p>
                        </div>
                        @endif
                        <div class="CdsDashboardMessagesGroup-create-group-model-member-list" id="membersList">
                            @foreach($members as $member)
                            <div class="CdsDashboardMessagesGroup-create-group-model-member-item member-item" 
                                 data-name="{{ strtolower($member->first_name.' '.$member->last_name) }}" 
                                 for="member-list-{{$member->id}}">
                                <input type="checkbox" 
                                       id="member-list-{{$member->id}}" 
                                       class="CdsDashboardMessagesGroup-create-group-model-member-checkbox members" 
                                       value="{{$member->id}}" 
                                       name="member_id[]">
                                <div class="CdsDashboardMessagesGroup-create-group-model-member-avatar">
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
                                <div class="CdsDashboardMessagesGroup-create-group-model-member-info">
                                    <p class="CdsDashboardMessagesGroup-create-group-model-member-name">{{$member->first_name." ".$member->last_name}}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
        </div>
    </div>
</form>

<link rel="stylesheet" href="{{ url('assets/css/custom-file-upload.css') }}">

<script>
    $(document).ready(function() {
        // Initialize FileUploadManager the same way as edit new group modal
        const grpUploader = new FileUploadManager('#grpMediaUpload', {
            maxFileSize: 10 * 1024 * 1024, // 10MB
            maxFiles: 5, // Maximum 5 files
            allowedTypes: [
                'image/jpeg', 
                'image/png', 
                'image/gif', 
            ],
            onFileAdded: function(fileData) {
                // FileUploadManager handles preview automatically
            },
            onFileRemoved: function(fileData) {
                // File removed successfully
            },
            onError: function(message) {
                // Custom error handling
                showNotification(message, 'error');
            }
        });
        
        // Initialize the uploader
        grpUploader.init();
        
                // FileUploadManager handles preview automatically

        // Form submission
        $("#popup-form").submit(function(e) {
            e.preventDefault();
            var is_valid = formValidation("popup-form");
            if (!is_valid) return false;

            var formData = new FormData(this);
            const files = grpUploader.getFiles();

            files.forEach((file, index) => {
                formData.append(`attachments[]`, file);
            });
            
            $.ajax({
                url: $(this).attr('action'),
                type: "post",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function() {
                    showLoader();
                    $(".CdsDashboardMessagesGroup-create-group-model-submit-btn").attr("disabled", "disabled");
                },
                success: function(response) {
                    hideLoader();
                    $(".CdsDashboardMessagesGroup-create-group-model-submit-btn").removeAttr("disabled");
                    if (response.status == true) {
                        successMessage(response.message);
                        closeCustomPopup();
                        if (response.redirect_back) {
                            window.location.href = response.redirect_back;
                        } else {
                            if (typeof loadData === 'function') {
                                loadData(1);
                            } else {
                                setTimeout(function() {
                                    window.location.reload();
                                }, 500);
                            }
                        }
                    } else {
                        if(response.error_type == 'validation'){
                            validation(response.message);
                        } else {
                            errorMessage(response.message);
                        }
                    }
                },
                error: function(xhr) {
                    hideLoader();
                    $(".CdsDashboardMessagesGroup-create-group-model-submit-btn").removeAttr("disabled");
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.error_type === 'validation') {
                        validation(xhr.responseJSON.message);
                    } else {
                        errorMessage('An unexpected error occurred. Please try again.');
                    }
                }
            });
        });
    });

    // Search functionality
    function filterMembers() {
        const searchInput = document.getElementById("searchMembersInput").value.toLowerCase();
        const membersList = document.getElementById("membersList");
        const members = membersList.getElementsByClassName("member-item");

        for (let member of members) {
            const memberName = member.getAttribute("data-name");
            if (memberName.includes(searchInput)) {
                member.style.display = "flex"; // Show matching members
            } else {
                member.style.display = "none"; // Hide non-matching members
            }
        }
    }
    
    // Group icon upload
    document.querySelector('#groupIcon').addEventListener('click', function () {
        $("#fileUploads").trigger("click");
    });
    
    function previewImage(event, val) {
        const fileInput1 = event.target; // First file input
        const fileInput2 = document.getElementById('fileInput2'); // Second file input

        if (fileInput1.files.length > 0) {
            const file = fileInput1.files[0]; // Get the selected file

            // Create a new DataTransfer object
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file); // Add the file to DataTransfer

            // Assign the file to the second file input
            fileInput2.files = dataTransfer.files;

            // File transferred successfully
        }

        const groupIcon = document.getElementById("groupIcon");
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Remove the overlay after image upload
                groupIcon.innerHTML = `<img src="${e.target.result}" alt="Group Icon" />`;
            };
            reader.readAsDataURL(file);
        }
    }

    // Update selected members count
    function updateSelectedCount() {
        const checkboxes = document.querySelectorAll('input[name="members[]"]:checked');
        const countElement = document.getElementById('selectedCount');
        countElement.textContent = checkboxes.length;
    }
</script>
@endsection

@section('custom-popup-footer')
<div class="CdsDashboardMessagesGroup-create-group-model-submit-section">
    <button type="submit" form="popup-form" class="CdsDashboardMessagesCompose-submit-btn">Create Group</button>
</div>
@endsection