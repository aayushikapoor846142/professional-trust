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

function initializeSocket(chatId, prevChatId = '') {
    if (prevChatId != '') {
        window.Echo.leave(`chat_blocked.${prevChatId}`);
        window.Echo.leave(`message_reaction`);
        window.Echo.leave(`chat.${prevChatId}`);
    }
    
    window.Echo.private(`chat.${chatId}`)
        .listen('ChatSocket', (e) => {
            console.log('Chat socket event:', e);
            handleChatSocketEvent(e, chatId);
        });
    
    window.Echo.private(`chat_blocked.${chatId}`)
        .listen('ChatBlocked', (e) => {
            console.log('Chat blocked event:', e);
            handleChatBlockedEvent(e);
        });
    
    window.Echo.private(`message_reaction`)
        .listen('MessageReaction', (e) => {
            console.log('Message reaction event:', e);
            handleMessageReactionEvent(e);
        });
}

function handleChatSocketEvent(e, chatId) {
    if (e.action === 'new_message') {
        // Handle new message
        console.log('New message received:', e);
    } else if (e.action === 'message_read') {
        // Handle message read
        console.log('Message read:', e);
    } else if (e.action === 'user_typing') {
        // Handle typing indicator
        console.log('User typing:', e);
    }
}

function handleChatBlockedEvent(e) {
    console.log('Chat blocked:', e);
    // Handle chat blocked event
}

function handleMessageReactionEvent(e) {
    console.log('Message reaction:', e);
    // Handle message reaction event
}

function initializeChat(chatId) {
    // Initialize chat functionality
    console.log('Initializing chat for ID:', chatId);
}
</script> 