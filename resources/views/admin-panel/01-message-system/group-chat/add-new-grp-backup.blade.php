 <div class="modal-dialog modal-dialog-centered modal-xl"> <!-- Optional: use fullscreen on small devices -->
    <div class="modal-content">
          <form id="popup-form" enctype="multipart/form-data" action="{{ baseUrl('group/create-group') }}" method="post">
        <div class="modal-header">
            <h5 class="modal-title">{{$pageTitle}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          
                @csrf
				<div class="cdsTYDashboard-chat-group-section flex-column flex-lg-row">
                    <div class="cdsTYDashboard-chat-group-section-main-panel">
                <div class="chatgroup-name-block">
                    <div class="form-group mb-3 text-center">
                        <label for="group_image" class="fw-semibold input-label mb-2">Group Icon</label>
                        <div class="create-group-icon" id="groupIcon">
                            <div class="icon-overlay">
                                <span class="icon-button">Add Icon</span>
                            </div>
                        </div>
                        <!-- Hidden File Upload for Group Icon -->
                        <input type="file" id="fileUploads" accept="image/*" style="display: none;" onchange="previewImage(event,this.value)" />
                        <div class="d-none">
                            <input type="file" id="fileInput2" name="group_image">
                        </div>
                    </div>
                    {{-- <div class="create-group-icon" id="groupIcon">
                        <div class="icon-overlay">
                            <span class="icon-button">Add Icon</span>
                        </div>
                    </div>
                    <input type="file" id="fileUploads" accept="image/*" style="display: none;" onchange="previewImage(event,this.value)" />
                    <div class="d-none">
                        <input type="file" id="fileInput2" name="group_image">
                    </div> --}}
                    <div>
                        {!! FormHelper::formInputText(['name'=>"name",
                        "label"=>" Enter Group Name",
                        "required"=>true, "input_class"=>"group-input",
                  
                        ]) !!}
                    </div>
                </div>


                <div class="create-group-type mt-3">
                    <label for="group_icon" class="col-form-label input-label">Category *</label>
                    {!! FormHelper::formRadio([
                    'name' => 'group_type',
                    'required' => true,
                    'options' => FormHelper::groupType(),
                    'value_column' => 'value',
                    'label_column' => 'label',
                    'id' => 'group-type',
                    'value' => 'label'
                    ]) !!}
                </div>
                <div class="col-md-12">
                    <div class="js-form-message square-bannerdiv">
                        <label for="banner_image" class="col-form-label input-label">Banner Image </label>
                        <div class="create-group-icon mt-0" id="bannerImage">
                            <div class="icon-overlay">
                                <span class="icon-button">Add Banner Image</span>
                            </div>
                        </div>
                        <!-- Hidden File Upload for Banner -->
                        <input type="file" id="bannerUploads" accept="image/*" style="display: none;" onchange="previewBannerImage(event,this.value)" />
                        <div class="d-none">
                            <input type="file" id="fileInput3" name="banner_image">
                        </div>
                    </div>

                    {{-- <div>
                        <div class="create-group-icon" id="bannerImage">
                            <div class="icon-overlay">
                                <span class="icon-button">Add Banner Image</span>
                            </div>
                        </div>
                        <input type="file" id="bannerUploads" accept="image/*" style="display: none;" onchange="previewBannerImage(event,this.value)" />
                        <div class="d-none">
                            <input type="file" id="fileInput3" name="banner_image">
                        </div>
                           </div> --}}
                        </div>
                 <div class="col-md-12">                    
                    {!! FormHelper::formTextarea([
                        'name'=>"description",
                        'id'=>"edit_description",
                        "label"=>"Enter Description",
                        'required'=>true,
                        'textarea_class'=>"noval cds-texteditor",
                        'class' => 'select2-input ga-country',
                   
                    ]) !!}
                </div> </div> <div class="cdsTYDashboard-chat-group-section-sidebar-panel">  
                <div class="group-members">
                    <label for="banner_image" class="col-form-label input-label">Select Members *</label>
                    <div class="chat-header">
                        <div class="group-search">
                            <a href="javascript:;" class="search-icon"><i class="fa-sharp fa-regular fa-magnifying-glass"></i></a>
                            <input type="text" placeholder="Search Members.." id="searchMembersInput" onkeyup="filterMembers()" />
                            <a href="javascript:;" class="clear-text"><i class="fa-times fa-regular fa-magnifying-glass"></i></a>
                        </div>
                    </div>
                    <div class="group-add-members-list" id="membersList">
                        @foreach($members as $member)
                            <div class="w-100 member-item" data-name="{{ strtolower($member->first_name." ".$member->last_name) }}" for="member-list-{{$member->id}}">
                                <div class="chat-item chat-request group-item">
                                    <input id="member-list-{{$member->id}}" class="members" type="checkbox" value="{{$member->id}}" name="member_id[]" />
                                    <div class="chat-avatar ms-2">
                                        @if($member->profile_image)
                                        <img src="{{ userDirUrl($member->profile_image, 'm') }}" alt="{{ $member->first_name }} {{ $member->last_name }}">
                                    @else
                                        @php
                                            $initial = strtoupper(substr($member->first_name, 0, 1)) . strtoupper(substr($member->last_name, 0, 1));
                                        @endphp
                                        <div class="group-icon" data-initial="{{ $initial }}">
                                        </div>
                                    @endif
                                    @if($member->is_login)
                                        <span class="status-online"></span>
                                    @else
                                        <span class="status-offline"></span>
                                    @endif
                                    </div>
                                    <div class="chat-info">
                                        <p class="chat-name">{{$member->first_name." ".$member->last_name}}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                 </div> </div>
           
        </div><div class="modal-footer"><div class="form-group text-start">
                    <button type="submit" class="btn add-CdsTYButton-btn-primary">Create Group</button>
                </div></div> </form>
    </div>
</div>
<script>
 function adjustBackgroundWidth() {
    const container = document.querySelector('.cdsTYDashboard-chat-group-section');
    const modalBody = document.querySelector('.full-width-modal-content .modal-body');

    if (container && modalBody) {
        const modalWidth = modalBody.clientWidth;
        const containerWidth = container.clientWidth;
        const gap = 40;

        const sideSpace = (modalWidth - containerWidth) / 2;  // Space on right side of container
        const sidebarWidth = containerWidth * 0.4;            // Sidebar = 40% of container

        const totalBgWidth = sideSpace + sidebarWidth + (gap / 2);

        modalBody.style.setProperty('--dynamic-bg-width', `${totalBgWidth}px`);
    }
}

window.addEventListener('load', adjustBackgroundWidth);
window.addEventListener('resize', adjustBackgroundWidth);

</script>
<script>
    $(document).ready(function() {
       

        $("#popup-form").submit(function(e) {
            e.preventDefault();
            var is_valid = formValidation("popup-form");
            if (!is_valid) {
                return false;
            }
            $(".group-members .errmsg").remove();
            if($(".members:checked").length == 0){
                $(".group-members").append("<div class='text-danger errmsg'>Add atleast one member to group</div>");
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
    document.querySelector('#groupIcon').addEventListener('click', function () {
        $("#fileUploads").trigger("click");
    });
    
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

    document.querySelector('#bannerImage').addEventListener('click', function () {
    $("#bannerUploads").trigger("click");
});

function previewBannerImage(event, val) {
        const fileInput1 = event.target; // First file input
        const fileInput3 = document.getElementById('fileInput3'); // Second file input

        if (fileInput1.files.length > 0) {
            const file = fileInput1.files[0]; // Get the selected file

            // Create a new DataTransfer object
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file); // Add the file to DataTransfer

            // Assign the file to the second file input
            fileInput3.files = dataTransfer.files;

            console.log('File transferred successfully!');
        }

        const bannerImage = document.getElementById("bannerImage");
        const file = event.target.files[0];
        $('#getval').val(file.name);
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Remove the overlay after image upload
                bannerImage.innerHTML = `<img src="${e.target.result}" alt="Banner Image" />`;
            };
            reader.readAsDataURL(file);
        }
    }
</script>