<div class="chatbot-header" data-chatId="{{$chatId}}">
    <div class="chatbot-user">
        <div class="chat-avatar">
            @if($user->profile_image != '')
            <img src="{{ $user->profile_image ? userDirUrl($user->profile_image, 't') : 'assets/images/default.jpg' }}"
                alt="Doris" />
            @else
            <div class="chatbot-user-icon" data-initial="{{ userInitial($user) }}"></div>
            @endif
            @if(loginStatus($user) == 1)
            <span class="status-online login-status chatOnlineStatus{{$chatId}}"></span>
            @else
            <span class="status-offline login-status chatOnlineStatus{{$chatId}}"></span>
            @endif
        </div>
        <div class="chatbot-name dropdown-toggle" title='{{$user->first_name . " " . $user->last_name}}' data-bs-toggle="dropdown" aria-expanded="false">
            {{$user->first_name . " " . $user->last_name}}
        </div>
        <div class="dropdown">
            <ul class="dropdown-menu">
                <div id="chatbot_block_unblock_div{{$chatId}}">
                    @if($chat->blocked_chat == 1 && $chat->blocked_by == auth()->user()->id)
                    <li><a class="dropdown-item" id="chatbot-unblock-chat" href="javascript:;"
                            onclick="unblockChat('{{$chatId}}')">Unblock <i class="fa-solid fa-unlock"></i></a></li>
                    @else
                    @if($chat->blocked_chat == 1 && $chat->blocked_by != auth()->user()->id)
                    @else
                    <li><a class="dropdown-item" id="chatbot-block-chat" href="javascript:;"
                            onclick="blockChat('{{$chatId}}')">Block <i class="fa-solid fa-circle-half-stroke"></i></a></li>
                    @endif
                    @endif
                </div>

                <li><a class="dropdown-item" href="{{baseUrl('message-centre/chat/' . $chat->unique_id)}}">Open Chat
                        Window <i class="fa-solid fa-window"></i></a></li>
                <li><a class="dropdown-item" href="javascript:;" onclick="deleteChat('{{$chatId}}')">Delete Chat <i class="fa fa-trash" aria-hidden="true"></i></a>
                </li>
                <li><a class="dropdown-item" onclick="clearChat('{{$chatId}}')" href="javascript:;">Clear Chat <i class="fa-solid fa-eraser" aria-hidden="true"></i></a></li>
            </ul>
        </div>
    </div>
    <input type="hidden" id="chatbot_reply_to_id{{$chatId}}">
    <input type="hidden" id="chatbot_edit_msg_id{{$chatId}}">

    <div class="chatbot-action">
        <button class="minimize-btn btn">
            <i class="fa-duotone fa-regular fa-angle-down cursor-pointer"></i>
        </button>
        <button type="button" class="btn chatbot-close" onclick="closeBot('{{$chatId}}','userChat')"><i
                class="fa fa-close"></i></button>
    </div>
</div>


