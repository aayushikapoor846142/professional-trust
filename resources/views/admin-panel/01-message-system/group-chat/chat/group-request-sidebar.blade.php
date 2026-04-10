<!--right sidebar for group join request -->
<!-- <div class="chat-profile-sidebar" id="groupJoinSidebar"> -->
    <div class="chat-profile-card rounded">
        <div class="chat-profile-title">
            <h2>Group Join Request</h2>
        </div>
    </div>

    <!-- Accordion -->
    <div class="accordion chat-profile-accordion" id="chatProfileAccordion">
        <h3>Recent</h3>
        <div class="recent-chats">
            @if($group_requests->isNotEmpty()) 
                @foreach($group_requests as $member) 
                    @if($member->group->added_by == auth()->id())
                        <div class="chat-item chat-request" id="join-request-{{$member->unique_id}}">
                            <div class="chat-avatar">
                                @php $profileImage = $member->requester->profile_image ? userDirUrl($member->requester->profile_image, 't') : null; $initial = strtoupper(substr($member->requester->first_name, 0, 1) .
                                substr($member->requester->last_name, 0, 1)); @endphp 
                                @if($profileImage)
                                    <img src="{{ $profileImage }}" alt="Profile Picture" />
                                @else
                                    <div class="group-icon" data-initial="{{ $initial }}"></div>
                                @endif
                                <span class="status-online"></span>
                            </div>
                            <div class="chat-info ">
                                <p class="chat-name"> {{$member->requester->first_name." ".$member->requester->last_name}}</p>
                                <div class="chat-request-action-btn">
                                    <a href="javascript:;" title="Add in Group" data-action="Add Member in Group" onclick="acceptJoinRequest({{$member->unique_id}},{{ $member->group_id }})"> <i class="tio-edit"></i> Accept </a>

                                    <a href="javascript:;" title="Cancel Request" data-action="Reject Request" onclick="rejectJoinRequest({{$member->unique_id}},{{ $member->group_id }})">
                                        Decline
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif 
                @endforeach 
            @else
                <!-- Empty chat request -->
                <div class="empty-chat-request">
                    <h5>No Request Available</h5>
                </div>
            @endif
        </div>
    </div>
<!-- </div> -->