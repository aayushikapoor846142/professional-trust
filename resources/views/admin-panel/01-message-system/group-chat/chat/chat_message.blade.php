@section('page-submenu')
{!! pageSubMenu('message') !!}
@endsection
<section class="cdsTYOnboardingDashboard-breadcrumb-section">
    <div class="cds-dashboard-chat-main-container-header">
        @include('admin-panel.01-message-system.message-centre.invite-users')
    </div>
</section>
<section class="CDSDashboardProfessional-main-container-body-inner-0">
    @php $openfor= request()->get('openfor'); @endphp
    <div class="cdsTYOnboardingDashboard-chat-main-container">
        @include('admin-panel.01-message-system.message-centre.chat_sidebar_header_common')

        <div class="chat-container" id="chat-container">
            <!-- <div class="loader">
                    <div class="text">Loading, please wait...</div>
                </div> -->
            @include('admin-panel.01-message-system.message-centre.chat_sidebar_common')

            <div class="message-container">
                @include("admin-panel.01-message-system.group-chat.chat.message-container")
            </div>
        </div>
    </div>
</section>

@php
$loader_html = minify_html(view("components.skelenton-loader.message-skeletonloader")->render());
@endphp
@section('javascript')

<script type="text/javascript">
    
var baseUrl = "{{ baseUrl('/') }}";
var openfor = '{{ $openfor }}';
function openInMobile(){
    //if (openfor === "mobile") {
        let url = window.location.pathname;
        let lastParam = url.split("/").filter(Boolean).pop();
        let attemptCount = 0;

        let checkExist = setInterval(function () {
            let $el = $('.groupchatdiv' + lastParam);
            if ($el.length) {
                clearInterval(checkExist);
               // alert('hello');
                $el.trigger('click');
            }

            attemptCount++;
            if (attemptCount > 50) { // Stop after 50 attempts (~15 seconds)
                clearInterval(checkExist);
                console.log("Element not found after multiple attempts.");
            }
        }, 300);
    //}
}
$(document).ready(function() {
    setTimeout(function () {
   openInMobile();
}, 2000); // 2-second delay

    conversationList();
    const chat = "{{$groupdata}}"
    if (chat.length === 0) {
        $('#sloader').hide();
    }
    
});

var loader_html = '{!! $loader_html !!}';
let users = '';
@if(($chat_members ?? '') != '')
users = '{{$chat_members}}';
@endif
let userId = '{{ auth()->user()->id }}';
var last_msg_id = 0;
var draftTimer;
let group_id = 0;
let prev_group_id = 0;
let cssdisplay = $('#chatsSearch').css('display');
let typingTimer; // Timer identifier
var typingDelay = 5000;
var last_msg_id = 0;
var last_react_id = 0;
var first_msg_id = 0;
var isFetching = false;
let other_last_group_id = 0;
var filesToUpload = [];
var baseUrl = "{{ baseUrl('/') }}";
let selectedFile = [];
$(window).on("load", function () {
    openInMobile();
    

    console.log("Page and all resources are fully loaded!");
});

document.addEventListener('DOMContentLoaded', async function() {
               
    if('{{$group_id}}'){
        await initializeGroupChat("{{$group_id}}");
        await initializeSocket("{{$group_id}}");
    }

        
});
$(document).ready(function() {
    $(document).on("click", ".message-upload-file", function() {
        closeRightSlidePanel();
      // $('.chat-profile-card').hide();
    });
    //alert(userId + 'smaks');
    $(document).on("click", ".group-chat-item", function() {
        $(".loader").show();
        if (window.innerWidth < 991) {
            $(".message-container").addClass("active");
        }$(".group-chat-item").removeClass("active-chat");
    $(this).addClass("active-chat");
        var url = $(this).data("href");
        var conversation_id = $(this).data("unique-id");
        var groupid = $(this).data("group-id");
        loadChatAjax(conversation_id, groupid);
        history.pushState(null, '', url);
    });
    $(document).on("click", ".other-group-info", function() {
        console.log("akki")
        if (window.innerWidth < 991) {
            $(".message-container").addClass("active");
        }
        var url = $(this).data("href");
        var conversation_id = $(this).data("unique-id");
        var groupid = $(this).data("group-id");
        loadGroupInfo(conversation_id, groupid);
        history.pushState(null, '', url);
    });
});

function resetChatState() {
    // Unbind events or clear existing intervals
    clearChatEvents();
    // Optionally reset DOM or variables related to the previous chat
}

function clearChatEvents() {
    // Example: Remove existing event listeners on the message container
    $(".message-container").off();
}

function initializeSocket(groupId, prevGroupId = '') {
    if (prevGroupId != '') {
        window.Echo.leave(`group-chat.${prevGroupId}`);
        window.Echo.leave(`group_message_reaction`);
    }
    initializeGroupSocket(groupId);
}

