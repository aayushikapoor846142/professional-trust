@extends('components.custom-popup', ['modalTitle' => $pageTitle ?? 'Add New Members'])

@section('custom-popup-content')
<link href="{{ url('assets/css/18CDS-add-new-group-modal.css') }}" rel="stylesheet" />

<form id="popup-form" class="js-validate member-form" action="{{ baseUrl('group-message/add-new-members/'.$group_id) }}" method="post">
    @csrf
    
    <!-- Validation Message Container -->
    <div id="validation-messages" class="validation-messages-container" style="display: none;">
        <div class="alert alert-danger" role="alert">
            <ul id="validation-errors-list" class="mb-0"></ul>
        </div>
    </div>
    
    <div class="group-members">
        <h3>Select Members</h3>
        <div class="group-search">
            <a href="javascript:;" class="search-icon"><i class="fa-sharp fa-regular fa-magnifying-glass"></i></a>
            <input type="text" placeholder="Search Members.." id="searchMembersInput" onkeyup="filterMembers()" />
            <a href="javascript:;" class="clear-text"><i class="fa-times fa-regular fa-magnifying-glass"></i></a>
        </div>
        <div class="group-add-more-members" id="membersList">
            @php 
                $member_exists = false;
            @endphp
            @foreach($members as $member)
            @if (!in_array($member->id, $group_members) )
            @php 
                $member_exists = true;
            @endphp
            <div class="w-100 member-item" data-name="{{ strtolower($member->first_name.' '.$member->last_name) }}">
                <div class="chat-item chat-request group-item">
                    <div class="d-flex align-items-center">
                        <div class="js-form-message">
                            <input id="member-list-{{$member->id}}" class="members member-checkbox" type="checkbox" value="{{$member->id}}" name="member_id[]" />
                        </div>
                        <label class="d-flex align-items-center ms-2" for="member-list-{{$member->id}}">
                            <div class="chat-avatar ms-2">
                                @php
                                    $profileImage = $member->profile_image ? userDirUrl($member->profile_image, 't') : null;
                                    $initial = strtoupper(substr($member->first_name, 0, 1) . substr($member->last_name, 0, 1));
                                @endphp
                            
                                @if($profileImage)
                                    <img src="{{ $profileImage }}" alt="Profile Picture">
                                @else
                                    <div class="group-icon" data-initial="{{ $initial }}"></div>
                                @endif
                            </div>
                            <div class="chat-info">
                                <p class="chat-name">{{$member->first_name." ".$member->last_name}}</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
        @if(!$member_exists)
        <div class="w-100 member-item">
            <p class="text-center">No members found</p>
        </div>
        @endif  
    </div>
    <input type="hidden" id="member_group_id" value="{{$group_id}}" name="group_id">
</form>

<style>
.member-checkbox {
    width: 18px !important;
    height: 18px !important;
    margin: 0 !important;
    cursor: pointer !important;
    opacity: 1 !important;
    position: relative !important;
    z-index: 1 !important;
}

.member-checkbox:checked {
    background-color: #007bff !important;
    border-color: #007bff !important;
}

.member-checkbox:focus {
    outline: 2px solid #007bff !important;
    outline-offset: 2px !important;
}

label[for^="member-list-"] {
    cursor: pointer !important;
    flex: 1 !important;
}

.member-item {
    cursor: pointer !important;
}

.validation-messages-container {
    margin-bottom: 20px;
}

.validation-messages-container .alert {
    border-radius: 8px;
    border: 1px solid #dc3545;
    background-color: #f8d7da;
    color: #721c24;
    padding: 12px 16px;
}

.validation-messages-container .alert ul {
    list-style: none;
    padding-left: 0;
}

.validation-messages-container .alert li {
    margin-bottom: 5px;
}

.validation-messages-container .alert li:last-child {
    margin-bottom: 0;
}
</style>

<script>
    $(document).ready(function() {
        // Ensure checkboxes work properly
        $(document).on('change', '.member-checkbox', function() {
            console.log('Checkbox changed:', this.id, this.checked);
        });
        
        // Add click handler for better compatibility
        $(document).on('click', '.member-checkbox', function(e) {
            e.stopPropagation();
            console.log('Checkbox clicked:', this.id);
        });
        
        // Ensure label clicks work
        $(document).on('click', 'label[for^="member-list-"]', function(e) {
            const checkboxId = $(this).attr('for');
            const checkbox = $('#' + checkboxId);
            if (checkbox.length) {
                checkbox.prop('checked', !checkbox.prop('checked'));
                console.log('Label clicked, checkbox toggled:', checkboxId, checkbox.prop('checked'));
            }
        });

        $("#popup-form").submit(function(e) {
            e.preventDefault();
            
            // Clear previous validation messages
            clearValidationMessages();
            
            var is_valid = formValidation("popup-form");
            if (!is_valid) {
                return false;
            }
            
            var formData = new FormData($(this)[0]);
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
                        showValidationMessages(response.message);
                    }
                },
                error: function(xhr) {
                    hideLoader();
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        showValidationMessages(xhr.responseJSON.message);
                    } else {
                        showValidationMessages("An error occurred. Please try again.");
                    }
                }
            });
        });
    });
    
    // Function to clear validation messages
    function clearValidationMessages() {
        $("#validation-messages").hide();
        $("#validation-errors-list").empty();
        $(".invalid-feedback").remove();
        $(".form-control").removeClass("is-invalid");
        $(".member-checkbox").removeClass("is-invalid");
    }
    
    // Function to show validation messages
    function showValidationMessages(message) {
        if (typeof message === 'object') {
            // If message is an object (validation errors), display them
            var errorsList = $("#validation-errors-list");
            errorsList.empty();
            
            $.each(message, function(field, errorMsg) {
                errorsList.append('<li>' + errorMsg + '</li>');
                
                // Also show field-specific errors
                var fieldElement = $('[name="' + field + '"]');
                if (fieldElement.length) {
                    fieldElement.addClass('is-invalid');
                    fieldElement.parents('.js-form-message').append(
                        '<div class="invalid-feedback d-block">' + errorMsg + '</div>'
                    );
                }
            });
            
            $("#validation-messages").show();
        } else {
            // If message is a string, show it as a general error
            $("#validation-errors-list").html('<li>' + message + '</li>');
            $("#validation-messages").show();
        }
    }
    
    function filterMembers() {
        const searchInput = document.getElementById("searchMembersInput").value.toLowerCase();
        const membersList = document.getElementById("membersList");
        const members = membersList.getElementsByClassName("member-item");

        for (let member of members) {
            const memberName = member.getAttribute("data-name");
            if (memberName.includes(searchInput)) {
                member.style.display = "block"; // Show matching members
            } else {
                member.style.display = "none"; // Hide non-matching members
            }
        }
    }
    
    document.querySelector('.clear-text').addEventListener('click', function () {
        const searchInput = document.getElementById('searchMembersInput');
        searchInput.value = '';
        searchInput.focus();
        filterMembers(); // Reapply the filtering logic
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
        $('#getval').val(file.name);
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
@section('custom-popup-footer')
<div class="CdsDashboardMessagesGroup-create-group-model-submit-section">
    @if($member_exists)
    <button type="submit" form="popup-form" class="CdsDashboardMessagesCompose-submit-btn" id="sendBtnnew">Submit</button>
    @endif
</div>
@endsection