
@if($group!=NULL)

<!-- Chat Messages -->
<div class="chat-messages grp-messages" id="grpMessages{{$group_id}}">
    <input type="hidden" value="{{baseUrl('group/send-msg/'.$group_id)}}" id="geturl" />
    <input type="hidden" value="" id="group_reply_to_id" />
    <input type="hidden" value="" id="grp_edit_message_id" />

    <input type="hidden" value="{{$group_id}}" id="get_group_id" />
    <div class="message-header cds-msgHead">
        <div class="message-header-block cds-groupHeader">
            <div class="message-title">
                <div class="back-chats" onclick="backToChats()">
                    <i class="fa-solid fa-angle-left"></i>
                </div>
                @if($group->type == 'Private')
                <div class="group-badge private-group">
                    {{ $group->type }}
                </div>
                @else
               
                @endif
                @if($group->group_image)
                <img src="{{ groupChatDirUrl($group->group_image, 't') }}" alt="Doris">
                @else
                @php
                $initial = strtoupper(substr($group->name, 0, 1));
                @endphp
                <div class="group-icon" data-initial="{{$initial}}"></div>
                @endif
                <!-- <p class="chat-name"> -->
                <h3 class="chat-name" id="headerGroupName">
                    {{ strlen($group->name) > 20 ? substr($group->name, 0, 20) . '...' : $group->name }}
                </h3>

                <!-- </p> -->
            </div>

            <div class="message-action-btn">
                <div class="group-members-list mobile-hide" onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('group/get-group-info/'.$group_id) }}">
                    <ul>
                        @php
                        $totalmembers = $group_members->count();
                        // dd($totalmembers);
                        @endphp
                        @foreach($group_members as $key => $member)
                        @if($key < 2) @if($member->member && $member->deleted_at==NULL)
                            <li>
                                <a href="javascript:;"
                                    title="{{$member->member->first_name.' '.$member->member->last_name}}">
                                    <div>
                                        <div class="chat-avatar">
                                            @php
                                            $initial = userInitial($member->member);
                                            $imageUrl = $member->member->profile_image ? userDirUrl($member->member->profile_image, 't') : null;
                                                @endphp
                            
                                            @if($imageUrl)
                                             <img src="{{ $imageUrl }}" alt="User Image">
                                                @else
                                            <div class="group-icon">{{ $initial }}</div>
                                            @endif 
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @endif
                            @endif
                            @endforeach
                            @if($totalmembers > 2)
                            <li>
                                <div>
                                    <div class="chat-avatar cds-grp-total-member">
                                       +{{$totalmembers - 2}}
                                    </div>
                                </div>
                            </li>
                            @endif
                    </ul>
                </div>
                <div class="search-chats">
                    <i class="fa-regular fa-magnifying-glass" onclick="toggleChatsSearch('open')" alt="Search" title="Search"></i>
                </div>
                <a href="javascript:;" onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('group/get-group-info/'.$group_id) }}" class="cdsiconlist mobile-hide">
                   <i class="fa-regular fa-user" alt="Group Info" title="User"></i>
                </a>  
                @if($currentGroupMember!=NULL && $currentGroupMember->is_admin==1)
                <div onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('group/get-group-join-request/'.$group_id) }}" class="group-req-header mobile-hide">
                    <!-- <i class="fa fa-plus-circle" aria-hidden="true"></i> -->
                    <i class="fa-regular fa-user-plus" title="User add"></i>
                    <span class="m-0 join-rqst-counter">@if(groupJoinRequestCount($group_id)>0){{groupJoinRequestCount($group_id) }}@endif</span>
                </div>
                
                @endif
                <div onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('group/get-shared-file/'.$group_id) }}"  class="cdsiconlist mobile-hide">
                    <i class="fa-regular fa-folder" title="File Folder"></i>
                </div>
                        @if($currentGroupMember!=NULL && $currentGroupMember->is_admin==1)

                        <div class="mobile-hide">
                            <a href="{{baseUrl('group-settings/'.$group->unique_id)}}" class="cdsiconlist">
                            <i class="fa-solid fa-gear"></i>
                            </a>
                        </div>

                        @endif
                 @if((checkGroupPermission('members_can_add_members', $group_id)) || ($currentGroupMember!=NULL && $currentGroupMember->is_admin==1))
                <i class="fa-regular fa-users-medical modal-toggle" data-modal="addMember" onclick="openCustomPopup(this)" data-href="<?php echo baseUrl('group/add-new-members/' . $group->unique_id) ?>" title="Add New Member"></i>
                @endif
                <div class="dropdown chat-dropdown">
                    <button class="btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fa-solid fa-ellipsis" title="More"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a id="clear_chat" class="dropdown-item" href="javascript:;">Clear Chat <i class="fa-solid fa-eraser"></i></a>
                        </li>
                        <li class="mobile-show">
                            <a onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('group/get-group-info/'.$group_id) }}"  class="dropdown-item">
                                Group Info <i class="fa-solid fa-user-group"></i>
                            </a>
                        </li>
                        <li class="mobile-show">
                            <a onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('group/get-group-join-request/'.$group_id) }}" class="dropdown-item">
                                Group Requests {{ groupJoinRequestCount($group_id) }} <i class="fa-solid fa-users-medical"></i>
                            </a>
                        </li>
                        <li class="mobile-show">
                            <a onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('group/get-shared-file/'.$group_id) }}" class="dropdown-item">
                                Group Files <i class="fa-solid fa-folder-open"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="search-chats-toggle" id="chatsSearch" style="display: none;">
            <input type="text" id="search_input" placeholder="Search here..." />
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
    <div class="message-content cds-chatBodyFullView pt-0" id="scrollDiv">
        <form id="clear-messages">
            @csrf
            <div style="display: none;" id="selectAllDiv" class="select-all-checkbox">
                <div>
                    <div>                    
                        <label class="cds-checkbox">
                            <input type="checkbox" id="selectAll" />
                            <span class="checkmark"></span>
                        </label>
                        <label for="selectAll">Select All</label>
                    </div>
                </div>
                <div class="cds-action-btn">
                    <button id="cancelClear" type="button" class="btn btn-dark btn-sm">Cancel Clear</button>
                    <button id="clearChatBtn" type="submit" class="CdsTYButton-btn-primary btn-sm">Clear Selected Messages</button>
                </div>
            </div>

            <div class="messages_read" id="messages_read">
                @if($chat_empty)
                @include("admin-panel.01-message-system.group-chat.chat.empty-group")
                @else
                @include('components.skelenton-loader.message-skeletonloader')
                @endif
            </div>
        </form>
    </div>
    <div class="typing-area position-relative">
        <div class="typing-chat" style="display: none;">
            <div class="typechat-message"><span class="membertyping"></span> typing...</div>
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
        <p class="quoted-message">Reply quoted message</p>
        <span class="username myChatReply{{ $group->id }}" id="myreply">MY Reply</span>
    </div>
    <div class="position-relative">
        <div id="userList" class="user-list" style="display: none;"></div>
    </div>
    <div class="message-input" id="sendmsg">
        
        @if((checkGroupPermission('only_admins_can_post', $group_id)) || ($currentGroupMember->is_admin==1))
         
        <div class="message-input-box">
            <div class="send-message-input" id="textareaWrapper" contenteditable="true">
                <ul id="memberSuggestions" class="cds-autocomplete-suggestions"></ul>
                <textarea placeholder="Enter Message" id="sendmsgg" data-id="{{ $group_id }}" class="dynamic-textarea" name="send_msg"></textarea>
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
        @else
         {{'only Admins can post.'}}
        @endif
        <div class="backtobottom">
            <span class="scroll-btn" id="scrollToBottom"><i class="fa-solid fa-angle-down fa-lg me-1"></i> <span class="d-none d-md-inline-block">Jump to Bottom</span></span>
        </div>
    </div>
   
    
