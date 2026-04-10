@props(['replyMessage'])

<div class="reply-message" id="reply_quoted_msg">
    <div class="reply-icons">
        <i class="fa-solid fa-turn-up"></i>
        <i class="fa-solid fa-xmark" onclick="closeReplyto()"></i>
    </div>
    <div class="reply-content">
        <p class="quoted-message">
            <strong>{{ $replyMessage->sentBy->first_name }}:</strong>
            {{ Str::limit($replyMessage->message, 100) }}
        </p>
    </div>
</div>

<script>
function showReplyTo(messageId, messageText, senderName) {
    const replyContainer = document.getElementById('reply_quoted_msg');
    const quotedMessage = replyContainer.querySelector('.quoted-message');
    
    quotedMessage.innerHTML = `<strong>${senderName}:</strong> ${messageText}`;
    replyContainer.style.display = 'block';
    
    // Set the reply_to_id for form submission
    document.getElementById('reply_to_id').value = messageId;
}

function closeReplyto() {
    const replyContainer = document.getElementById('reply_quoted_msg');
    replyContainer.style.display = 'none';
    document.getElementById('reply_to_id').value = '';
}
</script> 