function initializeGroupChat(groupId) {
    var chatArea = document.getElementById("grpMessages" + groupId);
    var modal = document.getElementById("file-upload-modal");
    var previewContainer = document.getElementById("filePreviewContainer");
    var uploadButton = document.getElementById("upload-button");
    var sendInput = document.getElementById("sendmsgg");
    var selectedFiles = []; // Store selected files
    var formFiles = [];
    
    prev_group_id = groupId
    last_msg_id = 0;
    group_id = groupId;
    prev_group_id = 0;
    cssdisplay = $('#chatsSearch').css('display');
    last_msg_id = 0;
    last_react_id = 0;
    first_msg_id = 0;
    isFetching = false;
    draftTimer = '';
    selectedFile = [];
    ACTIVE_GROUP_CHAT = group_id;
    if (document.querySelector("#sendmsgg")) {

        new EmojiPicker(".message-emoji-icon", {
            targetElement: "#sendmsgg"
        });
        $(document).on("blur", "#sendmsgg", function() {
            handleGrpWindowBlur(this);
        });
    }

    function handleGrpWindowBlur(input) {
        if (input.value.trim() === "") {
            input.value = "";
            $("#grp_edit_message_id").val("");

            console.log("Blur function called!");
        }
    }
    if(document.getElementById("scrollDiv")){
    document.getElementById("scrollDiv").addEventListener("scroll", function() {
        const scrollDiv = this;
        if (scrollDiv.scrollTop === 0 && !isFetching) {
            // Fetch older messages
            if (first_msg_id > 0) {
                fetchOlderChats();
            }
        }
    });
    }
    // 
    if (group_id != "" && group_id > 0) {
        $(document).ready(function() {
            // fetchChats(group_id);
            fetchGroupChatBotMessages(group_id);
            // updateTypingStatus(0)
        });
        const editorDiv = $('#sendmsgg'); // Input area
        let typingTimeout; // Timer for typing status
        let isTyping = false;
        editorDiv.on('input', function() {
            const inputLength = $(this).val().length;
            if (inputLength > 2 && !isTyping) {
                updateTypingStatus(1);
            } else {
                // updateTypingStatus(0)
            }
            // const inputText = $(this).val().trim();
            // const lastChar = inputText.slice(-1);
            // const atSymbolPosition = inputText.lastIndexOf('@');
            // if (lastChar === '@') {
            //     showUserList(inputText,group_id);
            // } else if (atSymbolPosition !== -1) {
            //     const query = inputText.slice(atSymbolPosition + 1).toLowerCase();

            //     if (query === 'everyone') {
            //         showMentionAllOption();
            //     } else {
            //         // const filteredUsers = users.filter(user => user.toLowerCase().includes(query));
            //         showUserList(inputText,group_id);
            //     }
            // } else {
            //     hideUserList();
            // }
            clearTimeout(typingTimer);
            typingTimer = setTimeout(onTypingStop, typingDelay);

            if (inputLength > 0 && inputLength % 5 === 0) {
                saveGroupChatToDraft($("#sendmsgg").val(), group_id);
            }

            // Optional: Add debounce/throttle to avoid excessive calls
            clearTimeout(draftTimer);
            draftTimer = setTimeout(() => {
                if (inputLength > 0 && inputLength % 5 === 0) {
                    saveGroupChatToDraft($('#sendmsgg'), group_id); // Save any remaining message
                }
            }, 3000);

        });
        editorDiv.on('blur', function() {
            // updateTypingStatus(0)
        });
        editorDiv.on("keyup", function() {
            showGroupMemberList($(this), group_id, "memberSuggestions");
        })
        document.getElementById('sendmsgg').addEventListener('keydown', function(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault(); // Prevent default behavior of Enter key
                sendMessage(); // Call your sendMessage function
                const textarea = document.querySelector(".dynamic-textarea");
                textarea.style.height = "40px";
            }
        });
        var groupContainerWindow = document.getElementById("messages_read");

        var selectAllCheckbox = document.getElementById('selectAll');
        selectAllCheckbox.addEventListener('change', () => {
            var messageCheckboxes = groupContainerWindow.querySelectorAll('.select-message'); // Query each time
            Array.from(messageCheckboxes).forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });

      
        // Get Laravel's base URL
        $('#load-more').on('click', function() {
            let page = $(this).data('page');
            let groupId = group_id;
            let button = $(this);
            let loader = $('#loading-spinner'); // Loader for better UX

            //loader.show();
            button.prop('disabled', true);

            $.ajax({
                url: `${baseUrl}/group/group-attachments/${groupId}?page=${page}`,
                type: 'GET',
                success: function(response) {
                    if (response.data.length > 0) {
                        $('#attachments-container').append(response.html); // Append new attachments
                        button.data('page', page + 1);

                        if (response.current_page >= response.last_page) {
                            button.hide();
                        }
                    } else {
                        button.hide(); // Hide button if no data returned
                    }

                },
                complete: function() {
                    loader.hide();
                    button.prop('disabled', false);
                }
            });
        });
    }

    var MAX_FILES = 6; // Maximum number of files allowed

    // Initialize Drag and Drop

    // function removeFile(index) {
    //     formFiles.splice(index, 1);
    //     updatePreview();
    // }
    $(document).on("click", ".remove-file", function() {
        var index = $(this).data("index");
        formFiles.splice(index, 1);
        updatePreview();
    });
    $(document).on("click", ".close-upload-modal", function() {
        formFiles = [];
        files = [];
        updatePreview();
    });

    function updatePreview() {
        previewContainer.innerHTML = "";
        formFiles.forEach((file, index) => {
            var reader = new FileReader();
            reader.onload = function(e) {
                var previewItem = document.createElement("div");
                previewItem.classList.add("preview-item");
                var fileExtension = file.name.split('.').pop().toLowerCase();
                var filePreview = "";
                if (file.type.startsWith("image/")) {
                    filePreview = `<img src="${e.target.result}" alt="Preview"><p>${file.name}</p>`;
                } else {
                    if (fileExtension == 'pdf') {
                        filePreview =
                            `<img src='assets/images/chat-icons/pdf-icon.png' alt="Preview"><p>${file.name}</p>`;
                    } else if (fileExtension == 'xls' || fileExtension == 'xlsx') {
                        filePreview =
                            `<img src='assets/images/chat-icons/xls-icon.png' alt="Preview"><p>${file.name}</p>`;
                    } else if (fileExtension == 'doc' || fileExtension == 'docx') {
                        filePreview =
                            `<img src='assets/images/chat-icons/doc-icon.png' alt="Preview"><p>${file.name}</p>`;
                    } else {
                        filePreview =
                            `<img src='assets/images/chat-icons/file-icon.png' alt="Preview"><p>${file.name}</p>`;
                    }
                }
                previewItem.innerHTML = `
                        ${filePreview}
                        <button data-index="${index}" type="button" class="remove-file">X</button>
                    `;
                previewContainer.appendChild(previewItem);
            };
            reader.readAsDataURL(file);
        });
    }
    // Open modal when file is dragged over
    chatArea.addEventListener("dragover", (e) => {
        e.preventDefault();
        if (e.dataTransfer && e.dataTransfer.items) {
            for (let i = 0; i < e.dataTransfer.items.length; i++) {
                if (e.dataTransfer.items[i].kind === "file") {
                    //$("#file-upload-modal").modal("show");
                    $("#uploadModal").modal("show");
                    break; // Stop checking after finding a file
                }
            }
        }
    });

    // Hide modal if user cancels upload


    modal.addEventListener("dragover", function(e) {
        e.preventDefault();
    });
    // Detect file drop inside modal
    modal.addEventListener("drop", function(e) {
        e.preventDefault(); // Prevent browser default behavior
        uploadFiles(e.dataTransfer.files);
    });
    sendInput.addEventListener('paste', function(event) {
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
        if (hasNonTextData) {
            //$("#file-upload-modal").modal("show");
            $("#uploadModal").modal("show");
        }
        if (files.length > 0) {
            uploadFiles(files);
        }
    });
    // Function to remove file from preview


    // Upload function
    function uploadFiles(files) {
        if (formFiles.length + files.length > MAX_FILES) {
            errorMessage("You can only upload a maximum " + MAX_FILES + " files.");
            return;
        }


        selectedFiles = Array.from(files); // Store selected files
        
        // previewContainer.innerHTML = ""; // Clear previous preview
        uploadButton.disabled = selectedFiles.length === 0; // Enable upload button

        function getFileExtension(file) {
            const fileName = file.name;
            const extension = fileName.split('.').pop().toLowerCase(); // Convert to lowercase for consistency
            return extension;
        }
        var filePreviewContainer = document.getElementById('filePreviewContainer');
        selectedFiles.forEach((file, index) => {
            filesToUpload.push(file);
            var previewDiv = createFilePreview(file, index);
            filePreviewContainer.appendChild(previewDiv);
        });
    }

    uploadButton.addEventListener("click", function() {

        var replyTo = $('#group_reply_to_id').val();
        var myurl = $('#geturl').val();

        if (formFiles.length === 0) {
            errorMessage("Upload files to upload");
            return false;
        }

        $("#upload-button").attr("disabled", "disabled");
        var formData = new FormData();
        formData.append("_token", "{{ csrf_token() }}");

        formFiles.forEach((file) => {
            formData.append("attachment[]", file);
        });
        formData.append('reply_to', replyTo);
        formData.append('send_msg', $('#sendmsgg').val());

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
                    $("#file-upload-modal").modal("hide");
                    resetPreview();
                } else {
                    errorMessage("Upload failed: " + data.message);
                    $("#file-upload-modal").modal("hide");
                }
            })
            .catch(error => console.error("Upload Error:", error));
    });

    function resetPreview() {
        selectedFiles = [];
        formFiles = [];
        files = [];
        previewContainer.innerHTML = "";
        uploadButton.disabled = true;
    }
}



