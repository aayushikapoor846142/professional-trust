<div class="chat-profile-card rounded">
            <div class="chat-profile-title">
                <h2>Group Info</h2>
            </div>
            <!-- Group Profile Header -->
            <div class="chat-profile-header text-center p-4 group-info group-sidebar-heading" id="group-name-edit">
                <div class="group-info-icon">
                    <a class="message-upload-file" href="javascript:;" onclick="">
                        @if($group->group_image)
                        <img class="chat-profile-picture" src="{{ groupChatDirUrl($group->group_image, 't') }}"
                            alt="{{$group->name}}">
                        @else
                        @php
                        $initial = strtoupper(substr($group->name, 0, 1));
                        @endphp
                        <div class="group-icon" data-initial="{{$initial}}"></div>
                        @endif
                    </a>
                    {{-- <input type="file"  name ="group_update_image" id="fileUpdates" accept="image/*" style="display: none;" />
                    <input class="chat-profile-name" type="text" style="display:none" id="editGroupName"
                        value="{{$group->name}}" maxlength="50"> --}}

                    <h2 class="chat-profile-name" id="groupName">{{$group->name}}</h2>

                </div>

                <div class="cds-rightGrpInfo">
                    <div class="group-info-actionbtn">
                        @if($currentGroupMember->is_admin==1)
                        <a href="javascript:;" onclick="confirmAction(this)" data-href="{{ baseUrl('group/delete-group/'.$group->unique_id) }}">
                            <i class="fa-regular fa-trash text-danger"></i>
                        </a>
                        @endif
                    </div>
                    <div class="group-chat-title">
                        @if($currentGroupMember->is_admin==1)

                        <a class="message-upload-file" href="javascript:;" onclick="openCustomPopup(this)" data-href="<?php echo baseUrl('group/edit-new-group/' . $group->unique_id) ?>">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endif
                    </div>
                  
                </div>
            </div>
            <div class="group-description">
                <p class="">{{Str::limit( $group->description ?? '',30)}}</p>
            </div>
            <!-- Accordion -->
            <div class="accordion chat-profile-accordion" id="chatProfileAccordion">
                <!-- Attached Files Section -->
                <div class="group-share-link">
                    @if($group->type == "public")
                    <span id="groupLinkCopy{{$group->unique_id}}" readonly class="form-control">{{url('g/join/').'/'.hash('sha256',$group->unique_id)}} </span>
                    <button type="button" onclick="groupCopyMessage('groupLinkCopy{{$group->unique_id}}')" class="btn btn-success">
                        <i class="fa-regular fa-copy"></i>
                    </button>
                    @endif
                </div>
                <div class="accordion-item" style="display: block;">
                    <h2 class="accordion-header" id="filesHeader">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#filesContent" aria-expanded="false" aria-controls="filesContent">
                            Members
                        </button>
                    </h2>
                    <div id="filesContent" class="accordion-collapse collapse show" aria-labelledby="filesHeader" data-bs-parent="#chatProfileAccordion">
                        <div class="accordion-body p-0">
                            @foreach($group_members as $member) @if($member->member)
                            <div class="w-100 @if($member->deleted_at){{'chat-disabled'}}@endif " for="member-list-{{$member->member->id}}">
                                <div class="chat-item chat-request group-item">
                                    <div class="chat-avatar">
                                        @if($member->member->profile_image)
                                        <img src="{{ userDirUrl($member->member->profile_image, 'm') }}" alt="{{ $member->member->first_name }} {{ $member->member->last_name }}" />
                                        @else @php $initial = strtoupper(substr($member->member->first_name, 0, 1)) . strtoupper(substr($member->member->last_name, 0, 1)); @endphp
                                        <div class="group-icon" data-initial="{{ $initial }}"></div>
                                        @endif @if($member->member->is_login)
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
                                            {{$member->member->first_name." ".$member->member->last_name}}
                                        </p>
                                        @if($member->is_admin==1)
                                        <span class="group-admin">Group Admin</span>
                                        @endif
                                    </div>
                                    @if($currentGroupMember->is_admin==1)
                                    <a href="javascript:;" onclick="confirmAction(this)" data-href="{{ baseUrl('group/remove-group-member/'.$member->id) }}">
                                        <i class="fa-regular fa-trash text-danger"></i>
                                    </a>

                                    @if($member->is_admin!=1)
                                    <a data-href="{{ baseUrl('group/make-group-admin/'.$member->unique_id) }}" onclick="confirmAnyAction(this)" title="Mark as Admin" data-action="Make Group Admin">
                                        <i class="fa-regular fa-user text-primary"></i>
                                    </a>
                                    @elseif($member->is_admin == 1)
                                    <a data-href="{{ baseUrl('group/remove-group-admin/'.$member->unique_id) }}" onclick="confirmAnyAction(this)" title="Remove as Admin" data-action="remove from Group Admin">
                                        <i class="fa-regular fa-user-times text-danger"></i>
                                    </a>
                                    @endif @endif @endif
                                </div>
                            </div>
                            @if($member->member->id==auth()->user()->id)
                            <div class="w-100">
                                <div class="chat-item cds-leave-group">
                                    <a href="javascript:;" title="Leave Group" data-action="leave this Group" onclick="checkRemoveadmin(this)" data-href="{{ baseUrl('group/remove-group-member/'.$member->id) }}">
                                        Leave Group
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                    </a>
                                </div>
                            </div>

                        @endif
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>