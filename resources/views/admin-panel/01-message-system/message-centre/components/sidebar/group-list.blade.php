<div class="chat-list" id="chatList">
    <div class="group-chat-title">
        <h2>Group Chats</h2>
        <a class="message-upload-file" href="javascript:;"
            onclick="openCustomPopup(this)" data-href="<?php echo baseUrl('group/add-new-group') ?>">
            <i class="fa-solid fa-users-medical"></i>
        </a>
    </div>
    <div class="chat-header">
        <div class="chat-sidebar-tabs mb-3">
            <a href="javascript:;" onclick="conversationList('',false)"
                class=" @if(Route::currentRouteName() == 'panel.group.index' || Route::currentRouteName() == 'panel.group.conversation') active @endif group-type"
                id="my-group">My</a>
            <a href="javascript:;" onclick="otherConversationList('',false)" class="group-type"
                id="other-groups">Other
            </a>
            <a href="javascript:;" onclick="pendingGroupJoinRequest()" class="group-type"
                id="pending-requests">Requested</a>
        </div>
        <div class="group-search mt-2">
            <a href="javascript:;" class="search-icon">
                <i class="fa-sharp fa-regular fa-magnifying-glass"></i>
            </a>
            <input type="text" id="groupSearch" onkeyup="getSearch(this.value)"
                placeholder="Search Groups" />
        </div>
    </div>
   
    <div class="recent-chats" >
        <h3 class="recent-head">Recent</h3>
        <div id="group-conversation-list"></div>
    </div>
    <div id="loading-spinner" class="mt-50" style="display: none;">
        @include('components.skelenton-loader.chatlistloder-skeleton')
    </div> 
</div> 