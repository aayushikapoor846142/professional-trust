<!-- Group Item 1 - Active -->
@if($groups->count() > 0)
@foreach($groups->sortByDesc(function ($grp) {
    return max(strtotime($grp->created_at), strtotime($grp->last_message_date ?? '0000-00-00 00:00:00'));
}) as $group)
<div class="group-messages-group-item {{ $group_id == $group->unique_id?'active':'' }}" data-group-id="{{ $group->unique_id }}" data-group-name="{{ $group->name }}" onclick="switchToGroup('{{ $group->unique_id }}', '{{ $group->id }}')" style="cursor: pointer;">
    <div class="group-messages-group-avatar">
        <div class="group-messages-group-avatar-image">
            @if($group->group_image)
                <img src="{{ groupChatDirUrl($group->group_image, 's') }}" alt="{{ $group->name }}">
            @else
                @php            
                $initial = strtoupper(substr($group->name, 0, 1)); // Extracts the first letter and converts to uppercase
                @endphp
                <span class="group-messages-group-initial">{{ $initial }}</span>
            @endif
        </div>
        <div class="group-messages-group-status online"></div>
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
    
    <div class="group-messages-group-info">
        <div class="group-messages-group-header">
            <h3 class="group-messages-group-name">{{ substr($group->name, 0, 20) }}</h3>
            <span class="group-messages-group-time">
                @if(checkGroupPermission('can_see_messages', $group->id))
                    @if ($group->lastMessage)
                        @php
                            $timezone = getUserTimezone();
                            $checkTimezone = isValidTimezone($timezone);
                            $lastMessageTime = optional($group->lastMessage)->created_at;
                            $formattedTime = $lastMessageTime ? $lastMessageTime->format('H:i') : '';
                        @endphp
                        @if($lastMessageTime)
                            @if($checkTimezone)
                                {{ $lastMessageTime->timezone($timezone)->format('H:i') }}
                            @else
                                {{ $formattedTime }}
                            @endif
                        @endif
                    @else
                        No chat yet
                    @endif
                @endif
            </span>
        </div>
        <div class="group-messages-group-message">
            @if(checkGroupPermission('can_see_messages', $group->id))
                @if ($group->lastMessage)
                    @if ($group->lastMessage->attachment)
                        <span class="group-messages-group-sender">{{ optional($group->lastMessage->sentBy)->first_name ?? 'Unknown' }}:</span>
                        <span class="group-messages-group-text">
                            <i><i class="fa fa-paperclip"></i> Attachment is sent</i>
                            <span class="group-messages-group-text">{!! makeBoldBetweenAsterisks(substr($group->lastMessage->message, 0, 30) . "..." ) !!}</span>
                        </span>
                    @else
                        <span class="group-messages-group-sender">{{ optional($group->lastMessage->sentBy)->first_name ?? 'Unknown' }}:</span>
                        <span class="group-messages-group-text">{!! makeBoldBetweenAsterisks(substr($group->lastMessage->message, 0, 30) . "..." ) !!}</span>
                    @endif
                @else
                    <span class="group-messages-group-text">No chat yet</span>
                @endif
            @else
                <span class="group-messages-group-text">Messages hidden</span>
            @endif
        </div>
        <div class="group-messages-group-meta">
            <div class="group-messages-group-members">
                <div class="group-messages-member-avatars">
                    @if(isset($group->groupMembers) && $group->groupMembers->count() > 0)
                        @foreach($group->groupMembers->take(3) as $member)
                            <div class="group-messages-member-avatar" title="{{ $member->member->first_name ?? 'Unknown' }}">
                                @if($member->user && $member->member->profile_image)
                                    <img src="{{ getUserProfileImage($member->member->profile_image, 's') }}" alt="{{ $member->member->first_name }} {{ $member->member->last_name }}">
                                @else
                                    <span>{{ strtoupper(substr($member->member->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($member->member->last_name ?? 'U', 0, 1)) }}</span>
                                @endif
                            </div>
                        @endforeach
                        @if($group->groupMembers->count() > 3)
                            <div class="group-messages-member-count">+{{ $group->groupMembers->count() - 3 }}</div>
                        @endif
                    @else
                        <div class="group-messages-member-count">0</div>
                    @endif
                </div>
            </div>
            <div class="group-messages-group-badge">
                @if(count($group->groupRequest) > 0)
                    <span class="group-messages-request-count">{{ count($group->groupRequest) }}</span>
                @endif
                @if($group->unreadMessage($group->id, auth()->user()->id) > 0)                
                    <span class="group-messages-unread-count">{{ $group->unreadMessage($group->id, auth()->user()->id) }}</span>                
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach
@else
<div class="group-messages-welcome-chat">
    <div class="group-messages-welcome-content">
        <div class="group-messages-welcome-icon">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
        </div>
        
        <h1 class="group-messages-welcome-title">No Groups Available</h1>
        <p class="group-messages-welcome-description">
        No groups are available for messaging
        </p>
    </div>
</div>

@endif
{{-- 
<!-- Group Item 2 -->
<div class="group-messages-group-item" data-group-id="2" data-group-name="Marketing Team">
    <div class="group-messages-group-avatar">
        <div class="group-messages-group-avatar-image">
            <span class="group-messages-group-initial">M</span>
        </div>
        <div class="group-messages-group-status online"></div>
    </div>
    
    <div class="group-messages-group-info">
        <div class="group-messages-group-header">
            <h3 class="group-messages-group-name">Marketing Team</h3>
            <span class="group-messages-group-time">15m ago</span>
        </div>
        <div class="group-messages-group-message">
            <span class="group-messages-group-sender">Sarah Wilson:</span>
            <span class="group-messages-group-text">Campaign launch next week</span>
        </div>
        <div class="group-messages-group-meta">
            <div class="group-messages-group-members">
                <div class="group-messages-member-avatars">
                    <div class="group-messages-member-avatar" title="Sarah Wilson">
                        <span>SW</span>
                    </div>
                    <div class="group-messages-member-avatar" title="Tom Brown">
                        <span>TB</span>
                    </div>
                    <div class="group-messages-member-count">+4</div>
                </div>
            </div>
            <div class="group-messages-group-badge">
                <span class="group-messages-unread-count">1</span>
            </div>
        </div>
    </div>
</div>

<!-- Group Item 3 -->
<div class="group-messages-group-item" data-group-id="3" data-group-name="Support Team">
    <div class="group-messages-group-avatar">
        <div class="group-messages-group-avatar-image">
            <span class="group-messages-group-initial">S</span>
        </div>
        <div class="group-messages-group-status offline"></div>
    </div>
    
    <div class="group-messages-group-info">
        <div class="group-messages-group-header">
            <h3 class="group-messages-group-name">Support Team</h3>
            <span class="group-messages-group-time">1h ago</span>
        </div>
        <div class="group-messages-group-message">
            <span class="group-messages-group-sender">Lisa Davis:</span>
            <span class="group-messages-group-text">Ticket #1234 resolved</span>
        </div>
        <div class="group-messages-group-meta">
            <div class="group-messages-group-members">
                <div class="group-messages-member-avatars">
                    <div class="group-messages-member-avatar" title="Lisa Davis">
                        <span>LD</span>
                    </div>
                    <div class="group-messages-member-avatar" title="Alex Turner">
                        <span>AT</span>
                    </div>
                    <div class="group-messages-member-count">+3</div>
                </div>
            </div>
            <div class="group-messages-group-badge">
                <span class="group-messages-unread-count">0</span>
            </div>
        </div>
    </div>
</div>

<!-- Group Item 4 - Private Group -->
<div class="group-messages-group-item private" data-group-id="4" data-group-name="Project Alpha">
    <div class="group-messages-group-avatar">
        <div class="group-messages-group-avatar-image">
            <span class="group-messages-group-initial">A</span>
        </div>
        <div class="group-messages-group-status online"></div>
        <div class="group-messages-private-badge" title="Private Group">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <circle cx="12" cy="16" r="1"></circle>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
        </div>
    </div>
    
    <div class="group-messages-group-info">
        <div class="group-messages-group-header">
            <h3 class="group-messages-group-name">Project Alpha</h3>
            <span class="group-messages-group-time">2h ago</span>
        </div>
        <div class="group-messages-group-message">
            <span class="group-messages-group-sender">You:</span>
            <span class="group-messages-group-text">Updated the documentation</span>
        </div>
        <div class="group-messages-group-meta">
            <div class="group-messages-group-members">
                <div class="group-messages-member-avatars">
                    <div class="group-messages-member-avatar" title="You">
                        <span>You</span>
                    </div>
                    <div class="group-messages-member-avatar" title="Project Manager">
                        <span>PM</span>
                    </div>
                    <div class="group-messages-member-count">+1</div>
                </div>
            </div>
            <div class="group-messages-group-badge">
                <span class="group-messages-unread-count">0</span>
            </div>
        </div>
    </div>
</div>

<!-- Group Item 5 - Join Request -->
<div class="group-messages-group-item join-request" data-group-id="5" data-group-name="Design Team">
    <div class="group-messages-group-avatar">
        <div class="group-messages-group-avatar-image">
            <span class="group-messages-group-initial">D</span>
        </div>
        <div class="group-messages-group-status pending"></div>
    </div>
    
    <div class="group-messages-group-info">
        <div class="group-messages-group-header">
            <h3 class="group-messages-group-name">Design Team</h3>
            <span class="group-messages-group-time">Pending</span>
        </div>
        <div class="group-messages-group-message">
            <span class="group-messages-group-text">Join request sent</span>
        </div>
        <div class="group-messages-group-meta">
            <div class="group-messages-group-members">
                <div class="group-messages-member-avatars">
                    <div class="group-messages-member-avatar" title="Design Lead">
                        <span>DL</span>
                    </div>
                    <div class="group-messages-member-count">+6</div>
                </div>
            </div>
            <div class="group-messages-group-badge">
                <span class="group-messages-request-status">Pending</span>
            </div>
        </div>
    </div>
</div>

--}}
