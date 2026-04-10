<div class="message-header cds-userMsgHead">
    <div class="message-header-block">
        <div class="message-title">
            <div class="back-chats" onclick="backToChats()">
                <i class="fa-solid fa-angle-left"></i>
            </div>
            <div class="chat-avatar"> 
                @if(auth()->user()->id!=$get_chat_professional->id)
                @if($get_chat_professional->profile_image != '')
                <img src="{{ $get_chat_professional->profile_image ? userDirUrl($get_chat_professional->profile_image, 't') : 'assets/images/default.jpg' }}"
                    alt="Doris">
                @else
                <div class="group-icon" data-initial="{{ userInitial($get_chat_professional) }}"></div>
                @endif
                @else
                @if($get_chat_user->profile_image != '')
                <img src="{{ $get_chat_user->profile_image ? userDirUrl($get_chat_user->profile_image, 't') : 'assets/images/default.jpg' }}"
                    alt="Doris">
                @else
                <div class="group-icon" data-initial="{{ userInitial($get_chat_user) }}"></div>
                @endif
                @endif

                @if(auth()->user()->id!=$get_chat_professional->id)
                @if(loginStatus($get_chat_professional) ==1)
                <span class="status-online login-status chatOnlineStatus{{$chat_id}}"></span>
                @else
                <span class="status-offline login-status chatOnlineStatus{{$chat_id}}"></span>
                @endif
                @else
                @if(loginStatus($get_chat_user) == 1)
                <span class="status-online login-status chatOnlineStatus{{$chat_id}}"></span>
                @else
                <span class="status-offline login-status  chatOnlineStatus{{$chat_id}}"></span>
                @endif
                @endif
            </div>
            <div class="chat-name">
                @if(auth()->user()->id!=$get_chat_professional->id)
                <h3>{{$get_chat_professional->first_name." ".$get_chat_professional->last_name}}</h3>
                @else
                <h3>{{$get_chat_user->first_name." ".$get_chat_user->last_name}}</h3>
                @endif
            </div>
        </div>
        <div class="message-action-btn">
            <div class="search-chats">
                <i class="fa-regular fa-magnifying-glass" onclick="toggleChatsSearch('open')" title="Search"></i>
            </div>
            <a href="javascript:;" onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('message-centre/get-user-profile/'.$chat_id) }}" class="chat-user">
                <i class="fa-regular fa-user" title="User"></i>
            </a>
            <a onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('message-centre/get-shared-file/'.$chat_id) }}"  class="chat-folder">
                <i class="fa-regular fa-folder" title="File Folder"></i>
            </a>

            <div class="dropdown chat-dropdown">
                <button class="btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="fa-solid fa-ellipsis" title="More"></i>
                </button>
                <ul class="dropdown-menu">
                    <div id="block_unblock_div{{$chat->id}}">
                        @if($chat->blocked_chat==1 && $chat->blocked_by==auth()->user()->id)
                        <li><a href="javascript:void(0)" class="dropdown-item"
                                onclick="unblockChat('{{$chat->id}}')" id="unblock_chat">Unblock
                                Chat <i class="fa-solid fa-unlock"></i></a></li>
                        @else
                        @if($chat->blocked_chat==1 && $chat->blocked_by!=auth()->user()->id )
                        @else
                        <li class="cds-user-more-option"><a href="javascript:void(0)" onclick="blockChat('{{$chat->id}}')" class="dropdown-item" id="block_chat">Block Chat <i class="fa-solid fa-circle-half-stroke"></i></a></li>
                        @endif
                        @endif
                    </div>
                    <li><a id="clear_chat" class="dropdown-item" href="javascript:;">Clear Chat <i class="fa-solid fa-eraser"></i></a>
                    </li>
                    <li><a class="dropdown-item" onclick="deleteChat('{{$chat->id}}')"
                            href="javascript:;">Delete Chat <i class="fa fa-trash" aria-hidden="true"></i></a>
                    </li>
                    <li class="chat-user-mobile">
                        <a class="dropdown-item" onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('message-centre/get-user-profile/'.$chat_id) }}"
                            href="javascript:;">User Profile <i class="fa-solid fa-user"></i>
                        </a>
                    </li>
                    <li class="chat-user-mobile">
                        <a class="dropdown-item" onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('message-centre/get-shared-file/'.$chat_id) }}"  class="chat-folder"
                            href="javascript:;">File Folder <i class="fa-solid fa-folder"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="search-chats-toggle" id="chatsSearch" style="display: none;">
        <input type="text" id="search_input" placeholder="Search here..." />
        <div>
            <button class="search-up" onclick="searchChatMessages('up')">
                <i class="fa-solid fa-angle-up"></i>
            </button>
            <button class="search-down" onclick="searchChatMessages('down')">
                <i class="fa-solid fa-angle-up"></i>
            </button>
            <button onclick="searchChatMessages('up')" id="searchbtn">
                <i class="fa-regular fa-magnifying-glass"></i>
            </button>
            <button onclick="toggleChatsSearch('close')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
</div> 