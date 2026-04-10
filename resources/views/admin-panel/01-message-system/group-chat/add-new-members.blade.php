@extends('components.custom-popup',['modalTitle'=>$pageTitle ?? 'Add New Members'])
@section('custom-popup-content')

<div class="CdsDashboardCustomPopup-modal-form-wrapper">
    <div class="CdsDashboardCustomPopup-modal-form-grid">
        <div class="CdsDashboardCustomPopup-modal-left-column">
            
            <form id="popup-form" class="js-validate member-form" action="{{ baseUrl('group/add-new-members/'.$group_id) }}" method="post">
                @csrf
                
                <div class="CdsDashboardCustomPopup-modal-form-header">
                    <h3 class="CdsDashboardCustomPopup-modal-title">Add New Members</h3>
                    <p class="CdsDashboardCustomPopup-modal-subtitle">Select members to add to your group</p>
                </div>
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">Available Members</label>
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
                        <button type="button" class="CdsDashboardCustomPopup-modal-clear-search" onclick="clearSearch()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    
                    @if($members->count() == 0)
                    <div class="CdsDashboardCustomPopup-modal-connection-hint">
                        <p>No members available to add. Please <a href="{{ baseUrl('connections/connect') }}" target="_blank">add connections</a> by clicking on the link.</p>
                    </div>
                    @else
                    <div class="CdsDashboardCustomPopup-modal-member-list" id="membersList">
                        @foreach($members as $member)
                        @if (!in_array($member->id, $group_members) )
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
                        @endif
                        @endforeach
                    </div>
                    @endif
                </div>
                
                <input type="hidden" id="member_group_id" value="{{$group_id}}" name="group_id">
            </form>
        </div>
    </div>
    
    <div class="CdsDashboardCustomPopup-modal-submit-section">
        <button type="submit" form="popup-form" class="CdsDashboardCustomPopup-modal-submit-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20">
                <path d="M5 13l4 4L19 7"></path>
            </svg>
            <span>Add Members</span>
        </button>
    </div>
</div>

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
                        closeCustomPopup();
                        // Reload the page or update the member list
                        if (response.redirect_back) {
                            redirect(response.redirect_back);
                        } else {
                            location.reload();
                        }
                    } else {
                        validation(response.message);
                    }
                },
                error: function() {
                    hideLoader();
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
                member.style.display = "flex"; // Show matching members
            } else {
                member.style.display = "none"; // Hide non-matching members
            }
        }
    }
    
    function clearSearch() {
        const searchInput = document.getElementById('searchMembersInput');
        searchInput.value = '';
        searchInput.focus();
        filterMembers(); // Reapply the filtering logic
    }
</script>

@endsection