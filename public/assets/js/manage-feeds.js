var last_comment_id = 0;
var first_comment_id = 0;
var isLoading = false;
var maxAutoLoads = 3;
var hasMorePages = true;
var currentFeedId = null; // Store current feed ID globally

function initializeManageFeedSocket(feedId) {
    console.log('Initializing WebSocket for feed:', feedId);
    
    // Store feed ID globally
    currentFeedId = feedId;
    
    // Join the private channel
    window.Echo.private(`feed-content.${feedId}`)
        .listen('FeedContentSocket', (e) => {
            
            var response = e.data || e;
            console.log(response);
            console.log(response.action+" = "+response.parent_comment_id+" = "+last_comment_id+" = "+response.last_comment_id);
            if (response.action == 'new_feed_comment' && response.parent_comment_id == 0) {
                if (!response.last_comment_id || response.last_comment_id != last_comment_id) {
                    if(response.comment_counts !== undefined && response.comment_counts == 1){
                        loadComments(true,response.feed_unique_id, 'socket');
                    }else{
                        loadComments(false,response.feed_unique_id, 'socket');
                    }
                }else{
                    alert("cannot call");
                }
            }
            
            if (response.action === 'new_feed_comment' && response.parent_comment_id != 0) {
                loadCommentReply(response.parent_comment_id);
            }
            if(response.action == 'edit_feed_comment'){
                loadUpdatedComments(response.last_comment_unique_id,response.comment);
            }
            if(response.action == 'delete_feed_comment'){
                if(response.action.is_reply)
                $("#comment-"+response.last_comment_unique_id).remove();
            }
            if ((response.action === 'feed_liked' || response.action === 'feed_unliked') && response.parent_comment_id != 0) {
                loadCommentReply(response.parent_comment_id);
            }
            if (response.action === 'comment_deleted') {
                var commentUniqueId = response.commentUniqueId || response.comment_unique_id;
                $('#comment-' + commentUniqueId).fadeOut(300, function() {
                    $(this).remove();
                });
            }
            
            if (response.action === 'comment_edited') {
                console.log('Comment edited:', response.commentUniqueId);
                var commentUniqueId = response.commentUniqueId || response.comment_unique_id;
                $('#editedMsg' + commentUniqueId).html('edited');
                $('#cpMsg' + commentUniqueId).html(response.editedComment || response.edited_comment);
            }
        })
        .error((error) => {
            console.error('WebSocket error:', error);
        });
    
    // Also listen for connection events
    window.Echo.connector.pusher.connection.bind('connected', () => {
        console.log('WebSocket connected successfully');
    });
    
    window.Echo.connector.pusher.connection.bind('error', (err) => {
        console.error('WebSocket connection error:', err);
    });
}
function loadCommentReply(parent_comment_id){
    $.ajax({
        type: "POST",
        url: BASEURL + '/my-feeds/fetch-reply-comments',
        data: {
            _token: csrf_token,
            parent_comment_id: parent_comment_id,
        },
        dataType: 'json',
        beforeSend: function () {
            
        },
        success: function (data) {
            $("#CDSFeed-comment-"+data.parent_comment_id).html(data.contents);
            $("#reply-count-"+data.parent_comment_id).html("("+data.reply_counts+")");
        },
        error: function(xhr, status, error) {
            console.error('Error loading comments:', error);
            console.error('Response:', xhr.responseText);
            isLoading = false;
        }
    });
}
function loadMoreReply(parent_comment_id,last_comment_id){
    $.ajax({
        type: "POST",
        url: BASEURL + '/my-feeds/load-more-replies',
        data: {
            _token: csrf_token,
            parent_comment_id: parent_comment_id,
            last_comment_id: last_comment_id,
        },
        dataType: 'json',
        beforeSend: function () {
            
        },
        success: function (data) {
            $("#CDSFeed-comment-"+data.parent_comment_id).find(".CDS-reply-load-more").remove();
            $("#CDSFeed-comment-"+data.parent_comment_id).append(data.contents);
        },
        error: function(xhr, status, error) {
            console.error('Error loading comments:', error);
            console.error('Response:', xhr.responseText);
            isLoading = false;
        }
    });
}
function loadComments(onload = false, feed_unique_id, comment_order = 'latest') {
    if(comment_order != 'socket'){
        if (isLoading || (!onload && !hasMorePages)) {
            console.log('Skipping load - isLoading:', isLoading, 'hasMorePages:', hasMorePages);
            return;
        }
    }else{
        comment_order = 'latest';
    }
    
    
    isLoading = true;
    
    //console.log('Loading comments for feed:', feed_unique_id, 'Order:', comment_order);

    $.ajax({
        type: "POST",
        url: BASEURL + '/my-feeds/fetch-comments',
        data: {
            _token: csrf_token,
            last_comment_id: last_comment_id,
            first_comment_id: first_comment_id,
            comment_order: comment_order,
            feed_id: feed_unique_id
        },
        dataType: 'json',
        beforeSend: function () {
            // Show loader
            var loader = '<div id="feed-loader" class="CDSFeed-loader">';
            loader += '<div class="spinner-border" role="status">';
            loader += '<span class="sr-only"></span>';
            loader += '</div>';
            loader += '<div>Loading...</div>';
            loader += '</div>';
            
            if (onload) {
                $(".CDSFeed-comments-list").html(loader);
            } else {
                // Remove any existing loader or view more button
                $("#feed-loader").remove();
                $(".CDSFeed-view-more").remove();
                $(".CDSFeed-comments-list").append(loader);
            }
        },
        success: function (data) {
            $("#feed-loader").remove();
            hasMorePages = data.has_more_comments;
            last_comment_id = data.last_comment_id;
            first_comment_id = data.first_comment_id;
            
            $(".CDSFeed-comments-count").html(data.comment_counts);
            if (onload) {
                $(".CDSFeed-comments-list").html(data.contents);
            } else {
                // Remove the view more button if it exists
                $(".CDSFeed-view-more").remove();
                if (comment_order === 'latest') {
                    // For new comments, prepend and highlight
                    var $newContent = $(data.contents);
                    $newContent.hide();
                    $(".CDSFeed-comments-list").prepend($newContent);
                    $newContent.fadeIn(500);
                } else {
                    $(".CDSFeed-comments-list").append(data.contents);
                }
            }
            if (comment_order !== 'latest') {
                $(".CDSFeed-comments-list").animate({
                    scrollTop: $(".CDSFeed-comments-list")[0].scrollHeight
                }, 500);
            }
            if (hasMorePages) {
                showViewMoreButton();
            } else if (onload && !data.contents) {
                var html = '<div class="text-center text-danger norecord">No records available</div>';
                $(".CDSFeed-comments-list").html(html);
            }
            
            isLoading = false;
        },
        error: function(xhr, status, error) {
            console.error('Error loading comments:', error);
            console.error('Response:', xhr.responseText);
            $("#feed-loader").remove();
            isLoading = false;
        }
    });
}
function loadUpdatedComments(last_unique_comment_id,comment){
    var commentHtml = "<p>"+comment+"</p>";
    commentHtml += "<div class='CDSFeed-comment-edited'>edited...<div>";
    $("#comment-"+last_unique_comment_id).find(".CDSFeed-comment-text").html(commentHtml);
}
// Helper function to verify WebSocket connection
function checkWebSocketConnection() {
    if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
        var state = window.Echo.connector.pusher.connection.state;
        console.log('WebSocket connection state:', state);
        return state === 'connected';
    }
    return false;
}

