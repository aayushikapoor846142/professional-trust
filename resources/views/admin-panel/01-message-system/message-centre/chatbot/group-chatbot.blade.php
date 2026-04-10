<div class="chatbot-header group-chatbot-header"  data-group-chatId="{{$groupId}}">
    <div class="chatbot-user">
        <div class="chat-avatar">
            @if($group->group_image != '')
            <img src="{{ $group->group_image ? groupChatDirUrl($group->group_image, 't') : 'assets/images/default.jpg' }}"
            alt="Doris" />
            @else
            @php
            $initial = strtoupper(substr($group->name, 0, 1));
            @endphp
            <div class="chatbot-user-icon" data-initial="{{ $initial }}"></div>
            @endif
        </div>
        <div class="chatbot-name dropdown-toggle" title='{{$group->name}}' data-bs-toggle="dropdown" aria-expanded="false">
            {{ strlen($group->name) > 10 ? substr($group->name, 0, 10) . '...' : $group->name }}
        </div>

        <div class="dropdown">
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="{{ baseUrl('group/chat/'.$group->unique_id) }}">Open Group Window  <i class="fa-solid fa-window"></i></a>
                </li>
                @if($currentGroupMember!=NULL && $currentGroupMember->is_admin==1)
                <li><a class="dropdown-item  modal-toggle" data-modal="addMember"
                    onclick="openCustomPopup(this)" data-href="<?php echo baseUrl('group/add-new-members/' . $group->unique_id) ?>"
                    href="javascript:;">Add New Member <i class="fa-regular fa-users-medical"></i></a></li>
                    @endif
                    <li><a class="dropdown-item  modal-toggle" data-modal="viewGroupMembers"
                        onclick="showPopup('<?php echo baseUrl('group/view-group-members/' . $group->unique_id) ?>')"
                        href="javascript:;">View Group Members <i class="fa-solid fa-users" aria-hidden="true"></i></a></li>
                        <li><a class="dropdown-item" onclick="clearGrpChat('{{$groupId}}')" href="javascript:;">Clear Chat <i class="fa-solid fa-eraser" aria-hidden="true"></i></a>
                        </li>
                        <li>
                            <a href="javascript:;" class="leave-this-group dropdown-item" title="Leave Group"
                            data-action="leave this Group" onclick="checkRemoveadmin(this)"
                            data-href="{{ baseUrl('group/remove-group-member/'.$member->id) }}">
                            Leave Group <i class="fa-solid fa-right-from-bracket"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <input type="hidden" value="" id="group_chatbot_reply_to_id{{$groupId}}">
        <input type="hidden" value="" id="grp_chatbot_edit_msg_id{{$groupId}}">
        <!--  onclick="toggleGroupChatbot('{{$groupId}}')" -->
        <div class="chatbot-action">
            <a class="btn text-white d-none" href="{{ baseUrl('group/chat/'.$group->unique_id) }}">
                <i class="fa fa-external-link-alt"></i>
            </a>
            <button class="minimize-btn btn" >
                <i class="fa-duotone fa-regular fa-angle-down cursor-pointer"></i>
            </button>
            <button type="button" class="btn chatbot-close" onclick="closeBot('{{$groupId}}','groupChat')"><i
                class="fa fa-close"></i></button>
            </div>
        </div>
        <div class="chatbot-content group-chatbot-content chatbotmsgBody" id="groupChatBotContent-{{$groupId}}">
            <form id="clear-grpbot-messages{{$groupId}}" class="cds-clearBot">
                @csrf
                <div style="display:none" id="grpbotSelectAllDiv{{$groupId}}" class="select-all-checkbox">
                    <div class="cds-clearBox">
                        <label class="cds-checkbox ">
                            <input type="checkbox" onchange="selectAllGrpbotCheckbox('{{$groupId}}')"
                            id="checkboxGrpSelectAll{{$groupId}}" class="checkbox" />
                            <span class="checkmark"></span>
                            <span class="selectAll">Select All</span>
                        </label>
                    </div>
                    <div class="cds-action-btn">
                        <button onclick="cancelGrpClear('{{$groupId}}')" id="cancelClearGrp" type="button"
                        class="btn btn-dark btn-sm">Cancel Clear</button>
                        <button onclick="clearGrpBtn('{{$groupId}}')" id="clearChatBtnGrp" type="button"
                        class="CdsTYButton-btn-primary btn-sm">Clear Selected Messages</button>
                    </div>
                </div>
            </form>
            <div class="chatbot-body" data-id="{{ $groupId }}" id="groupChatBody-{{$groupId}}"></div>
            <div class="group-chatbot-upload-file" data-id="{{ $groupId }}" style="display:none;"
            id="group-chatbot-upload-file-{{ $groupId }}">
            <div class="group-chatbot-file-uploader" id="group-preview-container-{{$groupId}}"></div>
            <div class="drop-text">Drop files or paste the copied file here to upload</div>
            <div class="group-chatbot-file-uploader-footer">
                <button type="button" type="button" class="CdsTYButton-btn-primary" data-id="{{ $groupId }}"
                id="group-upload-button-{{$groupId}}">Upload</button>
                <button type="button" type="button" data-id="{{ $groupId }}"
                class="btn btn-dark btn-sm gb-close-uploader">Close</button>
            </div>
        </div>
        
        <div class="chatbot-footer" >
            <div class="input-area">
                <div class="chatbotreply">
                    <div class="reply-message chatbot_reply_quoted_msg" id="group_chatbot_reply_quoted_msg{{ $groupId }}"
                    style="display: none;">
                    <div class="reply-icons">
                        <i class="fa-solid fa-turn-up"></i>
                        <i class="fa-solid fa-xmark" onclick="closeGrpChatBotReplyto('{{ $groupId }}')"></i>
                    </div>
                    <p class="quoted-message">Reply quoted message</p>
                    <span class="username myChatReply{{ $groupId }}" id="myreply">MY Reply</span>
                </div>
            </div>
            <div class="typing-area position-relative">
                <div class="typing-chat" id="typing-groupbot-{{ $groupId }}" style="display: none;">
                    <div class="typechat-message"><span class="membertyping"></span> typing...</div>
                    <div class="typing-indicator">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
            <div class="@if((checkGroupPermission('only_admins_can_post', $groupId)) || ($currentGroupMember->is_admin==1)) d-flex @else  d-none @endif align-item-center gap-2 w-100" >
                <div class="group-bot-input-wrapper">
                    <ul id="memberSuggestions-{{ $groupId }}" class="cds-autocomplete-suggestions"></ul>
                    <input type="text" class="groupChatInput" data-id="{{$groupId}}"
                    id="group-chatbot-{{$groupId}}-sendmsg" placeholder="Type a message..." />
                </div>
                <div class="chatbot-emoji-icon emoji-icon">
                    <i class="fa-sharp fa-solid fa-face-smile"></i>
                </div>
                <form id="file-upload-{{ $groupId }}" method="POST" enctype="multipart/form-data">
                    <label for="attachment" class="chatbot-emoji-icon emoji-icon"
                    style="cursor: pointer; position: relative;">
                    <input type="file" class="chatbotfileupload" name="attachment[]"
                    id="group-attachment-{{ $groupId }}" onchange="uploadGroupAtt(this,'{{$groupId}}')" required
                    multiple />
                    <!-- <i class="fas fa-file-upload"></i> -->
                    <i class="fas fa-upload" aria-hidden="true"></i>
                </label>
            </form>
            <button onclick="sendGroupChatBotMessage('{{$groupId}}')">
                <i class="fa-solid fa-send"></i>
            </button>
        </div>
        @if(!(checkGroupPermission('only_admins_can_post', $groupId)) && (!$currentGroupMember->is_admin==1))
        <div class="text-center w-100 chatbot-blocked">                                   
            <div class="cdsOnlyMsg">Only admins can post.</div>                                   
        </div>
        @endif

    </div>
    <div class="chatboat-backtobottom">
        <span class="scroll-btn chatboat-scrollToBottom" id="group-chatboat-scrollToBottom-{{$groupId}}"><i
            class="fa-solid fa-angle-down fa-lg"></i></span>
        </div>
    </div>
