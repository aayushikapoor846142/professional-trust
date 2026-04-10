@section('styles')
<style>
/* Override global smooth scrolling to prevent whole page scroll */
html, body {
    scroll-behavior: auto !important;
}

/* Ensure only the chat container scrolls smoothly */
#scrollDiv {
    scroll-behavior: smooth;
}
</style>
@endsection

<section class="cdsTYOnboardingDashboard-breadcrumb-section">
    <div class="cds-dashboard-chat-main-container-header">
        @include('admin-panel.01-message-system.message-centre.invite-users')
    </div>
</section>
<section class="CDSDashboardProfessional-main-container-body-inner-0">
    <div class="cdsTYOnboardingDashboard-chat-main-container">
        @php
        $openfor= request()->get('openfor');
        @endphp
        @include('admin-panel.01-message-system.message-centre.chat_sidebar_header_common')
        <div class="chat-container" id="chat-container">
            @include('admin-panel.01-message-system.message-centre.chat_sidebar_common')
            <!-- Chat Messages -->
            <div class="message-container">
                @include("admin-panel.01-message-system.message-centre.message-container")
            </div>
        </div>
    </div>
</section>
@php
$loader_html = minify_html(view("components.skelenton-loader.message-skeletonloader")->render());
@endphp
@section('javascript')

<script>
var loader_html = '{!! $loader_html !!}';
var prev_chat_id = 0;
var typingTimer; // Timer identifier
var typingDelay = 5000;
var draftTimer;
var last_msg_id = 0;
var first_msg_id = 0;
var isFetching = false;
var last_react_id = 0;
var chatId = 0; // Replace with the current chat ID
var userId = '{{ auth()->user()->id }}';
var selectedFile = [];
//const previewDiv = document.getElementById('img-preview-div');
var baseUrl = "{{ baseUrl('/') }}";
var openfor = '{{ $openfor }}';
$(window).on("load", function() {
    if (openfor == "mobile") {
        let url = window.location.pathname;
        let lastParam = url.split("/").filter(Boolean).pop();
        document.querySelector(".chatdiv" + lastParam).click();
    }
    console.log("Page and all resources are fully loaded!");
});

