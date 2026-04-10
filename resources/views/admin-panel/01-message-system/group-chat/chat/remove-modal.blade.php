<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{$pageTitle}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <b>Before you leaving make one of the existing member as admin</b>
            <div class="cds-form-group address2-div">
            @foreach($groupMembers as $member)
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
                        

                        @if($member->is_admin!=1)
                        <a data-href="{{ baseUrl('group/mark-group-admin/'.$member->unique_id.'/'.$currentGroupMember->unique_id) }}"
                            onclick="markAsAdmin(this)" title="Mark as Admin"
                            data-action="Make Group Admin">Mark as admin
                            <i class="fa-regular fa-user text-primary"></i>
                        </a>
                        
                        @endif
                        @endif
                    </div>
                </div>
                @endif
            @endforeach
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary close-upload-modal cdsTYDashboard-modal-cancel-btn" data-bs-dismiss="modal">Close</button>
        </div>

    </div>
</div>
  