// Debug function to manually test WebSocket
function testWebSocket(feedId) {
    console.log('Testing WebSocket for feed:', feedId);
    
    // Check connection
    if (!checkWebSocketConnection()) {
        console.error('WebSocket not connected!');
        return;
    }
    
    // List all subscribed channels
    if (window.Echo.connector.pusher.channels) {
        console.log('Subscribed channels:', Object.keys(window.Echo.connector.pusher.channels.channels));
    }
    
    // Manually trigger a comment load to test
    console.log('Manually triggering comment load...');
}
function followBack(user_id, type) {
    $.ajax({
        type: 'post',
        url: BASEURL+"/connections/connect/follow-back",
        data: {
            user_id: user_id,
            _token: csrf_token
        },
        dataType: 'json',
        success: function(data) {
            if (data.status == true) {
                successMessage(data.message);
                   $(`button[onclick="followBack(${user_id},'following')"]`)
                    .removeClass('CDSFeed-btn-primary')
                    .addClass('CDSFeed-btn-secondary')
                    .text('Unfollow')
                    .attr('onclick', `unfollow(${user_id},'following')`);
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
            url: BASEURL+"/connections/connect/remove/" + user_id+'/'+remove_connection,
            dataType: 'json',
            success: function(response) {
                if (response.status == true) {
                    successMessage(response.message);
                    $(`button[onclick="unfollow(${user_id},'following')"]`)
                            .removeClass('CDSFeed-btn-secondary')
                            .addClass('CDSFeed-btn-primary')
                            .text('Follow')
                            .attr('onclick', `followBack(${user_id},'following')`);
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
        url: BASEURL+"/connections/connect/remove-from-followers/" + user_id,
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

function addToFavorites(feedId) {
    $.ajax({
        type: 'post',
        url: BASEURL + "/my-feeds/favourites/" + feedId + "/add",
        data: {
            _token: csrf_token
        },
        dataType: 'json',
        success: function(data) {
            if (data.status) {
                successMessage(data.message);
                // Update the favorite link
        $('#add-fav-btn-'+feedId).hide();
                $('#remove-fav-btn-'+feedId).show();
            }
        },
        error: function(xhr, status, error) {
            console.log('Error adding to favorites:', error);
            errorMessage("Failed to add to favorites");
        }
    });
}

// Remove from favorites
function removeFromFavorites(feedId) {
    $.ajax({
        type: 'post',
        url: BASEURL + "/my-feeds/favourites/" + feedId + "/remove",
        data: {
            _token: csrf_token
        },
        dataType: 'json',
        success: function(data) {
            if (data.status) {
                successMessage(data.message);
                // Update the favorite link
            $('#remove-fav-btn-'+feedId).hide();
                $('#add-fav-btn-'+feedId).show();
            }
        },
        error: function(xhr, status, error) {
            console.log('Error removing from favorites:', error);
            errorMessage("Failed to remove from favorites");
        }
    });
}