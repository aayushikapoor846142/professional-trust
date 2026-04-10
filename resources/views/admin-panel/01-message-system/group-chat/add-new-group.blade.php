@extends('components.custom-popup',['modalTitle'=>$pageTitle ?? 'Create New Group'])
@section('custom-popup-content')

<div class="CdsDashboardCustomPopup-modal-form-wrapper">
    <div class="CdsDashboardCustomPopup-modal-form-grid">
        <div class="CdsDashboardCustomPopup-modal-left-column">
            
            <form id="popup-form" enctype="multipart/form-data" action="{{ baseUrl('group/create-group') }}" method="post">
                @csrf
                
                <div class="CdsDashboardCustomPopup-modal-form-header">
                    <h3 class="CdsDashboardCustomPopup-modal-title">Create New Group</h3>
                    <p class="CdsDashboardCustomPopup-modal-subtitle">Set up your group with basic information and select members</p>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-icon-upload-section">
                    <div class="CdsDashboardCustomPopup-modal-icon-circle" id="groupIcon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2196f3" stroke-width="2">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                    </div>
                    <div class="CdsDashboardCustomPopup-modal-icon-info">
                        <h3>Group Icon</h3>
                        <p>Click to upload</p>
                    </div>
                    <!-- Hidden File Upload for Group Icon -->
                    <input type="file" id="fileUploads" accept="image/*" style="display: none;" onchange="previewImage(event,this.value)" />
                    <div class="d-none">
                        <input type="file" id="fileInput2" name="group_image">
                    </div>
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
                                   {{ $loop->first ? 'checked' : '' }}
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
                    </div>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label" for="description">
                        Description <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <textarea 
                        id="edit_description"
                        name="description"
                        class="CdsDashboardCustomPopup-modal-textarea" 
                        placeholder="Enter group description"
                        required
                    ></textarea>
                </div>
            </form>
        </div>
        
        <div class="CdsDashboardCustomPopup-modal-right-column">
            <div class="CdsDashboardCustomPopup-modal-members-section">
                <h3 class="CdsDashboardCustomPopup-modal-members-header">
                    Select Members <span class="CdsDashboardCustomPopup-modal-required">*</span>
                </h3>
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
                

                
                @if($members->count() == 0)
                <div class="CdsDashboardCustomPopup-modal-connection-hint">
                    <p>No members available in the list. Please <a href="{{ baseUrl('connections/connect') }}" target="_blank">add connections</a> by clicking on the link.</p>
                </div>
                @else
                <div class="CdsDashboardCustomPopup-modal-member-list" id="membersList">
                    @foreach($members as $member)
                    <div class="CdsDashboardCustomPopup-modal-member-item member-item" 
                         data-name="{{ strtolower($member->first_name.' '.$member->last_name) }}" 
                         for="member-list-{{$member->id}}">
                        <input type="checkbox" 
                               id="member-list-{{$member->id}}" 
                               class="CdsDashboardCustomPopup-modal-member-checkbox members" 
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
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="CdsDashboardCustomPopup-modal-submit-section">
        <button type="submit" form="popup-form" class="CdsDashboardCustomPopup-modal-submit-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20">
                <path d="M5 13l4 4L19 7"></path>
            </svg>
            <span>Create Group</span>
        </button>
    </div>
</div>

<script src="{{url('assets/js/custom-file-upload.js')}}"></script>
<link rel="stylesheet" href="{{ url('assets/css/custom-file-upload.css') }}">

<script>
    $(document).ready(function() {
        // Add interactivity for member selection
        document.querySelectorAll('.CdsDashboardCustomPopup-modal-member-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const memberItem = this.closest('.CdsDashboardCustomPopup-modal-member-item');
                if (this.checked) {
                    memberItem.classList.add('CdsDashboardCustomPopup-modal-selected');
                } else {
                    memberItem.classList.remove('CdsDashboardCustomPopup-modal-selected');
                }
            });
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
        
        $("#popup-form").submit(function(e) {
            e.preventDefault();
            var is_valid = formValidation("popup-form");
            if (!is_valid) {
                return false;
            }
            $(".CdsDashboardCustomPopup-modal-members-section .errmsg").remove();
            if($(".members:checked").length == 0){
                $(".CdsDashboardCustomPopup-modal-members-section").append("<div class='text-danger errmsg'>Add atleast one member to group</div>");
                return false;
            }
            var formData = new FormData($(this)[0]);
            const files = grpUploader.getFiles();

            files.forEach((file, index) => {
                formData.append(`banner_image`, file);
            });
            var url = $("#popup-form").attr('action');
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
                        redirect(response.redirect_back);
                    } else {
                        validation(response.message);
                    }
                },
                error: function(xhr) {
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

            console.log('File transferred successfully!');
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
</script>
@endsection