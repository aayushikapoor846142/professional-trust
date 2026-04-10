var userId = currentUserId;

Dropzone.autoDiscover = false;
var mediaDropzone;
var media_files_uploaded = [];
var timestamp = "{{time()}}";
var upload_count = 0;
var isError = 0;
var last_comment_id = 0;
var draftTimer;
var discussion_id = 0;
var prev_feed_id = 0;
var cssdisplay = $('#chatsSearch').css('display');
var typingTimer; // Timer identifier
var typingDelay = 5000;
var last_react_id = 0;
var first_msg_id = 0;
var isFetching = false;
var chatArea;
var modal;
var previewContainer;
var uploadButton;
let v_page = 1;
let mainContainer;
var selectedFiles = []; // Store selected files
var formFiles = [];
var MAX_FILES = 6;
// Declare the functions here
let filesToUpload = [];
var file_files_uploaded = [];
var custom_editor_main;
var custom_editor_upload;
function initializeDiscussionSocket(discussionId, prevFeedId = '') {
    if (prevFeedId != '') {
        window.Echo.leave(`discussion-threads.${prevFeedId}`);
    }
   
    window.Echo.private(`discussion-threads.${discussionId}`).listen('DiscussionThreadSocket', (e) => {
   
        var response = e.data;
        if (response.action == 'new_comment' ) {
            if (response.last_comment_id != last_comment_id) {
                fetchComments(false,'','socket');
            }
        }
        if (response.action == 'comment_deleted') {
            var commentUniqueId = response.commentUniqueId;
            $('#comment-' + commentUniqueId).remove();
            //  conversationList();
        }
        if (response.action == 'comment_edited') {
            var commentUniqueId = response.commentUniqueId;
            $('#editedMsg' + commentUniqueId).html('edited');
            $('#cpMsg' + commentUniqueId).html(response.editedComment);
            // conversationList();
        }
    });
}