function fileSearchInputs(chat_id, type = "") {

    var query = $('#search-file-input').val();
    var chatId = chat_id;

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
        url: `${baseUrl}/message-centre/attachments/${chatId}?page=1&search=${query}&type=${type}`,
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


$(document).ready(function() {
    $(document).on("click", ".user-chat-item", function() {
    // Show loader immediately on all screen sizes
    $(".loader").show();  
$(".user-chat-item").removeClass("active-chat");
    $(this).addClass("active-chat");
    if (window.innerWidth < 991) {
        // Open message-container animation only for mobile screens
        $(".message-container").stop(true, true).css({
            'display': 'block', 
            'width': '0', 
            'right': '0'
        }).animate({
            width: '100%' 
        }, 300).addClass("active");
    }

    var url = $(this).data("href");
    var conversation_id = $(this).data("chat-unique-id");
    var chatid = $(this).data("chat-id");

    // Load chat content and hide loader after completion
    loadChatAjax(conversation_id, chatid, function() {
        $(".loader").hide(); // Hide loader after chat loads
    });

    history.pushState(null, '', url);
});



    // Close message-container when clicking .back-chats
    $(document).on("click", ".back-chats", function() {
        $(".message-container").stop(true, true).animate({
            width: '0%'
        }, 300, function() {
            $(this).removeClass("active").css('display', 'none');
        });
    });
});


function loadChatAjax(conversation_id, chatid) {
    
    $.ajax({
        type: 'POST',
        url: "{{ baseUrl('message-centre/chat-ajax') }}/" + conversation_id,
        dataType: 'json',
        data: {
            _token: "{{csrf_token()}}",
        },
        beforeSend: function() {
            $(".message-container").html('');
        },
        success: function(response) {
            $(".loader").hide();
            $(".message-container").html(response.contents);
            setTimeout(() => {
                $(".message-container").promise().done(() => {
                    initializeSocket(chatid);
                    initializeChat(chatid);
                });
            }, 1000);
        },
        error: function(xhr, status, error) {
            console.log('Error fetching messages:', error);
        }
    });
}
document.addEventListener('DOMContentLoaded',async function() {
    if('{{$chat_id}}'){
        await initializeSocket("{{$chat_id}}");
        await initializeChat("{{$chat_id}}");
    }
    
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

function initializeSocket(chatId, prevChatId = '') {
    if (prevChatId != '') {
        window.Echo.leave(`chat_blocked.${prevChatId}`);
        window.Echo.leave(`message_reaction`);
        window.Echo.leave(`chat.${prevChatId}`);
    }
    initializeChatSocket(chatId);

}

function initializeChat(chat_id) {
    prev_chat_id = chat_id
    last_msg_id = 0;
    first_msg_id = 0;
    isFetching = false;
    last_react_id = 0;
    chatId = chat_id; // Replace with the current chat ID
    userId = '{{ auth()->user()->id }}';
    ACTIVE_CHAT = chatId;
    selectedFile = [];
    var filesToUpload = []; 
    if (document.querySelector("#sendmsgg")) {

    new EmojiPicker(".message-emoji-icon", {
        targetElement: "#sendmsgg"
    });

    $(document).on("blur", "#sendmsgg", function() {
        handleChatBlur(this);
    });
    }
    function handleChatBlur(input) {
        if (input.value.trim() === "") {
            input.value = "";
            $("#edit_message_id").val("");

            console.log("Blur function called!");
        }
    }
    if (chatId != "" && chatId > 0) {

        document.getElementById("scrollDiv").addEventListener("scroll", function() {
            const scrollDiv = this;
            if (scrollDiv.scrollTop === 0 && !isFetching) {
          //  alert(first_msg_id);
                // Fetch older messages
                if (first_msg_id > 0) {
                    fetchOlderMessages(chatId);
                }
            }
        });


        $(document).ready(function() {
            fetchChatBotMessages(chatId);
            // updateTypingStatus(0);
            conversationList();
        });
        const sendMsgElement = document.getElementById('sendmsgg');

        if (!sendMsgElement) return; // Ensure element exists

        sendMsgElement.removeEventListener('keydown', handleSendMessagKeyDown);
        sendMsgElement.addEventListener('keydown', handleSendMessagKeyDown);

        // document.getElementById('attachment').removeEventListener('change', handleAttachmentUpload);
        // document.getElementById('attachment').addEventListener('change', handleAttachmentUpload);
        // document.getElementById('attachment').addEventListener('change', function() {
        //     const fileInput = this;
        //     const fileNameDiv = document.getElementById('fileName');

        //     if (fileInput.files && fileInput.files.length > 0) {
        //         const fileName = fileInput.files[0].name; // Get the name of the first selected file
        //         fileNameDiv.textContent = `Selected file: ${fileName}`; // Display the file name
        //         } else {
        //         fileNameDiv.textContent = ''; // Clear the file name if no file is selected
        //         }
        //     }
        // });
        
        var chatContainerWindow = document.getElementById("messages_read");

        var selectAllCheckbox = document.getElementById('selectAll');
        selectAllCheckbox.addEventListener('change', () => {
            var messageCheckboxes = chatContainerWindow.querySelectorAll('.select-message'); // Query each time
            Array.from(messageCheckboxes).forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
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
    $('#clear-messages').on('submit', function(e) {
        var clear_msg = Array.from($('.chat-messages input[name="clear_msg[]"]:checked').map(function() {
            return $(this).val(); // Get the value of the selected checkbox
        }));
        var myurl = BASEURL+"/message-centre/clear-message-centre/"+chat_id;
        e.preventDefault();

        $.ajax({
            type: 'post',
            url: myurl,
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'), // CSRF Token
                clear_msg: clear_msg

            },
            success: function(data) {
                var idsToRemove = data.msgIds;
                idsToRemove.forEach(function(id) {
                    $("#chatMessages" + chat_id + " #message-" + id).remove();
                    $("#chatbot-" + chat_id + " #message-" + id).remove();
                });
                $('#selectAllDiv').hide();
                $('#clearChatBtn').hide();
                $('.clear-checkbox').hide();
                var message_count = data.message_count;
                if (message_count < 1) {
                    $("#messages_read").html(
                        '<div class="welcome-chat"><h5>No message yet</h5></div>');
                    $('#chatBody-' + chat_id).html(
                        '<div class="welcome-chat"><h5>No message yet</h5></div>');
                }

                conversationList();
                successMessage(data.message);
            }
        });
    });
    $('#file-upload-form').on('submit', function(e) {
        e.preventDefault();
        var replyTo = $('#reply_to_id').val();;
        var myurl = `${baseUrl}/message-centre/send-msg/${chatId}`;
        // var attachmentFile = [];
        // attachmentFile.push($('#attachment')[0].files[0]);
        var formData = new FormData();
        // var attachment = $('#attachment')[0].files[0];
        var message = $('#messagenew').val();
        // formData.append('attachment[]', attachmentFile);
        formData.append('comment', message);
        formData.append('reply_to', replyTo);
        if(messagenew == '' && filesToUpload.length == 0){
            errorMessage("Upload files to send");
            return false;
        }
        // const files = document.getElementById('attachment').files;
        const files = filesToUpload;
        for (var i = 0; i < files.length; i++) {
            formData.append('attachment[]', files[i]);
        }

        $(".loader").show();



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
                    $('#reply_to_id').val('');
                    $('#messagenew').val('');
                    $('#reply_quoted_msg').hide();
                    $('#filePreviewContainer').html('');
                    $('.file-name').html();
                    filesToUpload = [];
                    // updateTypingStatus(0);
                } else {

                    errorMessage(data.message);
                    $('.loader').hide();
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
        var sentMsgCount = $('.chat-messages .sent-block').length;
        var rcvdMsgCount = $('.chat-messages .received-block').length;
        if (sentMsgCount > 0 || rcvdMsgCount > 0) {
            $('#clear-messages').find('#selectAllDiv').show();
            $('#clear-messages').find('.clear-checkbox').show();
            $('#clear-messages').find('#clearChatBtn').show();
        }
    });
    
    $(document).on('click', '#cancelClear', function() {
        $('#clear-messages').find('#selectAllDiv').hide();
        $('#clear-messages').find('.clear-checkbox').hide();
        $('#clear-messages').find('#clearChatBtn').hide();
        $(".select-message,#selectAll").prop("checked",false);
    });




    // Sélection des conversations
    const conversations = document.querySelectorAll(".conversation");
    const chatHeader = document.getElementById("chat-user");
    const chatMessages = document.querySelector(".chat-messages");
    const chatInput = document.querySelector(".chat-input textarea");

    conversations.forEach(conversation => {
        conversation.addEventListener("click", () => {
            const user = conversation.dataset.user;
            chatHeader.textContent = `Conversation avec ${user}`;
            chatMessages.innerHTML = `<p><strong>${user} :</strong> Bonjour, comment ça va ?</p>`;
            chatInput.focus();
        });
    });
    // right sidebar //



    $('#search_input').on('keydown', function(e) {
        if (e.key === 'ArrowUp') {
            e.preventDefault();
            searchUp(); // Navigate up on ArrowUp
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            searchDown(); // Navigate down on ArrowDown
        }
    });

    const textarea = document.querySelector(".dynamic-textarea");
   
    textarea.addEventListener("input", function() {
        // Reset height to auto to calculate the new height based on content
        this.style.height = "40px";

        // Set the height to either the scrollHeight or maxHeight, whichever is smaller
        const maxHeight = 150; // Max height in pixels
        this.style.height = Math.min(this.scrollHeight, maxHeight) + "px";
    });
    
    var MAX_FILES = 6;

    // Get Laravel's base URL
    $('#load-more').on('click', function() {
        var page = $(this).data('page');
        var button = $(this);
        var loader = $('#loading-spinner'); // Loader for better UX

        loader.show();
        button.prop('disabled', true);

        $.ajax({
            url: `${baseUrl}/message-centre/attachments/${chatId}?page=${page}`,
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
    // end copy clipboard images



    // Initialize Drag and Drop

    var chatMsgArea = document.getElementById("chatMessages"+chat_id);
    var modal = document.getElementById("file-upload-modal");
    var previewContainer = document.getElementById("filePreviewContainer");
    var uploadButton = document.getElementById("upload-button");
    var sendInput = document.getElementById("sendmsgg");
    var selectedFiles = []; // Store selected files
    var formFiles = [];
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
    chatMsgArea.addEventListener("dragover", (e) => {
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
    modal.addEventListener("dragover", function(e) {
        e.preventDefault();
    });
    // Detect file drop inside modal
    modal.addEventListener("drop", function(e) {
        e.preventDefault(); // Prevent browser default behavior
        uploadFiles(e.dataTransfer.files);
    });
    // document.addEventListener("paste", function(event) {
        sendInput.addEventListener('paste', function (event) {
            // //$("#file-upload-modal").modal("show");
            $("#uploadModal").modal("show");

            var items = (event.clipboardData || event.originalEvent.clipboardData).items;
            var files = [];
            var hasNonTextData = true;
            for (var item of items) {
                if (item.type.startsWith("text")) {
                    hasNonTextData = false;
                }else{
                    if (item.kind === "file") {
                        files.push(item.getAsFile());
                    }
                }
            }
            if (files.length > 0) {
                uploadFiles(files);
                $("#uploadModal").modal("show");
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

        var replyTo = $('#reply_to_id').val();
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
                    //$("#file-upload-modal").modal("hide");;
                    $("#uploadModal").modal("hide");
                    resetPreview();
                } else {
                    errorMessage("Upload failed: " + data.message);
                    //$("#file-upload-modal").modal("hide");;
                    $("#uploadModal").modal("hide");
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

function handleAttachmentUpload(event) {
    var fileInput = this;
    var fileNameDiv = document.getElementById('fileName');

    if (fileInput.files && fileInput.files.length > 0) {
        var fileName = fileInput.files[0].name; // Get the name of the first selected file
        fileNameDiv.textContent = `Selected file: ${fileName}`; // Display the file name
    } else {
        fileNameDiv.textContent = ''; // Clear the file name if no file is selected
    }
}
function handleSendMessagKeyDown(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault(); // Prevent default behavior of Enter key
        sendMessage(); // Call your sendMessage function

        const textarea = document.querySelector(".dynamic-textarea");
        if (textarea) {
            textarea.style.height = "40px";
        }
    }
}
function getConversation(chat_id) {
    const showChat = document.getElementById('chat-container');
    $.ajax({
        type: 'POST',
        url: "{{ baseUrl('message-centre/get-conversation/') }}/" + chat_id,
        dataType: 'json',
        data: {
            _token: "{{ csrf_token() }}",
            last_msg_id: last_msg_id
        },
        success: function(data) {
            $('.chat-messages').html(data.contents);
            showmessage - centre.classList.add("mobile-chat")
            last_msg_id = data.last_msg_id;
            // Append new messages
        },
        error: function(xhr, status, error) {
            console.log('Error fetching messages:', error);
        }
    });
}

function backToChats() {
    const showChat = document.getElementById('chat-container');

    if (showChat) {
        showChat.classList.remove("mobile-chat"); // Attempt to remove the class
    }
}



function getSearch(searchval) {
    var inputValue = $('#chatSearch').val();
    if (inputValue.trim() === '') {
        conversationList();
    } else {
        // Only fetch new messages if the last ID has been updated
        $.ajax({
            type: 'get',
            url: "{{ baseUrl('message-centre/search') }}",
            data:{
                search:inputValue
            },
            dataType: 'json',
            success: function(data) {
                $('#conversation-list').html(data.contents);
            },
            error: function(xhr, status, error) {
                console.log('Error fetching messages:', error);
            }
        });

    }
}

function conversationList(load_from = '') {
    var inputValue = $('#chatSearch').val();
    if (inputValue.trim() === '') {
       
        $.ajax({
            type: 'get',
            url: "{{ baseUrl('message-centre/conversation-list') }}",
            dataType: 'json',
            success: function(data) {
                $('#conversation-list').html(data.contents);
                var cid = $("#get_chat_id").val();
                setTimeout(() => {
                    $(".user-chat-item[data-chat-id="+cid+"]").addClass("active-chat");
                    var chatContainer = document.getElementById("conversation-list");
                    const activeChat = chatContainer.querySelector('.active-chat');
                    if (activeChat) {
                        // COMMENTED OUT: This was causing whole page scroll
                        // activeChat.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }, 100);
            },
            error: function(xhr, status, error) {
                console.log('Error fetching messages:', error);
            }
        });


    }
    // refreshMessaging();
    // setTimeout(conversationList, 20000);
}


// var onTypingStop = () => {
//     isTyping = false;
//     updateTypingStatus(0);
// };

// function messageInputChange() {
//     var inputLength = $("#sendmsgg").val().length;

//     if (inputLength > 2 && !isTyping) {
//         updateTypingStatus(1);
//     } else {
//         updateTypingStatus(0);
//     }
//     clearTimeout(typingTimer);
//     typingTimer = setTimeout(onTypingStop, typingDelay);

//     if (inputLength > 0 && inputLength % 5 === 0) {
//         saveDraft($("#sendmsgg").val());
//     }

//     // Optional: Add debounce/throttle to avoid excessive calls
//     clearTimeout(draftTimer);
//     draftTimer = setTimeout(() => {
//         if (inputLength > 0 && inputLength % 5 === 0) {
//             saveDraft($("#sendmsgg").val()); // Save any remaining message
//         }
//     }, 3000);

// }

// function updateTypingStatus(typingStatus) {
//     return false; // for now it is stopped calling this event;
//     if (typingStatus == 0) {
//         isTyping = false;
//     } else {
//         isTyping = true;
//     }
//     $.ajax({
//         url: '{{ baseUrl("message-centre/update-typing") }}',
//         type: 'POST',
//         data: {
//             _token: '{{ csrf_token() }}',
//             chat_id: chatId,
//             is_typing: typingStatus
//         },
//         success: function(response) {
//             //  alert('typing');
//         },
//         error: function(error) {
//             console.log('Error updating typing status', error);
//         }
//     });
// }

function fetchOlderMessages(chat_id) {
    isFetching = true;
    const showChat = document.getElementById('chat-container');
    $.ajax({
        type: 'GET',
        url: "{{ baseUrl('message-centre/fetch-older-chats') }}/" + chat_id + "/" +
            first_msg_id,
        dataType: 'json',
        data: {
            openfrom: 'chatWindow',
            _token: "{{ csrf_token() }}",

        },
        beforeSend: function() {
            $(".unread-message-from").remove();
            if (last_msg_id === 0) {
                $('#sloader').show()
            }
        },
        success: function(response) {
            showChat.classList.add("mobile-chat");
            isFetching = false;
            $('#messages_read').prepend(response.contents);
            first_msg_id = response.first_msg_id;
            conversationList();
            // COMMENTED OUT: This was causing whole page scroll
            // document.getElementById('message-' + response.message_id).scrollIntoView({
            //     behavior: 'smooth'
            // });
        },
        error: function(xhr, status, error) {
            console.log('Error fetching messages:', error);
        }
    });
}

function deleteMessageforAll(chat_msg_id, chat_msg_uid) {
    var geturl = "{{ baseUrl('message-centre/delete-message-for-all/') }}/" + chat_msg_id;
    $.ajax({
        type: 'get',
        url: geturl,
        data: {},
        success: function(data) {
            $('#message-' + chat_msg_uid).html(
                '<div class="message-block"><div class="text-message-block"><div class="message sent"><p class="deleted-message chat-message">This message was deleted.</p></div></div>'
            );
            conversationList();
        }
    });
}





function saveDraft(message) {
    $.ajax({
        type: 'post',
        url: "{{ baseUrl('message-centre/save-message-to-draft') }}",
        data: {
            '_token': "{{ csrf_token() }}",
            'chat_id': chatId,
            'message': message,
        },
        success: function(data) {

        }
    });
}

function sendMessage() {
    var editMessageId = $('#edit_message_id').val();
    var replyTo = $('#reply_to_id').val();
    var myurl = $('#geturl').val();
    const sendbtn = document.getElementById("sendBtn1");
    const msgInput = document.getElementById("sendmsgg");

    sendbtn.classList.add("disbled-btn")
    // msgInput.disabled = true;
    var formData = new FormData();
    formData.append('_token', $('input[name=_token]').val());
    formData.append('reply_to', replyTo);
    formData.append('send_msg', $('#sendmsgg').val());
    formData.append("openfrom", "chatWindow");
    
    var getval = $('#sendmsgg').val();
    for (var i = 0; i < selectedFile.length; i++) {
        formData.append('attachment[]', selectedFile[i]);
    }
    // formData.append('attachment[]', selectedFile); // Append the file
    if (editMessageId) {
        $.ajax({
            type: 'post',
            url: "{{ baseUrl('message-centre/update-message') }}/" + editMessageId,
            data: {
                _token: "{{ csrf_token() }}",
                message: getval
            },
            beforeSend:function(){
                $("#sendmsgg").attr("disabled","disabled");
            },
            success: function(response) {
                $("#sendmsgg").removeAttr("disabled","disabled");
                $('#cpMsg' + editMessageId).html(response.updated_message);
                document.querySelector('#sendmsgg').value = '';
                $('#edit_message_id').val('');
                sendbtn.classList.remove("disbled-btn");
            },
            error: function() {
                console.log('Error updating message');

            }
        });
    } else {


        $.ajax({
            type: 'post',
            url: myurl + "?sendmsg",
            data: formData,

            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend:function(){
                $("#sendmsgg").attr("disabled","disabled");
            },
            success: function(data) {
                $("#sendmsgg").removeAttr("disabled");
                if (data.status == true) {
                    $('#closemodal').click();
                }
                document.querySelector('#sendmsgg').value = '';
                $('#reply_to_id').val('');
                $('#reply_quoted_msg').hide();
                sendbtn.classList.remove("disbled-btn")
                document.querySelectorAll('.image-container').forEach(img => img.remove()); // Clear UI
                //   msgInput.disabled = false;
                if (data.status == false) {

                    errorMessage('Script Tag not allowed');
                }
            }
        });
    }

}

// function toggleFilesSidebar($chatId) {

//     fileSearchInputs(chatId, 'clear');
//     const sidebar = document.getElementById('filesidebar');
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

function previewImage(event) {
    const groupIcon = document.getElementById("groupIcon");
    const file = event.target.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Remove the overlay after image upload
            groupIcon.innerHTML = `<img src="${e.target.result}" alt="Group Icon" />`;
        };
        reader.readAsDataURL(file);
    }
}

function toggleChatsSearch(status) {
    const chatsearch = document.getElementById("chatsSearch");
    chatsearch.style.display = chatsearch.style.display === 'flex' ? 'none' : 'flex';
    if (status == "close") {
        $('#search_input').val('');
        searchChatMessages();
        $('.chat-message').removeClass('highlight');
    }
}
var currentIndex = -1; // Track the selected message
var searchResults = []; // Store matching messages

function searchChatMessages(direction = null) {
  searchResults = [];
  var searchQuery = $('#search_input').val().toLowerCase().trim();
  var chatId = $('#get_chat_id').val();

  // Reset previous results and highlights
  $('.chat-message').removeClass('highlight');

  if (searchQuery.length > 1) {
    // Step 1: Search through currently loaded messages
    $('#messages_read .chat-message').each(function () {
      const messageText = $(this).text().toLowerCase();
      if (messageText.includes(searchQuery)) {
        searchResults.push($(this)); // Store matching messages
      }
    });

    searchResults = searchResults.reverse(); // Prioritize newest matches

    // Step 2: Load older messages if no match found
    if (searchResults.length === 0) {
      fetchOlderMessages(chatId); // Load previous messages

      // Step 3: Wait for messages to load and search again
      let checkForNewMessages = setInterval(() => {
        if (!isFetching) {
          clearInterval(checkForNewMessages);

          // Search newly loaded messages
          let newMatches = [];
          $('#messages_read .chat-message').each(function () {
            const messageText = $(this).text().toLowerCase();
            if (messageText.includes(searchQuery)) {
              newMatches.push($(this)); // Store matched elements
            }
          });

          // Highlight if matches found
          if (newMatches.length > 0) {
            searchResults = newMatches.concat(searchResults);
            highlightMessage(0); // Highlight first match
          } else {
            warningMessage('No match found in previous messages.');
          }
        }
      }, 500); // Check every 500ms until messages are loaded
    } else {
      // Step 4: Handle navigation if matches are found
      if (direction === 'down') {
        searchDown();
      } else if (direction === 'up') {
        searchUp();
      } else {
        highlightMessage(0); // Highlight the first result
      }
    }
  } else {
    if(direction !== null)
        errorMessage('Minimum 2 characters required.');
  }
}

// Highlight a specific message
function highlightMessage(index) {
    $('.chat-message').removeClass('highlight'); // Remove previous highlights
    const message = searchResults[index];
    if (message && message.length > 0) {
        message.addClass('highlight'); // Highlight the message

        // COMMENTED OUT: This was causing whole page scroll
        // Scroll smoothly to the highlighted message
        // message[0].scrollIntoView({
        //     behavior: "smooth",
        //     block: "center"
        // });
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
    if (searchResults.length <= 0) return; // No results
    currentIndex = (currentIndex - 1 + searchResults.length) % searchResults.length; // Wrap around
    highlightMessage(currentIndex);
}
var typingTimeout;
var isTyping = false;

function closeReplyto() {
    const replyModal = document.getElementById('reply_quoted_msg');
    if (replyModal) {
        $('#chatbot_reply_quoted_msg').hide();
        $('#reply_quoted_msg').hide();
        $('#reply_to_id').val('');

    } else {
        console.error("Element with ID 'reply_quoted_msg' not found.");
    }
}




$(document).on('click', '#sendBtn1', function() {
    const textarea = document.querySelector(".dynamic-textarea");
    textarea.style.height = "40px";
    sendMessage();
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

function removeFileByName(files, nameToRemove) {
    return files.filter(file => file.name !== nameToRemove);
}
</script>
<script>
$(document).ready(function() {

    var chatBox = $("#scrollDiv");
    var topBtn = $("#scrollToTop");
    var bottomBtn = $("#scrollToBottom");
    var hideTimeout;
    var scrollTimeout;
    var lastScrollTop = chatBox.scrollTop();

    function showButton(btn, delay) {
        setTimeout(function() {
            btn.addClass("show");

            // Clear previous hide timeout
            clearTimeout(hideTimeout);

            // Hide button after 3-5 seconds
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
    chatBox.scroll(function() {
        var scrollPos = chatBox.scrollTop();
        var scrollHeight = chatBox[0].scrollHeight - chatBox.outerHeight();

        // Hide bottom button when at the bottom
        if (scrollPos >= scrollHeight - 5) {
            bottomBtn.removeClass("show");
        }
        // Check scrolling direction
        if (scrollPos > lastScrollTop) { // Scrolling down
            clearTimeout(scrollTimeout);
            showButton(topBtn, 0);
        } else { // Scrolling up
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(function() {
                showButton(bottomBtn, 0);
            }, 2000); // Show after 3 seconds
        }
        lastScrollTop = scrollPos;
    });
   // console.log(chatBox[0]);
    if(chatBox[0]){
    // COMMENTED OUT: This was causing whole page scroll on load
    // Auto-hide bottom button if already at bottom
    // chatBox.scrollTop(chatBox[0].scrollHeight);
}
});

/* select all checkbox */
function hideCheckbox(checkBoxId) {

    if ($("#" + checkBoxId).is(":checked")) {
        alert('checked');
        $("#" + checkBoxId).prop("checked", false);
    } else {
        $("#" + checkBoxId).prop("checked", true);
    }

}
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
function removeFilePreview(index) {
    // Remove preview from DOM
    var previewDiv = document.querySelector(`.file-preview[data-index="${index}"]`);
    if (previewDiv) {
        previewDiv.remove();
    }

    // Remove file from array (splicing at the specific index)
    filesToUpload.splice(index, 1);

    // Reassign new indices to remaining files in the array and DOM
    updateFilePreviews();
}

// Update file preview indices after removing a file
function updateFilePreviews() {
    const allPreviews = document.querySelectorAll('.file-preview');
    allPreviews.forEach((preview, idx) => {
        preview.dataset.index = idx; // Reassign the index
    });
}

</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const container = document.querySelector(".users-search-result .search-users");
    const userList = container.querySelector(".user-lists");
    var items;
    if(container.querySelector(".user-lists")){
        items = userList.children;
    }

    let hiddenCount = 0;
    const containerBottom = container.getBoundingClientRect().bottom;

    // Remove existing counter if any
    const existingCounter = document.querySelector(".user-more-counter");
    if (existingCounter) existingCounter.remove();

    for (let i = 0; i < items.length; i++) {
        const item = items[i];
        const itemBottom = item.getBoundingClientRect().bottom;

        if (itemBottom > containerBottom) {
            hiddenCount = items.length - i;
            break;
        }
    }

    if (hiddenCount > 0) {
        const moreDiv = document.createElement("div");
        moreDiv.className = "user-more-counter";
        moreDiv.textContent = `+${hiddenCount} more`;
        container.appendChild(moreDiv);
    }
});
</script>

@endsection