function fileSearchInputs(group_id, type = "") {


    let query = $('#search-file-input').val();
    let groupId = group_id;

    if (query != "") {
        $('.clear-text').show();
        console.log("1")
    } else {
        $('.clear-text').hide();
        console.log("2")

    }
    if (type == "clear") {
        $('#search-file-input').val('');
        $('.clear-text').hide();

    }
    $.ajax({
        url: `${baseUrl}/group/group-attachments/${groupId}?page=1&search=${query}&type=${type}`,
        type: 'GET',
        success: function(response) {
            console.log(response);
            if (response.html) {
                $('#attachments-container').html(response.html);
                if (response.current_page >= response.last_page) {
                    $('#load-more').hide();
                } else {
                    $('#load-more').show();
                }
            } else {
                $('#attachments-container').html('No Results Found');
                $('#load-more').hide();
            }
        }
    });

}

function loadGroupInfo(conversation_id, group_id) {
    $.ajax({
        type: 'GET',
        url: "{{ baseUrl('group/group-information') }}/" + conversation_id,
        dataType: 'json',
        data: {
            _token: "{{csrf_token()}}",
        },
        beforeSend: function() {
            $(".message-container").html('');
        },
        success: function(response) {
            $(".message-container").html('');
            if (response.status) {
                $(".message-container").html(response.contents);
            }
        },
        error: function(xhr, status, error) {
            console.log('Error fetching messages:', error);
        }
    });
}

function loadChatAjax(conversation_id, group_id) {
    $.ajax({
        type: 'GET',
        url: "{{ baseUrl('group/chat-ajax') }}/" + conversation_id,
        dataType: 'json',
        data: {
            _token: "{{csrf_token()}}",
        },
        beforeSend: function() {
            $(".message-container").html('');
        },
        success: function(response) {
            $(".loader").hide();
            if (response.status) {
                $(".message-container").html(response.contents);
                setTimeout(() => {
                    $(".message-container").promise().done(() => {
                        initializeSocket(group_id, prev_group_id);
                        initializeGroupChat(group_id);
                    });
                }, 1000);
            }
        },
        error: function(xhr, status, error) {
            console.log('Error fetching messages:', error);
        }
    });
}
var onTypingStop = () => {
    isTyping = false;
    // updateTypingStatus(0)
};

function updateTypingStatus(typingStatus) {
    return false; // for now it is stopped calling this event;
    if (typingStatus == 0) {
        isTyping = false;
    } else {
        isTyping = true;
    }

    var groupId = $('#get_group_id').val();
    $.ajax({
        url: '{{ baseUrl("group/update-typing") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            group_id: groupId,
            is_typing: typingStatus
        },
        success: function(response) {
            console.log('Typing status updated');
        },
        error: function(error) {
            console.log('Error updating typing status', error);
        }
    });
}


function openMembersModal() {
    var group_id = $('#get_group_id').val();
    $('#member_group_id').val(group_id);
}


function getGroupConversation(group_id) {
    const showChat = document.getElementById('chat-container');
    $.ajax({
        type: 'get',
        url: "{{ baseUrl('group/get-conversation/') }}/" + group_id,
        dataType: 'json',
        success: function(data) {
            $('.grp-messages').html(data.contents);
            showChat.classList.add("mobile-chat")
            console.log("1")
            // Append new messages
        },
        error: function(xhr, status, error) {
            console.log('Error fetching messages:', error);
        }
    });
}