<div class="chatbot-content chatbotmsgBody" id="chatBotContent-{{$chatId}}">
    <form id="clear-bot-messages{{$chatId}}" class="cds-clearBot">
        @csrf
        <div style="display:none" id="chatbotSelectAllDiv{{$chatId}}" class="select-all-checkbox">
            <div class="cds-clearBox">
                <label class="cds-checkbox ">
                    <input type="checkbox" onchange="selectAllChatbotCheckbox('{{$chatId}}')"
                        id="checkboxSelectAll{{$chatId}}" class="checkbox" />
                    <span class="checkmark"></span>
                    <span class="selectAll">Select All</span>
                </label>
            </div>
            <div class="cds-action-btn">
                <button onclick="cancelClear('{{$chatId}}')" id="cancelClearr" type="button"
                    class="btn btn-dark btn-sm">Cancel Clear</button>
                <button onclick="clearChatBtn('{{$chatId}}')" id="clearChatBtnn" type="button"
                    class="CdsTYButton-btn-primary btn-sm">Clear Selected Messages</button>
            </div>
        </div>
    </form>

    <div class="chatbot-body" data-id="{{ $chatId }}" id="chatBody-{{$chatId}}"></div>
    <div class="chatbot-upload-file" data-id="{{ $chatId }}" style="display:none;"
        id="chatbot-upload-file-{{ $chatId }}">
        <div class="chatbot-file-uploader" id="preview-container-{{$chatId}}"></div>
        <div class="drop-text">Drop files or paste the copied file here to upload</div>
        <div class="chatbot-file-uploader-footer">
            <button type="button" type="button" class="CdsTYButton-btn-primary" data-id="{{ $chatId }}"
                id="upload-button-{{$chatId}}">Upload</button>
            <button type="button" type="button" data-id="{{ $chatId }}"
                class="btn btn-dark btn-sm cb-close-uploader">Close</button>
        </div>
    </div>
    <div class="chatbot-footer">
        <div class="input-area">
            <div class="chatbotreply">
                <div class="reply-message" id="chatbot_reply_quoted_msg{{ $chatId }}" style="display: none;">
                    <div class="reply-icons">
                        <i class="fa-solid fa-turn-up"></i>
                        <i class="fa-solid fa-xmark" onclick="closeChatBotReplyto('{{ $chatId }}')"></i>
                    </div>
                    <p class="quoted-message">Reply quoted message</p>
                    <span class="username myChatReply{{ $chatId }}" id="myreply">MY Reply</span>
                </div>
            </div>
            <div class="chatbot-msg-send" style="display:{{ $chat->blocked_chat == 0 ? 'block' : 'none' }}">
                <div class="typing-area position-relative">
                    <div class="typing-chat" id="typing-chatbot-{{ $chatId }}" style="display: none;">
                        <div class="typechat-message">typing...</div>
                        <div class="typing-indicator">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 w-100">
                    <input type="text" class="chatInput" data-id="{{$chatId}}" id="chatbot-{{$chatId}}-sendmsg" placeholder="Type a message..." />
                    <div class="chatbot-emoji-icon emoji-icon">
                        <i class="fa-sharp fa-solid fa-face-smile"></i>
                    </div>
                    <form id="file-upload" method="POST" enctype="multipart/form-data">
                        <label for="attachment" class="chatbot-emoji-icon emoji-icon"
                            style="cursor: pointer; position: relative;">
                            <input type="file" class="chatbotfileupload" name="attachment[]" id="attachmentnew"
                                onchange="uploadAtt(this,'{{$chatId}}')" required multiple />
                            <!-- <i class="fas fa-file-upload"></i> -->
                            <i class="fas fa-upload" aria-hidden="true"></i>
                        </label>
                    </form>
                    <button onclick="sendChatBotMessage('{{$chatId}}')">
                        <i class="fa-solid fa-send"></i>
                    </button>
                </div>
            </div>
            <div class="text-center w-100 chatbot-blocked" style="display:{{ $chat->blocked_chat == 1 ? 'block' : 'none' }}">
                @if($chat->blocked_chat == 1 && $chat->blocked_by == auth()->user()->id)
                    Chat has been blocked
                @else
                    You cannot send message
                @endif
            </div>
            </div>
            <div class="chatboat-backtobottom">
                <span class="scroll-btn chatboat-scrollToBottom" id="chatboat-scrollToBottom-{{$chatId}}"><i
                    class="fa-solid fa-angle-down fa-lg"></i></span>
                </div>
            </div>
           
            </div>
</div>
<script>
cbChatBotContent[{{$chatId}}] = document.getElementById("chatbot-{{ $chatId }}");
cbFileUploadArea[{{$chatId}}] = document.getElementById("chatBody-{{ $chatId }}");
cbModal[{{$chatId}}] = document.getElementById("chatbot-upload-file-{{ $chatId }}");
cdPreviewContainer[{{$chatId}}] = document.getElementById("preview-container-{{ $chatId }}");
cbUploadButton[{{$chatId}}] = document.getElementById("upload-button-{{ $chatId }}");
cbSelectedFiles[{{$chatId}}] = []; // Store selected files
cbFormFiles[{{$chatId}}] = [];
cbMessageInput[{{$chatId}}] = document.getElementById("chatbot-{{$chatId}}-sendmsg");

cbFileUploadArea[{{$chatId}}].addEventListener("dragover", (e) => {
    e.preventDefault();
    if (e.dataTransfer && e.dataTransfer.items) {
        for (let i = 0; i < e.dataTransfer.items.length; i++) {
            if (e.dataTransfer.items[i].kind === "file") {
                $('.chatbot-upload-file').hide();
                $('.group-chatbot-upload-file').hide();
                $("#chatbot-upload-file-{{ $chatId }}").show();
                $("#preview-container-{{$chatId}}").css("border", "1px solid red");
                break; // Stop checking after finding a file
            }
        }
    }
   
});

