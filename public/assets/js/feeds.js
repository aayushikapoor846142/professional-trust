// 01 Dropdown JS
var userId = currentUserId;


// Script to initialize on page ready 

var last_comment_id = 0;
var draftTimer;
var feed_id = 0;
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
var v_page = 1;
var vs_page = 1;
var selectedFiles = []; // Store selected files
var formFiles = [];
var MAX_FILES = 6;
var modal;
var modalContent;
var openModalBtn;
var closeModalBtns;
var collapseBtn;
var users = [];
var filesToUpload = [];

    


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
// Declare the function

function initializeFeedSocket(feedId, prevFeedId = '') {
    if (prevFeedId != '') {
        window.Echo.leave(`feed-content.${prevFeedId}`);
    }
    fetchFeedMembers(feedId);
    window.Echo.private(`feed-content.${feedId}`).listen('FeedContentSocket', (e) => {
        var response = e.data;
        if (response.action == 'new_feed_comment' && response.parent_comment_id == 0) {
            if (response.last_comment_id != last_comment_id) {
                fetchChats();
            }
        }
        if (response.action == 'new_feed_comment' && response.parent_comment_id != 0) {
            showFeedReplies(response.parent_comment_id,'show');
        }
        if (response.action == 'comment_deleted') {
            var commentUniqueId = response.commentUniqueId;
            $('#comment-' + commentUniqueId).remove();
            //  feedConversationList();
        }
        if (response.action == 'comment_edited') {
            var commentUniqueId = response.commentUniqueId;
            $('#editedMsg' + commentUniqueId).html('edited');
            $('#cpMsg' + commentUniqueId).html(response.editedComment);
            // feedConversationList();
        }
    });
}