function fetchOlderChats(load_first_msg = false) {
    if (load_first_msg) {
        last_msg_id = 0;
    }
    var groupId = $('#get_group_id').val();
    const showChat = document.getElementById('chat-container');
    $.ajax({
        type: 'POST',
        url: "{{ baseUrl('group/fetch-older-chats') }}/" + groupId + "/" + first_msg_id,
        dataType: 'json',
        data: {
            _token: "{{csrf_token()}}",
        },
        beforeSend: function() {},
        success: function(response) {
            showChat.classList.add("mobile-chat");
            $('#sloader').hide();
            isFetching = false;
            $('#messages_read').prepend(response.contents);
            first_msg_id = response.first_msg_id;
            updateConversationList();

            // document.getElementById('message-'+response.message_id).scrollIntoView({
            //     behavior: 'smooth'
            // });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching messages:', error);
        }
    });
}

function getSearch(query) {
    hasMorePages = true;
    hasMoreGrpPages = true;
    isFetchingOther = false;

    if (document.getElementById('my-group').classList.contains('active')) {
            conversationList(query, false);
    } else if (document.getElementById('other-groups').classList.contains('active')) {
        otherConversationList(query, false);
    } else if (document.getElementById('pending-requests').classList.contains('active')) {
        pendingGroupJoinRequest(query, false);
    }
}
let last_group_id = 0; // Track the last fetched group ID
let currentPage = 1; // Track the current page
isFetching = false;
var hasMorePages = true; // Track if more pages are available
other_last_group_id = 0;
function updateConversationList(){
    hasMorePages = true;
    currentPage = 1;
    conversationList();
  
}
function updateGrpConversationList(){
    hasMoreGrpPages = true;
    currentGrpPage = 1;
    otherConversationList();
    
}

