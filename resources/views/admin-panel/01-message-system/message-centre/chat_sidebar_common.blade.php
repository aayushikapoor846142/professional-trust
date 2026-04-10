

<div class="chat-sidebar">
    <div class="chat-content">
        <div id="tab1" class="chat-tab-content">
            <div class="chat-profile-card rounded">
                <div class="chat-profile-title">
                    <h2>My Profile</h2>
                
                </div>
                <!-- Profile Header -->
                <div class="chat-profile-header text-center p-4">
                    
                    @php
                    $user = auth()->user();
                    $profileImage = $user->profile_image ? userDirUrl($user->profile_image, 't') : null;
                    $initial = strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1));
                @endphp
                
                @if($profileImage)
                    <img class="chat-profile-picture" src="{{ $profileImage }}" alt="Profile Picture">
                @else
                    <div class="group-icon chat-profile-picture" data-initial="{{ $initial }}"></div>
                @endif
                    <h2 class="chat-profile-name mt-3">{{auth()->user()->first_name." ".auth()->user()->last_name}}</h2>
                    @if(auth()->user()->is_login==1)
                    <p class="chat-profile-status text-success">Active</p>
                    @else
                    <p class="chat-profile-status text-danger">Inactive</p>
                    @endif
                 
                </div>

                <!-- Accordion -->
                <div class="accordion chat-profile-accordion" id="chatProfileAccordion">
                    <!-- About Section -->
                    <div class="accordion-item">
                    
                    </div>
                    <!-- Attached Files Section -->
                    <div class="accordion-item" style="display: none;">
                        <h2 class="accordion-header" id="filesHeader">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#filesContent" aria-expanded="false" aria-controls="filesContent">
                                Attached Files
                            </button>
                        </h2>
                        <div id="filesContent" class="accordion-collapse collapse" aria-labelledby="filesHeader"
                            data-bs-parent="#chatProfileAccordion">
                            <div class="accordion-body">
                                This is the Attached Files section content.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="tab2" class="chat-tab-content {{ (Route::currentRouteName() == 'panel.message-centre.list' || Route::currentRouteName() == 'panel.message-centre.conversation') ? 'active' : '' }}">
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
        </div>
        <div id="tab3" class="chat-tab-content {{ (Route::currentRouteName() == 'panel.group.list' || Route::currentRouteName() == 'panel.group.conversation') ? 'active' : '' }}">

            {{-- @include('admin-panel.01-message-system.message-centre.groups.chat.group-chats') --}}
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
                    {{--<i class="fa fa-spinner fa-spin"></i> Loading...--}}
                    @include('components.skelenton-loader.chatlistloder-skeleton')
                </div> 
            </div>
        </div>
        <div id="tab6"
            class="chat-tab-content {{ (Route::currentRouteName() == 'panel.feeds.index' || Route::currentRouteName() == 'panel.feeds.conversation') ? 'active' : '' }}">
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
        <div id="tab6"
            class="chat-tab-content {{ (Route::currentRouteName() == 'panel.connect.list' ) ? 'active' : '' }}">
            <div class="connection-list">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="chat-title p-0 pt-lg-3 border-0 h-auto">
                        <h2>Send Connection</h2>
                    </div>
                    <div class="send-new-connection" onclick="showNewConnections()">
                        <h6 class="mb-0">New Connections <i class="fa-regular fa-arrow-right"></i></h6>
                    </div>
                </div>
              
                @if(Route::currentRouteName() == 'panel.connect.list' )

                <!-- <div class="recent-chats" id="connected-ist"> -->
                <div>
                    <div id="pending-connect-list" class="bb-1 mt-3 mb-3 pe-2">
                        @include('admin-panel.01-message-system.connect.connect_sidebar_ajax')
                    </div>
                    <div class="connect-tab-list">
                        <!-- @include('admin-panel.01-message-system.connect.connect_sidebar_ajax') -->
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


@push("scripts")
<script>
//initEditor("feeds-description");
let page = 1;
let active_tab = "mygroup";
$(document).ready(function() {
    fetchChatNotifications(page);


});
document.addEventListener("DOMContentLoaded", () => {
    const chatTabs = document.querySelectorAll(".chat-tab:not(.notab)");
    const chatContents = document.querySelectorAll(".chat-tab-content");

    chatTabs.forEach((tab) => {
        tab.addEventListener("click", () => {
            // Remove active class from all tabs
            chatTabs.forEach((t) => t.classList.remove("active"));
            // Hide all chat tab contents
            chatContents.forEach((content) => content.classList.remove("active"));

            // Add active class to the clicked tab
            tab.classList.add("active");

            // Show the corresponding chat tab content
            const target = document.getElementById(tab.dataset.tab);
            target.classList.add("active");
        });
    });
});

