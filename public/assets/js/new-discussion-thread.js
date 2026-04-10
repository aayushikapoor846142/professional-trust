
var userId = currentUserId;

Dropzone.autoDiscover = false;
var mediaDropzone;
var media_files_uploaded = [];
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
var custom_editor_main = CustomEditor.init(".CDS_Thread_textarea");
var custom_editor_upload;
var editor;
// File upload manager instance for discussion board
var discussionFileUploader;

fetchComments();

// Initialize file upload functionality
initializeDiscussionFileUpload();

$(document).on('click', '#CdsDiscussionThread-comment-post', function() {
    saveThreadComment();
});


function initializeDiscussionSocket(discussionId, prevFeedId = '') {
    if (prevFeedId != '') {
        window.Echo.leave(`discussion-threads.${prevFeedId}`);
    }
    window.Echo.leave(`discussion-threads.${discussionId}`);
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
        url: BASEURL+"/manage-discussion-threads/fetch-content/" + discussionId,
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
                        $('.CdsDiscussionThread-comments-load').html(response.contents);
                    } else {
                        if(call_from == 'socket'){
                            $('.CdsDiscussionThread-comments-load').prepend(response.contents);
                        }else{
                            $('.CdsDiscussionThread-comments-load').append(response.contents);
                        }
                    }
                }else{
                    // if(order_type == 'most_recent'){
                    //     $('#messages_read').append(response.contents);
                    // }else{
                    //     $('#messages_read').prepend(response.contents);
                    // }
                }
            }
            last_comment_id = response.last_comment_id;
            first_msg_id = response.first_comment_id;
        },
        error: function(xhr, status, error) {
            console.log('Error fetching messages:', error);
        }
    });
}

