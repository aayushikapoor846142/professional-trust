<div class="chat-sidebar">
    <div class="chat-content">
        <div id="tab1" class="chat-tab-content">
            @include('admin-panel.01-message-system.message-centre.components.sidebar.profile-card')
        </div>
        <div id="tab2" class="chat-tab-content {{ (Route::currentRouteName() == 'panel.message-centre.list' || Route::currentRouteName() == 'panel.message-centre.conversation') ? 'active' : '' }}">
            @include('admin-panel.01-message-system.message-centre.components.sidebar.chat-list')
        </div>
        <div id="tab3" class="chat-tab-content {{ (Route::currentRouteName() == 'panel.group.list' || Route::currentRouteName() == 'panel.group.conversation') ? 'active' : '' }}">
            @include('admin-panel.01-message-system.message-centre.components.sidebar.group-list')
        </div>
        <div id="tab6" class="chat-tab-content {{ (Route::currentRouteName() == 'panel.feeds.index' || Route::currentRouteName() == 'panel.feeds.conversation') ? 'active' : '' }}">
            <div class="chat-list" id="chatList">
                <div class="group-chat-title">
                    <h2>Feeds</h2>
                    <a class="message-upload-file" href="javascript:;"
                        onclick="showPopup('<?php echo baseUrl('feeds/add-new-feed') ?>')">
                        <i class="fa-solid fa-users-medical"></i>
                    </a>
                </div>
                @if(Route::currentRouteName() == 'panel.feeds.index' || Route::currentRouteName() ==
                'panel.feeds.conversation')
                <div class="chat-header">
                    <div class="chat-sidebar-tabs mb-3">
                        <input type="hidden" value="my" id="list-feed-data">
                        <a href="javascript:;" onclick="listFeedsData('my', this)" class="active">My </a>
                        <a href="javascript:;" onclick="listFeedsData('other', this)" class="">Other </a>
                        <a href="javascript:;" onclick="listFeedsData('commented', this)" class="">Commented </a>
                    </div>
                    <div class="group-search mt-2">
                        <a href="javascript:;" class="search-icon">
                            <i class="fa-sharp fa-regular fa-magnifying-glass"></i></a>
                        <input type="text" id="feedsSearch" onkeyup="getFeedsSearch(this.value)"
                            placeholder="Search Feeds" />
                    </div>
                </div>
                <div class="recent-chats" id="feeds-sidebar-list">
                    @include('admin-panel.04-profile.feeds.conversation.partials.feeds-sidebar-ajax')
                </div>
                @endif
            </div>
        </div>
        <div id="tab4" class="chat-tab-content chat-requests-sidebar">
            @include('components.skelenton-loader.chatrequest-skeleton')
        </div>
        <div id="tab5" class="chat-tab-content chat-notifications">
            @include('components.skelenton-loader.chatnotifications-skeleton')
        </div>
        <div id="tab6" class="chat-tab-content {{ (Route::currentRouteName() == 'panel.connect.index' ) ? 'active' : '' }}">
            <div class="connection-list">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="chat-title p-0 pt-lg-3 border-0 h-auto">
                        <h2>Send Connection</h2>
                    </div>
                    <div class="send-new-connection" onclick="showNewConnections()">
                        <h6 class="mb-0">New Connections <i class="fa-regular fa-arrow-right"></i></h6>
                    </div>
                </div>
                @if(Route::currentRouteName() == 'panel.connect.index' )
                <div>
                    <div id="pending-connect-list" class="bb-1 mt-3 mb-3 pe-2">
                        @include('admin-panel.01-message-system.connect.connect_sidebar_ajax')
                    </div>
                    <div class="connect-tab-list">
                        <a href="javascript:;" onclick="connectConversationList('followers', this,'click')"
                            class="active">Followers</a>
                        <a href="javascript:;" onclick="connectConversationList('following', this,'click')"
                            class="">Following</a>
                    </div>
                    <div class="follow-connection-list" id="connected-ist">
                        @include('admin-panel.01-message-system.connect.connect_sidebar_ajax')
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div> 