function listFeedsData(type, element) {
    //   alert(type);
    $('a').removeClass('active')
    $(element).addClass('active');
    $('#list-feed-data').val(type);
    $.ajax({
        type: 'get',
        url: "{{ baseUrl('feeds/feeds-sidebar-list/') }}?type=" + type,
        dataType: 'json',
        success: function(data) {
            //alert(data);
            $('#feeds-sidebar-list').html(data.contents);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching messages:', error);
        }
    });

}



function fetchChatRequest() {
    $.ajax({
        url: "{{ baseUrl('message-centre/chat-requests') }}",
        dataType: "json",
        dataType: "json",
        beforeSend: function() {
            // $(".chat-requests").html("<div class='text-center'><i class='fa fa-spin fa-spinner'></i></div>");
        },
        success: function(response) {
            //alert(response.status);
            if (response.status) {
                $(".chat-requests-sidebar").html(response.contents);
                if (response.request_count < 1) {
                    $(".recent-chats").html('<h3>Recent</h3> No Chat Requests');
                }
            }
        }
    });
}




function fetchChatNotifications(page = 1) {
    let button = $('#load-more-notifications');
    let loader = $('#loading-spinner');

    loader.show();
    button.prop('disabled', true);

    $.ajax({
        url: BASEURL + `/group/chat-notfications?page=${page}`,
        type: "GET",
        dataType: "json",
        beforeSend: function() {
            // Optional: You can add a loading spinner here if needed
        },
        success: function(response) {

            if (response.status) {
                if (page === 1) {
                    $(".chat-notifications").html(response.contents);
                } else {
                    $(".chat-notifications").html(response.contents);
                }
                // Hide the button if there are no more pages to load
                if (response.current_page >= response.last_page) {
                    button.hide();
                }
            }
        },
        complete: function() {
            loader.hide();
            button.prop('disabled', false);
        }
    });
}

// Load More Button Click Event
$(document).on('click', '#load-more-notifications', function() {
    page++;
    fetchChatNotifications(page);
});
</script>

<script>
function requestToJoinGroup(groupId) {
    $.ajax({
        url: "{{ baseUrl('group/join-request') }}",
        type: "POST",
        data: {
            group_id: groupId,
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            if (response.status == true) {

                successMessage(response.message);
                redirect(response.redirect_back);
            } else {
                errorMessage(response.message);
            }
        },
        error: function(xhr) {
            alert("Something went wrong. Please try again.");
        }
    });
}
</script>

<script>
function withdrawRequest(groupId) {
    $.ajax({
        url: "{{ baseUrl('group/withdraw-request') }}",
        type: "POST",
        data: {
            group_id: groupId,
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            if (response.status == true) {

                successMessage(response.message);
                redirect(response.redirect_back);
            } else {
                errorMessage(response.message);
            }
        },
        error: function(xhr) {
            alert("Something went wrong. Please try again.");
        }
    });
}

function followConnection(user_id) {

    $.ajax({
        type: 'post',
        url: "{{ baseUrl('feeds/') }}/" + user_id + "/follow",
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            // alert(response);
            if (response.status == true) {

                successMessage(response.message);
                location.reload();
            } else {
                errorMessage(response.message);
            }
        },
        error: function(xhr, status, error) {
            console.log('Error fetching messages:', error);
        }
    });
}

function unfollowConnection(user_id) {
   
    Swal.fire({
        title: "Are you sure to unfollow?",
        text: "Your connection also removed",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "CdsTYButton-btn-primary",
        cancelButtonClass: "CdsTYButton-btn-primary CdsTYButton-border-thick ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        
        var remove_connection = "";
        if (result.value) {
            remove_connection = "yes";
        }else{
            remove_connection = "no";
        }
       
        $.ajax({
            type: 'post',
            url: "{{ baseUrl('feeds/') }}/" + user_id + "/unfollow",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{remove_connection:remove_connection},
            success: function(response) {
                if (response.status == true) {
                    successMessage(response.message);
                    location.reload();
                } else {
                    errorMessage(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log('Error fetching messages:', error);
            }
        });

    });
    
}
</script>
<script>
function showMoreTabs(value) {
    const showAllTabs = document.querySelector(".mobile-sidebar-show");
    const moreTab = document.querySelector(".more-tab");
    const fourTab = document.querySelector("#four-tab");
    if (value === "more") {
        showAllTabs.classList.add("active");
        fourTab.classList.remove("not-active")
        moreTab.style.display = 'none'
    } else {
        showAllTabs.classList.remove("active");
        fourTab.classList.add("not-active")
        moreTab.style.display = 'flex'
    }

}
</script>
<script>
function showNewConnections() {
    const newConnection = document.querySelector(".new-connection-block");
    newConnection.classList.add("active")
}


</script>
@endpush