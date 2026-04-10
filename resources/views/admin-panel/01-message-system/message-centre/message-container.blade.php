    @if($chat!=NULL)
   <div class="chat-messages" id="chatMessages{{$chat_id}}">
        <input type="hidden" value="{{baseUrl('message-centre/send-msg/'.$chat_id)}}" id="geturl">
        <input type="hidden" value="" id="edit_message_id">
        <input type="hidden" value="" id="reply_to_id">
        <input type="hidden" value="{{$chat_id}}" id="get_chat_id">
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
                    <!-- <button class="btn btn-outline-primary btn-sm cds-popupmsgBox"
                        onclick="openChat('{{$chat->id}}', '{{$get_chat_professional->unique_id}}')">Open
                        ChatBox</button> -->
                    <div class="search-chats">
                        <i class="fa-regular fa-magnifying-glass" onclick="toggleChatsSearch('open')" title="Search"></i>
                    </div>
                    <a href="javascript:;" onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('message-centre/get-user-profile/'.$chat_id) }}" class="chat-user">
                        <i class="fa-regular fa-user" title="User"></i>
                    </a>
                    <a onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('message-centre/get-shared-file/'.$chat_id) }}"  class="chat-folder">
                        <i class="fa-regular fa-folder" title="File Folder"></i>
                    </a>

                    <!-- <div> -->
                    <!-- <button id="chatPopupBtn" class="CdsTYButton-btn-primary chatPopupBtn">Chat</button> -->
                    <!-- </div> -->


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
        <div class="message-content cds-chatBodyFullView pt-0" id="scrollDiv">
            <form id="clear-messages">
                
                @csrf
                <div style="display:none" id="selectAllDiv" class="select-all-checkbox">
                    <div class="cds-clearBox">
                        <label class="cds-checkbox ">
                            <input type="checkbox" id="selectAll" class="checkbox" />
                            <span class="checkmark"></span>
                            <span class="selectAll">Select All</span>
                        </label>
                    </div>

                    
                    <div class="cds-action-btn">
                        <button id="cancelClear" type="button" class="btn btn-dark btn-sm">Cancel Clear</button>
                        <button id="clearChatBtn" type="submit" class="CdsTYButton-btn-primary btn-sm">Clear Selected Messages</button>
                    </div>
                    
                </div>

                <div class="messages_read" id="messages_read">
                    @if($chat_empty)
                    @include("admin-panel.01-message-system.message-centre.empty-chat")
                    @else
                    @include('components.skelenton-loader.message-skeletonloader')
                    @endif
                </div>
            </form>

            <div id="block_msg{{$chat->id}}" class="blocked-chat" style="color:black">
                @if($chat->blocked_chat==1 )
                <h3>Chat has been blocked</h3>
                @endif
            </div>
            <!-- Typing message animation -->

        </div>
        <div class="typing-area position-relative">
            <div class="typing-chat" style="display: none;">
                <div class="typechat-message">typing...</div>
                <div class="typing-indicator">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
        <div class="reply-message " id="reply_quoted_msg" style="display: none">
            <div class="reply-icons">
                <i class="fa-solid fa-turn-up"></i>
                <i class="fa-solid fa-xmark" onclick="closeReplyto()"></i>
            </div>
            <p class="quoted-message">Reply quoted message</p><span class="username myChatReply{{ $chat->id }}"
                id="myreply">MY
                Reply</span>
        </div>
         <div class="message-input sendmsgwindow{{$chat->id}}" style="display: @if($chat->blocked_chat!=1 ){{'flex'}} @else {{'none'}}@endif"
            id="sendmsg">
            <!-- <div id="img-preview-div"></div> -->
            <div class="message-input-box">
                <div class="send-message-input" id="textareaWrapper" contenteditable="true">
                    <textarea placeholder="Enter Message" id="sendmsgg" name="send_msg" data-id="{{ $chat->id }}" class="dynamic-textarea">{{ isset($draft_message) ? $draft_message : '' }}</textarea>
                    <!-- <button id="uploadButton" >Upload</button> -->
                </div>
                <div class="message-emoji-icon emoji-icon">
                    <i class="fa-sharp fa-solid fa-face-smile"></i>
                </div>

                <div class="message-upload-file modal-toggle" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="fas fa-upload"></i>
                </div>
                <button id="sendBtn1">
                    <i class="fa-solid fa-send"></i>
                </button>
            </div>
            <div class="backtobottom">
                <span class="scroll-btn" id="scrollToBottom"><i class="fa-solid fa-angle-down fa-lg me-1"></i> <span class="d-none d-md-inline-block">Jump to Bottom</span></span>
            </div>
        </div>
    </div>
    @else
    @include('admin-panel.01-message-system.message-centre.blank_chat')
    @endif
    <div class="modal fade cdsTYDashboardModal-container01" id="uploadModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <form id="file-upload-form">
            @csrf
            <div class="modal-dialog modal-dialog-centered cdsTYDashboardModal-container01-inner">
                <div class="cdsTYDashboardModal-container01-inner-content modal-content">
                    <div class="cdsTYDashboardModal-container01-inner-content-header modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Upload Document</h5>
                        <button type="button" id="closemodal" class="cdsTYDashboardModal-container01-btn-close"
                            data-bs-dismiss="modal" aria-label="Close">X</button>
                    </div>
                    <div class="cdsTYDashboardModal-container01-inner-content-body modal-body">
                        <div class="cds-modal-content">
                            <div class="cdsTYDashboardModal-container01-upload-documentModal upload-documentModal">
                                <svg width="37" height="36" viewBox="0 0 37 36" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14 25.5V16.5L11 19.5" stroke="#6D6D6D" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M14 16.5L17 19.5" stroke="#6D6D6D" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M33.5 15V22.5C33.5 30 30.5 33 23 33H14C6.5 33 3.5 30 3.5 22.5V13.5C3.5 6 6.5 3 14 3H21.5"
                                        stroke="#6D6D6D" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M33.5 15H27.5C23 15 21.5 13.5 21.5 9V3L33.5 15Z" stroke="#6D6D6D"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p>Drag and drop your files here
                                    <span class="d-block">or click to browse your files</span>
                                </p>
                                <input type="file" name="attachment" id="attachment" multiple />
                                <div id="fileName" class="file-name"></div>
                            </div>
                            <span style="color:red;font-size:12px;">Allowed types are:
                                Images (JPG, PNG, GIF, BMP, WEBP, SVG),
                                Excel files (.xls, .xlsx),
                                PDFs,
                                Plain text files (.txt),
                                Audio files (MP3),
                                Video files (MP4, MPEG).";
                            </span>
                            <div id="filePreviewContainer"></div>
                        </div>
                    </div>
                    <div class="cdsTYDashboardModal-container01-inner-content-footer">
                        <div class="editor-container">
                            <input class="form-control" type="text" name="message" id="messagenew" placeholder="Write message here...">
                            <div class="cdsTYDashboard-discussion-panel-view-editor-custom-container-action-buttons">
                                <div class="message-emoji-icon-modal emoji-icon">
                                    <i class="fa-sharp fa-solid fa-face-smile"></i>
                                </div>
                                <button type="submit" class="cdsTYDashboardbutton-style01 cdsTYDashboardbutton-purple"
                                    id="sendBtnnew"><i class="fa-solid fa-send" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
   
    
    <!-- Group add modal -->
    <div class="modal fade" tabindex="-1" aria-labelledby="file-upload-modal-label" aria-hidden="true" id="file-upload-modal">
        <div class="modal-xl modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body w-100">
                    <div id="preview-container"></div>
                    <div class="drop-text">Drop files or paste the copied file here to upload</div>
                </div>

                <div class="modal-footer w-100">
                    <button type="button" type="button" class="CdsTYButton-btn-primary me-2" id="upload-button">Upload</button>
                    <button type="button" type="button" class="btn btn-dark close-upload-modal" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>