<!-- Chat Header -->
<div class="group-messages-chat-header">
    <div class="group-messages-chat-header-left">
        <button class="group-messages-back-btn" id="backToGroups" title="Back to Groups">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>
        
        <div class="group-messages-chat-info">
            <div class="group-messages-chat-avatar">
                <div class="group-messages-chat-avatar-image">
                    @if($group->group_image)
                        <img src="{{ groupChatDirUrl($group->group_image, 't') }}" alt="{{ $group->name }}">
                    @else
                        @php
                        $initial = strtoupper(substr($group->name, 0, 1));
                        @endphp
                        <span class="group-messages-chat-initial">{{ $initial }}</span>
                    @endif
                </div>
                <div class="group-messages-chat-status online"></div>
                @if($group->type == 'Private')
                    <div class="group-messages-private-badge" title="Private Group">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <circle cx="12" cy="16" r="1"></circle>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                @endif
            </div>
            
            <div class="group-messages-chat-details">
                <h2 class="group-messages-chat-name" id="headerGroupName">
                    {{ strlen($group->name) > 20 ? substr($group->name, 0, 20) . '...' : $group->name }}
                </h2>
                <div class="group-messages-chat-meta">
                    <span class="group-messages-chat-type">{{ ucfirst($group->type ?? 'Public')  }} Group</span>
                    <span class="group-messages-chat-members">{{ $group_members->count() ?? 0 }} members</span>
                    @if(isset($group_members) && $group_members->count() > 0)
                        @php
                            $onlineMembers = $group_members->where('member.last_seen', '>', now()->subMinutes(5))->count();
                        @endphp
                        <span class="group-messages-chat-online">{{ $onlineMembers }} online</span>
                    @else
                        <span class="group-messages-chat-online">0 online</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="group-messages-chat-actions">
        <!-- <button class="group-messages-chat-action-btn" id="searchBtn" title="Search Messages">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
            </svg>
        </button> -->
        <button class="group-messages-chat-action-btn" id="searchToggleBtn" title="Search Messages">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
            </svg>
        </button>
        <button class="group-messages-chat-action-btn" id="groupInfoBtn" title="Group Information" onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('group-message/get-group-info/'.$group->unique_id) }}">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </button>
        
     
        
        @if($currentGroupMember && $currentGroupMember->is_admin == 1)
        <button class="group-messages-chat-action-btn" id="groupSettingsBtn" title="Group Settings" onclick="window.location.href='{{ baseUrl('group-settings/'.$group->unique_id) }}'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1 1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06-.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
            </svg>
        </button>
        @endif
        
        @if($currentGroupMember && $currentGroupMember->is_admin == 1)
        <button class="group-messages-chat-action-btn" id="groupRequestsBtn" title="Group Join Requests" onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('group-message/get-group-join-request/'.$group->unique_id) }}">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="8.5" cy="7" r="4"></circle>
                <line x1="20" y1="8" x2="20" y2="14"></line>
                <line x1="23" y1="11" x2="17" y2="11"></line>
            </svg>
            @if(groupJoinRequestCount($group->unique_id) > 0)
                <span class="group-messages-request-count">{{ groupJoinRequestCount($group->unique_id) }}</span>
            @endif
        </button>
        @endif
        
        <button class="group-messages-chat-action-btn" id="groupFilesBtn" title="Group Files" onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('group-message/get-shared-file/'.$group->unique_id) }}">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14,2 14,8 20,8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10,9 9,9 8,9"></polyline>
            </svg>
        </button>
        
        @if((checkGroupPermission('members_can_add_members', $group->unique_id)) || ($currentGroupMember && $currentGroupMember->is_admin == 1))
        <button class="group-messages-chat-action-btn" id="addMemberBtn" title="Add New Member" onclick="openCustomPopup(this)" data-href="{{ baseUrl('group-message/add-new-members/' . $group->unique_id) }}">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="8.5" cy="7" r="4"></circle>
                <line x1="20" y1="8" x2="20" y2="14"></line>
                <line x1="23" y1="11" x2="17" y2="11"></line>
            </svg>
        </button>
        @endif
    </div>
</div>

<!-- Chat Search Bar -->
<!-- <div class="group-messages-chat-search-bar" id="chatSearchBar">
    <div class="group-messages-chat-search-input-wrapper">
        <svg class="group-messages-chat-search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.35-4.35"></path>
        </svg>
        <input type="text" class="group-messages-chat-search-input" placeholder="Search in this conversation..." id="chatSearchInput">
        <button class="group-messages-chat-search-close" id="closeChatSearch">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
</div> -->