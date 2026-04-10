/**
 * Message Handler - Handle message operations for group chat
 */

class MessageHandler {
    constructor() {
        this.currentGroupId = null;
        this.lastMessageId = null;
        this.isTyping = false;
        this.typingTimer = null;
        this.messageQueue = [];
        this.isProcessing = false;
    }

    /**
     * Initialize message handler
     */
    init(groupId) {
        this.currentGroupId = groupId;
        this.bindEvents();
        this.startMessagePolling();
    }

    /**
     * Bind event listeners
     */
    bindEvents() {
        // Message form submission
        const messageForm = document.getElementById('message-form');
        if (messageForm) {
            messageForm.addEventListener('submit', (e) => this.handleMessageSubmit(e));
        }

        // Textarea auto-resize
        const textarea = document.getElementById('sendmsgg');
        if (textarea) {
            textarea.addEventListener('input', () => ChatUtils.autoResizeTextarea(textarea));
            textarea.addEventListener('keydown', (e) => this.handleTextareaKeydown(e));
        }

        // File upload
        const fileUploadBtn = document.getElementById('file-upload-btn');
        if (fileUploadBtn) {
            fileUploadBtn.addEventListener('click', () => this.openFileUpload());
        }

        // Emoji picker
        const emojiBtn = document.getElementById('emoji-btn');
        if (emojiBtn) {
            emojiBtn.addEventListener('click', () => this.openEmojiPicker());
        }
    }

    /**
     * Handle message form submission
     */
    handleMessageSubmit(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const message = formData.get('message').trim();
        
        if (!message) return;
        
        this.sendMessage(formData);
    }

    /**
     * Handle textarea keydown events
     */
    handleTextareaKeydown(event) {
        // Send message on Enter (without Shift)
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            document.getElementById('send-message-btn').click();
        }
        