function conversationList(search = '', fetchNewer = false) {
    // if(currentPage==1){
    //     hasMorePages=true;
    // }
    $(".recent-head").show();
    var inputValue = $('#groupSearch').val();
    $(".group-type").removeClass("active");
    $("#my-group").addClass("active");
    if (!hasMorePages) return; // Prevent multiple or unnecessary fetches
    isFetching = true;
    let loader = $('#loading-spinner');
   
    $.ajax({
        type: 'POST',
        url: "{{ baseUrl('group/groups-list') }}",
        dataType: 'json',
        data: {
            _token: "{{ csrf_token() }}",
            search: inputValue,
            page: fetchNewer ? currentPage + 1 : 1 // Fetch the next page if loading newer groups
        },
        beforeSend: function() {
            // loader.show();
            if(!fetchNewer){
                // $("#group-conversation-list").html('');
            }
        },
        success: function(data) {
            loader.hide();
            hasMoreGrpPages=true;
            if (fetchNewer) {
                // Append newer groups at the bottom
                $('#group-conversation-list').append(data.contents);
                currentPage++; // Move to the next page

            } else {
                // Replace the entire list for initial load or search
                $('#group-conversation-list').html(data.contents);
                currentPage = 1; // Reset to page 1
            }
            var groupId = $('#get_group_id').val();
            setTimeout(() => {
                $(".group-chat-item[data-group-id="+groupId+"]").addClass("active-chat");
                var chatContainer = document.getElementById("group-conversation-list");
                const activeChat = chatContainer.querySelector('.active-chat');
                if (activeChat) {
                    activeChat.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 100);
            hasMorePages = data.has_more_pages;
            isFetching = false;

            
            // loader.hide();

        },
        error: function(xhr, status, error) {
            console.error('Error fetching groups:', error);
            isFetching = false;
            loader.hide();
            hasMoreGrpPages=true;
        }
    });
}
let isFetchingOther = false;
let hasMoreGrpPages=true;
let currentGrpPage=1;
function otherConversationList(search = '', fetchNewer = false) {
    $(".recent-head").hide();
    var inputValue = $('#groupSearch').val();
    $(".group-type").removeClass("active");
    $("#other-groups").addClass("active");
    let loader = $('#loading-spinner');
    if (!hasMoreGrpPages ) return; // Prevent overlapping AJAX calls
    let isFetchingOther  = true;
    $.ajax({
        type: 'POST',
        url: "{{ baseUrl('group/other-groups-list') }}",
        dataType: 'json',
        data: {
            _token: "{{csrf_token()}}",
            search: inputValue,
            fetchNewer:fetchNewer,
            page: fetchNewer ? currentGrpPage + 1 : 1 // Fetch the next page if loading newer groups
        },
        beforeSend: function() {
            loader.show();
            if(!fetchNewer){
                $("#group-conversation-list").html('');
            }
            
        },
        success: function(data) {
            hasMorePages = true;
            loader.hide();
            if (fetchNewer) {
                // Append newer groups at the bottom
                $('#group-conversation-list').append(data.contents);
                currentGrpPage++; // Move to the next page

            } else {
                // Replace the entire list for initial load or search
                $('#group-conversation-list').html(data.contents);
                currentGrpPage = 1; // Reset to page 1
            }
            hasMoreGrpPages = data.has_more_pages ?? false;
            isFetchingOther = false;
           

        },
        error: function(xhr, status, error) {
            console.error('Error fetching groups:', error);
            isFetchingOther = false;
            loader.hide();

        }
    });
}

document.getElementById("group-conversation-list").addEventListener("scroll", function() {
    const scrollDiv = this;

    if (scrollDiv && 
    scrollDiv.scrollTop + scrollDiv.clientHeight >= scrollDiv.scrollHeight - 1 &&
    (hasMorePages || hasMoreGrpPages)
    ) {
        if (document.getElementById('my-group').classList.contains('active')) {
                conversationList('', true);
        } else if (document.getElementById('other-groups').classList.contains('active')) {
            otherConversationList('',true);
        } else if (document.getElementById('pending-requests').classList.contains('active')) {
            pendingGroupJoinRequest();
        }


    }
});

function pendingGroupJoinRequest(search = '') {
    $(".recent-head").hide();
    var inputValue = $('#groupSearch').val();
    $(".group-type").removeClass("active");
    $("#pending-requests").addClass("active");
    // if (inputValue.trim() === '') {
    $.ajax({
        type: 'get',
        url: "{{ baseUrl('group/pending-group-join-request') }}",
        dataType: 'json',
        data: {
            _token: "{{csrf_token()}}",
            search: inputValue
           
        },
        beforeSend:function(){$("#group-conversation-list").html('');
            //if(!fetchNewer){
               // 
            //}
        },
        success: function(data) {
            hasMorePages = true;
            isFetchingOther = false;
            hasMoreGrpPages = true;

            $('#group-conversation-list').html(data.contents);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching messages:', error);
        }
    });
    // setTimeout(conversationList, 50000);
    // }
}

$(".group-form").submit(function(e) {
    e.preventDefault();
    var is_valid = formValidation("form");
    if (!is_valid) {
        return false;
    }
    var formData = new FormData($(this)[0]);

    var url = $(".group-form").attr('action');
    $.ajax({
        url: url,
        type: "post",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        beforeSend: function() {
            showLoader();
        },
        success: function(response) {
            hideLoader();
            if (response.status == true) {
                successMessage(response.message);
                redirect(response.redirect_back);
            } else {
                validation(response.message);
            }
        },
        error: function() {
            internalError();
        }
    });

});

$(".member-form").submit(function(e) {
    e.preventDefault();
    var is_valid = formValidation("form");
    if (!is_valid) {
        return false;
    }
    var formData = new FormData($(this)[0]);
    var url = $(".member-form").attr('action');
    $.ajax({
        url: url,
        type: "post",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        beforeSend: function() {
            showLoader();
        },
        success: function(response) {
            hideLoader();
            if (response.status == true) {
                successMessage(response.message);
                redirect(response.redirect_back);
            } else {
                validation(response.message);
            }
        },
        error: function() {
            internalError();
        }
    });

});


$(document).on('click', '#sendBtn1', function() {
    const textarea = document.querySelector(".dynamic-textarea");
    textarea.style.height = "40px";
    sendMessage();
});



function sendMessage() {
    var editMessageId = $('#grp_edit_message_id').val();

    var replyTo = $('#group_reply_to_id').val();
    var getval = $('#sendmsgg').val();
    var myurl = $('#geturl').val();
    const sendbtn = document.getElementById("sendBtn1");
    const msgInput = document.getElementById("sendmsgg");
    sendbtn.classList.add("disbled-btn")
    // msgInput.disabled = true;

    let formData = new FormData();
    formData.append('_token', $('input[name=_token]').val());
    formData.append('reply_to', replyTo);
    formData.append('send_msg', getval);
    formData.append("openfrom", "chatWindow");
    for (let i = 0; i < selectedFile.length; i++) {
        formData.append('attachment[]', selectedFile[i]);
    }

    if (editMessageId) {
        $.ajax({
            type: 'post',
            url: "{{ baseUrl('group/update-message') }}/" + editMessageId,
            data: {
                _token: "{{ csrf_token() }}",
                message: getval
            },
            beforeSend:function(){
                $("#sendmsgg").attr("disabled","disabled");
            },
            success: function(response) {
                $("#sendmsgg").removeAttr("disabled");
                $('#cpMsg' + editMessageId).html(response.updated_message);
                document.querySelector('#sendmsgg').value = '';
                $('#grp_edit_message_id').val('');
                sendbtn.classList.remove("disbled-btn")

            },
            error: function() {
                console.log('Error updating message');

            }
        });
    } else {

        $.ajax({
            type: 'post',
            url: myurl,
            // data: {
            //     '_token': "{{ csrf_token() }}",
            //     'send_msg': getval,
            //     'reply_to': replyTo,
            // },
            data: formData,
            processData: false,
            contentType: false,
            beforeSend:function(){
                $("#sendmsgg").attr("disabled","disabled");
            },
            success: function(data) {
                $("#sendmsgg").removeAttr("disabled");
                $('#closemodal').click();
                var getid = data.id - 1;
                document.querySelector('#sendmsgg').value = '';
                $('#reply_quoted_msg').hide();
                $('#group_reply_to_id').val('');
                $('.emoji-wysiwyg-editor').html('');
                // updateTypingStatus(0)
                sendbtn.classList.remove("disbled-btn");
                document.querySelectorAll('.image-container').forEach(img => img.remove()); // Clear UI
                if (data.status == false) {

                    errorMessage('Script Tag not allowed');
                }
            }
        });
    }
    $('#sendmsgg').val('');
}
$(document).on('change', '#attachment', function() {
    var fileInput = this;
    var filePreviewContainer = document.getElementById('filePreviewContainer');

    // Loop through each file and create a preview
    Array.from(fileInput.files).forEach((file, index) => {
        filesToUpload.push(file); // Add the file to the array

        // Create file preview
        var previewDiv = createFilePreview(file, filesToUpload.length - 1);
        filePreviewContainer.appendChild(previewDiv);
    });
});
// Handle file drag-and-drop
$(document).on('drop', '#filePreviewContainer', function(e) {
    e.preventDefault();
    e.stopPropagation();

    var filePreviewContainer = document.getElementById('filePreviewContainer');
    Array.from(e.originalEvent.dataTransfer.files).forEach((file, index) => {
        filesToUpload.push(file); // Add file to array

        // Create file preview
        var previewDiv = createFilePreview(file, filesToUpload.length - 1);
        filePreviewContainer.appendChild(previewDiv);
    });
});
$(document).on('submit', '#clear-messages', function(e) {
    e.preventDefault();
    var clear_msg = Array.from($('.grp-messages input[name="clear_msg[]"]:checked').map(function() {
        return $(this).val(); // Get the value of the selected checkbox
    }));

    var myurl = "{{baseUrl('group/clear-group-messages/'.$group_id)}}";
    var group_id = "{{$group_id}}";
    $.ajax({
        type: 'POST',
        url: myurl,
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'), // CSRF Token
            clear_msg: clear_msg
        },
        success: function(data) {
            var idsToRemove = data.msgIds;
            var message_count = data.message_count;
            idsToRemove.forEach(function(id) {
                $('.grp-messages #message-' + id).remove();
                $('#group-chatbot-' + group_id + ' #message-' + id).remove();
            });
            $('#selectAllDiv').hide();
            $('#clearChatBtn').hide();
            $('.select-message').hide();
            $('#clear-messages').find('.clear-checkbox').hide();
            var msgCount = 0;

            if (message_count < 1) {
                $("#messages_read").html('<div class="welcome-chat"><h5>No message yet</h5></div>');
                $('#groupChatBody-' + group_id).html(
                    '<div class="welcome-chat"><h5>No message yet</h5></div>');
            }
            successMessage(data.message);

        }
    });

});


$(document).on('submit', '#file-upload-form', function(e) {
    e.preventDefault();
    var myurl = $('#geturl').val();

    var replyTo = $('#group_reply_to_id').val();

    let formData = new FormData();
    let attach

    let message = $('#messagenew').val();
    const files = filesToUpload;
    if(message == '' && files.length == 0){
        errorMessage("Files required to upload");
        return false;
    }
    // Get the file input element
    // const fileInput = document.getElementById('attachment');
    // Ensure the file input is being accessed correctly
    
    // Check if files are selected
    if (files.length > 0) {
        for (var i = 0; i < files.length; i++) {
            formData.append('attachment[]', files[i]);
        }
    } else {
        console.log('No file selected'); // Debugging: log when no file is selected
    }
    formData.append('message', message);
    formData.append('reply_to', replyTo);
    $('.loader').show();



    $.ajax({
        url: myurl,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            if (data.status == true) {
                successMessage(data.message);
                $('.loader').hide();
                $('#closemodal').click();
                $('#file-upload-form')[0].reset();
                $('#group_reply_to_id').val('');
                $('#messagenew').val('');
                $('#reply_quoted_msg').hide();
                $('#filePreviewContainer').html('');
                $('.file-name').html();
                filesToUpload = [];
                // updateTypingStatus(0)
            } else {
                $('.loader').hide();
                errorMessage(data.message);
                validation(data.message);

            }

        },
        error: function(response) {
            $('#loader').hide();
            $('#message').html('<p>File upload failed</p>');
        }
    });
});
$(document).on('click', '#clear_chat', function() {
    var sentMsgCount = $('.grp-messages .sent-block').length;
    var rcvdMsgCount = $('.grp-messages .received-block').length;
    if (sentMsgCount > 0 || rcvdMsgCount > 0) {
        $('#selectAllDiv').show();
        $('.grp-messages .clear-checkbox').show();
        $('#clearChatBtn').show();
    }
});
$(document).on('click', '#cancelClear', function() {
    $('#clear-messages').find('#selectAllDiv').hide();
    $('#clear-messages').find('.clear-checkbox').hide();
    $('#clear-messages').find('#clearChatBtn').hide();
    $(".select-message,#selectAll").prop("checked", false);
});
function createFilePreview(file, index) {
    var previewDiv = document.createElement('div');
    previewDiv.classList.add('file-preview');
    previewDiv.dataset.index = index;
    previewDiv.style.display = 'flex';
    previewDiv.style.alignItems = 'center';
    previewDiv.style.gap = '10px';
    previewDiv.style.border = '1px solid #ccc';
    previewDiv.style.padding = '8px';
    previewDiv.style.margin = '5px 0';
    previewDiv.style.borderRadius = '6px';
    previewDiv.style.position = 'relative';
    previewDiv.style.background = '#f9f9f9';

    var icon = document.createElement('div');
    icon.style.width = '40px';
    icon.style.height = '40px';
    icon.style.flexShrink = '0';
    icon.style.display = 'flex';
    icon.style.alignItems = 'center';
    icon.style.justifyContent = 'center';
    icon.style.overflow = 'hidden';
    icon.style.borderRadius = '4px';
    icon.style.background = '#eee';
    
    // Set preview icon based on file type
    if (file.type.startsWith('image/')) {
        var img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.style.width = '100%';
        img.style.height = '100%';
        img.style.objectFit = 'cover';
        icon.appendChild(img);
    } else if (file.type === 'application/pdf') {
        icon.textContent = '📄';
        icon.style.fontSize = '24px';
    } else {
        icon.textContent = '📁';
        icon.style.fontSize = '24px';
    }

    var fileNameSpan = document.createElement('span');
    fileNameSpan.textContent = file.name;
    fileNameSpan.style.flexGrow = '1';

    // Create progress bar
    var progress = document.createElement('div');
    progress.classList.add('progress-bar');
    progress.style.width = '0%';
    progress.style.height = '5px';
    progress.style.background = 'green';
    progress.style.position = 'absolute';
    progress.style.bottom = '0';
    progress.style.left = '0';
    progress.style.transition = 'width 0.3s';

    // Create close button
    var closeBtn = document.createElement('span');
    closeBtn.textContent = '✖';
    closeBtn.classList.add("preview-remove-btn");
    closeBtn.style.cursor = 'pointer';
    closeBtn.style.color = 'red';
    closeBtn.style.fontWeight = 'bold';
    closeBtn.style.marginLeft = '10px';
    closeBtn.style.flexShrink = '0';

    // On close button click
    closeBtn.addEventListener('click', function() {
        removeFilePreview(index);
    });

    // Append elements to preview div
    previewDiv.appendChild(icon);
    previewDiv.appendChild(fileNameSpan);
    previewDiv.appendChild(closeBtn);
    previewDiv.appendChild(progress);

    return previewDiv;
}
// Search functionality
</script>
<script type="text/javascript">
// Sélection des conversations
const conversations = document.querySelectorAll(".conversation");
const chatHeader = document.getElementById("chat-user");
const chatMessages = document.querySelector(".grp-messages");
const chatInput = document.querySelector(".chat-input textarea");

