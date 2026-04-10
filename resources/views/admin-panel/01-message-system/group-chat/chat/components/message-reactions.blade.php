@props(['message'])

<div class="message-reactions" id="reactions-{{ $message->id }}">
    @if($message->messageReactions && $message->messageReactions->count() > 0)
        <div class="reactions-container">
            @foreach($message->messageReactions->groupBy('reaction') as $reaction => $reactions)
                <div class="reaction-item" data-reaction="{{ $reaction }}">
                    <span class="reaction-emoji">{{ $reaction }}</span>
                    <span class="reaction-count">{{ $reactions->count() }}</span>
                </div>
            @endforeach
        </div>
    @endif
    
    <div class="reaction-actions">
        <button type="button" class="reaction-btn" onclick="showReactionPicker({{ $message->id }})">
            <i class="fa-regular fa-smile"></i>
        </button>
    </div>
</div>

<script>
function showReactionPicker(messageId) {
    const reactions = ['👍', '❤️', '😊', '😂', '😮', '😢', '😡', '👏'];
    const picker = document.createElement('div');
    picker.className = 'reaction-picker';
    picker.innerHTML = reactions.map(reaction => 
        `<button type="button" class="reaction-option" onclick="addReaction(${messageId}, '${reaction}')">${reaction}</button>`
    ).join('');
    
    // Position the picker near the reaction button
    const reactionBtn = event.target.closest('.reaction-btn');
    const rect = reactionBtn.getBoundingClientRect();
    picker.style.position = 'absolute';
    picker.style.top = rect.top - 50 + 'px';
    picker.style.left = rect.left + 'px';
    
    document.body.appendChild(picker);
    
    // Close picker when clicking outside
    document.addEventListener('click', function closePicker(e) {
        if (!picker.contains(e.target) && !reactionBtn.contains(e.target)) {
            picker.remove();
            document.removeEventListener('click', closePicker);
        }
    });
}

function addReaction(messageId, reaction) {
    $.ajax({
        url: baseUrl + 'group/add-reaction',
        method: 'POST',
        data: {
            message_id: messageId,
            reaction: reaction,
            _token: csrfToken
        },
        success: (response) => {
            if (response.status) {
                updateReactions(messageId);
            }
        }
    });
    
    // Remove reaction picker
    document.querySelector('.reaction-picker')?.remove();
}

function updateReactions(messageId) {
    // Update the reactions display for the message
    $.ajax({
        url: baseUrl + 'group/get-reactions/' + messageId,
        method: 'GET',
        success: (response) => {
            if (response.status) {
                const reactionsContainer = document.getElementById(`reactions-${messageId}`);
                if (reactionsContainer) {
                    reactionsContainer.innerHTML = response.html;
                }
            }
        }
    });
}
</script> 