function initializeDiscussionContent(discussionId) {
        
    selectedFiles = []; // Store selected files
    formFiles = [];
    MAX_FILES = 6;
    prev_feed_id = discussionId
    last_comment_id = 0;
    // feed_id = discussionId;
    discussion_id = discussionId;
    cssdisplay = $('#chatsSearch').css('display');
    last_comment_id = 0;
    last_react_id = 0;
    first_msg_id = 0;
    isFetching = false;
    draftTimer = '';
    ACTIVE_FEED_CONTENT = discussion_id;
    new EmojiPicker(".message-emoji-icon", {
        targetElement: "#commentBox",
        onEmojiSelect:function(emoji){
            if (custom_editor_main) {
                custom_editor_main.appendText(emoji);
            }
        }
    });
    new EmojiPicker(".message-emoji-icon-modal", {
        targetElement: "#commentModal",
        onEmojiSelect:function(emoji){
            if (custom_editor_upload) {
                custom_editor_upload.appendText(emoji);
            }
        }
    });
    if(document.getElementById("scrollDiv")){
        document.getElementById("scrollDiv").addEventListener("scroll", function() {
            var scrollDiv = this;
            if (scrollDiv.scrollTop === 0 && !isFetching) {
                // Fetch older messages
                if (first_msg_id > 0) {
                    fetchOlderChats();
                }
            }
        });
    }

    if (discussion_id != "" && discussion_id > 0) {
   
        $(document).ready(function() {
         
            fetchComments(discussion_id);
            //  updateTypingStatus(0);
        });
        var editorDiv = $('#commentBox'); // Input area
        var typingTimeout; // Timer for typing status
        var isTyping = false;
        editorDiv.on('input', function() {
            var inputLength = $(this).val().length;
            // if (inputLength > 2 && !isTyping) {
            //     updateTypingStatus(1);
            // } else {
            //     updateTypingStatus(0);
            // }
            var inputText = $(this).val().trim();
            var lastChar = inputText.slice(-1);
            var atSymbolPosition = inputText.lastIndexOf('@');
            if (lastChar === '@') {
                showUserList(inputText);
            } else if (atSymbolPosition !== -1) {
                var query = inputText.slice(atSymbolPosition + 1).toLowerCase();

                if (query === 'everyone') {
                    showMentionAllOption();
                } else {
                    var filteredUsers = users.filter(user => user.toLowerCase().includes(query));
                    showUserList(filteredUsers);
                }
            } else {
                hideUserList();
            }
            // clearTimeout(typingTimer); 
            // typingTimer = setTimeout(onTypingStop, typingDelay);

            // if (inputLength > 0 && inputLength % 5 === 0) {
            //     saveDraft($("#commentBox").val());
            // }

            // Optional: Add debounce/throttle to avoid excessive calls
            // clearTimeout(draftTimer);
            // draftTimer = setTimeout(() => {
            //     if (inputLength > 0 && inputLength % 5 === 0) {
            //         saveDraft(message); // Save any remaining message
            //     }
            // }, 3000);

        });
        // editorDiv.on('blur', function () {
        //     updateTypingStatus(0);
        // });

        

        function handleMention(event) {
            var inputText = messageInput.value;
            var lastChar = inputText.slice(-1);
            var atSymbolPosition = inputText.lastIndexOf('@');

            if (lastChar === '@') {
                showUserList(inputText);
            } else if (atSymbolPosition !== -1) {
                var query = inputText.slice(atSymbolPosition + 1).toLowerCase();

                if (query === 'everyone') {
                    showMentionAllOption();
                } else {
                    var filteredUsers = users.filter(user => user.toLowerCase().includes(query));
                    showUserList(filteredUsers);
                }
            } else {
                hideUserList();
            }
        }

        function showUserList(filteredUsers) {
            if (filteredUsers.length === 0) {
                userList.style.display = 'none';
                return;
            }

            userList.innerHTML = '';
            filteredUsers.forEach(user => {
                var userItem = document.createElement('div');
                userItem.textContent = user;
                userItem.addEventListener('click', () => handleUserClick(user));
                userList.appendChild(userItem);
            });

            userList.style.display = 'block';
        }

        function showMentionAllOption() {
            userList.innerHTML = ''; // Clear any existing user suggestions
            var mentionAllItem = document.createElement('div');
            mentionAllItem.textContent = allUsersTag;
            mentionAllItem.addEventListener('click', () => handleMentionAll());
            userList.appendChild(mentionAllItem);
            userList.style.display = 'block';
        }

        function hideUserList() {
            userList.style.display = 'none';
        }

        function handleUserClick(user) {
            var inputText = editorDiv.val();
            var atSymbolPosition = inputText.lastIndexOf('@');
            var newMessage = inputText.slice(0, atSymbolPosition) + '*@' + user + ' *';
            editorDiv.val(newMessage); // Update the text content

            editorDiv.trigger("focus");

            // Move the caret to the end of the text
            var range = document.createRange();
            var sel = window.getSelection();
            range.selectNodeContents(editorDiv[0]);
            range.collapse(false); // Collapse to the end
            sel.removeAllRanges();
            sel.addRange(range);
            hideUserList();
        }

        function toggleShareIcons(postId) {
            var allShareLists = document.querySelectorAll('.share-list');
            var currentShareList = document.getElementById(`sharedList-${postId}`);
            var isVisible = currentShareList.classList.contains('visible');
            allShareLists.forEach((list) => {
                list.classList.add('hidden');
                list.classList.remove('visible');
            });


            if (!isVisible) {
                currentShareList.classList.remove('hidden');
                currentShareList.classList.add('visible');
            }
        }
        // Get Laravel's base URL
        


    }
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

    // function removeFile(index) {
    //     formFiles.splice(index, 1);
    //     updatePreview();
    // }
    
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
                    filePreview =
                        `<img src="${e.target.result}" alt="Preview" style="height:50px;width:50px;"><p>${file.name}</p>`;
                } else {
                    if (fileExtension == 'pdf') {
                        filePreview =
                            `<img src='assets/images/chat-icons/pdf-icon.png' alt="Preview" style="height:50px;width:50px;"><p>${file.name}</p>`;
                    } else if (fileExtension == 'xls' || fileExtension == 'xlsx') {
                        filePreview =
                            `<img src='assets/images/chat-icons/xls-icon.png' alt="Preview" style="height:50px;width:50px;"><p>${file.name}</p>`;
                    } else if (fileExtension == 'doc' || fileExtension == 'docx') {
                        filePreview =
                            `<img src='assets/images/chat-icons/doc-icon.png' alt="Preview" style="height:50px;width:50px;"><p>${file.name}</p>`;
                    } else {
                        filePreview =
                            `<img src='assets/images/chat-icons/file-icon.png' alt="Preview" style="height:50px;width:50px;"><p>${file.name}</p>`;
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
    
    document.addEventListener("paste", function(event) {
        
        var items = (event.clipboardData || event.originalEvent.clipboardData).items;
        var files = [];
        var hasNonTextData = false;
        for (var item of items) {
            if (item.kind === "file") {
                files.push(item.getAsFile());
                hasNonTextData = true;
            }
        }
        if(hasNonTextData){
            // $("#file-upload-modal").modal("show");
            if (files.length > 0) {
                uploadFiles(event.dataTransfer.files);
            }
        }
        
    });
    // Function to remove file from preview


    // Upload function
    // function uploadFiles(files) {
    //     if (formFiles.length + files.length > MAX_FILES) {
    //         errorMessage("You can only upload a maximum " + MAX_FILES + " files.");
    //         return;
    //     }


    //     selectedFiles = Array.from(files); // Store selected files
    //     // previewContainer.innerHTML = ""; // Clear previous preview
    //     uploadButton.disabled = selectedFiles.length === 0; // Enable upload button

    //     function getFileExtension(file) {
    //         var fileName = file.name;
    //         var extension = fileName.split('.').pop().toLowerCase(); // Convert to lowercase for consistency
    //         return extension;
    //     }
    //     selectedFiles.forEach((file, index) => {

    //         if (file.size > 25 * 1024 * 1024) { // Check if file is larger than 1 MB
    //             errorMessage("File " + file.name + " is too large. Maximum allowed size is 25 MB.");
    //             return; // Skip this file if it's too large
    //         }
    //         var reader = new FileReader();
    //         reader.onload = function(e) {
    //             formFiles.push(file);
    //             var previewItem = document.createElement("div");
    //             previewItem.classList.add("preview-item");

    //             var fileExtension = getFileExtension(file);
    //             var filePreview = "";
    //             if (file.type.startsWith("image/")) {
    //                 filePreview =
    //                     `<img src="${e.target.result}" alt="Preview" style="height:50px;width:50px;"><p>${file.name}</p>`;
    //             } else {
    //                 if (fileExtension == 'pdf') {
    //                     filePreview =
    //                         `<img src='assets/images/chat-icons/pdf-icon.png' alt="Preview" style="height:50px;width:50px;"><p>${file.name}</p>`;
    //                 } else if (fileExtension == 'xls' || fileExtension == 'xlsx') {
    //                     filePreview =
    //                         `<img src='assets/images/chat-icons/xls-icon.png' alt="Preview" style="height:50px;width:50px;"><p>${file.name}</p>`;
    //                 } else if (fileExtension == 'doc' || fileExtension == 'docx') {
    //                     filePreview =
    //                         `<img src='assets/images/chat-icons/doc-icon.png' alt="Preview" style="height:50px;width:50px;"><p>${file.name}</p>`;
    //                 } else {
    //                     filePreview =
    //                         `<img src='assets/images/chat-icons/file-icon.png' alt="Preview" style="height:50px;width:50px;"><p>${file.name}</p>`;

    //                 }
    //                 //   filePreview = `<button data-index="${index}" type="button" class="remove-file">X</button> `;
    //             }
    //             previewItem.innerHTML = `
    //                 ${filePreview}
    //                 <button data-index="${index}" type="button" class="remove-file">X</button>
    //             `;
    //             previewContainer.appendChild(previewItem);

    //         };
    //         reader.readAsDataURL(file);
    //     });
    // }
    // function resetPreview() {
    //     selectedFiles = [];
    //     previewContainer.innerHTML = "";
    //     uploadButton.disabled = true;
    //     formFiles = [];
    //     files = [];
    // }
}


function uploadFiles(files) {
    if (formFiles.length + files.length > MAX_FILES) {
        errorMessage("You can only upload a maximum " + MAX_FILES + " files.");
        return;
    }
    selectedFiles = Array.from(files); // Store selected files
    // previewContainer.innerHTML = ""; // Clear previous preview
    uploadButton.disabled = selectedFiles.length === 0; // Enable upload button

    function getFileExtension(file) {
        var fileName = file.name;
        var extension = fileName.split('.').pop().toLowerCase(); // Convert to lowercase for consistency
        return extension;
    }
    selectedFiles.forEach((file, index) => {

        if (file.size > 25 * 1024 * 1024) { // Check if file is larger than 1 MB
            errorMessage("File " + file.name + " is too large. Maximum allowed size is 25 MB.");
            return; // Skip this file if it's too large
        }
        var reader = new FileReader();
        reader.onload = function(e) {
            formFiles.push(file);
            var previewItem = document.createElement("div");
            previewItem.classList.add("preview-item");

            var fileExtension = getFileExtension(file);
            var filePreview = "";
            if (file.type.startsWith("image/")) {
                filePreview =
                    `<img src="${e.target.result}" alt="Preview" style="height:50px;width:50px;"><p>${file.name}</p>`;
            } else {
                if (fileExtension == 'pdf') {
                    filePreview =
                        `<img src='assets/images/chat-icons/pdf-icon.png' alt="Preview" style="height:50px;width:50px;"><p>${file.name}</p>`;
                } else if (fileExtension == 'xls' || fileExtension == 'xlsx') {
                    filePreview =
                        `<img src='assets/images/chat-icons/xls-icon.png' alt="Preview" style="height:50px;width:50px;"><p>${file.name}</p>`;
                } else if (fileExtension == 'doc' || fileExtension == 'docx') {
                    filePreview =
                        `<img src='assets/images/chat-icons/doc-icon.png' alt="Preview" style="height:50px;width:50px;"><p>${file.name}</p>`;
                } else {
                    filePreview =
                        `<img src='assets/images/chat-icons/file-icon.png' alt="Preview" style="height:50px;width:50px;"><p>${file.name}</p>`;

                }
                //   filePreview = `<button data-index="${index}" type="button" class="remove-file">X</button> `;
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

function resetPreview() {
    selectedFiles = [];
    previewContainer.innerHTML = "";
    uploadButton.disabled = true;
    formFiles = [];
    files = [];
    closeModal();
}

function loadDiscussionContentAjax(conversation_id, discussion_id,open_comment = 0) {
  
    v_page = 1;
    $.ajax({
        type: 'GET',
        url: BASEURL+"/discussion-threads/content-ajax/" + conversation_id,
        dataType: 'json',
        data: {
            _token: csrf_token,
            open_comment:open_comment,
        },
        beforeSend: function() {
            $(".feed-container").html('');
        },
        success: function(response) {
            $(".feed-container").html('');
            if (response.status) {
                $(".feed-left-side").hide();
                // $("#discussion-categories").hide();
                $(".feed-container").show();
                $(".feed-container").html(response.contents);
                setTimeout(() => {
                    $(".feed-container").promise().done(() => {
                        custom_editor_main = CustomEditor.init(".custom_editor_main",{
                            users: {
                                url: BASEURL+'/discussion-threads/fetch-discussion-thread-users',
                                method: 'POST',
                                data: {
                                    _token: csrf_token,
                                    discussion_id:discussion_id
                                },
                                queryParam: 'search',
                                dataPath: 'users',
                                transform: (user) => ({
                                    id: user.id,
                                    name: user.name,
                                    avatar: ''
                                })
                            },
                            usersLoadDelay: 300, // Debounce delay for AJAX requests
                            onUsersLoad: (users, query) => {
                                console.log(`Loaded ${users.length} users for query: ${query}`);
                            },
                            onUsersError: (error, query) => {
                                console.error('Failed to load users:', error);
                            },
                            onMention: (user) => {
                                // Handle mention - maybe notify the user, save to database, etc.
                                console.log(`${user.name} was mentioned!`);
                            },
                            onChange: (content) => {
                                // Auto-save or other functionality
                                console.log('Editor content updated');
                            }
                        });
                        custom_editor_upload = CustomEditor.init(".custom_editor_upload_file");
                        
                        initializeDiscussionSocket(discussion_id, prev_feed_id);
                        initializeDiscussionContent(discussion_id);
                    });
                    chatArea = document.getElementById("chatMessages");
                    mainContainer = document.getElementById("layoutSidenav_content");
                    modal = document.getElementById("file-upload-modal");
                    previewContainer = document.getElementById("preview-container");
                    uploadButton = document.getElementById("upload-button");

                    mainContainer.addEventListener("dragover", (e) => {
                        e.preventDefault();
                        $("#uploadModal").modal("show");
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

                    uploadButton.addEventListener("click", function() {

                        var replyTo = $('#reply_to_id').val();
                        var myurl = $('#geturl').val();

                        if (formFiles.length === 0) {
                            errorMessage("Upload files to upload");
                            return false;
                        }
                        $("#upload-button").attr("disabled", "disabled");
                        var formData = new FormData();
                        formData.append("_token", csrf_token);

                        formFiles.forEach((file) => {
                            formData.append("attachment[]", file);
                        });
                        formData.append('reply_to', replyTo);
                        formData.append('send_msg', $('#commentBox').val());

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
                            .catch(error => console.log("Upload Error:", error));
                        });
                }, 1000);
            }
        },
        error: function(xhr, status, error) {
            console.log('Error fetching messages:', error);
        }
    });
}
function showReplies(parent_comment_id,toggleReply=''){
    $.ajax({
        type: 'post',
        url: BASEURL+"/discussion-threads/fetch-comment-replies",
        data: {
            _token:csrf_token,
            parent_comment_id:parent_comment_id
        },
        dataType:"json",
        success: function (data) {
            $(".parent-replies").hide();
            if(toggleReply != ''){
                if(toggleReply == 'show'){
                    $('#pr-' + parent_comment_id).html(data.contents);
                    $('#pr-' + parent_comment_id).show();
                }else{
                    $('#pr-' + parent_comment_id).html('');
                    $('#pr-' + parent_comment_id).hide();
                }
                
            }
            $("#comment-count-"+parent_comment_id).html(data.comment_counts+" comment(s)");
        }
    });
}
function likeComment(emoji,comment_id,discussion_board_id){
    $.ajax({
        type: 'post',
        url: BASEURL+"/discussion-threads/comment/"+comment_id+"/like",
        data: {
            _token:csrf_token,
            discussion_board_id:discussion_board_id,
            comment_id:comment_id,
            emoji:emoji
        },
        dataType:"json",
        success: function (data) {
            $("#comment-"+data.comment_id).replaceWith(data.contents);
        }
    });
}
function unlikeComment(id,comment_id,discussion_board_id){
    $.ajax({
        type: 'post',
        url: BASEURL+"/discussion-threads/comment/"+id+"/unlike",
        data: {
            _token:csrf_token,
            comment_id:comment_id,
            discussion_board_id:discussion_board_id,
        },
        dataType:"json",
        success: function (data) {
            $("#comment-"+data.comment_id).replaceWith(data.contents);
        }
    });
}
function markCommentAsAnswer(comment_id){
    $.ajax({
        type: 'post',
        url: BASEURL+"/discussion-threads/comment/"+comment_id+"/mark-as-answer",
        data: {
            _token:csrf_token,
        },
        dataType:"json",
        success: function (data) {
            $("#comment-"+data.comment_id).replaceWith(data.contents);
        }
    });
}
function removeCommentAsAnswer(comment_id){
    $.ajax({
        type: 'post',
        url: BASEURL+"/discussion-threads/comment/"+comment_id+"/remove-as-answer",
        data: {
            _token:csrf_token,
        },
        dataType:"json",
        success: function (data) {
            $("#comment-"+data.comment_id).replaceWith(data.contents);
        }
    });
}

function markPotentialAnswer(comment_id){
    $.ajax({
        type: 'post',
        url: BASEURL+"/discussion-threads/comment/"+comment_id+"/potential-answer",
        data: {
            _token:csrf_token,
        },
        dataType:"json",
        success: function (data) {
            $("#comment-"+data.comment_id).replaceWith(data.contents);
        }
    });
}
function removePotentialAnswer(comment_id){
    $.ajax({
        type: 'post',
        url: BASEURL+"/discussion-threads/comment/"+comment_id+"/remove-potential-answer",
        data: {
            _token:csrf_token,
        },
        dataType:"json",
        success: function (data) {
            $("#comment-"+data.comment_id).replaceWith(data.contents);
        }
    });
}

function loadMoreComment(){
    var order_type = $("#order_type").val();
    fetchComments(false,order_type,'load_more');
}
function fetchComments(load_first_comment = false,order_type='',call_from = 'load') {
    if (load_first_comment) {
        last_comment_id = 0;
    }
  
    
    var discussionId = $('#get_discussion_id').val();
    // var discussionId = 30;
    if(discussionId === undefined){
        return false;
    }
   
    var showChat = document.getElementById('chat-container');
    if(order_type == ''){
        order_type = $("#order_type").val();
    }
    var comment_id = '';
    if(order_type == 'older_comments'){
        comment_id = first_msg_id;
    }else{
        comment_id = last_comment_id;
    }
    $.ajax({
        type: 'POST',
        url: BASEURL+"/discussion-threads/fetch-content/" + discussionId,
        dataType: 'json',
        data: {
            _token: csrf_token,
            last_comment_id:last_comment_id,
            first_comment_id:first_msg_id,
            order_type:order_type,
            call_from:call_from,
        },
        beforeSend: function() {
            if (last_comment_id === 0) {
                if (window.innerWidth < 991) {
                    $(".loader").show();
                }
            }
        },
        success: function(response) {
            $(".loader").hide();
            if(response.has_prev_comment == 0){
                $("#load_more").hide();
            }else{
                $("#load_more").show();
                $("#load_more button").html("Load More ("+response.has_prev_comment+" more comments)");
            }
            
            if (response.new_comment) {
                $('.welcome-chat').hide();
                if (!document.getElementById('comment-' + response.last_comment_unique_id)) {
                    if (comment_id == 0) {
                        $('#messages_read').html(response.contents);
                    } else {
                        if(call_from == 'socket'){
                            $('#messages_read').prepend(response.contents);
                        }else{
                            $('#messages_read').append(response.contents);
                        }
                    }
                }else{
                    // if(order_type == 'most_recent'){
                    //     $('#messages_read').append(response.contents);
                    // }else{
                    //     $('#messages_read').prepend(response.contents);
                    // }
                }
                conversationList();
                // var target = $('#message-'+response.message_id);
                // document.getElementById('comment-' + response.comment_id).scrollIntoView({
                //     behavior: 'smooth'
                // });
            }
            last_comment_id = response.last_comment_id;
            first_msg_id = response.first_comment_id;
        },
        error: function(xhr, status, error) {
            console.log('Error fetching messages:', error);
        }
    });
}

function fetchOlderChats(load_first_comment = false) {
    if (load_first_comment) {
        last_comment_id = 0;
    }

    var groupId = $('#get_discussion_id').val();
    var showChat = document.getElementById('chat-container');
    $.ajax({
        type: 'POST',
        url: BASEURL+"/feeds/fetch-older-feeds/" + discussionId + "/" + first_comment_id,
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
            first_comment_id = response.first_comment_id;
            conversationList();
            document.getElementById('comment-' + response.comment_id).scrollIntoView({
                behavior: 'smooth'
            });
        },
        error: function(xhr, status, error) {
            console.log('Error fetching messages:', error);
        }
    });
}

function getSearch(searchval) {
    var inputValue = $('#feedsSearch').val();
    if (inputValue.trim() === '') {

    } else {
        // Only fetch new messages if the last ID has been updated
        $.ajax({
            type: 'get',
            url: BASEURL+"/feeds/search/" + inputValue,
            dataType: 'json',
            success: function(data) {
                $('#feeds-sidebar-list').html(data.contents);
                // Append new messages
            },
            error: function(xhr, status, error) {
                console.log('Error fetching messages:', error);
            }
        });
    }
}

function conversationList(v_page = 1,categoryId = null) {

    var categoryId = $('#categoryId').val();
  
    var inputValue = $('#feedsSearch').val();
    var type = $('#list-feed-data').val();
    $("#feedLink").hide(); 
    // if (inputValue.trim() === '') {
        $.ajax({
            type: 'get',
            url: BASEURL+"/discussion-threads/discussion-sidebar-list?type=" + type + "&page=" + v_page,
            dataType: 'json',
            data:{   
                categoryId:categoryId
            }, 
            beforeSend: function() {
                $("#loading-spinner").show(); 
            },
            success: function(data) {
                if (v_page === 1) {
             
                    // If it's the first page of search results, replace existing content
                    $("#discussion-search").show();
                    $("#feeds-sidebar-list").html(data.contents);
                    $("#loading-spinner").hide();
                } else {
                    // Otherwise, append the data
                    $("#feeds-sidebar-list").append(data.contents);
                    $("#loading-spinner").hide();
                }
            
        
                    if (data.current_page < data.last_page) {
                        $("#feedLink").show();  
                    } else {
                        $("#feedLink").hide(); 
                    }

            },
            error: function(xhr, status, error) {
                console.log('Error fetching messages:', error);
                $("#loading-spinner").hide(); 
            }
        });

    // }
}



function replyTo(chat_msg_id, msg) {
    $('#reply_to_id').val(chat_msg_id);
    $('#reply_quoted_msg').show();
    $('#myreply').html(msg);
    var messageElement = document.getElementById('reply_quoted_msg');
    messageElement.scrollIntoView({
        behavior: 'smooth',
        block: 'center'
    });

}
function sendMessage() {
    var editCommentId = $('#edit_comment_id').val();
    var replyTo = $('#reply_to_id').val();
   // var getval = $('#commentBox').val();
   var getval =  document.getElementById("commentBox").value;
    var myurl = $('#geturl').val();
    var sendbtn = document.getElementById("sendBtn1");
    var msgInput = document.getElementById("commentBox");
    sendbtn.classList.add("disbled-btn")
    if (editCommentId) {
        $.ajax({
            type: 'post',
            url: BASEURL+"/discussion-threads/update-comment/" + editCommentId,
            data: {
                _token: csrf_token,
                comment: getval
            },
            success: function(response) {
                $('#cpMsg' + editCommentId).html(response.updated_comment);
                // document.getElementById("editor").innerHTML = '';
                document.getElementById("commentBox").value = '';
                custom_editor_main.reset();
                $('#edit_comment_id').val('');
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
            data: {
                '_token': csrf_token,
                'comment': getval,
                'reply_to': replyTo,
            },
            success: function(data) {
                $('#closemodal').click();
                var getid = data.id - 1;
                // document.getElementById("editor").innerHTML = '';
                $('#reply_quoted_msg').hide();
                $('#reply_to_id').val('');
                 custom_editor_main.clear();
                //updateTypingStatus(0);
                sendbtn.classList.remove("disbled-btn")
                if (data.status == false) {
                    errorMessage('Script Tag not allowed');
                }
            }
        });
    }

    $('#commentBox').val('');
}
function submitForm() {
    var is_valid = formValidation("form");
    if (!is_valid) {
        return false;
    }

    var formData = new FormData($('#discussionCreateForm')[0]);
    var url = $("#discussionCreateForm").attr('action');
    $.ajax({
        url: url,
        type: "post",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        beforeSend: function() {
            $('#loader').show();
        },
        success: function(response) {
            $('#loader').hide();
            if (response.status == true) {
                successMessage(response.message);
                redirect(response.redirect_back)
                //   window.location.reload();
            } else {
                validation(response.message);
            }
        },
        error: function() {
            $('#loader').hide();
            internalError();
        }
    });
}
function toggleSidebar() {
    var sidebar = document.getElementById('profilesidebar');
    var screenWidth = window.innerWidth;
    if (screenWidth < 500) {
        var currentPosition = sidebar.style.right;
        if (currentPosition === '0px') {
            sidebar.style.right = '-100%';
        } else {
            sidebar.style.right = '0';
        }
    } else {
        var currentPosition = sidebar.style.right;
        if (currentPosition === '0px') {
            sidebar.style.right = '-340px';
        } else {
            sidebar.style.right = '0';
        }
    }
}
function previewImage(event, val) {


    var fileInput1 = event.target; // First file input
    var fileInput2 = document.getElementById('fileInput2'); // Second file input

    if (fileInput1.files.length > 0) {
        var file = fileInput1.files[0]; // Get the selected file

        // Create a new DataTransfer object
        var dataTransfer = new DataTransfer();
        dataTransfer.items.add(file); // Add the file to DataTransfer

        // Assign the file to the second file input
        fileInput2.files = dataTransfer.files;

        console.log('File transferred successfully!');
    }

    var groupIcon = document.getElementById("groupIcon");
    var file = event.target.files[0];
    $('#getval').val(file.name);
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            // Remove the overlay after image upload
            groupIcon.innerHTML = `<img src="${e.target.result}" alt="Group Icon" />`;
        };
        reader.readAsDataURL(file);
    }
}

function deleteDiscussionComment(comment_id, comment_uid) {
   
    var geturl = BASEURL+"/discussion-threads/" + comment_uid + '/delete';
    $.ajax({
        type: 'get',
        url: geturl,
        data: {},
        success: function(data) {
            $('#comment-' + comment_uid).remove();
        }
    });
}
function closeReplyto() {
    var replyModal = document.getElementById('reply_quoted_msg');
    if (replyModal) {
        $('#reply_quoted_msg').hide();
        $('#reply_to_id').val('');
    } else {
        console.log("Element with ID 'reply_quoted_msg' not found.");
    }
}
function scrollToMessage(comemntId) {
    var messageElement = document.getElementById('comment-' + comemntId);
    $('#cpMsg' + comemntId).addClass('highlight-message');
    if (messageElement) {
        messageElement.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });

    }
    setTimeout(function() {
        $('#cpMsg' + comemntId).removeClass('highlight-message');
    }, 6000); // 3000 milliseconds = 3 seconds

}
function editDiscussionComment(commentId, uniqueId) {
    var commentElement = $('#cpMsg' + uniqueId);
    var editCommentId = $('#edit_comment_id').val(uniqueId);
    custom_editor_main.setValue(commentElement.text());

}
function getDiscussionThreadSearch(searchval) {
    var inputValue = $('#discussionThreadSearch').val();
    var type = $('#list-feed-data').val();

    if (inputValue.trim() === '') {
        listDiscussionsData();
    } else {
        // conversationList();
        $.ajax({
            type: 'get',
            url: BASEURL+"/discussion-threads/discussion-sidebar-list?type=" + type + '&search=' + inputValue,
            dataType: 'json',
            success: function(data) {
                $('#feeds-sidebar-list').html(data.contents);
            },
            error: function(xhr, status, error) {
                console.log('Error fetching messages:', error);
            }
        });
    }
}
function editDiscussion(id){
    
    $.ajax({
        type: 'get',
        url: BASEURL+"/discussion-threads/edit/" + id,
        dataType: 'json',
        beforeSend:function(){
            $('.edit-discussion-content').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i></div>');
        },
        success: function(data) {
            $('.edit-discussion-content').html(data.contents);
            document.querySelector('.edit-discussion-content').scrollIntoView({
                behavior: 'smooth', // smooth scrolling animation
                block: 'start'      // scroll to top of the element
            });
        },
        error: function(xhr, status, error) {
            console.log('Error fetching messages:', error);
        }
    });
}
function listDiscussionsData(type, element) {
    
    $('a').removeClass('active')
    $(element).addClass('active');
    $('#list-feed-data').val(type);
    var categoryId = $("#categoryId").val();
    // conversationList();
    $.ajax({
        type: 'get',
        url: BASEURL+"/discussion-threads/discussion-sidebar-list?type=" + type+"&categoryId=" + categoryId,
        dataType: 'json',
        success: function(data) {
            $('#feeds-sidebar-list').html(data.contents);
        },
        error: function(xhr, status, error) {
            console.log('Error fetching messages:', error);
        }
    });

}
function confirmDiscussionAction(e) {
    var url = $(e).attr("data-href");
    Swal.fire({
        title: "Are you sure to delete?",
        text: "Deleted Discussion related all comments",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "btn btn-primary",
        cancelButtonClass: "btn btn-danger ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            redirect(url);
        }
    });
}

// Script to initialize on page ready

$(document).ready(function() {
     $(document).on("change","#order_type",function(){
        if($(this).val()  == 'most_recent'){
            fetchComments(false,$(this).val());
        }else{
            fetchComments(true,$(this).val());
        }
    });
    var textarea = document.querySelector(".dynamic-textarea");
    if (textarea) {
        textarea.addEventListener("input", function() {
            // Reset height to auto to calculate the new height based on content
            this.style.height = "40px";

            // Set the height to either the scrollHeight or maxHeight, whichever is smaller
            var maxHeight = 150; // Max height in pixels
            this.style.height = Math.min(this.scrollHeight, maxHeight) + "px";
        });
    }
    $(document).on("click", ".cdsTYDashboard-feed-list-segment,.cdsTYDashboard-feed-action-button-link", function() {
        var url = $(this).data("href");
        $(".cdsTYDashboard-feed-list-segment").removeClass("active-dicussion");
        $(this).addClass("active-dicussion");
        var conversation_id = $(this).data("unique-id");
        var discussionid = $(this).data("discussion-id");
 
        var type = $(this).data("type");
        var open_comment = 0;
        if(type == 'comment'){
            open_comment = 1;
        }
        loadDiscussionContentAjax(conversation_id, discussionid,open_comment);
        history.pushState(null, '', url);
    });

    $(document).on("click", ".like-btn", function(e) {
        e.preventDefault();
        // Get the feed ID from the button's data attribute
        var discussionId = $(this).data('id');
        var button = $(this); // Reference to the clicked button

        $.ajax({
            url: BASEURL + `/feeds/${discussionId}/like`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.liked) {
                    button.html('<i class="fa-solid fa-thumbs-up"></i> Liked');
                } else {
                    button.html('<i class="fa-regular fa-thumbs-up"></i> Like');
                }
            },
            error: function(xhr, status, error) {
                console.log('Error:', error);
            }
        });
    });

    $(document).on("click", "#load-more", function() {
        var page = $(this).data('page');
        var discussionId = discussion_id;
        var button = $(this);
        var loader = $('#loading-spinner'); // Loader for better UX

        loader.show();
        button.prop('disabled', true);

        $.ajax({
            url: `${BASEURL}/feeds/group-medias/${discussionId}?page=${page}`,
            type: 'GET',
            success: function(response) {
                if (response.data.length > 0) {
                    $('#medias-container').append(response.html); // Append new medias
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
  

    $(document).on("click", "#feedLink", function (event) {
        v_page++;
        conversationList(v_page);
    });
    
    
    $(document).on('click', '#sendBtn1', function() {
        // var textarea = document.querySelector(".dynamic-textarea");
        // textarea.style.height = "40px";
        sendMessage();
    });

    $(document).on('show.bs.modal', '#file-upload-form', function () {
        $("#sendBtnnew").prop("disabled", false);
    });

    // $(document).on('submit', '#file-upload-form', function(e) {
    //     e.preventDefault();
    //     var myurl = $('#geturl').val();
    //     var replyTo = $('#reply_to_id').val();
    //     var formData = new FormData();
    //     var attach
    //     var comment = $('#commentModal').val();
    //     // Get the file input element
    //     var fileInput = document.getElementById('attachment');
    //     $("#sendBtnnew").prop("disabled", true);
    //     // Ensure the file input is being accessed correctly
    //     console.log(fileInput);

    //     // Check if files are selected
    //     if (fileInput.files && fileInput.files.length > 0) {
    //         var attachment = fileInput.files[0]; // Get the first selected file
    //         console.log('Selected file:', attachment); // Debugging: log the selected file
    //         formData.append('attachment[]', attachment); // Append the file to FormData
    //     } else {
    //         console.log('No file selected'); // Debugging: log when no file is selected
    //     }
    //     formData.append('comment', comment);
    //     formData.append('reply_to', replyTo);
    //     $('#loader').show();


    //     $.ajax({
    //         url: myurl,
    //         type: 'POST',
    //         data: formData,
    //         processData: false,
    //         contentType: false,
    //         headers: {
    //             'X-CSRF-TOKEN': csrf_token
    //         },
    //         success: function(data) {
    //             console.log(data);
    //             if (data.status == true) {
    //                 successMessage(data.message);
    //                 $('#loader').hide();
    //                 $('#closemodal').click();
    //                 $('#file-upload-form')[0].reset();
    //                 $('#reply_to_id').val('');
    //                 $('#commentModal').val('');
    //                 $('#reply_quoted_msg').hide();
    //                 $('#fileName').html('');
    //                 $('.file-name').html();
    //                 setTimeout(() => {
    //                     $("#sendBtnnew").prop("disabled", false); // Re-enable when modal closes
    //                 }, 500);
    //                 // updateTypingStatus(0);
    //             } else {
    //                 $("#sendBtnnew").prop("disabled", false);
    //                 errorMessage(data.message);
    //                 validation(data.message);

    //             }

    //         },
    //         error: function(response) {
    //             $("#sendBtnnew").prop("disabled", false);
    //             $('#loader').hide();
    //             $('#message').html('<p>File upload failed</p>');
    //         }
    //     });
    // });
    $(document).on('click', '#clear_chat', function() {
        var sentMsgCount = $('.sent-block').length;
        var rcvdMsgCount = $('.received-block').length;
        if (sentMsgCount > 0 || rcvdMsgCount > 0) {
            $('#selectAllDiv').show();
            $('.select-message').show();
            $('#clearChatBtn').show();
        }
    });
    // $(document).on('change', '#attachment', function() {
    //     var fileInput = this;
    //     var fileNameDiv = document.getElementById('fileName');
    
    //     // Check if any file was selected
    //     if (fileInput.files && fileInput.files.length > 0) {
    //         var fileName = fileInput.files[0].name; // Get the name of the first selected file
    //         fileNameDiv.textContent = `Selected file: ${fileName}`; // Display the file name
    //     } else {
    //         fileNameDiv.textContent = ''; // Clear the file name if no file is selected
    //     }
    // });

    let filesToUpload = [];

    let file_files_uploaded = []; // Array to track uploaded file names
    
    // Handle file selection and preview (for both drag-and-drop and file input change)
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
    
    
    // Allow file dragging
    $(document).on('dragover', '#filePreviewContainer', function(e) {
        e.preventDefault();
        e.stopPropagation();
    });
    
    // Allow file for paste
    // Handle paste event (files copied to clipboard)
    // Listen for the paste event anywhere on the page
// $(document).on('paste', function(e) {
//     e.preventDefault();
//     e.stopPropagation();

//     // Check if the clipboard data contains files (images, documents, etc.)
//     var clipboardData = e.originalEvent.clipboardData;
//     if (clipboardData && clipboardData.files.length > 0) {
//         var filePreviewContainer = document.getElementById('filePreviewContainer');

//         // Show the upload modal when files are pasted
//         $("#uploadModal").modal('show');

//         // Loop through the files pasted
//         Array.from(clipboardData.files).forEach((file, index) => {
//             filesToUpload.push(file); // Add the file to the upload array

//             // Create file preview for each pasted file
//             var previewDiv = createFilePreview(file, filesToUpload.length - 1);
//             filePreviewContainer.appendChild(previewDiv);
//         });
//     }
// });


    // Function to create file preview
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
    
    // Remove file preview and update array
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
    
    // Handle the file upload process
    let successCounter = 0; // Tracks how many files have been successfully uploaded
    
    // $(document).on('click', '#sendBtnnew', function() {
    //     successCounter = 0; // Reset counter before starting upload
    //     if(filesToUpload.length > 0 ){
    //         uploadFilesSequentially(0); // Start uploading from the first file
    //     }else{
    //         var editorContent = document.getElementById("modelEditor").innerHTML;
    //         if(editorContent != ""){
    //             submitFinalForm();
    //         }else{
    //             errorMessage("please enter comment or select file for upload.");
    //         }
            
    //     }
        
    // });
    
    // // Function to upload files sequentially
    // function uploadFilesSequentially(index) {
    //     if (index >= filesToUpload.length) {
    //         return; // All files have been uploaded
    //     }
    
    //     let file = filesToUpload[index];
    //     let formData = new FormData();
    //     formData.append('file', file);
    //     formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    //     formData.append('discussionId', $("#get_discussion_id").val());
    //     formData.append('comment', $("#commentModal").val());
    
    //     let previewDiv = document.querySelector(`.file-preview[data-index="${index}"]`);
    //     let progressBar = previewDiv.querySelector('.progress-bar');
        
    //     $.ajax({
    //         url: $('#uploadUrl').val(),
    //         type: 'POST',
    //         data: formData,
    //         processData: false,
    //         contentType: false,
    //         beforeSend: function () {
    //             $(".loader").show();
    //         },
    //         xhr: function() {
    //             var xhr = new XMLHttpRequest();
    //             xhr.upload.addEventListener('progress', function(e) {
    //                 if (e.lengthComputable) {
    //                     var percent = Math.round((e.loaded / e.total) * 100);
    //                     progressBar.style.width = percent + '%';
    //                 }
    //             });
    //             return xhr;
    //         },
    //         success: function(response) {
    //            $(".loader").hide();
    //             progressBar.style.background = 'blue'; // Successful upload
    
    //             file_files_uploaded.push(response.filename); // Save the uploaded file name
    //             successCounter++; // Increment the success counter
    
    //             // If all files are uploaded, proceed with final submit
    //             if (successCounter === filesToUpload.length) {
    //                 submitFinalForm();
    //             } else {
    //                 uploadFilesSequentially(index + 1); // Continue uploading the next file
    //             }
    //         },
    //         error: function(error) {
    //             console.error('Upload failed:', error);
    //             progressBar.style.background = 'red'; // Failed upload
    //             uploadFilesSequentially(index + 1); // Continue even if error
    //         }
    //     });
    // }
    
    // // Final form submission after all files are uploaded
    // function submitFinalForm() {
    //     let finalFormData = new FormData();
    //     finalFormData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    //     finalFormData.append('discussionId', $("#get_discussion_id").val());
    
    //     var editorContent = document.getElementById("commentModal").innerHTML;
    //     finalFormData.append('comment', editorContent);
    //     // finalFormData.append('comment', $("#commentModal").val());

    //     file_files_uploaded.forEach(function(file) {
    //         finalFormData.append('file_files_uploaded[]', file);
    //     });
    
    //     $.ajax({
    //         url: $('#geturl').val(),
    //         type: 'POST',
    //         data: finalFormData,
    //         processData: false,
    //         contentType: false,
    //         beforeSend: function () {
    //             $(".loader").show();
    //         },
    //         success: function(data) {
    //             $(".loader").hide();
    //             if (data.status) {
    //                 successMessage(data.message);
    //                 $("#uploadModal").modal('hide');
    //                 filesToUpload = [];
    //                 $("#commentModal").val("");
    //                 custom_editor_upload.reset();
    //                 $("#filePreviewContainer").html("");
    //                 document.getElementById("modelEditor").innerHTML = "";
    //             } else {
    //                 errorMessage(data.message);
    //                 validation(data.message);
    //             }
    //         },
    //         error: function(response) {
    //             errorMessage('Final form submit failed');
    //         }
    //     });
    // }

    $(document).on('click', '#sendBtnnew', function() {
        successCounter = 0; // Reset counter before starting upload
        failedCounter = 0;  // Add failed counter
        
        if(filesToUpload.length > 0 ){
            uploadFilesSequentially(0); // Start uploading from the first file
        }else{
            var editorContent = $("#commentModal").val();
            if(editorContent != ""){
                submitFinalForm();
            }else{
                errorMessage("please enter comment or select file for upload.");
            }
        }
    });

    // Function to upload files sequentially
    function uploadFilesSequentially(index) {
        if (index >= filesToUpload.length) {
            // Check if we should submit after all uploads are processed
            checkAndSubmitForm();
            return;
        }

        let file = filesToUpload[index];
        let formData = new FormData();
        formData.append('file', file);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('discussionId', $("#get_discussion_id").val());
        formData.append('comment', $("#commentModal").val());

        let previewDiv = document.querySelector(`.file-preview[data-index="${index}"]`);
        let progressBar = previewDiv.querySelector('.progress-bar');
        
        $.ajax({
            url: $('#uploadUrl').val(),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $(".loader").show();
            },
            xhr: function() {
                var xhr = new XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        var percent = Math.round((e.loaded / e.total) * 100);
                        progressBar.style.width = percent + '%';
                    }
                });
                return xhr;
            },
            success: function(response) {
                $(".loader").hide();
                progressBar.style.background = 'green'; // Successful upload
                
                file_files_uploaded.push(response.filename); // Save the uploaded file name
                successCounter++; // Increment the success counter
                
                // Continue uploading the next file
                uploadFilesSequentially(index + 1);
            },
            error: function(error) {
                console.error('Upload failed:', error);
                progressBar.style.background = 'red'; // Failed upload
                failedCounter++; // Increment failed counter
                
                // Continue uploading the next file even if this one failed
                uploadFilesSequentially(index + 1);
            }
        });
    }

    // Check if all uploads are complete and decide whether to submit
    function checkAndSubmitForm() {
        const totalProcessed = successCounter + failedCounter;
        
        if (totalProcessed === filesToUpload.length) {
            if (successCounter > 0) {
                // At least some files uploaded successfully, proceed with form submission
                submitFinalForm();
            } else if (failedCounter === filesToUpload.length) {
                // All files failed to upload
                errorMessage("All file uploads failed. Please try again.");
            }
        }
    }

    // Final form submission after successful file uploads
    function submitFinalForm() {
        let finalFormData = new FormData();
        finalFormData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        finalFormData.append('discussionId', $("#get_discussion_id").val());

        var editorContent = $("#commentModal").val();
        finalFormData.append('comment', editorContent);

        // Only append successfully uploaded files
        file_files_uploaded.forEach(function(file) {
            finalFormData.append('file_files_uploaded[]', file);
        });

        $.ajax({
            url: $('#geturl').val(),
            type: 'POST',
            data: finalFormData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $(".loader").show();
            },
            success: function(data) {
                $(".loader").hide();
                if (data.status) {
                    successMessage(data.message);
                    $("#uploadModal").modal('hide');
                    
                    // Reset everything
                    filesToUpload = [];
                    file_files_uploaded = [];
                    successCounter = 0;
                    failedCounter = 0;
                    $("#commentModal").val("");
                    custom_editor_upload.reset();
                    $("#filePreviewContainer").html("");
                    // document.getElementById("modelEditor").innerHTML = "";
                    //   fetchComments();
                } else {
                    errorMessage(data.message);
                    validation(data.message);
                }
            },
            error: function(response) {
                $(".loader").hide();
                errorMessage('Final form submit failed');
            }
        });
    }

    $(document).on('keydown', '#commentBox', function() {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault(); // Prevent default behavior of Enter key
            sendMessage(); // Call your sendMessage function
            var textarea = document.querySelector(".dynamic-textarea");
            textarea.style.height = "40px";
        }
    });
    $(document).on('click', '.share-email', function() {
        var postUrl = $(this).data('url'); // Get the specific URL for this post
        var subject = encodeURIComponent("Check this out!");
        var body = encodeURIComponent("Here's something interesting: " + postUrl);
        var mailtoLink = "mailto:?subject=" + subject + "&body=" + body;
        window.open(mailtoLink);

    });

    //   Share on WhatsApp
    $(document).on('click', '.share-whatsapp', function() {
        var postUrl = $(this).data('url');
        var message = encodeURIComponent("Check this out: " + postUrl);
        var whatsappLink = "https://wa.me/?text=" + message;
        window.open(whatsappLink, '_blank');
    });

    // Share on Instagram (Instagram doesn't support direct sharing through URL, so we just open the app or website)
    $(document).on('click', '.share-instagram', function() {
        var postUrl = $(this).data('url');
        var text = encodeURIComponent("Check this out: " + postUrl);
        var instagramLink = "https://www.instagram.com/" + message;;
        window.open(instagramLink, '_blank');
    });

    // Share on Twitter
    $(document).on('click', '.share-twitter', function() {
        var postUrl = $(this).data('url');
        var text = encodeURIComponent("Check this out: " + postUrl);
        var twitterLink = "https://twitter.com/intent/tweet?text=" + text;
        window.open(twitterLink, '_blank');
    });

    // Share on LinkedIn
    $(document).on('click', '.share-linkedin', function() {
        var postUrl = $(this).data('url');
        var text = encodeURIComponent("Check this out!");
        var linkedInLink = "https://www.linkedin.com/shareArticle?mini=true&url=" + postUrl + "&title=" + text;
        window.open(linkedInLink, '_blank');
    });

    $(document).on("click", ".open-dropzone-post", function(event) {
        $(".discussion-media-dropzone").toggle();
    });
    conversationList();
    // fetchComments();
    $("#discussionCreateForm").submit(function(e) {
        e.preventDefault();

        var pendingUploads = 0;
        if (mediaDropzone.getQueuedFiles().length > 0) {
            pendingUploads++;
            mediaDropzone.on("queuecomplete", function () {
                pendingUploads--;
                if (pendingUploads === 0 && isError === 0) {
                    // poi_upload = 1;
                    upload_count = 1;
                    if (upload_count === 1) {
                        $('.document-error').html('');
                        submitForm();
                    }
                }
            });
            mediaDropzone.processQueue();
        }else{
            submitForm();
        }
    });
});