function likeComment(emoji,comment_id,discussion_board_id){
    $.ajax({
        type: 'post',
        url: BASEURL+"/manage-discussion-threads/comment/"+comment_id+"/like",
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
        url: BASEURL+"/manage-discussion-threads/comment/"+id+"/unlike",
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

function deleteDiscussionComment(comment_id, comment_uid) {
   
    var geturl = BASEURL+"/discussion-threads/" + comment_uid + '/delete';
    $.ajax({
        type: 'get',
        url: geturl,
        data: {},
        success: function(data) {
            $('#comment-' + comment_uid).remove();
            successMessage("Comment Removed");
        }
    });
}
function editDiscussionComment(uniqueId) {
    var commentElement = $('#comment-' + uniqueId).find(".CdsDiscussionThread-comment-content");
    $('#edit_comment_id').val(uniqueId);
    custom_editor_main.setValue(commentElement.text());
    
    // Scroll to the comment editor card smoothly
    var editorCard = document.querySelector(".CdsDiscussionThread-comment-editor-card");
    if (editorCard) {
        editorCard.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }
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
function markCommentAsAnswer(comment_id){
    $.ajax({
        type: 'post',
        url: BASEURL+"/manage-discussion-threads/comment/"+comment_id+"/mark-as-answer",
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
        url: BASEURL+"/manage-discussion-threads/comment/"+comment_id+"/remove-as-answer",
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
        url: BASEURL+"/manage-discussion-threads/comment/"+comment_id+"/potential-answer",
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
        url: BASEURL+"/manage-discussion-threads/comment/"+comment_id+"/remove-potential-answer",
        data: {
            _token:csrf_token,
        },
        dataType:"json",
        success: function (data) {
            $("#comment-"+data.comment_id).replaceWith(data.contents);
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

    // File Upload Functions for Discussion Board
    function initializeDiscussionFileUpload() {
        // Initialize file upload manager
        discussionFileUploader = new FileUploadManager('#discussionMediaUpload', {
            maxFileSize: 10 * 1024 * 1024, // 10MB
            maxFiles: 1,
            allowedTypes: [
                'image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 'image/svg+xml',
                'application/pdf',
                'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/csv', 'application/csv',
                'audio/mpeg', 'video/mp4', 'video/mpeg'
            ],
            allowedExtensions: [
                '.jpg', '.jpeg', '.png', '.gif', '.bmp', '.webp', '.svg',
                '.pdf', '.doc', '.docx', '.xls', '.xlsx', '.csv', '.txt', '.mp3', '.mp4', '.mpeg'
            ],
            onFileAdded: function(fileData) {
                console.log('File added to discussion:', fileData.name);
                // Show the upload container when files are added
                const container = document.getElementById('discussionFileUploadContainer');
                container.style.display = 'block';
                setTimeout(() => container.classList.add('show'), 10);
            },
            onFileRemoved: function(fileData) {
                console.log('File removed from discussion:', fileData.name);
                // Hide the upload container if no files remain
                if (discussionFileUploader.getFileCount() === 0) {
                    const container = document.getElementById('discussionFileUploadContainer');
                    container.classList.remove('show');
                    setTimeout(() => container.style.display = 'none', 300);
                }
            },
            onError: function(message) {
                errorMessage(message);
            }
        });
        
        // Initialize the uploader
        if (discussionFileUploader.init()) {
            console.log('Discussion file upload initialized successfully');
        } else {
            console.error('Failed to initialize discussion file upload');
        }
    }

    // Toggle file upload container visibility
    $(document).on('click', '#discussionFileUploadTrigger', function() {
        const container = document.getElementById('discussionFileUploadContainer');
        if (container.style.display === 'none' || container.style.display === '') {
            container.style.display = 'block';
            setTimeout(() => container.classList.add('show'), 10);
            // Focus on the upload area
            const uploadArea = container.querySelector('.CDSFeed-upload-area');
            if (uploadArea) {
                uploadArea.focus();
            }
        } else {
            container.classList.remove('show');
            setTimeout(() => container.style.display = 'none', 300);
        }
    });

    // Update saveThreadComment function to include file uploads
    function saveThreadComment() {
        var editCommentId = $('#edit_comment_id').val();
        // var replyTo = $('#reply_to_id').val();
        var getval = document.getElementById("duscussionCommentBox").value;
        var myurl = $('#geturl').val();
        var sendbtn = document.getElementById("CdsDiscussionThread-comment-post");
        
        sendbtn.classList.add("disbled-btn");
        
        if (editCommentId) {
            // Handle edit comment (no file upload for edits)
            $.ajax({
                type: 'post',
                url: BASEURL+"/manage-discussion-threads/update-comment/" + editCommentId,
                data: {
                    _token: csrf_token,
                    comment: getval
                },
                success: function(data) {
                    if(data.status == true){
                        $('#cpMsg' + editCommentId).html(data.updated_comment);
                        document.getElementById("duscussionCommentBox").value = '';
                        $('#edit_comment_id').val('');
                        custom_editor_main.reset();
                        sendbtn.classList.remove("disbled-btn");
                        // location.reload();
                    } else {
                        errorMessage(data.message);
                    }
                },
                error: function() {
                    console.log('Error updating message');
                    sendbtn.classList.remove("disbled-btn");
                }
            });
        } else {
            // Handle new comment with file upload
            var formData = new FormData();
            formData.append('_token', csrf_token);
            formData.append('comment', getval);
            // formData.append('reply_to', replyTo);
            
            // Add files if any
            if (discussionFileUploader && discussionFileUploader.getFileCount() > 0) {
                try {
                    const files = discussionFileUploader.getFiles();
                    files.forEach((file, index) => {
                        formData.append(`attachment`, file);
                    });
                } catch (error) {
                    console.error('Error getting files:', error);
                    errorMessage('Failed to process uploaded files');
                    sendbtn.classList.remove("disbled-btn");
                    return;
                }
            }
            
            $.ajax({
                type: 'post',
                url: myurl,
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    if(data.status == true){
                        $('#reply_quoted_msg').hide();
                        $('#reply_to_id').val('');
                        sendbtn.classList.remove("disbled-btn");
                        
                        // Reset file uploader
                        if (discussionFileUploader) {
                            discussionFileUploader.reset();

                            const container = document.getElementById('discussionFileUploadContainer');
                            container.classList.remove('show');
                            setTimeout(() => container.style.display = 'none', 300);
                        }
                        
                        // Clear comment box
                        document.getElementById("duscussionCommentBox").value = '';
                        custom_editor_main.reset();
                        // location.reload();
                    } else {
                        errorMessage(data.message);
                        sendbtn.classList.remove("disbled-btn");
                    }
                },
                error: function() {
                    console.log('Error posting comment');
                    sendbtn.classList.remove("disbled-btn");
                    errorMessage('Failed to post comment. Please try again.');
                }
            });
        }
    }