function initializeFeedContent(feedId) {
    
    // const dropdown = document.querySelector(".cdsTYDashboard-custom01-dropdown");
    // const dropdownBtn = dropdown.querySelector(".cdsTYDashboard-custom01-btn");
    // const dropdownContent = dropdown.querySelector(".cdsTYDashboard-custom01-content");

    // dropdownBtn.addEventListener("click", function (event) {
    //     event.stopPropagation(); // Prevents event bubbling
    //     dropdown.classList.toggle("active");
    //     adjustDropdownPosition();
    // });

    // document.addEventListener("click", function (event) {
    //     if (!dropdown.contains(event.target)) {
    //         dropdown.classList.remove("active");
    //     }
    // });

    // window.addEventListener("resize", adjustDropdownPosition);

    // function adjustDropdownPosition() {
    //     const rect = dropdownContent.getBoundingClientRect();
    //     const screenWidth = window.innerWidth;

    //     dropdown.classList.remove("right", "left", "center");

    //     if (rect.right > screenWidth) {
    //         dropdown.classList.add("right"); // Align to right if overflowing
    //     } else if (rect.left < 0) {
    //         dropdown.classList.add("left"); // Align to left if overflowing
    //     } else {
    //         dropdown.classList.add("center"); // Default center
    //     }
    // }
    prev_feed_id = feedId
    last_comment_id = 0;
    feed_id = feedId;
    prev_feed_id = 0;
    cssdisplay = $('#chatsSearch').css('display');
    last_comment_id = 0;
    last_react_id = 0;
    first_msg_id = 0;
    isFetching = false;
    draftTimer = '';
    var sendInput = document.getElementById("commentBox");
    ACTIVE_FEED_CONTENT = feed_id;
    new EmojiPicker(".message-emoji-icon", {
        targetElement: "#commentBox"
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
    if (feed_id != "" && feed_id > 0) {
        fetchChats(feed_id);
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
                // showUserList(inputText);
                //  let filteredUsers = users.filter(user =>
                //     user.toLowerCase().includes(query.toLowerCase())
                // );
                // showUserList(filteredUsers);
            } else if (atSymbolPosition !== -1) {
                var query = inputText.slice(atSymbolPosition + 1).toLowerCase();
                if (query === 'everyone') {
                    showMentionAllOption('everyone');
                } else {
                    
                    // var filteredUsers = users.filter(user => user.toLowerCase().includes(query));
                    // showUserList(filteredUsers);
                    // var filteredUsers = users.filter(user =>
                    //     user.first_name.toLowerCase().includes(query) ||
                    //     user.last_name.toLowerCase().includes(query)
                    // );
                    // let filteredUsers = users.filter(user =>
                    //     user.toLowerCase().includes(query.toLowerCase())
                    // );
                  
                    let filteredUsers = users.filter(user =>
                        user.toLowerCase().includes(query.toLowerCase())
                    );
                    
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
               
                // showUserList(inputText);
                 let filteredUsers = users.filter(user =>
                        user.toLowerCase().includes(query.toLowerCase())
                    );
                  
                    showUserList(filteredUsers);
            } else if (atSymbolPosition !== -1) {
                var query = inputText.slice(atSymbolPosition + 1).toLowerCase();

                if (query === 'everyone') {
                    showMentionAllOption('everyone');
                } else {
                    // var filteredUsers = users.filter(user => user.toLowerCase().includes(query));
                   
                    // showUserList(filteredUsers);
                    // var filteredUsers = users.filter(user =>
                    //     user.first_name.toLowerCase().includes(query) ||
                    //     user.last_name.toLowerCase().includes(query)
                    // );
                    let filteredUsers = users.filter(user =>
                        user.toLowerCase().includes(query.toLowerCase())
                    );
                  
                    showUserList(filteredUsers);
                }
            } else {
                hideUserList();
            }
        }

        function showUserList(filteredUsers) {
          
            if (filteredUsers.length === 0) {
                userList.hide();
                return;
            }
           
            userList.empty();
            // filteredUsers.forEach(user => {
            //     var userItem = document.createElement('div');
            //     userItem.textContent = user;
            //     userItem.addEventListener('click', () => handleUserClick(user));
            //     userList.appendChild(userItem);
            // });

            filteredUsers.forEach(user => {
                var userItem = document.createElement('li');
                userItem.textContent = user ; // same value used for display
                userItem.addEventListener('click', () => handleUserClick(user));
                // userList.appendChild(userItem);
                userList.get(0).appendChild(userItem);
            });
            userList.show();
        }
       
        function showMentionAllOption(everyone) {
            // userList.innerHTML = ''; // Clear any existing user suggestions
            userList.empty();
            var mentionAllItem = document.createElement('li');
            mentionAllItem.textContent = everyone;
            mentionAllItem.addEventListener('click', () => handleMentionAll());
            userList.get(0).appendChild(mentionAllItem);
            userList.hide();
        }

        function hideUserList() {
            userList.hide();
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
        
    }


    // function removeFile(index) {
    //     formFiles.splice(index, 1);
    //     updatePreview();
    // }
    $(document).on("keydown", "#commentBox", function(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault(); // Prevent default behavior of Enter key
            sendMessage(); // Call your sendMessage function
            var textarea = document.querySelector(".dynamic-textarea");
            textarea.style.height = "40px";
        }
    });
    $(document).on("click", ".remove-file", function() {
        var index = $(this).data("index");
        formFiles.splice(index, 1);
        updatePreview();
    });
    $(document).on("click", ".close-upload-modal", function() {
        formFiles = [];
        files = [];
       
        closeModal();
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
    
    sendInput.addEventListener("paste", function(event) {
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

    modal = document.getElementById("uploadModal");
    modalContent = document.querySelector("#uploadModal .modal-content");
    openModalBtn = document.getElementById("openModal");
    closeModalBtns = document.querySelectorAll(".cdsTYDashboard-modal-close-btn, .cdsTYDashboard-modal-cancel-btn");
    // collapseBtn = document.querySelector(".cdsTYDashboard-modal-collapse-btn");

    
    

    // // Close Modal (Click on Close Button or Outside)
    // closeModalBtns.forEach(btn => {
    //     btn.addEventListener("click", closeModal);
    // });

    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });

    // Toggle Side Panel Mode
    // collapseBtn.addEventListener("click", () => {
    //     modal.classList.toggle("side-panel");
    // });
}
function openFileUploader(){
    $("#cds-show-file-processing").hide();
    modal.classList.remove("side-panel", "hide");
    modal.classList.add("show");
    modal.style.display = "flex";  // Ensure display is set
}

// Close Modal with smooth fade-out
// function closeModal() {
//     modal.classList.remove("show");
//     modal.classList.add("hide");
//     $("#cds-show-file-processing").hide();
//     // Wait for animation to complete before hiding
//     setTimeout(() => {
//         modal.style.display = "none";
//     }, 300);
// }
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

function resetPreview() {
    selectedFiles = [];
    previewContainer.innerHTML = "";
    uploadButton.disabled = true;
    formFiles = [];
    files = [];
    closeModal();
}

function loadFeedContentAjax(conversation_id, feed_id,open_comment = 0) {
    v_page = 1;
    $.ajax({
        type: 'GET',
        url: BASEURL+"/feeds/content-ajax/" + conversation_id,
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
                $(".feed-container").show();
                $(".feed-container").html(response.contents);
                setTimeout(() => {
                    $(".feed-container").promise().done(() => {
                        initializeFeedSocket(feed_id, prev_feed_id);
                        initializeFeedContent(feed_id);
                    });
                    chatArea = document.getElementById("chatMessages");
                    modal = document.getElementById("file-upload-modal");
                    previewContainer = document.getElementById("preview-container");
                    uploadButton = document.getElementById("sendBtnnew");
                    
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
                        e.preventDefault();
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
                        formData.append('send_msg', $('#sendmsg').val());
                        $("#cds-show-file-processing").show();
                        $("#cds-show-file-processing").html("<center><i class='fa fa-spinner fa-spin fa-2x'></i></center>")
                        fetch(myurl + "?sendmsg", {
                                method: "POST",
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status == true) {
                                    successMessage("Files uploaded successfully!");
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


function fetchChats(load_first_comment = false) {
    if (load_first_comment) {
        last_comment_id = 0;
    }
    var feedId = $('#get_feed_id').val();
    if(feedId === undefined){
        return false;
    }
    var showChat = document.getElementById('chat-container');
    $.ajax({
        type: 'POST',
        url: BASEURL+"/feeds/fetch-content/" + feedId + "/" + last_comment_id,
        dataType: 'json',
        data: {
            _token: csrf_token,
        },
        beforeSend: function() {
            if (last_comment_id === 0) {
                if (window.innerWidth < 991) {
                    $(".loader").show();
                }
            }
        },
        success: function(response) {
            console.log(response)
            // showChat.classList.add("mobile-chat");
            $(".loader").hide();
            if (response.new_comment) {
                $('.welcome-chat').hide();
                if (!document.getElementById('comment-' + response.last_comment_unique_id)) {
                    if (last_comment_id == 0) {
                        $('#messages_read').html(response.contents);
                    } else {
                        $('#messages_read').append(response.contents);
                    }

                }
                feedConversationList();
                // var target = $('#message-'+response.message_id);
                // document.getElementById('comment-' + response.comment_id).scrollIntoView({
                //     behavior: 'smooth'
                // });
            }
            last_comment_id = response.last_comment_id;
            first_msg_id = response.first_msg_id;
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
    var groupId = $('#get_feed_id').val();
    var showChat = document.getElementById('chat-container');
    $.ajax({
        type: 'POST',
        url: BASEURL+"/feeds/fetch-older-feeds/" + feedId + "/" + first_comment_id,
        dataType: 'json',
        data: {
            _token: csrf_token,
        },
        beforeSend: function() {},
        success: function(response) {
            showChat.classList.add("mobile-chat");
            $('#sloader').hide();
            isFetching = false;
            $('#messages_read').prepend(response.contents);
            first_comment_id = response.first_comment_id;
            feedConversationList();
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

function feedConversationList(v_page = 1,type="",search) {
  
    var inputValue = $('#feedsSearch').val();
    var type = $('#list-feed-data').val();
    var search = $('#feedsSearch').val();
    var my_type = $("#my-list-feed-data").val();
    if(type == 'all'){
        $("#list-sub-feed-data").val('pinned');
    }else if(type == 'my'){
        $("#list-sub-feed-data").val('scheduled');
    }
    $.ajax({
        type: 'get',
        url: BASEURL+"/feeds/feeds-sidebar-list?type=" + type + "&page=" + v_page+"&search="+search+"&sub_type="+my_type,
        dataType: 'json',
        data:{   
            status:feed_status
        },       
        success: function(data) {
           
            if(search != ''){
                if (v_page === 1) {
                    $("#feeds-sidebar-list").html('');
                    $("#feeds-sidebar-list").html(data.contents);
                } else {
                    $("#feeds-sidebar-list").append(data.contents);
                }

                if (data.current_page == data.last_page) {
                    $("#feedLink").hide();
                }else{
                    if(data.total_records != 0){
                        $("#feedLink").show();
                    }else{
                        $("#feedLink").hide();
                    }
                }
            }else{
                if (v_page === 1) {
                    $("#feeds-sidebar-list").html(data.contents);
                } else {
                    $("#feeds-sidebar-list").append(data.contents);
                }
                if (data.current_page == data.last_page) {
                    $("#feedLink").hide();
                }else{
                    $("#feedLink").show();
                }
            }
            
        },
        error: function(xhr, status, error) {
            console.log('Error fetching messages:', error);
        }
    });
}

function subFeedConversationList(vs_page = 1)
{
    // var sub_type = $('#list-sub-feed-data').val();
    var sub_type = $('#my-list-feed-data').val();

    if(sub_type == undefined){
        sub_type = "scheduled";
    }else{
        sub_type = sub_type;
    }
    
    var type = $('#list-feed-data').val();
    $.ajax({
        type: 'get',
        url: BASEURL+"/feeds/sub-feeds-sidebar-list?sub_type=" + sub_type + "&page=" + vs_page + "&type=" + type,
        dataType: 'json',
        success: function(data) {
            if (vs_page === 1) {
                $("#sub-feeds-sidebar-list").html('');
                $("#sub-feeds-sidebar-list").html(data.contents);
            } else {
                $("#sub-feeds-sidebar-list").append(data.contents);
            }

            if (data.current_page == data.last_page) {
                $("#subfeedLink").hide();
            }else{
                if(data.total_records != 0){
                    $("#subfeedLink").show();
                }else{
                    $("#subfeedLink").hide();
                }
            }
        },
        error: function(xhr, status, error) {
            console.log('Error fetching messages:', error);
        }
    });
}
function replyTo(chat_msg_id, msg) {
    $('#reply_to_id').val(chat_msg_id);
    $('#reply_quoted_msg').show();
    $('#myreply').html(msg);
}

function sendMessage() {
    var editCommentId = $('#edit_comment_id').val();
    var replyTo = $('#reply_to_id').val();
    var getval = $('#commentBox').val();
    var myurl = $('#geturl').val();
    var sendbtn = document.getElementById("sendBtn1");
    var msgInput = document.getElementById("commentBox");
    sendbtn.classList.add("disbled-btn")
    if (editCommentId) {
        $.ajax({
            type: 'post',
            url: BASEURL+"/feeds/update-comment/" + editCommentId,
            data: {
                _token: csrf_token,
                comment: getval
            },
            success: function(response) {
                $('#cpMsg' + editCommentId).html(response.updated_comment);
                document.querySelector('#commentBox').value = '';
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
                document.querySelector('#commentBox').value = '';
                $('#reply_quoted_msg').hide();
                $('#reply_to_id').val('');
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
function deleteComment(comment_id, comment_uid) {
    var geturl = BASEURL+"/feeds/" + comment_uid + '/delete';
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
function editFeedComment(commentId, uniqueId) {
    var commentElement = $('#cpMsg' + uniqueId);
    var editCommentId = $('#edit_comment_id').val(uniqueId);
    $('#commentBox').val(commentElement.text());

}
function getFeedsSearch(searchval) {
    var inputValue = $('#feedsSearch').val();
    var type = $('#list-feed-data').val();
   
    if (inputValue.trim() === '') {
    } else {
        feedConversationList();
    }
}
function listFeedsData(type, element) {
    $('a').removeClass('active')
    $(element).addClass('active');
    $('#list-feed-data').val(type);
    $('#my-list-feed-data').val('scheduled');
    v_page = 1;
    vs_page = 1;
    feedConversationList();
    subFeedConversationList();
}

function listSubFeedsData(type, element) {
    let $clicked = $(element).closest('a').length ? $(element).closest('a') : $(element);
    
    $('.sub-feed-list-div a').removeClass('active');

    $clicked.addClass('active');

    // $('#list-sub-feed-data').val(type);
    $("#my-list-feed-data").val(type);

    vs_page = 1;
    subFeedConversationList();
}

function editFeed(id){
    $.ajax({
        type: 'get',
        url: BASEURL+"/feeds/edit/" + id,
        dataType: 'json',
        beforeSend:function(){
            $('.edit-feed-content').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i></div>');
        },
        success: function(data) {
            $('.edit-feed-content').html(data.contents);
        },
        error: function(xhr, status, error) {
            console.log('Error fetching messages:', error);
        }
    });
}

function confirmFeedAction(e) {
    var url = $(e).attr("data-href");
    Swal.fire({
        title: "Are you sure to delete?",
        text: "Deleted feeds related all comments likes",
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


function followBack(user_id, type) {
    $.ajax({
        type: 'post',
        url: BASEURL+"/connect/follow-back",
        data: {
            user_id: user_id,
            _token: csrf_token
        },
        dataType: 'json',
        success: function(data) {
            if (data.status == true) {
                successMessage(data.message);
                window.location.reload();
            }
        },
        error: function(xhr, status, error) {
            console.log('Error fetching messages:', error);
        }
    });
}

function unfollow(user_id, type) {
   Swal.fire({
        title: "Are you sure to unfollow?",
        text: "Your connection also removed",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "btn btn-primary",
        cancelButtonClass: "btn btn-danger ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        
        var remove_connection = "";
        if (result.value) {
            remove_connection = "yes";
        }else{
            remove_connection = "no";
        }
       
        $.ajax({
            type: 'get',
            url: BASEURL+"/connect/remove/" + user_id+'/'+remove_connection,
            dataType: 'json',
            success: function(response) {
                if (response.status == true) {
                    successMessage(response.message);
                    window.location.reload();
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
    });
    
}

function removeFromFollowers(user_id, type) {
    $.ajax({
        type: 'get',
        url: BASEURL+"/connect/remove-from-followers/" + user_id,
        dataType: 'json',
        success: function(response) {
            if (response.status == true) {
                successMessage(response.message);
                //  connectConversationList(type, this, 'onload');
                //   toConnectList(1);
                window.location.reload();
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
function shareOpen() {
    const shareList = document.querySelector(".share-list");
    const shareBtn = document.querySelector(".share-btn");

    // Check if the share list exists
    if (shareList) {
        // Toggle the 'active' class to show/hide the share list
        shareList.classList.toggle("active");

        // Optional: If you want to close the share list when clicking outside
        document.addEventListener("click", function(event) {
            if (!shareBtn.contains(event.target) && !shareList.contains(event.target)) {
                shareList.classList.remove("active");
            }
        });
    }
}
function goToFeedList(){
    $(".feed-left-side").show();
    $(".feed-container").hide();
    var url = BASEURL+"/feed/manage";
    history.pushState(null, '', url);
}
function showFeedReplies(parent_comment_id,toggleReply='',parenet_comment_id=''){
    $.ajax({
        type: 'post',
        url: BASEURL+"/feed/fetch-comment-replies",
        data: {
            _token:csrf_token,
            parent_comment_id:parent_comment_id
        },
        dataType:"json",
        success: function (data) {
            $(".parent-replies").hide();
            $(".parent-replies").html('');
            if(toggleReply != ''){
                if(toggleReply == 'show'){
                    $('#pr-' + parent_comment_id).html(data.contents);
                    $('#pr-' + parent_comment_id).show();
                    $("#pr-"+parenet_comment_id).show();
                }else{
                    $('#pr-' + parent_comment_id).html('');
                    $('#pr-' + parent_comment_id).hide();
                }
                
            }
            $("#comment-count-"+parent_comment_id).html(data.comment_counts+" comment(s)");
        }
    });
}
// var onTypingStop = () => {
//     isTyping = false;
//     updateTypingStatus(0);
// };

// function updateTypingStatus(typingStatus) {
//     return false; // for now it is stopped calling this event;
//     if(typingStatus == 0){
//         isTyping = false;
//     }else{
//         isTyping = true;
//     }

//     var groupId = $('#get_feed_id').val();
//     $.ajax({
//         url: BASEURL+'/feeds/update-typing',
//         type: 'POST',
//         data: {
//             _token: csrf_token,
//             feed_id: groupId,
//             is_typing: typingStatus
//         },
//         success: function(response) {
//             console.log('Typing status updated');
//         },
//         error: function(error) {
//             console.log('Error updating typing status', error);
//         }
//     });
// }


// Initialize Script on page ready

$(document).ready(function() {
    feedConversationList();
    subFeedConversationList();
    $(document).on("click", ".cdsTYDashboard-feed-list-segment,.cdsTYDashboard-feed-action-button-link", function() {
        var url = $(this).data("href");
        var conversation_id = $(this).data("unique-id");
        var feedid = $(this).data("feed-id");
        var type = $(this).data("type");
        var open_comment = 0;
        if(type == 'comment'){
            open_comment = 1;
        }
        loadFeedContentAjax(conversation_id, feedid,open_comment);
        history.pushState(null, '', url);
    });
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
    $(document).on("click",".like-btn",function(e){
        e.preventDefault();
        var feedId = $(this).data('id');
        var button = $(this); // Reference to the clicked button
        $.ajax({
            url: BASEURL + `/feeds/${feedId}/like`,
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
    $(document).on("click","#load-more",function(e){
        var page = $(this).data('page');
        var feedId = feed_id;
        var button = $(this);
        var loader = $('#loading-spinner'); // Loader for better UX

        loader.show();
        button.prop('disabled', true);

        $.ajax({
            url: `${BASEURL}/feeds/group-medias/${feedId}?page=${page}`,
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
    $(document).on("click",".share-email",function(e){
        var postUrl = $(this).data('url'); // Get the specific URL for this post
        var subject = encodeURIComponent("Check this out!");
        var body = encodeURIComponent("Here's something interesting: " + postUrl);
        var mailtoLink = "mailto:?subject=" + subject + "&body=" + body;
        window.open(mailtoLink);

    });

    //   Share on WhatsApp
    $(document).on("click",".share-whatsapp",function(e){
        var postUrl = $(this).data('url');
        var message = encodeURIComponent("Check this out: " + postUrl);
        var whatsappLink = "https://wa.me/?text=" + message;
        window.open(whatsappLink, '_blank');
    });

    // Share on Instagram (Instagram doesn't support direct sharing through URL, so we just open the app or website)
    $(document).on("click",".share-instagram",function(e){
        var postUrl = $(this).data('url');
        var text = encodeURIComponent("Check this out: " + postUrl);
        var instagramLink = "https://www.instagram.com/" + message;;
        window.open(instagramLink, '_blank');
    });

    // Share on Twitter
    $(document).on("click",".share-twitter",function(e){
        var postUrl = $(this).data('url');
        var text = encodeURIComponent("Check this out: " + postUrl);
        var twitterLink = "https://twitter.com/intent/tweet?text=" + text;
        window.open(twitterLink, '_blank');
    });

    // Share on LinkedIn
    $(document).on("click",".share-linkedin",function(e){
        var postUrl = $(this).data('url');
        var text = encodeURIComponent("Check this out!");
        var linkedInLink = "https://www.linkedin.com/shareArticle?mini=true&url=" + postUrl + "&title=" + text;
        window.open(linkedInLink, '_blank');
    });
    $(document).on("click", "#feedLink", function (event) {
        v_page++;
        feedConversationList(v_page);
    });

    $(document).on("click", "#subfeedLink", function (event) {
        vs_page++;
        subFeedConversationList(vs_page);
    });
    $(document).on('click', '#sendBtn1', function() {
        var textarea = document.querySelector(".dynamic-textarea");
        textarea.style.height = "40px";
        sendMessage();
    });

    
    $(document).on('submit', '#file-upload-form', function(e) {
        e.preventDefault();
        var myurl = $('#geturl').val();

        var replyTo = $('#reply_to_id').val();

        let formData = new FormData();
        let attach

        let message = $('#messagenew').val();
        const files = filesToUpload;
        if(message == '' && files.length == 0){
            errorMessage("Files required to upload");
            return false;
        }
    
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
        var sentMsgCount = $('.sent-block').length;
        var rcvdMsgCount = $('.received-block').length;
        if (sentMsgCount > 0 || rcvdMsgCount > 0) {
            $('#selectAllDiv').show();
            $('.select-message').show();
            $('#clearChatBtn').show();
        }
    });

    $(document).on('change', '#attachment', function () {
        // var fileNameDiv = document.getElementById('fileName');
    
        // if (this.files && this.files.length > 0) {
        //     var fileName = this.files[0].name;
        //     console.log(fileName);
        //     fileNameDiv.textContent = `Selected file: ${fileName}`;
        // } else {
        //     fileNameDiv.textContent = '';
        // }
    });
    $(document).on('change', '#fileUploads', function(e) {
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
            formData.append("group_image", file);
            formData.append("_token", $('meta[name="csrf-token"]').attr(
                "content")); // Add CSRF token if needed
            var groupId = $('#get_feed_id').val();

            // AJAX request to upload the file
            $.ajax({
                url: BASEURL+"/feeds/" + groupId + "/save-image",
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
                    console.log(xhr.responseText); // Debug error
                    internalError(); // Show error
                }
            });
        }
    });

    
    
});


function confirmRepostFeed(e) {
    var url = $(e).attr("data-href");
    Swal.fire({
        title: "Are you sure to repost Feed?",
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


function fetchFeedMembers(feedId) {
    $.ajax({
        url: BASEURL + "/feeds/members-search", // Replace with your Laravel route
        type: "GET",
        data: {
            _token: csrf_token,
            feedId: feedId,
        },
        dataType: "json",
        success: function (response) {
            if (response.status) {
                // users = response.members;
                users = response.members;
            }
        },
        error: function (xhr, status, error) {
            console.log("Error adding reaction:", error);
        },
    });
}