<script>
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

function sendMessage(chatId, message) {
    $.ajax({
        url: `${baseUrl}/message-centre/send-msg/${chatId}`,
        type: 'POST',
        data: {
            _token: "{{csrf_token()}}",
            send_msg: message
        },
        success: function(response) {
            if (response.status) {
                // Message sent successfully
                console.log('Message sent:', response);
            }
        },
        error: function(xhr, status, error) {
            console.log('Error sending message:', error);
        }
    });
}

function fetchChats(chatId, lastMsgId) {
    $.ajax({
        url: `${baseUrl}/message-centre/fetch-chats/${chatId}/${lastMsgId}`,
        type: 'GET',
        success: function(response) {
            if (response.new_msg) {
                // Update messages list
                $("#messages_read").append(response.contents);
            }
        },
        error: function(xhr, status, error) {
            console.log('Error fetching chats:', error);
        }
    });
}
</script> 