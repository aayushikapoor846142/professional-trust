<div class="chat-tabs">
        <a class="chat-tab common-tooltip" data-tab="tab1">
            <i class="fa-regular fa-user"></i>
                <span class="tooltiptext">Profile</span>
        </a>
        <a class="chat-tab common-tooltip notab @if(Route::currentRouteName() == 'panel.message-centre.index' || Route::currentRouteName() == 'panel.message-centre.conversation'){{'active'}}@endif"
            href="{{baseUrl('message-centre')}}">
            <div class="position-relative">
                <i class="fa-regular fa-messages"></i>
                <span class="chat-message-count   messages-count"
                    style="@if(unreadTotalChatMessages(auth()->user()->id)>0){{ 'opcity:1' }} @else{{ 'opacity:0' }}@endif">{{unreadTotalChatMessages()}}</span>
                </span>
            </div>
            <span class="tooltiptext">Messages</span>
        </a>
        <a class="chat-tab common-tooltip notab @if(Route::currentRouteName() == 'panel.group.list' || Route::currentRouteName() == 'panel.group.conversation'){{'active'}} @endif"
            href="{{baseUrl('group/chat')}}">
            <div class="position-relative">
                <i class="fa-regular fa-user-group"></i>
                <span class="chat-request-count group-messages-count counter-all"
                style="@if(unreadTotalGroupMessages(auth()->user()->id)>0){{ 'opacity:1' }} @else{{ 'opacity:0' }}@endif">{{unreadTotalGroupMessages()}}</span>                
            </div>
            <span class="tooltiptext">Groups</span>
        </a>
        <a class="action-tab more-tab " onclick="showMoreTabs('more')">
            More
        </a>
        {{-- <a class="chat-tab notab @if(Route::currentRouteName() == 'panel.feeds.index' || Route::currentRouteName() == 'panel.feeds.conversation'){{'active'}} @endif not-active"
            id="four-tab" href="{{baseUrl('feeds/manage')}}">
            <i class="fa-regular fa-rss"></i>
        </a>  --}}
        <div class="mobile-sidebar-show">
             {{-- <a class="chat-tab notab @if(Route::currentRouteName() == 'panel.feeds.index' || Route::currentRouteName() == 'panel.feeds.conversation'){{'active'}} @endif"
                href="{{baseUrl('feeds/manage')}}">
                <i class="fa-regular fa-rss"></i>
            </a>  --}}
            <a class="chat-tab common-tooltip" onclick="fetchChatRequest()" data-tab="tab4">
                <div class="position-relative">
                    <i class="fa-regular fa-message-arrow-down"></i>
                    <span style="@if(chatReqstCount(auth()->user()->id)>0){{ 'opacity:1' }} @else{{ 'opacity:0' }}@endif
                      " class="chat-request-count chatrequest-count
                        counter-all">{{chatReqstCount(auth()->user()->id)}}</span>
                </div>
                <span class="tooltiptext">Invitation</span>
            </a>
            <a class="chat-tab common-tooltip" onclick="fetchChatNotifications()" data-tab="tab5">
                <div class="position-relative">
                    <i class="fa fa-bell" aria-hidden="true"></i>
                    <span style="@if(chatNotificationsCount(auth()->user()->id)>0){{ 'opacity:1' }} @else{{ 'opacity:0' }}@endif"
                        class="chat-request-count notification-count counter-all ">{{chatNotificationsCount(auth()->user()->id)}}</span>
                </div>
                <span class="tooltiptext">Notification</span>
            </a>
            <a class="action-tab" onclick="showMoreTabs('less')">
                Less
            </a>
        </div>
    </div>