        // Start typing indicator
        this.startTypingIndicator();
    }

    /**
     * Send message
     */
    sendMessage(formData) {
        const sendBtn = document.getElementById('send-message-btn');
        const originalText = sendBtn.innerHTML;
        
        // Disable send button
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        
        // Add message to queue
        const messageData = {
            id: ChatUtils.generateUniqueId(),
            content: formData.get('message'),
            timestamp: new Date().toISOString(),
            status: 'sending'
        };
        
        this.addMessageToUI(messageData);
        
        // Send to server
        $.ajax({
            url: baseUrl + 'group/send-message',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                if (response.status) {
                    this.updateMessageStatus(messageData.id, 'sent', response.message_id);
                    this.clearMessageInput();
                    this.stopTypingIndicator();
                    ChatUtils.scrollToBottom();
                } else {
                    this.updateMessageStatus(messageData.id, 'failed');
                    ChatUtils.showNotification(response.message || 'Failed to send message', 'error');
                }
            },
            error: () => {
                this.updateMessageStatus(messageData.id, 'failed');
                ChatUtils.showNotification('Failed to send message', 'error');
            },
            complete: () => {
                sendBtn.disabled = false;
                sendBtn.innerHTML = originalText;
            }
        });
    }

    /**
     * Add message to UI
     */
    addMessageToUI(messageData) {
        const messagesContainer = document.getElementById('chat-messages');
        if (!messagesContainer) return;
        
        const messageElement = document.createElement('div');
        messageElement.className = 'message-item own-message';
        messageElement.id = `message-${messageData.id}`;
        messageElement.setAttribute('data-message-id', messageData.id);
        
        messageElement.innerHTML = `
            <div class="message-content">
                <div class="message-text">${ChatUtils.sanitizeHtml(messageData.content)}</div>
                <div class="message-time">${ChatUtils.formatTimestamp(messageData.timestamp)}</div>
                <div class="message-status">
                    <i class="fa-solid fa-clock status-sending"></i>
                </div>
            </div>
        `;
        
        messagesContainer.appendChild(messageElement);
    }

    /**
     * Update message status
     */
    updateMessageStatus(messageId, status, serverMessageId = null) {
        const messageElement = document.getElementById(`message-${messageId}`);
        if (!messageElement) return;
        
        const statusElement = messageElement.querySelector('.message-status');
        if (!statusElement) return;
        
        let statusIcon = '';
        let statusClass = '';
        
        switch (status) {
            case 'sending':
                statusIcon = 'fa-clock';
                statusClass = 'status-sending';
                break;
            case 'sent':
                statusIcon = 'fa-check';
                statusClass = 'status-sent';
                break;
            case 'delivered':
                statusIcon = 'fa-check-double';
                statusClass = 'status-delivered';
                break;
            case 'read':
                statusIcon = 'fa-check-double';
                statusClass = 'status-read';
                break;
            case 'failed':
                statusIcon = 'fa-exclamation-triangle';
                statusClass = 'status-failed';
                break;
        }
        
        statusElement.innerHTML = `<i class="fa-solid ${statusIcon} ${statusClass}"></i>`;
        
        // Update message ID if server provided one
        if (serverMessageId) {
            messageElement.setAttribute('data-server-message-id', serverMessageId);
        }
    }

    /**
     * Clear message input
     */
    clearMessageInput() {
        const textarea = document.getElementById('sendmsgg');
        if (textarea) {
            textarea.value = '';
            textarea.style.height = 'auto';
        }
        
        // Clear reply
        const replyContainer = document.getElementById('reply_quoted_msg');
        if (replyContainer) {
            replyContainer.style.display = 'none';
        }
        
        document.getElementById('reply_to_id').value = '';
    }

    /**
     * Start typing indicator
     */
    startTypingIndicator() {
        if (this.isTyping) return;
        
        this.isTyping = true;
        
        $.ajax({
            url: baseUrl + 'group/typing-indicator',
            method: 'POST',
            data: {
                group_id: this.currentGroupId,
                is_typing: true,
                _token: csrfToken
            }
        });
        
        // Clear existing timer
        if (this.typingTimer) {
            clearTimeout(this.typingTimer);
        }
        
        // Set timer to stop typing indicator
        this.typingTimer = setTimeout(() => {
            this.stopTypingIndicator();
        }, 3000);
    }

    /**
     * Stop typing indicator
     */
    stopTypingIndicator() {
        if (!this.isTyping) return;
        
        this.isTyping = false;
        
        if (this.typingTimer) {
            clearTimeout(this.typingTimer);
            this.typingTimer = null;
        }
        
        $.ajax({
            url: baseUrl + 'group/typing-indicator',
            method: 'POST',
            data: {
                group_id: this.currentGroupId,
                is_typing: false,
                _token: csrfToken
            }
        });
    }

    /**
     * Start message polling
     */
    startMessagePolling() {
        setInterval(() => {
            this.pollNewMessages();
        }, 3000); // Poll every 3 seconds
    }

    /**
     * Poll for new messages
     */
    pollNewMessages() {
        if (!this.currentGroupId) return;
        
        $.ajax({
            url: baseUrl + 'group/get-new-messages',
            method: 'GET',
            data: {
                group_id: this.currentGroupId,
                last_message_id: this.lastMessageId
            },
            success: (response) => {
                if (response.status && response.messages.length > 0) {
                    this.addNewMessages(response.messages);
                    this.lastMessageId = response.messages[response.messages.length - 1].id;
                }
            }
        });
    }

    /**
     * Add new messages to UI
     */
    addNewMessages(messages) {
        const messagesContainer = document.getElementById('chat-messages');
        if (!messagesContainer) return;
        
        const wasAtBottom = this.isAtBottom(messagesContainer);
        
        messages.forEach(message => {
            const messageElement = this.createMessageElement(message);
            messagesContainer.appendChild(messageElement);
        });
        
        // Scroll to bottom if user was already at bottom
        if (wasAtBottom) {
            ChatUtils.scrollToBottom();
        }
        
        // Play notification sound
        this.playNotificationSound();
    }

    /**
     * Create message element
     */
    createMessageElement(message) {
        const messageElement = document.createElement('div');
        messageElement.className = `message-item ${message.is_own ? 'own-message' : 'other-message'}`;
        messageElement.id = `message-${message.id}`;
        messageElement.setAttribute('data-message-id', message.id);
        
        const messageHtml = `
            <div class="message-avatar">
                <img src="${message.sender_avatar}" alt="${message.sender_name}">
            </div>
            <div class="message-content">
                <div class="message-header">
                    <span class="sender-name">${message.sender_name}</span>
                    <span class="message-time">${ChatUtils.formatTimestamp(message.created_at)}</span>
                </div>
                <div class="message-text">${ChatUtils.sanitizeHtml(message.message)}</div>
                ${message.attachments ? this.renderAttachments(message.attachments) : ''}
                ${message.is_own ? '<div class="message-status"><i class="fa-solid fa-check-double status-read"></i></div>' : ''}
            </div>
        `;
        
        messageElement.innerHTML = messageHtml;
        return messageElement;
    }

    /**
     * Render attachments
     */
    renderAttachments(attachments) {
        if (!attachments) return '';
        
        const attachmentArray = attachments.split(',');
        let html = '<div class="message-attachments">';
        
        attachmentArray.forEach(attachment => {
            const fileExtension = attachment.split('.').pop().toLowerCase();
            const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension);
            
            if (isImage) {
                html += `
                    <div class="attachment-item">
                        <img src="${baseUrl}group-chat/${attachment}" alt="Attachment" onclick="previewFile('${attachment}', 'image')">
                    </div>
                `;
            } else {
                html += `
                    <div class="attachment-item">
                        <a href="${baseUrl}group-chat/${attachment}" target="_blank" class="file-attachment">
                            <i class="fa-solid fa-file"></i>
                            <span>${attachment}</span>
                        </a>
                    </div>
                `;
            }
        });
        
        html += '</div>';
        return html;
    }

    /**
     * Check if scroll is at bottom
     */
    isAtBottom(container) {
        const threshold = 100; // pixels from bottom
        return container.scrollHeight - container.scrollTop - container.clientHeight < threshold;
    }

    /**
     * Play notification sound
     */
    playNotificationSound() {
        const audio = document.getElementById('message-notification-sound');
        if (audio) {
            audio.play().catch(() => {
                // Ignore autoplay restrictions
            });
        }
    }

    /**
     * Open file upload
     */
    openFileUpload() {
        const fileUploadContainer = document.getElementById('file-upload-container');
        if (fileUploadContainer) {
            fileUploadContainer.style.display = 'block';
        }
    }

    /**
     * Open emoji picker
     */
    openEmojiPicker() {
        // Implementation for emoji picker
        console.log('Open emoji picker');
    }

    /**
     * Delete message
     */
    deleteMessage(messageId) {
        if (!confirm('Are you sure you want to delete this message?')) return;
        
        $.ajax({
            url: baseUrl + 'group/delete-message',
            method: 'POST',
            data: {
                message_id: messageId,
                _token: csrfToken
            },
            success: (response) => {
                if (response.status) {
                    const messageElement = document.getElementById(`message-${messageId}`);
                    if (messageElement) {
                        messageElement.remove();
                    }
                    ChatUtils.showNotification('Message deleted');
                } else {
                    ChatUtils.showNotification(response.message || 'Failed to delete message', 'error');
                }
            },
            error: () => {
                ChatUtils.showNotification('Failed to delete message', 'error');
            }
        });
    }

    /**
     * Edit message
     */
    editMessage(messageId, newContent) {
        $.ajax({
            url: baseUrl + 'group/edit-message',
            method: 'POST',
            data: {
                message_id: messageId,
                message: newContent,
                _token: csrfToken
            },
            success: (response) => {
                if (response.status) {
                    const messageElement = document.getElementById(`message-${messageId}`);
                    if (messageElement) {
                        const textElement = messageElement.querySelector('.message-text');
                        if (textElement) {
                            textElement.textContent = newContent;
                        }
                    }
                    ChatUtils.showNotification('Message updated');
                } else {
                    ChatUtils.showNotification(response.message || 'Failed to update message', 'error');
                }
            },
            error: () => {
                ChatUtils.showNotification('Failed to update message', 'error');
            }
        });
    }
}

// Initialize message handler
const messageHandler = new MessageHandler();

// Export for global access
window.MessageHandler = messageHandler; 