conversations.forEach(conversation => {
    conversation.addEventListener("click", () => {
        const user = conversation.dataset.user;
        chatHeader.textContent = `Conversation avec ${user}`;
        chatMessages.innerHTML = `<p><strong>${user} :</strong> Bonjour, comment ça va ?</p>`;
        chatInput.focus();
    });
});
</script>

<script>
function showTextBox() {
    $('#groupName').hide();
    $('#editGroupName').show();
    $('#groupTick').show();
    $('#group-name-edit').addClass("edit-group-enabled")
    $('#group-name-edit').removeClass("edit-group-disabled")

}

function updateGroupName() {
    var getval = $('#editGroupName').val();
    $('#group-name-edit').addClass("edit-group-disabled")
    $('#group-name-edit').removeClass("edit-group-enabled")

    $.ajax({
        type: 'get',
        url: "{{baseUrl('group/update-group-name/'.$group_id)}}",
        data: {
            name: getval
        },
        success: function(data) {

            $('#headerGroupName').html(getval);
            $('#groupName').html(getval);
            $('#editGroupName').hide();
            $('#groupName').show();
            $('#groupTick').hide();

        }
    });

}


// right sidebar //
function toggleFilesSidebar(groupId) {

    fileSearchInputs(groupId, 'clear');
    const sidebar = document.getElementById('filesidebar');
    const screenWidth = window.innerWidth;
    if (screenWidth < 500) {
        const currentPosition = sidebar.style.right;
        if (currentPosition === '0px') {
            sidebar.style.right = '-100%';
        } else {
            sidebar.style.right = '0';
        }
    } else {
        const currentPosition = sidebar.style.right;
        if (currentPosition === '0px') {
            sidebar.style.right = '-420px';
        } else {
            sidebar.style.right = '0';
        }
    }
}
// right sidebar //
// function toggleSidebar() {
//     const sidebar = document.getElementById('profilesidebar');
//     const screenWidth = window.innerWidth;
//     if (screenWidth < 500) {
//         const currentPosition = sidebar.style.right;
//         if (currentPosition === '0px') {
//             sidebar.style.right = '-100%';
//         } else {
//             sidebar.style.right = '0';
//         }
//     } else {
//         const currentPosition = sidebar.style.right;
//         if (currentPosition === '0px') {
//             sidebar.style.right = '-420px';
//         } else {
//             sidebar.style.right = '0';
//         }
//     }
// }

