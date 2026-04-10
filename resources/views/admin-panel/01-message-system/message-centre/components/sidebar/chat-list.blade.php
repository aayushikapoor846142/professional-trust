<div class="chat-list" id="chatList">
    <div class="chat-title">
        <h2>Chats</h2>
    </div>
    <div class="chat-header">
        <div class="group-search">
            <a href="javascript:;" class="search-icon"><i
                    class="fa-sharp fa-regular fa-magnifying-glass"></i></a>
            <input type="text" id="chatSearch" onkeyup="getSearch(this.value)"
                placeholder="Search Messages or Users">
        </div>
    </div>
    
    @if(Route::currentRouteName() == 'panel.message-centre.list' || Route::currentRouteName() ==
    'panel.message-centre.conversation') 
    <div class="recent-chats" id="conversation-list" >
        @include('admin-panel.01-message-system.message-centre.chat_sidebar_ajax')
    </div>
    @endif
</div> 