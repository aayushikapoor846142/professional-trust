<div class="typing-area position-relative">
    <div class="typing-chat" style="display: none;">
        <div class="typechat-message">typing...</div>
        <div class="typing-indicator">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</div>
<div class="reply-message " id="reply_quoted_msg" style="display: none">
    <div class="reply-icons">
        <i class="fa-solid fa-turn-up"></i>
        <i class="fa-solid fa-xmark" onclick="closeReplyto()"></i>
    </div>
    <p class="quoted-message">Reply quoted message</p><span class="username myChatReply{{ $chat->id }}"
        id="myreply">MY
        Reply</span>
</div>
<div class="message-input sendmsgwindow{{$chat->id}}" style="display: @if($chat->blocked_chat!=1 ){{'flex'}} @else {{'none'}}@endif"
    id="sendmsg">
    <div class="message-input-box">
        <div class="send-message-input" id="textareaWrapper" contenteditable="true">
            <textarea placeholder="Enter Message" id="sendmsgg" name="send_msg" data-id="{{ $chat->id }}" class="dynamic-textarea">{{ isset($draft_message) ? $draft_message : '' }}</textarea>
        </div>
        <div class="message-emoji-icon emoji-icon">
            <i class="fa-sharp fa-solid fa-face-smile"></i>
        </div>

        <div class="message-upload-file modal-toggle" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="fas fa-upload"></i>
        </div>
        <button id="sendBtn1">
            <i class="fa-solid fa-paper-plane"></i>
        </button>
    </div>
</div> 