// right sidebar for join group request//
// function toggleGroupJoinSidebar() {
//     const sidebar = document.getElementById('groupJoinSidebar');
//     const screenWidth = window.innerWidth;
//     if (screenWidth < 500) {
//         const currentPosition = sidebar.style.right;
//         if (currentPosition === '0px') {
//             sidebar.style.right = '-100%';
//         } else {
//             sidebar.style.right = '0';
//         }
//     } else {
//         const currentPosition = sidebar.style.right;
//         if (currentPosition === '0px') {
//             sidebar.style.right = '-420px';
//         } else {
//             sidebar.style.right = '0';
//         }
//     }
// }
</script>

<!-- group pic add -->
<script>
function previewImage(event, val) {


    const fileInput1 = event.target; // First file input
    const fileInput2 = document.getElementById('fileInput2'); // Second file input

    if (fileInput1.files.length > 0) {
        const file = fileInput1.files[0]; // Get the selected file

        // Create a new DataTransfer object
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file); // Add the file to DataTransfer

        // Assign the file to the second file input
        fileInput2.files = dataTransfer.files;

        console.log('File transferred successfully!');
    }

    const groupIcon = document.getElementById("groupIcon");
    const file = event.target.files[0];
    $('#getval').val(file.name);
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Remove the overlay after image upload
            groupIcon.innerHTML = `<img src="${e.target.result}" alt="Group Icon" />`;
        };
        reader.readAsDataURL(file);
    }
}
</script>
<script>
function toggleChatsSearch(status) {
    const chatsearch = document.getElementById("chatsSearch");
    chatsearch.style.display = chatsearch.style.display === 'flex' ? 'none' : 'flex';
    if (status == "close") {
        $('#search_input').val('');
        searchChatMessages();
        $('.chat-message').removeClass('highlight');

    }
}
let currentIndex = -1; // Track the selected message
let searchResults = []; // Store matching messages


function searchChatMessages(direction = null) {
    let searchQuery = $('#search_input').val().toLowerCase().trim();
    let lastMessageId = $('.chat-message').first().data('id'); // ID of the oldest message
    searchResults = [];
    $('.chat-message').removeClass('highlight');

    if (searchQuery.length > 1) {
        // Step 1: Search through currently loaded messages
        $('#messages_read .chat-message').each(function() {
            const messageText = $(this).text().toLowerCase();
            if (messageText.includes(searchQuery)) {
                searchResults.push($(this)); // Store matched elements
            }
        });

        // Step 2: Load older messages if no match is found
        if (searchResults.length === 0) {
            fetchOlderChats(false); // Load older messages

            // Wait for the fetch to complete and then search again
            let checkForNewMessages = setInterval(() => {
                if (!isFetching) {
                    clearInterval(checkForNewMessages);

                    // Step 3: Search newly loaded messages
                    let newMatches = [];
                    $('#messages_read .chat-message').each(function() {
                        const messageText = $(this).text().toLowerCase();
                        if (messageText.includes(searchQuery)) {
                            newMatches.push($(this)); // Store matched elements
                        }
                    });

                    // Step 4: Highlight matches if found
                    if (newMatches.length > 0) {
                        searchResults = newMatches.concat(searchResults);
                        highlightMessage(0); // Highlight the first found match
                    } else {
                        warningMessage('No match found in previous messages.');
                    }
                }
            }, 500); // Check every 500ms if messages have loaded
        } else {
            // Step 5: Handle navigation for existing matches
            searchResults = searchResults.reverse();
            if (direction === 'down') {
                searchDown();
            } else if (direction === 'up') {
                searchUp();
            } else {
                highlightMessage(0);
            }
        }
    } else {
        if(direction !== null)
            errorMessage('Minimum 2 characters required for search.');
    }
}

// Highlight a specific message
function highlightMessage(index) {

    $('.chat-message').removeClass('highlight'); // Remove previous highlights
    const message = searchResults[index];
    if (message && message.length > 0) {
        message.addClass('highlight'); // Highlight the message

        // Scroll smoothly to the highlighted message
        message[0].scrollIntoView({
            behavior: "smooth",
            block: "center"
        });
    }
   
}

// Navigate to the previous match
function searchUp() {
    if (searchResults.length === 0) return; // No results
    currentIndex = (currentIndex + 1 + searchResults.length) % searchResults.length; // Wrap around
    highlightMessage(currentIndex);
}

// Navigate to the next match
function searchDown() {
    if (searchResults.length < 0) return; // No results
    currentIndex = (currentIndex - 1 - searchResults.length) % searchResults.length; // Wrap around
    highlightMessage(currentIndex);
}