cbModal[{{$chatId}}].addEventListener("dragover", function(e) {
    e.preventDefault();
});

// Detect file drop inside modal
cbModal[{{$chatId}}].addEventListener("drop", function(e) {
    e.preventDefault();
    cbUploadFiles(e.dataTransfer.files, '{{ $chatId }}');
});
cbMessageInput[{{$chatId}}].addEventListener("paste", function(event) {
    // $("#file-upload-modal").modal("show");

    var items = (event.clipboardData || event.originalEvent.clipboardData).items;
    var files = [];
    var hasNonTextData = false;
    for (var item of items) {
        if (item.kind === "file") {
            files.push(item.getAsFile());
            hasNonTextData = true;
        } else if (!item.type.startsWith("text")) {
            hasNonTextData = true;
        }
    }
    if (files.length > 0) {
        $("#chatbot-upload-file-{{ $chatId }}").show();
        cbUploadFiles(files, "{{ $chatId }}");
    }
});
cbUploadButton[{{$chatId}}].addEventListener("click", function() {

    var replyTo = $('#chatbot_reply_to_id{{$chatId}}').val();
    var myurl = BASEURL + '/message-centre/send-msg/{{ $chatId }}';

    if (cbFormFiles[{{$chatId}}].length === 0) {
        errorMessage("Upload files to upload");
        return false;
    }
    $("#upload-button").attr("disabled", "disabled");
    var formData = new FormData();
    formData.append("_token", "{{ csrf_token() }}");

    cbFormFiles[{{$chatId}}].forEach((file) => {
        formData.append("attachment[]", file);
    });
    formData.append('reply_to', replyTo);
    formData.append('send_msg', $('#chatbot-{{$chatId}}-sendmsg').val());

    fetch(myurl + "?sendmsg", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status == true) {
                successMessage("Files uploaded successfully!");
                // modal.style.display = "none";
                //   location.reload();
                $("#chatbot-upload-file-{{ $chatId }}").hide();
                cbResetPreview('{{ $chatId }}');
            } else {
                errorMessage("Upload failed: " + data.message);
                $("#chatbot-upload-file-{{ $chatId }}").hide();
            }
        })
        .catch(error => console.error("Upload Error:", error));
});
</script>
<script>
    function leaveGroup() {
        $('.leave-this-group').click();
    }
    $(document).ready(function() {
        var chatBotId = "{{$chatId}}";
        var chatBox = $("#chatBody-{{ $chatId }}");
        var topBtn = $("#scrollToTop");
        console.log(topBtn + "hello");
        var bottomBtn = $("#chatboat-scrollToBottom-" + chatBotId);
        var hideTimeout, scrollTimeout;
        var lastScrollTop = chatBox.scrollTop();

        function showButton(btn, delay) {
            setTimeout(function() {
                btn.addClass("show");
                clearTimeout(hideTimeout);
                hideTimeout = setTimeout(function() {
                    btn.removeClass("show");
                }, 3000);
            }, delay);
        }

    // Scroll to bottom function
    bottomBtn.click(function() {
        chatBox.animate({
            scrollTop: chatBox[0].scrollHeight
        }, 500);
        showButton(bottomBtn, 0);
    });

    // Scroll to top function
    topBtn.click(function(e) {
        e.preventDefault();
        chatBox.animate({
            scrollTop: 0
        }, 500);
        showButton(topBtn, 0);
    });

    // Detect scrolling
    chatBox.on("scroll", function() {
        var scrollPos = chatBox.scrollTop();
        var scrollHeight = chatBox[0].scrollHeight - chatBox.outerHeight();

        // Hide bottom button when at the bottom
        if (scrollPos >= scrollHeight - 5) {
            bottomBtn.removeClass("show");
        } else {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(function() {
                showButton(bottomBtn, 0);
            }, 2000);
        }

        // Check scrolling direction
        if (scrollPos > lastScrollTop) { // Scrolling down
            showButton(topBtn, 0);
        }

        lastScrollTop = scrollPos;
    });

    // Auto-hide bottom button if already at bottom on load
    if (chatBox.scrollTop() >= chatBox[0].scrollHeight - chatBox.outerHeight() - 5) {
        bottomBtn.removeClass("show");
    } else {
        showButton(bottomBtn, 0);
    }
});
</script>