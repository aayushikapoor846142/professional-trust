    <div class="cdsTYDashboard-integrated-container-component" data-container-id="1">
        <div class="cdsTYDashboard-integrated-container-header">
            <div class="cdsTYDashboard-integrated-header-left">
                <h1 class="cdsTYDashboard-integrated-container-title">Message Overview</h1>
            </div>
            <div class="cdsTYDashboard-integrated-header-controls">
                <button class="cdsTYDashboard-integrated-sidebar-toggle" aria-label="Toggle Sidebar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </button>
                <button class="cdsTYDashboard-integrated-minimize-btn" aria-label="Minimize Container">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 15l7-7 7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    

<section id="overview" class="cdsTYDashboard-integrated-section-header">
                        <!-- <h2>Message Overview</h2> -->
                        <p>Welcome to your minimalist dashboard. You can collapse the sidebar using the arrow button for more screen space, or minimize entire containers using the chevron button in the header.</p>
                        
                        <!-- Summary Cards -->
                        <div class="cdsTYDashboard-main-summary-cards">
                            <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('cases')">
                                <div class="cdsTYDashboard-main-card-header">
                                    <div class="cdsTYDashboard-main-card-info">
                                        <div class="cdsTYDashboard-main-card-label">Total Chats</div>
                                        <div class="cdsTYDashboard-main-card-value" data-count="{{ $totalChats ?? 0 }}">{{ $totalChats ?? 0 }}</div>
                                    </div>
                                    <div class="cdsTYDashboard-main-card-icon" style="background: rgba(94, 114, 228, 0.1); color: #5e72e4;">
                                        📁
                                    </div>
                                </div>
                                {{--<div class="cdsTYDashboard-main-card-footer">
                                    <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                                        <span>↑</span>
                                        <span>12%</span>
                                    </div>
                                    <span style="color: #6b7280;">vs last month</span>
                                </div>--}}
                            </div>

                            <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('appointments')">
                                <div class="cdsTYDashboard-main-card-header">
                                    <div class="cdsTYDashboard-main-card-info">
                                        <div class="cdsTYDashboard-main-card-label">Unread Messages</div>
                                        <div class="cdsTYDashboard-main-card-value" data-count="{{ $unreadMessages ?? 0 }}">{{ $unreadMessages ?? 0 }}</div>
                                    </div>
                                    <div class="cdsTYDashboard-main-card-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                        📅
                                    </div>
                                </div>
                                {{--<div class="cdsTYDashboard-main-card-footer">
                                    @if(countCase('open') > 0)
                                        <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                                            <span>↑</span>
                                            <span>{{ countCase('open') ?? [] }}</span>
                                        </div>
                                        <span style="color: #6b7280;">upcoming</span>
                                    @else
                                        <span style="color: #6b7280;">No upcoming active cases</span>
                                    @endif
                                </div>--}}
                            </div>

                            <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('messages')">
                                <div class="cdsTYDashboard-main-card-header">
                                    <div class="cdsTYDashboard-main-card-info">
                                        <div class="cdsTYDashboard-main-card-label">Group Chats</div>
                                        <div class="cdsTYDashboard-main-card-value" data-count="{{ $totalGroupChats ?? 0 }}">{{ $totalGroupChats ?? 0 }}</div>
                                    </div>
                                    <div class="cdsTYDashboard-main-card-icon" style="background: rgba(251, 146, 60, 0.1); color: #fb923c;">
                                        💌
                                    </div>
                                </div>
                                {{--<div class="cdsTYDashboard-main-card-footer">
                                    @if(($unreadMessages ?? 0) > 0)
                                        <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                                            <span>↑</span>
                                            <span>{{ $unreadMessages ?? 0 }}</span>
                                        </div>
                                        <span style="color: #6b7280;">unread</span>
                                    @else
                                        <span style="color: #6b7280;">All caught up!</span>
                                    @endif
                                </div>--}}
                            </div>

                            <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('invoices')">
                                <div class="cdsTYDashboard-main-card-header">
                                    <div class="cdsTYDashboard-main-card-info">
                                        <div class="cdsTYDashboard-main-card-label">Invitations</div>
                                        <div class="cdsTYDashboard-main-card-value" data-count="{{ $invitations ?? 0 }}">{{ $invitations ?? 0 }}</div>
                                    </div>
                                    <div class="cdsTYDashboard-main-card-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                        💳
                                    </div>
                                </div>
                                {{--<div class="cdsTYDashboard-main-card-footer">
                                    @if(($pendingInvoices ?? 0) > 0)
                                        <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                                            <span>↑</span>
                                            <span>{{ $pendingInvoices ?? 0 }}</span>
                                        </div>
                                        <span style="color: #6b7280;">pending</span>
                                    @else
                                        <span style="color: #6b7280;">All invoices cleared</span>
                                    @endif
                                </div>--}}
                            </div>
                            <div class="cdsTYDashboard-main-summary-card" onclick="handleCardClick('invoices')">
                                <div class="cdsTYDashboard-main-card-header">
                                    <div class="cdsTYDashboard-main-card-info">
                                        <div class="cdsTYDashboard-main-card-label">Notifications</div>
                                        <div class="cdsTYDashboard-main-card-value" data-count="{{ $notifications ?? 0 }}">{{ $notifications ?? 0 }}</div>
                                    </div>
                                    <div class="cdsTYDashboard-main-card-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                        💳
                                    </div>
                                </div>
                                {{--<div class="cdsTYDashboard-main-card-footer">
                                    @if(($pendingInvoices ?? 0) > 0)
                                        <div class="cdsTYDashboard-main-trend-indicator cdsTYDashboard-main-trend-up">
                                            <span>↑</span>
                                            <span>{{ $pendingInvoices ?? 0 }}</span>
                                        </div>
                                        <span style="color: #6b7280;">pending</span>
                                    @else
                                        <span style="color: #6b7280;">All invoices cleared</span>
                                    @endif
                                </div>--}}
                            </div>
                        </div>

                    </section>                       
                    <div class="CdsDashboardChat-container">
                        <!-- Recent Chats Panel -->
                        <div class="CdsDashboardChat-panel">
                            <div class="CdsDashboardChat-header">
                                <div class="CdsDashboardChat-title-section">
                                    <div class="CdsDashboardChat-icon chat">
                                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="CdsDashboardChat-title">Recent Chats</div>
                                        <div class="CdsDashboardChat-subtitle">5 active conversations</div>
                                    </div>
                                </div>
                                <a href="{{baseUrl('/individual-chats')}}" class="CdsDashboardChat-view-all">
                                    View All
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>

                            <div class="CdsDashboardChat-list">
                                @if(!empty($recentChats))
                                    @php
                                        $totalUnread = 0;
                                    @endphp
                                    @foreach($recentChats as $chat)
                                        @if(isset($chat->addedBy) && $chat->addedBy->id!=auth()->user()->id)
                                            @php
                                                $chat_with=$chat->addedBy;
                                            @endphp
                                        @else
                                            @php
                                                $chat_with=$chat->chatWith;
                                            @endphp
                                        @endif
                                        <!-- Chat Item 1 -->
                                        <div class="CdsDashboardChat-item {{ $chat->unreadMessage($chat->id, auth()->id()) > 0 ? 'unread' : '' }}">
                                            <div class="CdsDashboardChat-avatar-wrapper">
                                                <div class="CdsDashboardChat-avatar user">
                                                    @if($chat_with->profile_image != '')
                                                        <img src="{{ $chat_with->profile_image ? userDirUrl($chat_with->profile_image, 's') : 'assets/images/default.jpg' }}" alt="LD">
                                                    @else
                                                        {{ userInitial($chat_with) }}
                                                    @endif
                                                </div>
                                                @if(loginStatus($chat_with) == 1)
                                                    <div class="CdsDashboardChat-status-indicator"></div>
                                                @else
                                                    <span class="CdsDashboardChat-status-indicator away"></span>
                                                @endif
                                            </div>
                                            <div class="CdsDashboardChat-content">
                                                <div class="CdsDashboardChat-content-header">
                                                    <div>
                                                        <span class="CdsDashboardChat-name">{{$chat_with->first_name." ".$chat_with->last_name}}</span>
                                                        <span class="CdsDashboardChat-category work">Work</span>
                                                    </div>
                                                </div>
                                                <div class="CdsDashboardChat-message">
                                                    @if ($chat->lastMessage)
                                                        @if ($chat->lastMessage->attachment)
                                                            Attachment
                                                            @else
                                                            {{ substr($chat->lastMessage->message, 0, 30) . "..." }}
                                                        @endif
                                                    @else
                                                        No chat yet
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="CdsDashboardChat-meta">
                                                @php
                                                    $timezone = getUserTimezone();
                                                    $checkTimezone = isValidTimezone($timezone);
                                                    $lastMessageTime = optional($chat->lastMessage)->created_at;

                                                    if ($lastMessageTime) {
                                                        $lastMessageTime = $checkTimezone
                                                            ? $lastMessageTime->timezone($timezone)
                                                            : $lastMessageTime;

                                                        $formattedTime = $lastMessageTime->diffForHumans();
                                                    }
                                                @endphp
                                                @if($lastMessageTime)
                                                    <div class="CdsDashboardChat-time">{{ $formattedTime }}</div>
                                                @endif                                                
                                                <div class="CdsDashboardChat-badges">
                                                    @if($chat->unreadMessage($chat->id,auth()->user()->id) > 0)
                                                        @php
                                                            $unreadCount = $chat->unreadMessage($chat->id, auth()->user()->id);
                                                            $totalUnread += $unreadCount;
                                                        @endphp
                                                        <span class="CdsDashboardChat-unread-count">{{$chat->unreadMessage($chat->id,auth()->user()->id)}}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- Recent Group Chats Panel -->
                        <div class="CdsDashboardChat-panel">
                            <div class="CdsDashboardChat-header">
                                <div class="CdsDashboardChat-title-section">
                                    <div class="CdsDashboardChat-icon group">
                                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="CdsDashboardChat-title">Recent Group Chats</div>
                                        <div class="CdsDashboardChat-subtitle">5 active groups</div>
                                    </div>
                                </div>
                                <a href="{{baseUrl('/group-message')}}" class="CdsDashboardChat-view-all">
                                    View All
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>

                            <div class="CdsDashboardChat-list">
                                @if(isset($groupdata))
                                    @if($groupdata)
                                        @foreach($groupdata->sortByDesc(function ($grp) {
                                            return max(strtotime($grp->created_at), strtotime($grp->last_message_date ?? '0000-00-00 00:00:00'));
                                        }) as $grp)
                                            <!-- Group Chat Item 1 -->
                                            <div class="CdsDashboardChat-item {{$grp->unreadMessage($grp->id,auth()->user()->id) > 0 ? 'unread' : ''  }}">
                                                <div class="CdsDashboardChat-avatar-wrapper">
                                                    @if($grp->group_image)
                                                        <img src="{{ groupChatDirUrl($grp->group_image, 's') }}" alt="Doris">
                                                    @else
                                                        @php            
                                                            $initial = strtoupper(substr($grp->name, 0, 1)); 
                                                        @endphp
                                                            <div class="CdsDashboardChat-avatar group-pr">{{$initial}}
                                                            </div>
                                                    @endif
                                                  
                                                </div>
                                                <div class="CdsDashboardChat-content">
                                                    <div class="CdsDashboardChat-content-header">
                                                        <div>
                                                            <span class="CdsDashboardChat-name">{{substr($grp->name, 0, 20)}}</span>
                                                            <span class="CdsDashboardChat-category project"> {{ $grp->type }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="CdsDashboardChat-group-info">
                                                        @if ($grp->lastMessage)
                                                            @if($grp->lastMessage->sentBy != '')
                                                            <span class="CdsDashboardChat-group-name">
                                                                {{$grp->lastMessage->sentBy->first_name .' '.$grp->lastMessage->sentBy->last_name}}
                                                            </span>
                                                            @endif
                                                        @else
                                                            <span class="CdsDashboardChat-group-name">No chat yet</span>
                                                        @endif
                                                        <span class="CdsDashboardChat-members-count">
                                                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                                            </svg>
                                                            {{$grp->groupMembers->count()}} members
                                                        </span>
                                                    </div>
                                                   
                                                    @if(checkGroupPermission('can_see_messages', $grp->id))
                                                        @if ($grp->lastMessage)
                                                       
                                                            @if ($grp->lastMessage->attachment)
                                                                <div class="CdsDashboardChat-message">Attachment</div>
                                                            @else
                                                                <div class="CdsDashboardChat-message"> {!! makeBoldBetweenAsterisks(substr($grp->lastMessage->message, 0, 30) . "..." ) !!}</div>
                                                            @endif
                                                        @else
                                                            <div class="CdsDashboardChat-message">No chat yet</div>
                                                        @endif
                                                    @endif
                                                    
                                                </div>
                                                <div class="CdsDashboardChat-meta">
                                                    @if(checkGroupPermission('can_see_messages', $grp->id))
                                                        @php
                                                            $timezone = getUserTimezone();
                                                            $checkTimezone = isValidTimezone($timezone);
                                                            $lastMessageTime = optional($grp->lastMessage)->created_at;

                                                            if ($lastMessageTime) {
                                                                $lastMessageTime = $checkTimezone
                                                                    ? $lastMessageTime->timezone($timezone)
                                                                    : $lastMessageTime;

                                                                $formattedTime = $lastMessageTime->diffForHumans();
                                                            }
                                                        @endphp

                                                        @if($lastMessageTime)
                                                            <span class="CdsDashboardChat-time">{{ $formattedTime }}</span>
                                                        @endif
                                                    @endif
                                                    <div class="CdsDashboardChat-badges">
                                                        @if($grp->unreadMessage($grp->id,auth()->user()->id) > 0)  
                                                            <span class="CdsDashboardChat-unread-count">{{$grp->unreadMessage($grp->id,auth()->user()->id)}}</span>              
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
  
                                    @else
                                        <div class="empty-chat-request">
                                            <h5>No Groups Available</h5>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>  