$('#search_input').on('keydown', function(e) {
    if (e.key === 'ArrowUp') {
        e.preventDefault();
        searchUp(); // Navigate up on ArrowUp
    } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        searchDown(); // Navigate down on ArrowDown
    }
});
</script>
<!-- back tp chats -->
<script>
function backToChats() {
    const showChat = document.getElementById('chat-container');

    if (showChat) {
        showChat.classList.remove("mobile-chat"); // Attempt to remove the class
    }
}
</script>
<!-- File upload name -->
<script>
   
// document.getElementById('attachment').addEventListener('change', function() {
//     const fileInput = this;
//     const fileNameDiv = document.getElementById('fileName');

//     // Check if any file was selected
//     if (fileInput.files && fileInput.files.length > 0) {
//         const fileName = fileInput.files[0].name; // Get the name of the first selected file
//         fileNameDiv.textContent = `Selected file: ${fileName}`; // Display the file name
//     } else {
//         fileNameDiv.textContent = ''; // Clear the file name if no file is selected
//     }
// });



function deleteMessageforAll(chat_msg_id, chat_msg_uid) {
    var geturl = "{{baseUrl('group/delete-message-for-all/')}}/" + chat_msg_id;
    $.ajax({
        type: 'get',
        url: geturl,
        data: {},
        success: function(data) {
            $('#message-' + chat_msg_uid).html(
                '<div class="message-block"><div class="text-message-block"><div class="message sent"><p class="chat-message deleted-message">This message was deleted.</p></div></div></div>'
            );
            updateConversationList();

        }
    });
}

function deleteMessage(chat_msg_id, chat_msg_uid) {
    var geturl = "{{baseUrl('group/delete-message-centre-msg/')}}/" + chat_msg_id;
    $.ajax({
        type: 'get',
        url: geturl,
        data: {},
        success: function(data) {
            $('#message-' + chat_msg_uid).remove();
        }
    });
}
</script>
<script>
function closeReplyto() {
    const replyModal = document.getElementById('reply_quoted_msg');
    if (replyModal) {
        $('#reply_quoted_msg').hide();
        $('#group_reply_to_id').val('');
    } else {
        console.error("Element with ID 'reply_quoted_msg' not found.");
    }
}
</script>

<script>
const textarea = document.querySelector(".dynamic-textarea");
if(textarea){
textarea.addEventListener("input", function() {
    // Reset height to auto to calculate the new height based on content
    this.style.height = "40px";

    // Set the height to either the scrollHeight or maxHeight, whichever is smaller
    const maxHeight = 150; // Max height in pixels
    this.style.height = Math.min(this.scrollHeight, maxHeight) + "px";
});
}
</script>

<script>
$(document).ready(function() {

    
    // Trigger the form submission when a file is selected
    $("#fileUploads").on("change", function(e) {

        var fileInput = e.target;
        if (fileInput.files.length > 0) {
            // Create a FormData object
            var formData = new FormData();
            var file = fileInput.files[0];

            // Validate the file
            var allowedExtensions = ["image/jpeg", "image/png", "image/jpg"];
            // var maxSize = 5 * 1024 * 1024; // 5MB

            if (!allowedExtensions.includes(file.type)) {
                validation("Only JPEG, JPG, and PNG formats are allowed.");
                return false;
            }
         

            // Append the file and additional data if needed
            formData.append("group_image", file);
            formData.append("_token", $('meta[name="csrf-token"]').attr(
                "content")); // Add CSRF token if needed
            var groupId = $('#get_group_id').val();

            // AJAX request to upload the file
            $.ajax({
                url: "{{ baseUrl('group/') }}/" + groupId + "/save-image",
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function() {
                    showLoader(); // Show loader
                },
                success: function(response) {
                    hideLoader(); // Hide loader
                    if (response.status === true) {
                        successMessage(response.message); // Display success message
                        location.reload(); // Reload the page or update the UI dynamically
                    } else {
                        validation(response.message); // Show validation message
                    }
                },
                error: function(xhr) {
                    hideLoader(); // Hide loader
                    console.error(xhr.responseText); // Debug error
                    internalError(); // Show error
                }
            });
        }
    });
});
</script>

<script>


function removeDarg(event) {
    const button = event.target; // The clicked button
    // const fileName = button.prev(".image-text");
    const pTag = button.closest(".images-div").querySelector(".image-text").textContent;
    const parentElement = button.closest(".images-div"); // Find the nearest parent with class
    // console.log(selectedFile);

    selectedFile = removeFileByName(selectedFile, pTag);
    console.log(selectedFile);

    if (parentElement) {
        parentElement.remove(); // Remove the parent element
    }
}


function acceptJoinRequest(member_id,id) {
    $.ajax({
        type: 'get',
        url: "{{ baseUrl('group/add-group-member') }}/" + member_id,
        dataType: 'json',
        success: function(response) {
            if (response.status == true) {
                successMessage(response.message);
                fetchGroupChatBotMessages(id);
                $('#join-request-' + response.unique_id).remove();
                if ((response.group_join_rqst_count) > 0) {
                    $('.join-rqst-counter').html(response.group_join_rqst_count);
                } else {
                    $('.join-rqst-counter').html('');
                }
            } else {
                errorMessage(response.message);
            }
            // console.log(data.contents);
            // $('.to-connet-div').html(data.contents);
        },
        error: function(xhr, status, error) {
            console.log('Error fetching messages:', error);
        }
    });
}


function rejectJoinRequest(member_id,id) {
    $.ajax({
        type: 'get',
        url: "{{ baseUrl('group/reject-group-member') }}/" + member_id,
        dataType: 'json',
        success: function(response) {
            if (response.status == true) {
                successMessage(response.message);
                // fetchChats();
                fetchGroupChatBotMessages(id);
                $('#join-request-' + response.unique_id).remove();


            } else {
                errorMessage(response.message);
            }
            // console.log(data.contents);
            // $('.to-connet-div').html(data.contents);
        },
        error: function(xhr, status, error) {
            console.log('Error fetching messages:', error);
        }
    });
}

function backToOtherGroup() {
    console.log("asdasd")
    const msgContainer = document.querySelector('.message-container');
    if (msgContainer) {
        msgContainer.classList.remove("active"); // Attempt to remove the class
    }
}
</script>
@endsection
<!--  -->