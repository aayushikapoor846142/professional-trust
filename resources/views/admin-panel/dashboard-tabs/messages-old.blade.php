@extends('admin-panel.layouts.app')

@section('content')
@include('admin-panel.dashboard-tabs.common.dashboard-nav', ['activeTab' => 'messages'])

<!-- Dashboard Container -->
<main class="cdsTYDashboard-main-main-content">
    <div class="CdsMessageCentreOverview-container">
        <!-- Header -->
        <div class="CdsMessageCentreOverview-header">
            <h1>Message Center</h1>
            <div class="CdsMessageCentreOverview-header-actions">
                {{--<button class="CdsMessageCentreOverview-btn CdsMessageCentreOverview-btn-primary" onclick="cdsMessageCentreOverviewComposeMessage()">
                    <span>✉️</span> Compose Message
                </button>--}}
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="CdsMessageCentreOverview-stats-grid">
            <div class="CdsMessageCentreOverview-stat-card">
                <div class="CdsMessageCentreOverview-stat-icon">💬</div>
                <div class="CdsMessageCentreOverview-stat-value">{{ $totalChats ?? 0 }}</div>
                <div class="CdsMessageCentreOverview-stat-label">Total Chats</div>
            </div>
            <div class="CdsMessageCentreOverview-stat-card">
                <div class="CdsMessageCentreOverview-stat-icon">📧</div>
                <div class="CdsMessageCentreOverview-stat-value">{{ $unreadMessages ?? 0 }}</div>
                <div class="CdsMessageCentreOverview-stat-label">Unread Messages</div>
            </div>
            <div class="CdsMessageCentreOverview-stat-card">
                <div class="CdsMessageCentreOverview-stat-icon">👥</div>
                <div class="CdsMessageCentreOverview-stat-value">{{ $totalGroupChats ?? 0 }}</div>
                <div class="CdsMessageCentreOverview-stat-label">Group Chats</div>
            </div>
            <div class="CdsMessageCentreOverview-stat-card">
                <div class="CdsMessageCentreOverview-stat-icon">✉️</div>
                <div class="CdsMessageCentreOverview-stat-value">{{ $invitations ?? 0 }}</div>
                <div class="CdsMessageCentreOverview-stat-label">Invitations</div>
            </div>
            <div class="CdsMessageCentreOverview-stat-card">
                <div class="CdsMessageCentreOverview-stat-icon">🔔</div>
                <div class="CdsMessageCentreOverview-stat-value">{{ $notifications ?? 0 }}</div>
                <div class="CdsMessageCentreOverview-stat-label">Notifications</div>
            </div>
        </div>

        <!-- Content Grid -->
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
                            <a class="CdsMessageCentreOverview-chat-item" href="{{baseUrl('/message-centre/chat/'.$chat->unique_id)}}">
                                @if(isset($chat->unread_count) && $chat->unread_count > 0)
                                    <div class="CdsMessageCentreOverview-unread-indicator"></div>
                                @endif
                                <div class="CdsMessageCentreOverview-chat-avatar">
                                    @if(optional($chat->chatWith)->profile_image)
                                        <img src="{{ userDirUrl(optional($chat->chatWith)->profile_image, 't') }}" alt="{{ optional($chat->chatWith)->first_name }}">
                                    @else
                                        {{ strtoupper(substr(optional($chat->chatWith)->first_name ?? 'U', 0, 1)) }}
                                    @endif
                                </div>
                                <div class="CdsMessageCentreOverview-chat-content">
                                    <div class="CdsMessageCentreOverview-chat-name">{{ optional($chat->chatWith)->first_name }} {{ optional($chat->chatWith)->last_name }}</div>
                                    <div class="CdsMessageCentreOverview-chat-preview">
                                        @if(optional($chat->lastMessage))
                                            {{ Str::limit(optional($chat->lastMessage)->message ?? '', 50) }}
                                        @else
                                            No messages yet
                                        @endif
                                    </div>
                                </div>
                                <div class="CdsMessageCentreOverview-chat-time">
                                    @if(!empty($chat->lastMessage))
                                        {{ \Carbon\Carbon::parse($chat->lastMessage->created_at)->diffForHumans() }}
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    @else
                        <div class="CdsMessageCentreOverview-no-chats">
                            <div class="CdsMessageCentreOverview-no-chats-icon">💬</div>
                            <div class="CdsMessageCentreOverview-no-chats-text">No recent chats</div>
                            <div class="CdsMessageCentreOverview-no-chats-subtext">Start a conversation to see it here</div>
                        </div>
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
                                @php            
                                $initial = strtoupper(substr($group->name, 0, 2)); // Extracts the first letter and converts to uppercase
                                @endphp
                                <div class="group-icon dark-tiber" data-initial="{{$initial}}">
                                </div>
                                <div class="CdsMessageCentreOverview-chat-content">
                                    <div class="CdsMessageCentreOverview-chat-name">{{ $group->name }}</div>
                                    <div class="CdsMessageCentreOverview-chat-preview">
                                        @if(optional($group->lastMessage))
                                            {{ Str::limit(optional($group->lastMessage)->message ?? '', 50) }}
                                        @else
                                            No messages yet
                                        @endif
                                    </div>
                                </div>
                                <div class="CdsMessageCentreOverview-chat-time">
                                    @if(optional($group->lastMessage))
                                        {{ \Carbon\Carbon::parse($group->lastMessage->created_at)->diffForHumans() }}
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    @else
                        <div class="CdsMessageCentreOverview-no-chats">
                            <div class="CdsMessageCentreOverview-no-chats-icon">👥</div>
                            <div class="CdsMessageCentreOverview-no-chats-text">No group chats</div>
                            <div class="CdsMessageCentreOverview-no-chats-subtext">Join or create a group to see it here</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ url('assets/css/message-system/message-overview.css') }}">
@endsection

@section('javascript')
@include('admin-panel.dashboard-tabs.common.dashboard-scripts')
@endsection
