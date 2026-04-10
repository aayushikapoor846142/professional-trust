@extends('admin-panel.layouts.app')
@section('page-submenu')
{!! pageSubMenu('message') !!}
@endsection

@section('styles')
<link href="{{ url('assets/css/message-system/message-overview.css') }}" rel="stylesheet" />
@endsection
@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
<h1>Message Center</h1>
 <div class="CdsMessageCentreOverview-stats-grid">
            <div class="CdsMessageCentreOverview-stat-card">
                <div class="CdsMessageCentreOverview-stat-icon">💬</div>
                <div class="CdsMessageCentreOverview-stat-value">{{ $totalChats }}</div>
                <div class="CdsMessageCentreOverview-stat-label">Total Chats</div>
            </div>
            <div class="CdsMessageCentreOverview-stat-card">
                <div class="CdsMessageCentreOverview-stat-icon">📧</div>
                <div class="CdsMessageCentreOverview-stat-value">{{ $unreadMessages }}</div>
                <div class="CdsMessageCentreOverview-stat-label">Unread Messages</div>
            </div>
            <div class="CdsMessageCentreOverview-stat-card">
                <div class="CdsMessageCentreOverview-stat-icon">👥</div>
                <div class="CdsMessageCentreOverview-stat-value">{{ $totalGroupChats }}</div>
                <div class="CdsMessageCentreOverview-stat-label">Group Chats</div>
            </div>
            <div class="CdsMessageCentreOverview-stat-card">
                <div class="CdsMessageCentreOverview-stat-icon">✉️</div>
                <div class="CdsMessageCentreOverview-stat-value">{{ $invitations }}</div>
                <div class="CdsMessageCentreOverview-stat-label">Invitations</div>
            </div>
            <div class="CdsMessageCentreOverview-stat-card">
                <div class="CdsMessageCentreOverview-stat-icon">🔔</div>
                <div class="CdsMessageCentreOverview-stat-value">{{ $notifications }}</div>
                <div class="CdsMessageCentreOverview-stat-label">Notifications</div>
                <!-- <span class="CdsMessageCentreOverview-notification-badge">New</span> -->
            </div>
        </div>

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
        <div class="CdsMessageCentreOverview-content-grid">
            <div class="CdsMessageCentreOverview-chat-section">
                <div class="CdsMessageCentreOverview-section-header">
                    <h2 class="CdsMessageCentreOverview-section-title">
                        <span class="CdsMessageCentreOverview-section-title-icon">💬</span>
                        Recent Chats
                    </h2>
                    <a class="CdsMessageCentreOverview-view-all" href="{{baseUrl('/message-centre')}}">View All →</a>
                </div>
                <div class="CdsMessageCentreOverview-chat-list" id="CdsMessageCentreOverview-recentChats">
                    @if(isset($recentChats) && count($recentChats) > 0)
                        @foreach($recentChats as $chat)
                            @php
                                $chat_with = $chat->addedBy && $chat->addedBy->id != auth()->user()->id ? $chat->addedBy : $chat->chatWith;
                            @endphp
                            <a class="CdsMessageCentreOverview-chat-item" href="{{baseUrl('/message-centre/chat/'.$chat->unique_id)}}">
                                {!! getProfileImage($chat->addedBy->unique_id) !!}
                                <div class="CdsMessageCentreOverview-chat-content">
                                    <div class="CdsMessageCentreOverview-chat-name">{{ $chat_with->first_name ?? '' }} {{ $chat_with->last_name ?? '' }}</div>
                                    <div class="CdsMessageCentreOverview-chat-message">
                                        @if($chat->lastMessage)
                                            {{ Str::limit($chat->lastMessage->message, 40) }}
                                        @else
                                            <span class="text-muted">No messages yet</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="CdsMessageCentreOverview-chat-time"> 
                                    @if($chat->lastMessage)
                                            {{ getTimeAgo($chat->lastMessage->created_at) }}
                                    @endif</span>
                            </a>
                        @endforeach
                    @else
                        <div class="text-muted">No recent chats found.</div>
                    @endif
                </div>
            </div>

            <div class="CdsMessageCentreOverview-chat-section">
                <div class="CdsMessageCentreOverview-section-header">
                    <h2 class="CdsMessageCentreOverview-section-title">
                        <span class="CdsMessageCentreOverview-section-title-icon">👥</span>
                        Recent Group Chats
                    </h2>
                    <a class="CdsMessageCentreOverview-view-all" href="{{baseUrl('/group/chat')}}">View All →</a>
                </div>
                <div class="CdsMessageCentreOverview-chat-list" id="CdsMessageCentreOverview-groupChats">
                    @if(isset($recentGroupChats) && count($recentGroupChats) > 0)
                        @foreach($recentGroupChats as $group)
                            <a class="CdsMessageCentreOverview-chat-item" href="{{baseUrl('/group/chat/'.$group->unique_id)}}">
                                <!-- <div class="CdsMessageCentreOverview-unread-indicator"></div> -->
                                    @php            
                                    $initial = strtoupper(substr($group->name, 0, 2)); // Extracts the first letter and converts to uppercase
                                    @endphp
                                    <div class="group-icon dark-tiber" data-initial="{{$initial}}">
                                    </div>
                                <!-- <div class="CdsMessageCentreOverview-chat-avatar CdsMessageCentreOverview-group-avatar">{{$initial}}</div> -->
                                <div class="CdsMessageCentreOverview-chat-content">
                                    <div class="CdsMessageCentreOverview-chat-name">{{ $group->name }}</div>
                                    <div class="CdsMessageCentreOverview-chat-message">
                                        @if($group->lastMessage)
                                            {{ Str::limit($group->lastMessage->message, 40) }}
                                        @else
                                            <span class="text-muted">No messages yet</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="CdsMessageCentreOverview-chat-time">
                                     @if($group->lastMessage){{getTimeAgo($group->lastMessage->created_at)}}@endif</span>
                            </a>
                        @endforeach
                    @else
                        <div class="text-muted">No recent group chats found.</div>
                    @endif
                </div>
            </div>
        </div>
			</div>
	
	</div>
  </div>
</div>

@endsection

@section('javascript')
  
@endsection