</div>

@else 
@include('admin-panel.01-message-system.group-chat.chat.blank_chat') @endif
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
{{--<form id="file-upload-form">
    @csrf
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" id="uploadModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Upload Document</h5>
                    <button type="button" id="closemodal" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="file-upload-form">
                        @csrf
                        <div class="cds-modal-content">
                            <div class="upload-documentModal">
                                <svg width="37" height="36" viewBox="0 0 37 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14 25.5V16.5L11 19.5" stroke="#6D6D6D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M14 16.5L17 19.5" stroke="#6D6D6D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M33.5 15V22.5C33.5 30 30.5 33 23 33H14C6.5 33 3.5 30 3.5 22.5V13.5C3.5 6 6.5 3 14 3H21.5" stroke="#6D6D6D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M33.5 15H27.5C23 15 21.5 13.5 21.5 9V3L33.5 15Z" stroke="#6D6D6D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p>
                                    Drag and drop your files here
                                    <span class="d-block">or click to browse your files</span>
                                </p>
                                <input type="file" name="attachment" id="attachment" required />
                                <div id="fileName" class="file-name"></div>
                            </div>
                            <span style="color: red; font-size: 12px;">
                                Allowed types are: Images (JPG, PNG, GIF, BMP, WEBP, SVG), Excel files (.xls, .xlsx), PDFs, Plain text files (.txt), Audio files (MP3), Video files (MP4, MPEG).";
                            </span>
                            <input class="form-control" type="text" name="message" id="messagenew" placeholder="Write message here..." />
                            <button type="submit" class="upload-modalbtn" id="sendBtnnew">Send</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</form> --}}
<!-- End Content -->

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
