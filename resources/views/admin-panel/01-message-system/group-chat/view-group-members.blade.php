<div class="modal-dialog  modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{$pageTitle}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="popup-form" class="js-validate member-form" action="{{ baseUrl('group/add-new-members/'.$group_id) }}" method="post">
                @csrf
                <div class="group-members">
                    <div class="group-add-more-members" id="membersList">
                        @foreach($group_members as $member)
                        @if($member->member)
                        <div class="w-100 @if($member->deleted_at){{'chat-disabled'}}@endif "
                            for="member-list-{{$member->member->id}}">
                            <div class="chat-item chat-request group-item">
                                <div class="chat-avatar">

                                    @if($member->member->profile_image)
                                    <img src="{{ userDirUrl($member->member->profile_image, 'm') }}"
                                        alt="{{ $member->member->first_name }} {{ $member->member->last_name }}">
                                    @else
                                    @php
                                    $initial = strtoupper(substr($member->member->first_name, 0, 1)) .
                                    strtoupper(substr($member->member->last_name, 0, 1));
                                    @endphp
                                    <div class="group-icon" data-initial="{{ $initial }}">
                                    </div>
                                    @endif

                                    @if($member->member->is_login)
                                    <span class="status-online"></span>
                                    @else
                                    <span class="status-offline"></span>
                                    @endif

                                </div>

                                @if($member->member->id==auth()->user()->id )
                                <div class="chat-info">
                                    <p class="chat-name">You</p>
                                    @if($currentGroupMember->is_admin==1)
                                    <span class="group-admin">Group Admin</span>
                                    @endif
                                </div>
                                @else
                                <div class="chat-info group-member-name">
                                    <p class="chat-name">
                                        {{$member->member->first_name." ".$member->member->last_name}}</p>
                                    @if($member->is_admin==1)
                                    <span class="group-admin">Group Admin</span>
                                    @endif
                                </div>
                                @if($currentGroupMember->is_admin==1)
                                <a href="javascript:;" onclick="confirmAction(this)"
                                    data-href="{{ baseUrl('group/remove-group-member/'.$member->id) }}">
                                    <i class="fa-regular fa-trash text-danger"></i>
                                </a>

                                @if($member->is_admin!=1)
                                <a data-href="{{ baseUrl('group/make-group-admin/'.$member->unique_id) }}"
                                    onclick="confirmAnyAction(this)" title="Mark as Admin"
                                    data-action="Make Group Admin">
                                    <i class="fa-regular fa-user text-primary"></i>
                                </a>
                                @elseif($member->is_admin == 1)
                                <a data-href="{{ baseUrl('group/remove-group-admin/'.$member->unique_id) }}"
                                    onclick="confirmAnyAction(this)" title="Remove as Admin"
                                    data-action="remove from Group Admin">
                                    <i class="fa-regular fa-user-times text-danger"></i>
                                </a>
                                @endif
                                @endif
                                @endif
                            </div>
                        </div>
                       
                        @endif
                        @endforeach
                    </div>

                </div>
                <input type="hidden" id="member_group_id" value="{{$group_id}}" name="group_id">
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#popup-form").submit(function(e) {
            e.preventDefault();
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
                        validation(response.message);
                    }
                },
                error: function() {
                    internalError();
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