</div>

<script>
    function leaveGroup() {
        $('.leave-this-group').click();
    }
    $(document).ready(function() {
        var groupChatBotId = "{{$groupId}}";
        var chatBox = $("#groupChatBody-{{$groupId}}");
        var topBtn = $("#scrollToTop");
        var bottomBtn = $("#group-chatboat-scrollToBottom-" + groupChatBotId);
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

<script>
    gbFileUploadArea[{{$groupId}}] = document.getElementById("groupChatBody-{{ $groupId }}");
    gbModal[{{$groupId}}] = document.getElementById("group-chatbot-upload-file-{{ $groupId }}");
    gdPreviewContainer[{{$groupId}}] = document.getElementById("group-preview-container-{{ $groupId }}");
    gbUploadButton[{{ $groupId}}] = document.getElementById("group-upload-button-{{ $groupId }}");
    gbSelectedFiles[{{$groupId}}] = []; // Store selected files
    gbFormFiles[{{$groupId}}] = [];
    gbMessageInput[{{$groupId}}] = document.getElementById("group-chatbot-{{$groupId}}-sendmsg");

    gbFileUploadArea[{{$groupId}}].addEventListener("dragover", (e) => {
        e.preventDefault();
        if (e.dataTransfer && e.dataTransfer.items) {
            for (let i = 0; i < e.dataTransfer.items.length; i++) {
                if (e.dataTransfer.items[i].kind === "file") {
                    $('.group-chatbot-upload-file').hide();
                    $('.chatbot-upload-file').hide();
                    $("#group-chatbot-upload-file-{{ $groupId }}").show();
                    $("#group-preview-container-{{$groupId}}").css("border", "1px solid red");
                    break; // Stop checking after finding a file
                }
            }
        }
    });

gbModal[{{$groupId}}].addEventListener("dragover", function(e) {
    e.preventDefault();
});


// Detect file drop inside modal
gbModal[{{$groupId}}].addEventListener("drop", function(e) {
    e.preventDefault();
    gbUploadFiles(e.dataTransfer.files, '{{ $groupId }}');
});
gbMessageInput[{{$groupId}}].addEventListener("paste", function(event) {
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
        $("#chatbot-upload-file-{{ $groupId }}").show();
        gbUploadFiles(files, "{{ $groupId }}");
    }
});
gbUploadButton[{{$groupId}}].addEventListener("click", function() {

    var replyTo = $('#group_chatbot_reply_to_id{{$groupId}}').val();
    var myurl = BASEURL + '/group/send-msg/{{ $groupId }}';

    if (gbFormFiles[{{$groupId}}].length === 0) {
        errorMessage("Upload files to upload");
        return false;
    }
    $("#upload-button").attr("disabled", "disabled");
    var formData = new FormData();
    formData.append("_token", "{{ csrf_token() }}");

    gbFormFiles[{{$groupId}}].forEach((file) => {
        formData.append("attachment[]", file);
    });
    formData.append('reply_to', replyTo);
    formData.append('send_msg', $('#group-chatbot-{{$groupId}}-sendmsg').val());

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
                $("#group-chatbot-upload-file-{{ $groupId }}").hide();
                gbResetPreview('{{ $groupId }}');
            } else {
                errorMessage("Upload failed: " + data.message);
                $("#group-chatbot-upload-file-{{ $groupId }}").hide();
            }
        })
    .catch(error => console.error("Upload Error:", error));
});
</script>