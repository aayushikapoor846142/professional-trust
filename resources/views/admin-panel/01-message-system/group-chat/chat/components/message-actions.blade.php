@props(['message', 'isOwn' => false])

<div class="message-actions" id="message-actions-{{ $message->id }}">
    <div class="action-buttons">
        @if($isOwn)
            <button type="button" class="action-btn edit-btn" onclick="editMessage({{ $message->id }})">
                <i class="fa-solid fa-edit"></i>
            </button>
            <button type="button" class="action-btn delete-btn" onclick="deleteMessage({{ $message->id }})">
                <i class="fa-solid fa-trash"></i>
            </button>
        @else
            <button type="button" class="action-btn reply-btn" onclick="replyToMessage({{ $message->id }})">
                <i class="fa-solid fa-reply"></i>
            </button>
            <button type="button" class="action-btn react-btn" onclick="showReactionPicker({{ $message->id }})">
                <i class="fa-regular fa-smile"></i>
            </button>
        @endif
        
        <button type="button" class="action-btn more-btn" onclick="showMessageMenu({{ $message->id }})">
            <i class="fa-solid fa-ellipsis"></i>
        </button>
    </div>
    
    <!-- Message Menu Dropdown -->
    <div class="message-menu" id="message-menu-{{ $message->id }}" style="display: none;">
        <ul class="menu-list">
            @if($isOwn)
                <li><a href="#" onclick="editMessage({{ $message->id }})">Edit Message</a></li>
                <li><a href="#" onclick="deleteMessage({{ $message->id }})">Delete Message</a></li>
            @else
                <li><a href="#" onclick="replyToMessage({{ $message->id }})">Reply</a></li>
                <li><a href="#" onclick="forwardMessage({{ $message->id }})">Forward</a></li>
            @endif
            <li><a href="#" onclick="copyMessageText({{ $message->id }})">Copy Text</a></li>
            <li><a href="#" onclick="reportMessage({{ $message->id }})">Report</a></li>
        </ul>
    </div>
</div>

<script>
function showMessageMenu(messageId) {
    // Hide all other menus
    document.querySelectorAll('.message-menu').forEach(menu => {
        menu.style.display = 'none';
    });
    
    // Show this menu
    const menu = document.getElementById(`message-menu-${messageId}`);
    menu.style.display = 'block';
    
    // Position menu
    const button = event.target.closest('.more-btn');
    const rect = button.getBoundingClientRect();
    menu.style.position = 'absolute';
    menu.style.top = rect.bottom + 'px';
    menu.style.left = rect.left + 'px';
    
    // Close menu when clicking outside
    document.addEventListener('click', function closeMenu(e) {
        if (!menu.contains(e.target) && !button.contains(e.target)) {
            menu.style.display = 'none';
            document.removeEventListener('click', closeMenu);
        }
    });
}

function editMessage(messageId) {
    const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
    const messageText = messageElement.querySelector('.message-text').textContent;
    
    // Create edit form
    const editForm = document.createElement('div');
    editForm.className = 'edit-message-form';
    editForm.innerHTML = `
        <textarea class="edit-textarea">${messageText}</textarea>
        <div class="edit-actions">
            <button type="button" onclick="saveEdit(${messageId})">Save</button>
            <button type="button" onclick="cancelEdit(${messageId})">Cancel</button>
        </div>
    `;
    
    messageElement.querySelector('.message-content').appendChild(editForm);
    editForm.querySelector('.edit-textarea').focus();
}

function saveEdit(messageId) {
    const editForm = document.querySelector(`[data-message-id="${messageId}"] .edit-message-form`);
    const newText = editForm.querySelector('.edit-textarea').value;
    
    $.ajax({
        url: baseUrl + 'group/update-message',
        method: 'POST',
        data: {
            message_id: messageId,
            message: newText,
            _token: csrfToken
        },
        success: (response) => {
            if (response.status) {
                const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
                messageElement.querySelector('.message-text').textContent = newText;
                editForm.remove();
            }
        }
    });
}

function cancelEdit(messageId) {
    const editForm = document.querySelector(`[data-message-id="${messageId}"] .edit-message-form`);
    editForm.remove();
}

function deleteMessage(messageId) {
    if (confirm('Are you sure you want to delete this message?')) {
        $.ajax({
            url: baseUrl + 'group/delete-message',
            method: 'POST',
            data: {
                message_id: messageId,
                _token: csrfToken
            },
            success: (response) => {
                if (response.status) {
                    const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
                    messageElement.remove();
                }
            }
        });
    }
}

function replyToMessage(messageId) {
    const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
    const messageText = messageElement.querySelector('.message-text').textContent;
    const senderName = messageElement.querySelector('.sender-name')?.textContent || 'User';
    
    showReplyTo(messageId, messageText, senderName);
}

function forwardMessage(messageId) {
    // Implement forward functionality
    console.log('Forward message:', messageId);
}

function copyMessageText(messageId) {
    const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
    const messageText = messageElement.querySelector('.message-text').textContent;
    
    navigator.clipboard.writeText(messageText).then(() => {
        // Show success message
        if (typeof toastr !== 'undefined') {
            toastr.success('Message copied to clipboard');
        }
    });
}

function reportMessage(messageId) {
    // Implement report functionality
    console.log('Report message:', messageId);
}
</script> 