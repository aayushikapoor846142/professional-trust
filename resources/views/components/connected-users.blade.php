<!-- floting msg -->
<div class="message-show-block active">
    <div onclick="messageToggleToRight()" class="message-expand-btn">
        <button class="btn">
            <i class="fa-solid fa-angle-left"></i>
        </button>
    </div>
    <!-- <span class="hide-messages" onclick="messageToggleToRight()"><i class="fa-solid fa-angle-right"></i></span> -->

    <div class="cds-chatIconMobileView">
        <span class="mobile-hide-messages" onclick="messageToggleToRight()"><i class="fa-solid fa-angle-right"></i></span>
        <a href="javascript:;" class="chaticon" title="Messages" onclick="messageExpand()">
            <img src="{{'assets/images/icons/chatbot.svg'}}" class="chaticonImg" alt="Chatbot">
        </a>
    </div>

    <span class="hide-messages" onclick="messageToggleToRight()"><i class="fa-solid fa-angle-right"></i></span>

    <div class="message-head" onclick="messageExpand()">
        <div class="position-relative badge-number">
            <span class="msg-title">Messages</span>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill total-messages-count">@if((unreadTotalChatMessages()+unreadTotalGroupMessages())>0){{unreadTotalChatMessages()+unreadTotalGroupMessages()}}@endif</span>
        </div>
        <div>
            {{--<span class="chat-request-count total-messages-count">{{unreadTotalChatMessages()+unreadTotalGroupMessages()}}</span>--}}
        </div>
        <button class="btn">
            <i class="fa-duotone fa-regular fa-angle-up cursor-pointer"></i>
        </button>
    </div>
    <!-- msg body -->
    <div class="message-expand">
        <div class="msg-body">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-users-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-users" type="button" role="tab" aria-controls="pills-users"
                        aria-selected="true">Users</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-groups-tab" data-bs-toggle="pill" data-bs-target="#pills-groups"
                        type="button" role="tab" aria-controls="pills-groups" aria-selected="false">Groups</button>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <!-- user -->
                <div class="tab-pane fade show active" id="pills-users" role="tabpanel"
                    aria-labelledby="pills-users-tab" tabindex="0">
                    @php
                    $chatUsersList=chatUsersList();
                    @endphp
                    <div class="msg-userTab">
                        @include('components.partials.chat-user-list',['chatUsersList'=>$chatUsersList])

                    </div>
                </div>
                <!-- # user -->
                <!-- groups -->
                <div class="tab-pane fade" id="pills-groups" role="tabpanel" aria-labelledby="pills-groups-tab"
                    tabindex="0">
                    @php
                    $currentUserGroupsList=currentUserGroupsList();
                    @endphp
                    <div class="msg-groupsTab">
                        @include('components.partials.group-user-list',['currentUserGroupsList'=>$currentUserGroupsList])
                    </div>
                </div>
                <!-- # groups -->
            </div>
        </div>
    </div>
</div>

<script>


function messageExpand() {

    const msgExpand = document.querySelector(".message-expand");
    const msgHead = document.querySelector(".message-head");
    if (msgExpand.style.height === '70vh') {
        msgExpand.style.height = '0';
        msgHead.classList.remove('active');
    } else {
        msgExpand.style.height = '70vh';
        msgHead.classList.add('active');
    }
    refreshMessaging();
}

function refreshMessaging() {
    $.ajax({
        url: BASEURL + "/message-centre/refresh-chat-list/",
        type: "GET",
        success: function(data) {
            $(".msg-userTab").html(data.contents);
        }
    });
    $.ajax({
        url: BASEURL + "/group/refresh-group-list/",
        type: "GET",
        success: function(data) {
            $(".msg-groupsTab").html(data.contents);
        }
    });
}

function messageToggleToRight() {
    const msgbotContainer = document.querySelector(".message-show-block");
    const chatBoxArea = document.querySelector(".cds-custom-chat-box");
        
    if (msgbotContainer.classList.contains('active')) {
        localStorage.setItem(
            "minimizedMessagingBox",
            false
        );
        msgbotContainer.classList.remove('active'); 
        chatBoxArea.classList.remove('active'); // Remove 'active' from chat-box-area
    } else {
       
        localStorage.setItem(
            "minimizedMessagingBox",
            true
        );
        msgbotContainer.classList.add('active'); 
        chatBoxArea.classList.add('active'); // Add 'active' to chat-box-area
    }
}
</script>