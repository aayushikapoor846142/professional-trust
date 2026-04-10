@props(['groupId'])

<div class="typing-area position-relative">
    <div class="typing-chat" id="typing-indicator-{{ $groupId }}" style="display: none;">
        <div class="typechat-message">
            <span class="membertyping"></span> typing...
        </div>
        <div class="typing-indicator">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</div>

<script>
function showTypingIndicator(groupId, userName) {
    const indicator = document.getElementById(`typing-indicator-${groupId}`);
    if (indicator) {
        indicator.querySelector('.membertyping').textContent = userName;
        indicator.style.display = 'block';
    }
}

function hideTypingIndicator(groupId) {
    const indicator = document.getElementById(`typing-indicator-${groupId}`);
    if (indicator) {
        indicator.style.display = 'none';
    }
}

// Auto-hide typing indicator after 3 seconds
function startTypingTimer(groupId) {
    setTimeout(() => {
        hideTypingIndicator(groupId);
    }, 3000);
}
</script> 