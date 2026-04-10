/**
 * Group Messages JavaScript Functionality
 * Handles all interactive features like reactions, message actions, and real-time updates
 * Follows the pattern of individual-chats.js
 */

// Global constants

class GroupMessagesManager {
    constructor() {
        this.filesToUpload = [];
        this.typingTimer = null;
        this.typingDelay = 1500;
        this.isTyping = false;
        this.groupId = null;
        this.groupUniqueId = null;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.currentUserId = this.getCurrentUserId();
        this.currentUserName = this.getCurrentUserName();
        this.presenceChannel = null;
        this.channelReady = false;
        this.hasPreviousMessages = null;
        // Global message ID variables accessible from any function
        this.lastMessageId = 0;
        this.firstMessageId = null;
        
        // Infinite scroll properties
        this.isLoadingOlderMessages = false;
        this.hasMoreMessages = true;
        this.scrollThreshold = 100; // pixels from top to trigger load
        
        // Enhanced typing properties - Same as group-chatapp.js
        this.groupTypingTimers = {};
        this.groupCurrentlyTyping = {};
        
        this.init();
    }

    init() {
        this.groupId = document.getElementById('get_group_id')?.value;
        this.groupUniqueId = document.getElementById('get_group_unique_id')?.value;
        if(this.groupId){
        this.bindEvents();
        }
        this.initializeEmojiPicker();
        this.initializeDragAndDrop();
        this.initializePresenceChannel();
        this.initializeSocketForGroup(this.groupId);
        this.initializeMessageIds();
        this.addMessageOptionsStyles();
        this.initializeFilePanel();
        // Bind chat-specific events
        this.rebindGroupEvents();
        
        // Scroll to bottom immediately after initialization
        this.scrollToBottom();
        
        // Test scroll event binding after initialization
        setTimeout(() => {
            this.testScrollEventBinding();
            this.scrollToBottom();
            this.initializeScrollToBottomButton();
        }, 1000);
        
        // Also scroll to bottom when DOM is fully loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.scrollToBottom();
                this.handleContentLoaded();
            });
        } else {
            // DOM is already loaded, scroll immediately
            this.scrollToBottom();
            this.handleContentLoaded();
        }
        
        // Scroll to bottom when window is resized
        window.addEventListener('resize', () => {
            setTimeout(() => {
                this.scrollToBottom();
            }, 100);
        });
        
        // Scroll to bottom when page becomes visible (user returns to tab)
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                setTimeout(() => {
                    this.scrollToBottom();
                }, 200);
            }
        });
        
        // Scroll to bottom when window gains focus
        window.addEventListener('focus', () => {
            setTimeout(() => {
                this.scrollToBottom();
            }, 100);
        });
        
        // Initialize search functionality
        this.initializeSearchWithDebouncing();
    }

    bindEvents() {

        // Attachment button
        $(document).on('click', '#attachBtn', () => {
            this.openFileUploadModal();
        });
        
        // Initialize FileUploadManager for message attachments
        this.initializeFileUploadManager();
        

        // Typing indicator - Enhanced version with debouncing
        $(document).on('input', '#messageInput', (e) => {
            this.handleTypingWithDebounce(e.target.value, this.groupId);
        });

        // Message reactions
        

        // Message options

        // Copy message
        $(document).on('click', '.copy-message', (e) => {
            this.handleCopyMessage(e);
        });

        // Edit message
        // $(document).on('click', '.edit-message', (e) => {
        //     this.handleEditMessage(e);
        // });

        // Delete message
        // $(document).on('click', '.delete-message', (e) => {
        //     this.handleDeleteMessage(e);
        // });

        // Scroll to bottom
        $(document).on('click', '#scrollToBottom', () => {
            this.scrollToBottom();
        });

        // File panel events
        $(document).on('click', '.CdsGroupChat-files-section-header', () => {
            this.toggleFilesSection();
        });

        $(document).on('click', '#searchFilesBtn', () => {
            this.toggleFileSearch();
        });

        $(document).on('click', '#clearFileSearch', () => {
            this.clearFileSearch();
        });

        $(document).on('keyup', '#search-file-input', (e) => {
            if (e.key === 'Enter') {
                this.searchFiles();
            }
        });

        $(document).on('click', '#loadMoreFiles', () => {
            this.loadMoreFiles();
        });

        // Sidebar group click - handle group switching
        $(document).on('click', '.group-item', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            // Try multiple ways to get the group ID
            let groupUniqueId = $(e.currentTarget).data('group-unique-id');
            let groupId = $(e.currentTarget).data('group-id');
            
            // If still no group ID, try to get from href or other attributes
            if (!groupUniqueId) {
                const href = $(e.currentTarget).attr('href');
                if (href && href.includes('/')) {
                    groupUniqueId = href.split('/').pop();
                }
            }
            
           
            
            if (groupUniqueId) {
                this.switchToGroup(groupUniqueId, groupId);
            }
        });

        // Emoji picker events
        // $(document).on('click', '.group-messages-emoji-icon', (e) => {
        //     this.toggleEmojiPicker(e);
        // });

        // $(document).on('click', '.group-messages-emoji-option', (e) => {
        //     this.selectEmoji(e);
        // });

        // Infinite scroll for older messages - bind after DOM is ready
        this.bindScrollEvent();

        // Drag and drop events
        $(document).on('dragover', '.group-messages-input-container', (e) => {
            e.preventDefault();
            e.stopPropagation();
            $(e.currentTarget).addClass('drag-over');
        });

        $(document).on('dragleave', '.group-messages-input-container', (e) => {
            e.preventDefault();
            e.stopPropagation();
            $(e.currentTarget).removeClass('drag-over');
        });

        $(document).on('drop', '.group-messages-input-container', (e) => {
            e.preventDefault();
            e.stopPropagation();
            $(e.currentTarget).removeClass('drag-over');
            
            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                this.handleFileSelection(files);
            }
        });

        // Close reply when clicking outside
        // $(document).on('click', (e) => {
        //     if (!$(e.target).closest('.group-messages-reply-indicator').length && 
        //         !$(e.target).closest('.group-messages-input-container').length) {
        //         if (window.replyToMessage) {
        //             this.hideReplyTo();
        //         }
        //     }
        // });

        // Reply functionality - Similar to individual-chat.js
        $(document).on('click', '.replyTo', (e) => {
            e.preventDefault();
            const messageId = $(e.currentTarget).data('message-id');
            const messageText = $(e.currentTarget).data('message-text') || 
                               $(e.currentTarget).closest('.group-messages-message').find('.group-messages-chat-message').text().trim();
            this.setReplyTo(messageId, messageText, this.groupId);
        });

        // Reply to message option in dropdown
        $(document).on('click', '.reply-to-message-option', (e) => {
            e.preventDefault();
            const messageId = $(e.currentTarget).data('message-id');
            const messageText = $(e.currentTarget).data('message-text') || 
                               $(e.currentTarget).closest('.group-messages-message').find('.group-messages-chat-message').text().trim();
            this.setReplyTo(messageId, messageText, this.groupId);
        });

        // Close message options dropdown when clicking outside
        $(document).on('click', (e) => {
            if (!$(e.target).closest('.group-messages-message-options').length) {
                $('.group-messages-dropdown-menu').removeClass('active');
            }
        });

        // Enter key in message input
        // $(document).on('keypress', '#messageInput', (e) => {
        //     if (e.key === 'Enter' && !e.shiftKey) {
        //         e.preventDefault();
        //         this.sendMessage();
        //     }
        // });

        // Escape key to cancel reply mode
        $(document).on('keydown', '#messageInput', (e) => {
            if (e.key === 'Escape') {
                if (window.replyToMessage) {
                    e.preventDefault();
                    this.hideReplyTo();
                }
            }
        });

        // Clear reply when message input is cleared
        $(document).on('input', '#messageInput', (e) => {
            if (e.target.value === '' && window.replyToMessage) {
                this.hideReplyTo();
            }
        });

        // Send message button
        $(document).on('click', '#sendBtn', () => {
            this.sendMessage();
        });

        // // Message options
        // Message options
        $(document).on('click', '.group-messages-options-btn', (e) => {
            this.toggleMessageOptions(e);
        });

        // // Copy message
        // $(document).on('click', '[onclick*="groupCopyMessage"]', (e) => {
        //     this.handleCopyMessage(e);
        // });

        // // Delete message
        // $(document).on('click', '[onclick*="deleteGroupMessage"]', (e) => {
        //     this.handleDeleteMessage(e);
        // });

        // Edit message
        // $(document).on('click', '[onclick*="editGroupMessage"]', (e) => {
        //     this.handleEditMessage(e);
        // });

        // Remove reaction
        $(document).on('click', '.own-reaction', (e) => {
            this.handleRemoveReaction(e);
        });

        // Close dropdowns when clicking outside
        $(document).on('click', (e) => {
            if (!$(e.target).closest('.group-messages-message-options').length) {
                $('.group-messages-dropdown-menu').removeClass('active');
            }
        });

        // Reaction tooltips
        $(document).on('mouseenter', '.group-messages-reactions-tooltip', function() {
            $(this).find('.group-messages-reactions-tooltip-content').show();
        }).on('mouseleave', '.group-messages-reactions-tooltip', function() {
            $(this).find('.group-messages-reactions-tooltip-content').hide();
        });

        // Handle close upload modal
        $(document).on('click', '.close-upload-modal', function() {
            groupMessagesManager.resetFilePreview();
        });

    }


    // Add reaction to message
    addReaction(messageId, emoji) {
        $.ajax({
            url: '/group-message/add-reaction',
            method: 'POST',
            data: {
                message_id: messageId,
                reaction: emoji,
                _token: this.csrfToken
            },
            success: (response) => {
                if (response.success) {
                    this.updateMessageReactions(messageId, emoji, 'add');
                }
            },
            error: (xhr) => {
                console.error('Failed to add reaction:', xhr);
            }
        });
    }

    // Remove reaction from message
    handleRemoveReaction(e) {
        e.preventDefault();
        const reactionId = $(e.currentTarget).data('id');
        const messageId = $(e.currentTarget).closest('.group-messages-message').data('message-id');
        
        $.ajax({
            url: '/group-message/remove-reaction',
            method: 'POST',
            data: {
                reaction_id: reactionId,
                _token: this.csrfToken
            },
            success: (response) => {
                if (response.success) {
                    this.updateMessageReactions(messageId, null, 'remove', reactionId);
                }
            },
            error: (xhr) => {
                console.error('Failed to remove reaction:', xhr);
            }
        });
    }

    // Update message reactions display
    updateMessageReactions(messageId, emoji, action, reactionId = null) {
        const messageElement = $(`#message-${messageId}`);
        const reactionsContainer = messageElement.find('.group-messages-message-reactions');
        
        if (action === 'add') {
            if (reactionsContainer.length === 0) {
                const newReactionsHtml = `
                    <div class="group-messages-message-reactions">
                        <div class="group-messages-reaction">
                            <span class="group-messages-reaction-emoji own-reaction" onclick="groupMessagesManager.handleRemoveReaction(event)" data-id="new-reaction">
                                ${emoji}
                            </span>
                            <span class="group-messages-reaction-count">1</span>
                        </div>
                    </div>
                `;
                messageElement.find('.group-messages-message-bubble').append(newReactionsHtml);
            } else {
                const existingReaction = reactionsContainer.find(`[data-emoji="${emoji}"]`);
                if (existingReaction.length > 0) {
                    const countElement = existingReaction.next('.group-messages-reaction-count');
                    countElement.text(parseInt(countElement.text()) + 1);
                } else {
                    const newReactionHtml = `
                        <div class="group-messages-reaction">
                            <span class="group-messages-reaction-emoji own-reaction" onclick="groupMessagesManager.handleRemoveReaction(event)" data-id="new-reaction" data-emoji="${emoji}">
                                ${emoji}
                            </span>
                            <span class="group-messages-reaction-count">1</span>
                        </div>
                    `;
                    reactionsContainer.append(newReactionHtml);
                }
            }
        } else if (action === 'remove') {
            if (reactionId) {
                const reactionElement = reactionsContainer.find(`[data-id="${reactionId}"]`);
                if (reactionElement.length > 0) {
                    const countElement = reactionElement.next('.group-messages-reaction-count');
                    const currentCount = parseInt(countElement.text());
                    
                    if (currentCount > 1) {
                        countElement.text(currentCount - 1);
                    } else {
                        reactionElement.closest('.group-messages-reaction').remove();
                    }
                }
            }
        }
    }

    // Toggle message options menu
    toggleMessageOptions(e) {
        e.preventDefault();
        const messageElement = $(e.currentTarget).closest('.group-messages-message');
        const optionsMenu = messageElement.find('.group-messages-dropdown-menu');
        // Close other open menus
        $('.group-messages-dropdown-menu').not(optionsMenu).removeClass('active');
        
        // Toggle current menu
        optionsMenu.toggleClass('active');
    }

    // Handle reply to message
    handleReply(e) {
        e.preventDefault();
        const messageId = $(e.currentTarget).data('message-id');
        const groupId = $(e.currentTarget).data('group-id');
        const messageText = $(e.currentTarget).data('message-text') || 
                           $(e.currentTarget).closest('.group-messages-message').find('.group-messages-chat-message').text().trim();
        
        this.setReplyTo(messageId, messageText, groupId);
    }

    // Set reply to message - Similar to individual-chat.js
    setReplyTo(messageId, messageText, groupId) {
        const replyToIdElement = document.getElementById('group_reply_to_id');
        const myreplyElement = document.getElementById('group_myreply');
        const replyQuotedMsgElement = document.getElementById('group_replyQuotedMsg');
        
        // Store reply data for when message is sent
        window.replyToMessage = {
            messageId: messageId,
            groupId: groupId || this.groupId,
            messageText: messageText
        };
        
        if (replyToIdElement) {
            replyToIdElement.value = messageId;
        }
        if (myreplyElement) {
            myreplyElement.textContent = messageText;
        }
        if (replyQuotedMsgElement) {
            replyQuotedMsgElement.style.display = 'flex';
        }
        
        // Show reply indicator
        this.showReplyIndicator(messageId, messageText);
        
        // Focus on message input
        $('#messageInput').focus();
    }

    // Hide reply to message - Similar to individual-chat.js
    hideReplyTo() {
        const replyToId = document.getElementById('group_reply_to_id');
        const replyQuotedMsg = document.getElementById('group_replyQuotedMsg');
        
        if (replyToId) {
            replyToId.value = '';
        }
        if (replyQuotedMsg) {
            replyQuotedMsg.style.display = 'none';
        }
        // Also hide the custom reply indicator
        $('.group-messages-reply-indicator').hide();
        window.replyToMessage = null;
    }

    // Show reply indicator
    showReplyIndicator(messageId, messageText) {
        const truncatedText = messageText.length > 50 ? messageText.substring(0, 50) + '...' : messageText;
        
        let replyIndicator = $('.group-messages-reply-indicator');
        if (replyIndicator.length === 0) {
            replyIndicator = $('<div class="group-messages-reply-indicator"></div>');
            $('.group-messages-input-area').prepend(replyIndicator);
        }
        $("#group_reply_to_id").val(messageId);
        replyIndicator.html(`
            <div class="group-messages-reply-content">
                <span class="group-messages-reply-label">Replying to:</span>
                <span class="group-messages-reply-text">${truncatedText}</span>
                <button class="group-messages-reply-cancel">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
        `);
        
        replyIndicator.show();
    }

    // Cancel reply
    cancelReply() {
        this.hideReplyTo();
    }

    // Handle copy message
    handleCopyMessage(e) {
        e.preventDefault();
        var messageId = $(e.currentTarget).attr('id');
        const messageText = $(`#${messageId}`).text().trim();
        
        navigator.clipboard.writeText(messageText).then(() => {
            this.showToast('Message copied to clipboard!', 'success');
        }).catch(() => {
            this.showToast('Failed to copy message', 'error');
        });
    }

    // Handle delete message
    handleDeleteMessage(e) {
        e.preventDefault();
        const messageId = $(e.currentTarget).attr('data-message-id');
        if (confirm('Are you sure you want to delete this message?')) {
            $.ajax({
                url: BASEURL+'/group-message/delete-message-for-me/'+messageId,
                method: 'POST',
                data: {
                    _token: this.csrfToken
                },
                success: (response) => {
                    if (response.success) {
                        // $(`#message-${uniqueId}`).fadeOut(300, function() {
                        //     $(this).remove();
                        // });
                        this.showToast('Message deleted successfully', 'success');
                    }
                },
                error: (xhr) => {
                    console.error('Failed to delete message:', xhr);
                    this.showToast('Failed to delete message', 'error');
                }
            });
        }
    }

    handleDeleteMessageForAll(e) {
        e.preventDefault();
        const messageId = $(e.currentTarget).attr('data-message-id');
        if (confirm('Are you sure you want to delete this message?')) {
            $.ajax({
                url: BASEURL+'/group-message/delete-message-for-all/'+messageId,
                method: 'POST',
                data: {
                    _token: this.csrfToken
                },
                success: (response) => {
                    if (response.success) {
                        // $(`#message-${uniqueId}`).fadeOut(300, function() {
                        //     $(this).remove();
                        // });
                        this.showToast('Message deleted successfully', 'success');
                    }
                },
                error: (xhr) => {
                    console.error('Failed to delete message:', xhr);
                    this.showToast('Failed to delete message', 'error');
                }
            });
        }
    }

    // Handle edit message
    handleEditMessage(e) {
        e.preventDefault();
        const uniqueId = $(e.currentTarget).attr('data-message-id');
        $("#grp_edit_message_id").val(uniqueId);
        const messageElement = $(`#cpMsg${uniqueId}`);
        const currentText = messageElement.text().trim();
        $("#messageInput").val(currentText);
        $("#messageInput").focus();
    }


    // Cancel edit
    cancelEdit(uniqueId) {
        $(`#cpMsg${uniqueId}`).show();
        $(`#message-${uniqueId} .group-messages-edit-input`).remove();
    }
    sendMessage() {
        var edit_message_id = $("#grp_edit_message_id").val();
        // alert(edit_message_id);
        var edit_message_id = $("#grp_edit_message_id").val();
        if(edit_message_id){
            this.updateMessage(edit_message_id);
        }else{
            this.sendNewMessage();
        }
    }
    // Send message
    updateMessage(messageId) {
        const newText = $(`#messageInput`).val();
        $("#sendBtn").prop('disabled', true);
        $("#messageInput").prop('disabled', true);
        $.ajax({
            url: BASEURL + '/group-message/update-message/' + messageId,
            method: 'POST',
            data: {
                message: newText,
                _token: this.csrfToken
            },
            success: (response) => {
                if (response.status) {
                    $(`#cpMsg${messageId}`).text(newText).show();
                    $("#sendBtn").prop('disabled', false);
                    $("#messageInput").prop('disabled', false);
                    this.cancelReply();
                    this.clearMessageInput();
                    // this.showToast('Message updated successfully', 'success');
                }
            },
            error: (xhr) => {
                console.error('Failed to update message:', xhr);
                this.showToast('Failed to update message', 'error');
            }
        });
    }

    sendNewMessage() {
        const message = $('#messageInput').val().trim();
        if (!message && this.filesToUpload.length === 0) return;
        
        const messageData = {
            message: message,
            group_id: this.groupId,
            _token: this.csrfToken
        };

        // Add reply data if replying to a message
        if (window.replyToMessage) {
            messageData.reply_to = window.replyToMessage.messageId;
        }

        // If there are files to upload, use the file upload flow
        if (this.filesToUpload.length > 0) {
            this.openFileUploadModal();
            // Set the message in the modal
            $('#messagenew').val(message);
            return;
        }

        $("#sendBtn").prop('disabled', true);
        $("#messageInput").prop('disabled', true);
        
        $.ajax({
            url: BASEURL+'/group-message/send-message',
            method: 'POST',
            data: messageData,
            success: (response) => {
                if (response.status) {
                    $('#messageInput').val('');
                    $("#sendBtn").prop('disabled', false);
                    $("#messageInput").prop('disabled', false);
                    this.cancelReply();
                    this.clearMessageInput();
                    this.scrollToBottom();
                    // this.showToast('Message sent successfully!', 'success');
                } else {
                    this.showToast(response.message || 'Failed to send message', 'error');
                }
            },
            error: (xhr) => {
                console.error('Failed to send message:', xhr);
                this.showToast('Failed to send message', 'error');
                $("#sendBtn").prop('disabled', false);
                $("#messageInput").prop('disabled', false);
            }
        });
    }

    // Show error message
    showError(message) {
        this.showToast(message, 'error');
    }

    // Handle file upload with message
    handleFileUploadWithMessage(message = '') {
        if (this.filesToUpload.length === 0) {
            this.showToast('Please select files to upload', 'error');
            return;
        }

        const replyTo = $('#group_reply_to_id').val();
        const myurl = $('#geturl').val();

        $("#sendBtnnew").prop('disabled', true);
        const formData = new FormData();

        // Add files
        this.filesToUpload.forEach((file) => {
            formData.append("attachment[]", file);
        });

        // Add message and reply data
        formData.append('message', message);
        formData.append('reply_to', replyTo);
        formData.append('_token', this.csrfToken);

        $.ajax({
            url: myurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': this.csrfToken
            },
            success: (response) => {
                if (response.status == true) {
                    this.showToast(response.message || "Files uploaded successfully!", 'success');
                    $('#uploadModal').modal('hide');
                    $('#file-upload-form')[0].reset();
                    $('#group_reply_to_id').val('');
                    $('#messagenew').val('');
                    $('#group_replyQuotedMsg').hide();
                    this.resetFilePreview();
                    this.hideReplyTo();
                    
                    // Refresh messages if needed
                    if (response.refresh_messages) {
                        this.loadGroupMessages(this.groupUniqueId, this.lastMessageId);
                        
                    }
                } else {
                    this.showToast(response.message || "Upload failed", 'error');
                }
            },
            error: (xhr) => {
                this.showToast('File upload failed', 'error');
                console.error('Upload error:', xhr);
            },
            complete: () => {
                $("#sendBtnnew").prop('disabled', false);
            }
        });
    }

    // Handle drag and drop on the entire chat container
    handleChatContainerDragAndDrop() {
        const chatContainer = document.querySelector('.group-messages-chat-container');
        if (!chatContainer) return;

        // Prevent default drag behaviors
        chatContainer.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
        });

        chatContainer.addEventListener('dragenter', (e) => {
            e.preventDefault();
            e.stopPropagation();
            chatContainer.classList.add('drag-over');
        });

        chatContainer.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (!chatContainer.contains(e.relatedTarget)) {
                chatContainer.classList.remove('drag-over');
            }
        });

        chatContainer.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            chatContainer.classList.remove('drag-over');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                this.handleFileSelection(files);
                $('#uploadModal').modal('show');
            }
        });
    }

    // Initialize file panel
    initializeFilePanel() {
        // File upload panel functionality
        this.handleChatContainerDragAndDrop();
        
        // Initialize emoji picker for modal
        if (typeof EmojiPicker !== 'undefined') {
            new EmojiPicker(".message-emoji-icon-modal", {
                targetElement: "#messagenew"
            });
        }
    }

    // Load group messages using AJAX
    loadGroupMessages(groupUniqueId, lastMessageId = 0) {
        const requestData = {
            _token: this.csrfToken,
            group_id: groupUniqueId,
            last_message_id: lastMessageId
        };

        $.ajax({
            url: BASEURL + '/group-message/load-messages/' + groupUniqueId,
            method: 'POST',
            data: requestData,
            beforeSend: () => {
                // Show loading indicator if needed
                this.showLoadingIndicator();
            },
            success: (response) => {
                if (response.status) {
                    if(lastMessageId == 0){
                        this.handleMessagesLoaded(response,false);
                    }else{
                        this.handleMessagesLoaded(response);
                    }
                } else {
                    console.error('Failed to load messages:', response.message);
                    this.showToast('Failed to load messages', 'error');
                }
            },
            error: (xhr, status, error) => {
                console.error('AJAX error loading messages:', error);
                this.showToast('Failed to load messages', 'error');
            },
            complete: () => {
                // Hide loading indicator
                this.hideLoadingIndicator();
            }
        });
    }

    loadGroupSidebar() {
        $.ajax({
            url: BASEURL + '/group-message/fetch-group-sidebar',
            method: 'POST',
            data: {
                _token: this.csrfToken,
                group_id: this.groupUniqueId
            },
            success: (response) => {
                if (response.status) {
                    this.handleGroupSidebarLoaded(response);
                }
            }   
        });
    }

    handleGroupSidebarLoaded(response) {
        $("#groupsList").html(response.sidebar_content);
    }

    // Handle messages loaded from AJAX
    handleMessagesLoaded(data,appendMessage = true) {
        // console.log(data);
        const { messages_content, last_msg_id, first_msg_id } = data;
        // Update message ID tracking
        if (last_msg_id) {
            this.lastMessageId = last_msg_id;
        }
        if (first_msg_id) {
            this.firstMessageId = first_msg_id;
        }

        // Process and display messages
        if(appendMessage){  
            $("#messagesContainer").append(messages_content);
        }else{
            $("#messagesContainer").html(messages_content);
        }
        this.updateMessageCounters();
        // Update UI elements that depend on message data
        this.updateMessageCounters();
        this.scrollToBottom();
    }

    // Render individual message HTML
    renderMessage(message) {
        // This would render the message based on whether it's sent or received
        // You can use the existing message components or create dynamic HTML
        if (message.user_id == this.currentUserId) {
            // Sent message - use the sent message template
            return this.renderSentMessage(message);
        } else {
            // Received message - use the received message template
            return this.renderReceivedMessage(message);
        }
    }

    // Render sent message HTML
    renderSentMessage(message) {
        // This would create the HTML for sent messages
        // You can either make an AJAX call to get the rendered HTML
        // or create the HTML structure manually
        return `<div class="group-messages-message sent" data-message-id="${message.id}" id="message-${message.unique_id}">
            <div class="group-messages-message-content">
                <div class="group-messages-message-bubble">
                    <div class="group-messages-message-text">
                        <p class="group-messages-chat-message" id="cpMsg${message.unique_id}">${message.message}</p>
                    </div>
                    <span class="group-messages-message-time-sent">
                        <i class="fa-sharp fa-regular fa-clock"></i>
                        ${this.formatMessageTime(message.created_at)}
                    </span>
                </div>
            </div>
        </div>`;
    }

    // Render received message HTML
    renderReceivedMessage(message) {
        // This would create the HTML for received messages
        return `<div class="group-messages-message received" data-message-id="${message.id}" id="message-${message.unique_id}">
            <div class="group-messages-message-avatar">
                <img src="${message.sent_by?.profile_image || ''}" alt="${message.sent_by?.first_name || 'User'}" class="group-messages-message-avatar-image" />
            </div>
            <div class="group-messages-message-content">
                <div class="group-messages-message-bubble">
                    <div class="group-messages-message-text">
                        <p class="group-messages-chat-message" id="cpMsg${message.unique_id}">${message.message}</p>
                    </div>
                    <span class="group-messages-message-time-received">
                        <i class="fa-sharp fa-regular fa-clock"></i>
                        ${this.formatMessageTime(message.created_at)}
                    </span>
                </div>
            </div>
        </div>`;
    }

    // Format message time
    formatMessageTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: true 
        });
    }

    // Show loading indicator
    showLoadingIndicator() {
        // Add loading indicator to the chat container
        const loadingHtml = `
            <div class="group-messages-loading" id="group-messages-loading">
                <div class="group-messages-loading-spinner"></div>
                <span>Loading messages...</span>
            </div>
        `;
        
        $('.group-messages-messages-container').prepend(loadingHtml);
    }

    // Hide loading indicator
    hideLoadingIndicator() {
        $('#group-messages-loading').remove();
    }

    // Update message counters
    updateMessageCounters() {
        const totalMessages = $('.group-messages-message').length;
        // Update any UI elements that show message count
        $('.group-messages-message-count').text(totalMessages);
    }

    // Initialize emoji picker
    initializeEmojiPicker() {
        // Initialize emoji picker for main input
        
        if (typeof EmojiPicker !== 'undefined') {
            new EmojiPicker("#emojiBtn", {
                targetElement: "#messageInput"
            });
        }

        // Initialize emoji picker for modal
        if (typeof EmojiPicker !== 'undefined') {
            new EmojiPicker(".message-emoji-icon-modal", {
                targetElement: "#messagenew"
            });
        }

        if(typeof EmojiPicker !== 'undefined'){

            $("#messagesContainer .message-reaction").each(function () {
                var ele_id = $(this).attr("id");
                var message_id = $(this).data("message-id");
                var e = $(this);

                var ele_id = "#emojiBtn-" + message_id;
                if ($(ele_id)) {
                    new EmojiPicker(ele_id, {
                        onEmojiSelect: (selectedEmoji) => {
                            $.ajax({
                                url: BASEURL + "/group-message/add-reaction", // Replace with your Laravel route
                                type: "POST",
                                data: {
                                    _token: csrf_token,
                                    message_id: message_id,
                                    reaction: selectedEmoji,
                                },
                                success: function (response) {},
                                error: function (xhr, status, error) {
                                    console.error("Error adding reaction:", error);
                                },
                            });
                        },
                    });
                }
            });
        }
    }

    // Initialize drag and drop
    initializeDragAndDrop() {
        // File drag and drop functionality
        this.initializeFileDragAndDrop();
    }

    // Initialize file drag and drop functionality
    initializeFileDragAndDrop() {
        const chatArea = document.querySelector('.group-messages-chat-container');
        const modal = document.getElementById('file-upload-modal');
        const previewContainer = document.getElementById('filePreviewContainer');
        const uploadButton = document.getElementById('upload-button');
        const sendInput = document.getElementById('messageInput');
        
        if (!chatArea || !modal || !previewContainer || !uploadButton || !sendInput) {
            console.warn('Some drag and drop elements not found');
            return;
        }

        // Open modal when file is dragged over chat area
        chatArea.addEventListener("dragover", (e) => {
            e.preventDefault();
            if (e.dataTransfer && e.dataTransfer.items) {
                for (let i = 0; i < e.dataTransfer.items.length; i++) {
                    if (e.dataTransfer.items[i].kind === "file") {
                        $("#uploadModal").modal("show");
                        break;
                    }
                }
            }
        });

        // Handle drag over on modal
        modal.addEventListener("dragover", function(e) {
            e.preventDefault();
        });

        // Detect file drop inside modal
        modal.addEventListener("drop", function(e) {
            e.preventDefault();
            if (e.dataTransfer.files.length > 0) {
                groupMessagesManager.handleFileSelection(e.dataTransfer.files);
            }
        });

        // Handle paste events in message input
        // sendInput.addEventListener('paste', function(event) {
        //     var items = (event.clipboardData || event.originalEvent?.clipboardData)?.items;
        //     var files = [];
        //     var hasNonTextData = false;
            
        //     if (items) {
        //         for (var item of items) {
        //             if (item.kind === "file") {
        //                 files.push(item.getAsFile());
        //                 hasNonTextData = true;
        //             } else if (!item.type.startsWith("text")) {
        //                 hasNonTextData = true;
        //             }
        //         }
        //     }
            
        //     if (hasNonTextData) {
        //         $("#uploadModal").modal("show");
        //     }
        //     if (files.length > 0) {
        //         groupMessagesManager.handleFileSelection(files);
        //     }
        // });

        // Handle file input change
        $(document).on('change', '#attachment', function() {
            var fileInput = this;
            var filePreviewContainer = document.getElementById('filePreviewContainer');

            // Loop through each file and create a preview
            Array.from(fileInput.files).forEach((file, index) => {
                groupMessagesManager.filesToUpload.push(file);

                // Create file preview
                var previewDiv = groupMessagesManager.createFilePreview(file, groupMessagesManager.filesToUpload.length - 1);
                filePreviewContainer.appendChild(previewDiv);
            });
        });

        // Handle file drag and drop on preview container
        $(document).on('drop', '#filePreviewContainer', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var filePreviewContainer = document.getElementById('filePreviewContainer');
            Array.from(e.originalEvent.dataTransfer.files).forEach((file, index) => {
                groupMessagesManager.filesToUpload.push(file);

                // Create file preview
                var previewDiv = groupMessagesManager.createFilePreview(file, groupMessagesManager.filesToUpload.length - 1);
                filePreviewContainer.appendChild(previewDiv);
            });
        });

        // Handle file upload form submission
        $(document).on('submit', '#file-upload-form', function(e) {
            e.preventDefault();
            groupMessagesManager.handleFileUploadFormSubmit();
        });

        // Handle close modal
        $(document).on('click', '#closemodal', function() {
            groupMessagesManager.resetFilePreview();
        });

        // Handle remove file from preview
        $(document).on('click', '.preview-remove-btn', function() {
            var index = $(this).closest('.file-preview').data('index');
            groupMessagesManager.removeFilePreview(index);
        });

        // Handle close upload modal
        $(document).on('click', '.close-upload-modal', function() {
            groupMessagesManager.resetFilePreview();
        });
    }

    // Upload files function
    uploadFiles(files) {
        const MAX_FILES = 6;
        if (files.length > MAX_FILES) {
            this.showToast(`You can only upload a maximum ${MAX_FILES} files.`, 'error');
            return;
        }
        
        const filePreviewContainer = document.querySelector('.CDSFeed-file-list');
        
        Array.from(files).forEach((file, index) => {
            this.filesToUpload.push(file);
            var previewDiv = this.createFilePreview(file, this.filesToUpload.length - 1);
            filePreviewContainer.appendChild(previewDiv);
        });
        $(".CDSFeed-file-list").show();
    }

    // Create file preview
    createFilePreview(file, index) {
        var previewDiv = document.createElement('div');
        previewDiv.classList.add('file-preview');
        previewDiv.dataset.index = index;
        previewDiv.style.cssText = `
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #ccc;
            padding: 8px;
            margin: 5px 0;
            border-radius: 6px;
            position: relative;
            background: #f9f9f9;
        `;

        var icon = document.createElement('div');
        icon.style.cssText = `
            width: 40px;
            height: 40px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 4px;
            background: #eee;
        `;
        
        // Set preview icon based on file type
        if (file.type.startsWith('image/')) {
            var img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.style.cssText = 'width: 100%; height: 100%; object-fit: cover;';
            icon.appendChild(img);
        } else if (file.type === 'application/pdf') {
            icon.textContent = '📄';
            icon.style.fontSize = '24px';
        } else {
            icon.textContent = '📁';
            icon.style.fontSize = '24px';
        }

        var fileNameSpan = document.createElement('span');
        fileNameSpan.textContent = file.name;
        fileNameSpan.style.flexGrow = '1';

        // Create progress bar
        var progress = document.createElement('div');
        progress.classList.add('progress-bar');
        progress.style.cssText = `
            width: 0%;
            height: 5px;
            background: green;
            position: absolute;
            bottom: 0;
            left: 0;
            transition: width 0.3s;
        `;

        // Create close button
        var closeBtn = document.createElement('span');
        closeBtn.textContent = '✖';
        closeBtn.classList.add("preview-remove-btn");
        closeBtn.style.cssText = `
            cursor: pointer;
            color: red;
            font-weight: bold;
            margin-left: 10px;
            flex-shrink: 0;
        `;

        // Append elements to preview div
        previewDiv.appendChild(icon);
        previewDiv.appendChild(fileNameSpan);
        previewDiv.appendChild(closeBtn);
        previewDiv.appendChild(progress);

        return previewDiv;
    }

    // Remove file preview
    removeFilePreview(index) {
        if (index >= 0 && index < this.filesToUpload.length) {
            this.filesToUpload.splice(index, 1);
            console.log(`File at index ${index} removed. Total files: ${this.filesToUpload.length}`);
        }
    }

    // Update file preview
    updateFilePreview() {
        // This method is no longer needed as FileUploadManager handles previews
        console.log('File preview update requested - handled by FileUploadManager');
    }

    // Reset file preview
    resetFilePreview() {
        this.filesToUpload = [];
        console.log('File preview reset - filesToUpload cleared');
    }

    // Handle file upload form submission
    handleFileUploadFormSubmit() {
        var replyTo = $('#group_reply_to_id').val();
        var myurl = $('#geturl').val();
        var message = $('#messagenew').val();
        // Get files from FileUploadManager
       let files = [];
if (this.messageUploader && typeof this.messageUploader.getFiles === 'function') {
    files = this.messageUploader.getFiles();
} else {
    files = this.filesToUpload || [];
}  
       
          if (files.length === 0 && !message.trim()) {
            this.showToast("Files or message required to upload", 'error');
            return false;
        }

        $("#sendBtnnew").prop('disabled', true);
        var formData = new FormData();

        // Add files
        files.forEach((file) => {
            formData.append("attachment[]", file);
        });

        // Add message and reply data
        formData.append('message', message);
        formData.append('group_id', this.groupId);
        formData.append('group_unique_id', this.groupUniqueId);
        formData.append('reply_to', replyTo);
        formData.append('_token', this.csrfToken);

        $.ajax({
            url: myurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': this.csrfToken
            },
            success: (response) => {
                if (response.status === true) {
                    this.showToast(response.message || "Files uploaded successfully!", 'success');
                    $('#uploadModal').modal('hide');
                    $('#file-upload-form')[0].reset();
                    $('#group_reply_to_id').val('');
                    $('#messagenew').val('');
                    $('#group_replyQuotedMsg').hide();
                    this.resetFilePreview();
                    this.hideReplyTo();
                } else {
                    this.showToast(response.message || "Upload failed", 'error');
                }
            },
            error: (xhr) => {
                this.showToast('File upload failed', 'error');
                console.error('Upload error:', xhr);
            },
            complete: () => {
                $("#sendBtnnew").prop('disabled', false);
            }
        });
    }

    // Open file upload modal
    openFileUploadModal() {
        $('#uploadModal').modal('show');
        this.resetFilePreview();
    }

    // Handle file selection
    handleFileSelection(files) {
        if (files && files.length > 0) {
            // Convert FileList-like object to array and add to filesToUpload
            const fileArray = Array.from(files);
            
            const MAX_FILES = 6;
            if (this.filesToUpload.length + fileArray.length > MAX_FILES) {
                this.showToast(`You can only upload a maximum ${MAX_FILES} files.`, 'error');
                return;
            }
            
            // Add files to the upload queue
            fileArray.forEach(file => {
                this.filesToUpload.push(file);
            });
            
            console.log(`Added ${fileArray.length} files to upload queue. Total files: ${this.filesToUpload.length}`);
        }
    }

    // Initialize presence channel
    initializePresenceChannel() {
        // WebSocket presence channel
    }

    // Initialize socket for group
    initializeSocketForGroup(groupId) {
        
        // if (!groupId) {
        //     // console.error('Group ID is required for socket initialization');
        //     return;
        // }
        // Initialize presence channel for group
        this.initializeGroupPresenceChannel(groupId);
        
        // Initialize private channels for group events
        this.initializeGroupPrivateChannels(groupId);
    }

    // Initialize presence channel for group (similar to group-chatapp.js)
    initializeGroupPresenceChannel(groupId) {
        if (!groupId) {
            return;
        }
        try {
            if (this.presenceChannel) {
                window.Echo.leave(this.presenceChannel);
            }
        } catch (e) {
            // console.log('Error leaving existing channel:', e);
        }
        
        // Join the presence channel
        this.presenceChannel = window.Echo.join(`presence-group.${groupId}`);
        
        this.presenceChannel
            .here((users) => {
                // console.log('Successfully joined presence group channel, users:', users);
                this.channelReady = true;
                this.updateOnlineUsers(users);
            })
            .joining((user) => {
                // console.log('User joining group:', user);
                this.handleUserJoined(user);
            })
            .leaving((user) => {
                // console.log('User leaving group:', user);
                this.handleUserLeft(user);
                this.hideTypingIndicator(groupId);
            })
            .listenForWhisper('typing', (e) => {
                // console.log('Typing whisper group received:', e);
                if (e.userId != this.currentUserId) {
                    if (e.typing) {
                        this.showTypingIndicator(groupId, e.userName || 'User');
                    } else {
                        setTimeout(() => {
                            this.hideTypingIndicator(groupId);
                        }, 1500);
                    }
                }
            })
            .error((error) => {
                console.error('Presence channel error:', error);
                this.channelReady = false;
            });
        
        // Listen for subscription events on the Pusher channel
        setTimeout(() => {
            const pusherChannel = window.Echo.connector.pusher.channel(`presence-group.${groupId}`);
            if (pusherChannel) {
                pusherChannel.bind('pusher:subscription_error', (status) => {
                    console.error('Subscription error:', status);
                    if (status === 403) {
                        console.error('Authorization failed - check your channels.php');
                    }
                });
            }
        }, 100);
    }

    // Initialize private channels for group events
    initializeGroupPrivateChannels(groupId) {
        // Leave any existing channels first
        try {
            window.Echo.leave(`group-message-socket.${this.currentUserId}`);
          
            if(groupId){
                window.Echo.leave(`group-message.${groupId}`);
                window.Echo.leave(`groupMessageReaction.${groupId}`);
              
                // Group chat channel for messages and events - Same flow as group-chatapp.js
                window.Echo.private(`group-message.${groupId}`).listen(
                    "GroupChatSocket",
                    (e) => {
                        const response = e.data;
                        // console.log('Group chat socket event received:', response);
                        // Handle events based on action type - Same logic as group-chatapp.js
                        if (response.action == "group_member_removed") {
                            if (response.receiver_id == this.currentUserId) {
                                this.handleGroupMemberRemoved(response.group_id);
                            }
                            // Update conversation list if needed
                            
                        }

                        if (response.action == "group_deleted") {
                            this.handleGroupDeleted(response.group_id);
                        }
                        
                        // Only process these actions if this is the active group
                        if (this.groupId == groupId) {
                            if (response.action == "new_message") {
                                // if(response.receiver_id == this.currentUserId){
                                    if (response.last_message_id !== this.lastMessageId) {
                                        // console.log('New message received, fetching messages...');
                                        this.loadGroupMessages(this.groupUniqueId, this.lastMessageId);
                                        this.loadGroupSidebar();
                                    }    
                                // }
                                
                            }

                            if (response.action == "group_message_read") {
                                if (response.receiver_id == this.currentUserId) {
                                    this.handleMessageRead(response);
                                }
                                
                            }

                            if (response.action == "user_typing") {
                                this.handleUserTyping(response);
                            }

                            if (response.action == "deleted_msg_for_everyone") {
                                this.handleMessageDeletedForEveryone(response);
                                
                            }

                            if (response.action == "delete_selected_attachments") {
                                this.handleAttachmentsDeleted(response);
                                
                            }
                            if (response.action == "delete_msg_for_me") {
                                if (this.currentUserId == response.sender_id) {
                                    const messageUniqueId = response.messageUniqueId;
                                    $(`#message-${messageUniqueId}`).remove();
                                }
                                
                            }
                            if (response.action == "message_edited") {
                                this.handleMessageEdited(response);
                                
                            }

                            if (response.action == "new_member_joined") {
                                this.loadGroupMessages(this.groupUniqueId, this.lastMessageId);
                            }

                            if (response.action == "new_file_uploaded") {
                                this.handleFileUploaded(response);
                            }
                        }
                    }
                );

                // Group message reaction channel
                window.Echo.private(`groupMessageReaction.${groupId}`).listen(
                    "GroupMessageReactionChange",
                    (event) => {
                        // console.log('Group message reaction event received:', event);
                        this.handleGroupMessageReactionEvent(event, groupId);
                    }
                );
            }
            window.Echo.private(`group-message-socket.${this.currentUserId}`).listen(
                "GroupMessageSocket",
                (e) => {
                    const response = e.data;
                    // console.log('Group chat socket event received:', response);
                    // Handle events based on action type - Same logic as group-chatapp.js
                    if (response.action == "new_group_added") {
                        // this.loadGroupMessages(this.groupUniqueId, this.lastMessageId);
                        this.loadGroupSidebar();
                    }
                }
            );
        } catch (e) {
            // console.log('Error leaving existing channels:', e);
        }
        

    }

    // Handle group chat socket events
    handleGroupChatSocketEvent(response, groupId) {
        const { action, receiver_id, group_id, last_message_id, message, sender_id } = response;

        switch (action) {
            case 'new_message':
                if (last_message_id !== this.lastMessageId) {
                    // console.log('New message received, fetching messages...');
                    this.loadGroupMessages(this.groupUniqueId, this.lastMessageId);
                    this.loadGroupSidebar();
                }
                break;

            case 'group_member_removed':
                if (receiver_id == this.currentUserId) {
                    this.handleGroupMemberRemoved(group_id);
                }
                break;

            case 'group_deleted':
                this.handleGroupDeleted(group_id);
                break;

            case 'group_message_read':
                if (receiver_id == this.currentUserId) {
                    this.handleMessageRead(response);
                }
                break;

            case 'user_typing':
                this.handleUserTyping(response);
                break;

            case 'deleted_msg_for_everyone':
                this.handleMessageDeletedForEveryone(response);
                break;

            case 'delete_selected_attachments':
                this.handleAttachmentsDeleted(response);
                break;

            case 'new_file_uploaded':
                this.handleFileUploaded(response);
                break;

            default:
                // console.log('Unknown group chat action:', action);
        }
    }

    // Handle group message reaction events
    handleGroupMessageReactionEvent(event, groupId) {
        const { messageUniqueId, status } = event;
        
        // Fetch updated message with reactions
        $.ajax({
            type: "POST",
            url: BASEURL + `/group-message/reacted-message/${groupId}/${messageUniqueId}`,
            dataType: "json",
            data: {
                _token: this.csrfToken,
            },
            success: (response) => {
                // Update message in chat container
                const messageElement = $(`.group-messages-chat-container #message-${response.messageUniqueId}`);
                if (messageElement.length > 0) {
                    messageElement.html(response.contents);
                }

                // Handle reaction removal
                if (status == "remove") {
                    const reactionsList = messageElement.find(`#reactionsList${messageUniqueId}`);
                    if (reactionsList.length > 0) {
                        reactionsList.hide();
                    }
                }

                // Reinitialize emoji functionality
                this.initializeEmojiPicker();
            },
            error: (xhr, status, error) => {
                console.error("Error in reaction:", error);
            },
        });
    }

    // Handle user joined group
    handleUserJoined(user) {
        // console.log('User joined group:', user);
        // this.showToast(`${user.name || 'User'} joined the group`, 'info');
        this.updateOnlineUsers();
    }

    // Handle user left group
    handleUserLeft(user) {
        // console.log('User left group:', user);
        // this.showToast(`${user.name || 'User'} left the group`, 'info');
        this.updateOnlineUsers();
    }

    // Update online users display
    updateOnlineUsers(users = null) {
        if (!users && this.presenceChannel) {
            // Get current users from channel
            // This would need to be implemented based on your UI requirements
        }
        
        // Update UI to show online users
        // This would need to be implemented based on your UI requirements
    }

    // Show typing indicator
    showTypingIndicator(groupId, userName) {
        const typingElement = $(`.group-messages-typing-indicator[data-group-id="${groupId}"]`);
        if (typingElement.length === 0) {
            const typingHtml = `
                <div class="group-messages-typing-indicator" data-group-id="${groupId}">
                    <span class="typing-text">${userName} is typing...</span>
                    <div class="typing-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            `;
            $('.group-messages-chat-container').append(typingHtml);
        } else {
            typingElement.find('.typing-text').text(`${userName} is typing...`);
            typingElement.show();
        }
    }

    // Hide typing indicator
    hideTypingIndicator(groupId = null) {
        if (groupId) {
            // Hide typing indicator for specific group
            $(`.group-messages-typing-indicator[data-group-id="${groupId}"]`).hide();
        } else {
            // Hide all typing indicators
            $('.group-messages-typing-indicator').hide();
        }
    }

    // Handle group member removed
    handleGroupMemberRemoved(groupId) {
        this.showToast('You have been removed from this group', 'error');
        // Redirect or update UI as needed
        setTimeout(() => {
            window.location.href = BASEURL + "/group-message";
        }, 2000);
    }

    // Handle group deleted
    handleGroupDeleted(groupId) {
        this.showToast('This group has been deleted', 'error');
        // Redirect or update UI as needed
        setTimeout(() => {
            window.location.href = BASEURL + "/group-message";
        }, 2000);
    }

    // Handle message read
    handleMessageRead(response) {
        const { unread_count } = response;
        
        // Update unread count display
        if (unread_count == "0") {
            $(".group-messages-count").css("opacity", "0");
        } else {
            $(".group-messages-count").css("opacity", "1");
        }
        $(".group-messages-count").html(unread_count);
    }

    // Handle user typing - Same as group-chatapp.js
    handleUserTyping(response) {
        const { sender_id, isTyping, member_typing } = response;
        
        if (sender_id != this.currentUserId) {
            if (isTyping == 1) {
                this.showTypingIndicator(this.groupId, member_typing);
            } else {
                this.hideTypingIndicator(this.groupId);
            }
        }
    }

    // Enhanced typing indicator handling - Same as group-chatapp.js
    handleTypingIndicator(groupId, isTyping, memberName = '') {
        if (isTyping) {
            this.showTypingIndicator(groupId, memberName);
        } else {
            this.hideTypingIndicator(groupId);
        }
    }

    // Handle message deleted for everyone
    handleMessageDeletedForEveryone(response) {
        const { messageUniqueId } = response;
        const messageElement = $(`#message-${messageUniqueId}`);
        
        if (messageElement.length > 0) {
            messageElement.find('.group-messages-chat-message').html(
                '<p class="deleted-message chat-message">This message was deleted.</p>'
            );
            messageElement.find('.group-messages-message-actions').hide();
        }
    }

    // Handle attachments deleted
    handleAttachmentsDeleted(response) {
        const { messageUniqueId, attachments } = response;
        const messageElement = $(`#message-${messageUniqueId}`);
        
        if (messageElement.length > 0 && attachments && attachments.length > 0) {
            // Update message to remove deleted attachments
            // This would need to be implemented based on your attachment handling
            // console.log('Attachments deleted for message:', messageUniqueId);
        }
    }

    // Handle file uploaded
    handleFileUploaded(response) {
        const { file } = response;
        // console.log('New file uploaded:', file);
        this.showToast('New file uploaded to the group', 'info');
    }

    // Handle message edited - Same as group-chatapp.js
    handleMessageEdited(response) {
        const { messageUniqueId, editedMessage } = response;
        // Update the edited message indicator and content
        const editedMsgElement = $(`#editedMsg${messageUniqueId}`);
        const messageContentElement = $(`#cpMsg${messageUniqueId}`);
        
        if (editedMsgElement.length > 0) {
            editedMsgElement.html("edited");
        }
        
        if (messageContentElement.length > 0) {
            messageContentElement.html(editedMessage);
        }
    }

    // Update conversation list - Same as group-chatapp.js
    updateConversationList() {
        // This method would update the sidebar conversation list
        // Implementation depends on your UI structure
        // console.log('Updating conversation list...');
        
        // You can implement this based on your needs:
        // - Refresh unread counts
        // - Update last message previews
        // - Reorder conversations by activity
        // - Update online status indicators
    }

    // Handle typing indicator - Enhanced version similar to group-chatapp.js
    handleTyping(message) {
        if (this.typingTimer) {
            clearTimeout(this.typingTimer);
        }

        if (message.length > 0) {
            // Send typing indicator
            this.sendTypingIndicator(true);
            
            // Set timer to stop typing indicator
            this.typingTimer = setTimeout(() => {
                this.sendTypingIndicator(false);
            }, this.typingDelay);
        } else {
            // Stop typing indicator immediately if message is empty
            this.sendTypingIndicator(false);
        }
    }

    // Enhanced typing handling with debouncing - Same as group-chatapp.js
    handleTypingWithDebounce(message, groupId) {
        // Clear existing typing timer
        if (this.groupTypingTimers && this.groupTypingTimers[groupId]) {
            clearTimeout(this.groupTypingTimers[groupId]);
        }

        // Initialize timers object if not exists
        if (!this.groupTypingTimers) {
            this.groupTypingTimers = {};
        }

        // Handle typing indicator
        if (message.length > 0) {
            if (!this.groupCurrentlyTyping || !this.groupCurrentlyTyping[groupId]) {
                if (!this.groupCurrentlyTyping) this.groupCurrentlyTyping = {};
                this.groupCurrentlyTyping[groupId] = true;
                // Send typing status
                this.sendTypingIndicator(true, groupId);
            }
            
            this.groupTypingTimers[groupId] = setTimeout(() => {
                if (this.groupCurrentlyTyping && this.groupCurrentlyTyping[groupId]) {
                    this.groupCurrentlyTyping[groupId] = false;
                    this.sendTypingIndicator(false, groupId);
                }
            }, 1500);
        } else {
            if (this.groupCurrentlyTyping && this.groupCurrentlyTyping[groupId]) {
                this.groupCurrentlyTyping[groupId] = false;
                this.sendTypingIndicator(false, groupId);
            }
        }
    }

    // Send typing indicator via presence channel - Enhanced version
    sendTypingIndicator(isTyping, groupId = null) {
        const targetGroupId = groupId || this.groupId;
        
        if (this.presenceChannel && this.channelReady) {
            this.presenceChannel.whisper('typing', {
                userId: this.currentUserId,
                userName: this.currentUserName,
                typing: isTyping,
                groupId: targetGroupId
            });
        }
    }

    // Start typing for the current group
    startTyping() {
        this.isTyping = true;
        this.whisperTyping(true);
    }

    // Stop typing for the current group
    stopTyping() {
        this.isTyping = false;
        this.whisperTyping(false);
        clearTimeout(this.typingTimer);
        
        // Also clear group-specific typing timers
        if (this.groupTypingTimers && this.groupTypingTimers[this.groupId]) {
            clearTimeout(this.groupTypingTimers[this.groupId]);
        }
        
        // Update group typing status
        if (this.groupCurrentlyTyping && this.groupCurrentlyTyping[this.groupId]) {
            this.groupCurrentlyTyping[this.groupId] = false;
        }
    }

    // Send typing status via WebSocket whisper for groups
    whisperTyping(isTyping) {
        if (!this.presenceChannel || !this.channelReady) {
            // console.log('Presence channel not ready, attempting to initialize...');
            this.initializePresenceChannel();
            return;
        }

        try {
            this.presenceChannel.whisper('typing', {
                userId: this.currentUserId,
                userName: this.currentUserName || 'User',
                typing: isTyping,
                groupId: this.groupId
            });
            // console.log('Group typing whisper sent:', isTyping, 'for group:', this.groupId);
        } catch (error) {
            console.error('Group whisper error:', error);
        }
    }

    // Handle typing when input is empty or user stops typing
    handleTypingStop(groupId) {
        if (this.groupCurrentlyTyping && this.groupCurrentlyTyping[groupId]) {
            this.groupCurrentlyTyping[groupId] = false;
            this.sendTypingIndicator(false, groupId);
        }
        
        // Clear typing timer for this group
        if (this.groupTypingTimers && this.groupTypingTimers[groupId]) {
            clearTimeout(this.groupTypingTimers[groupId]);
        }
    }

    // Clean up channels when leaving
    cleanupChannels() {
        if (this.presenceChannel) {
            try {
                window.Echo.leave(this.presenceChannel);
                this.presenceChannel = null;
            } catch (e) {
                // console.log('Error leaving presence channel:', e);
            }
        }

        if (this.groupId) {
            try {
                window.Echo.leave(`group-message.${this.groupId}`);
                window.Echo.leave(`groupMessageReaction.${this.groupId}`);
            } catch (e) {
                // console.log('Error leaving private channels:', e);
            }
        }

        this.channelReady = false;
    }

    // Initialize message IDs
    initializeMessageIds() {
        // Get message IDs from DOM or data attributes
        if (typeof cdsGroupMessagesApp !== 'undefined' && cdsGroupMessagesApp.elements) {
            this.lastMessageId = cdsGroupMessagesApp.elements.lastMessageId || 0;
            this.firstMessageId = cdsGroupMessagesApp.elements.firstMessageId || 0;
            this.hasPreviousMessages = cdsGroupMessagesApp.elements.hasPreviousMessages || 0;
        }else{
            this.lastMessageId = 0;
            this.firstMessageId = 0;
            this.hasPreviousMessages = 0;
        }
        // Initialize infinite scroll properties
        this.hasMoreMessages = this.firstMessageId > 0;
    }

    // Add message options styles
    addMessageOptionsStyles() {
        // Add any required styles
    }

    // Rebind group events
    rebindGroupEvents() {
        // Rebind events after dynamic content updates
        this.bindScrollEvent();
    }

    // Handle new message received
    handleNewMessage(data) {
        // Add new message to the container
        const messageHtml = this.renderMessage(data.message);
        $('#messagesContainer').append(messageHtml);
        
        // Auto-scroll to bottom
        this.scrollToBottom();
        
        // Show notification
        this.showToast(`New message from ${data.message.sender_name}`, 'info');
    }

    // Handle message update
    handleMessageUpdate(data) {
        const messageElement = $(`#message-${data.unique_id}`);
        if (messageElement.length > 0) {
            messageElement.find('.group-messages-chat-message').text(data.edited_message);
            messageElement.find('.group-messages-edited-indicator').show();
        }
    }

    // Handle message delete
    handleMessageDelete(data) {
        const messageElement = $(`#message-${data.unique_id}`);
        if (messageElement.length > 0) {
            messageElement.find('.group-messages-chat-message').text('This message was deleted.');
            messageElement.find('.group-messages-message-actions').hide();
        }
    }

    // Handle reaction update
    handleReactionUpdate(data) {
        this.updateMessageReactions(data.message_id, data.reaction, 'add');
    }

    // Render message HTML
    renderMessage(message) {
        // Generate message HTML based on message type
        return `<div class="group-messages-message ${message.type}" data-message-id="${message.id}">
            <!-- Message content based on type -->
        </div>`;
    }

    // Scroll to bottom of messages
    scrollToBottom() {
        const container = $('#messagesContainer')[0];
        if (container) {
            container.scrollTop = container.scrollHeight;
            // Hide scroll to bottom button after scrolling
            this.hideScrollToBottomButton();
        }
    }

    // Initialize scroll to bottom button functionality
    initializeScrollToBottomButton() {
        const container = $('#messagesContainer')[0];
        const scrollButton = $('#scrollToBottom');
        
        if (container && scrollButton.length) {
            // Show/hide button based on scroll position
            container.addEventListener('scroll', () => {
                this.handleScrollPosition();
            });
            
            // Initial check
            this.handleScrollPosition();
        }
    }

    // Handle scroll position to show/hide scroll button
    handleScrollPosition() {
        const container = $('#messagesContainer')[0];
        const scrollButton = $('#scrollToBottom');
        
        if (container && scrollButton.length) {
            const scrollTop = container.scrollTop;
            const scrollHeight = container.scrollHeight;
            const clientHeight = container.clientHeight;
            
            // Show button if not at bottom, hide if at bottom
            if (scrollHeight - scrollTop - clientHeight > 100) {
                this.showScrollToBottomButton();
            } else {
                this.hideScrollToBottomButton();
            }
            
            // If user manually scrolls to bottom, ensure they're at the very bottom
            if (scrollHeight - scrollTop - clientHeight < 10) {
                this.scrollToBottom();
            }
        }
    }

    // Show scroll to bottom button
    showScrollToBottomButton() {
        $('#scrollToBottom').fadeIn(200);
    }

    // Hide scroll to bottom button
    hideScrollToBottomButton() {
        $('#scrollToBottom').fadeOut(200);
    }

    // Handle content loading completion and scroll to bottom
    handleContentLoaded() {
        // Wait for images and other content to load
        const images = $('#messagesContainer img');
        if (images.length > 0) {
            let loadedImages = 0;
            images.each(function() {
                if (this.complete) {
                    loadedImages++;
                } else {
                    $(this).on('load', function() {
                        loadedImages++;
                        if (loadedImages === images.length) {
                            // All images loaded, scroll to bottom
                            this.scrollToBottom();
                        }
                    }.bind(this));
                }
            });
            
            // If all images are already loaded, scroll immediately
            if (loadedImages === images.length) {
                this.scrollToBottom();
            }
        } else {
            // No images, scroll immediately
            this.scrollToBottom();
        }
    }

    // Show toast notification
    showToast(message, type = 'info') {
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else {
            const toast = $(`
                <div class="group-messages-toast group-messages-toast-${type}">
                    ${message}
                </div>
            `);
            
            $('body').append(toast);
            toast.fadeIn(300);
            
            setTimeout(() => {
                toast.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }
    }

    // Get current user ID
    getCurrentUserId() {
        return document.querySelector('meta[name="user-id"]')?.getAttribute('content');
    }

    // Get current user name
    getCurrentUserName() {
        return document.querySelector('meta[name="user-name"]')?.getAttribute('content');
    }

    // Cancel edit mode
    cancelEditMode() {
        // Cancel edit mode functionality
        this.clearEditMode();
    }

    // Clear edit mode
    clearEditMode() {
        // Clear edit mode functionality
        $("#sendBtn").prop('disabled', false);
        $("#messageInput").prop('disabled', false); 
        $("#messageInput").val('');
    }

    // Function to switch to a different group
    switchToGroup(groupUniqueId, groupId) {
        if (!groupUniqueId || groupUniqueId === this.groupUniqueId) {
            // console.log('Same group or invalid ID, not switching');
            return; // Same group or invalid ID
        }

        // console.log('Switching to group:', groupUniqueId, 'Group ID:', groupId);

        // Stop typing and leave previous group's presence channel
        this.stopTyping();
        this.hideTypingIndicator();
        this.leaveGroup();

        // Update the current group unique ID
        this.groupUniqueId = groupUniqueId;
        this.groupId = groupId;
        
        // Update hidden input value
        const groupIdInput = document.getElementById('get_group_unique_id');
        if (groupIdInput) {
            groupIdInput.value = groupUniqueId;
        }

        // Update URL without page reload (optional)
        if (window.history && window.history.pushState) {
            const newUrl = BASEURL + '/group-message/chat/' + groupUniqueId;
            window.history.pushState({groupId: groupUniqueId}, '', newUrl);
        }

        // Reset message IDs for new group
        this.lastMessageId = 0;
        this.firstMessageId = 0;
        $(".group-messages-group-item").removeClass("active");
        $(".group-messages-group-item[data-group-id='"+groupUniqueId+"']").addClass("active");
        // Initialize socket for the new group
        this.initializeSocketForGroup(groupId);
         // Show loading indicator
         this.showGroupLoading();
        // Load messages for the new group
        this.switchGroupWindow(groupUniqueId);

        // Update active state in sidebar
        this.updateSidebarActiveState(groupUniqueId);

        // Clear current message input and reply
        this.clearMessageInput();
        this.hideReplyTo();
        
       
    }

    switchGroupWindow(groupUniqueId) {
        $.ajax({
            url: BASEURL+'/group-message/switch-group/'+groupUniqueId,
            type: 'POST',
            data: {
                _token: this.csrfToken,
                group_id: groupUniqueId,
                last_msg_id: 0, // Start from beginning for new group
            },
            success: (response) => {
                if (response.status === true) {
                    // Update message IDs
                    this.lastMessageId = response.last_msg_id || 0;
                    this.firstMessageId = response.first_msg_id || 0;
                    
                    // Update messages container
                    $(".group-messages-main").html(response.message);
                    
                    // Reinitialize socket after content is loaded
                    this.initializeSocketForGroup(this.groupId);
                    
                    // Re-bind events for new group elements
                    this.rebindGroupEvents();
                    
                    // Re-bind scroll event for infinite scroll
                    this.bindScrollEvent();
                    
                    // Scroll to bottom
                    this.scrollToBottom();
                    this.loadGroupFiles();
                    // console.log('Group window switched successfully for:', groupUniqueId);
                
                } else {
                    this.showError(response.message || 'Failed to load group messages');
                }
            },
            error: (xhr, status, error) => {
                this.showError('Failed to load group messages: ' + error);
            }
        });
    }

    // Function to load messages for a specific group
    // loadGroupMessages(groupUniqueId) {
    //     $.ajax({
    //         url: BASEURL+'/group-message/load-messages/'+groupUniqueId,
    //         type: 'POST',
    //         data: {
    //             _token: this.csrfToken,
    //             group_id: groupUniqueId,
    //             last_msg_id: 0, // Start from beginning for new group
    //         },
    //         success: (response) => {
    //             if (response.status === true) {
    //                 // Update message IDs
    //                 this.lastMessageId = response.last_msg_id || 0;
    //                 this.firstMessageId = response.first_msg_id || 0;
                    
    //                 // Update messages container
    //                 $("#messagesContainer").html(response.message);
    //                 $
    //                 // Re-bind events for new group elements
    //                 this.rebindGroupEvents();
                    
    //                 // Scroll to bottom
    //                 this.scrollToBottom();
                    
    //                 // console.log('Group switched successfully:', {
    //                     groupUniqueId: groupUniqueId,
    //                     lastMessageId: this.lastMessageId,
    //                     firstMessageId: this.firstMessageId
    //                 });
    //             } else {
    //                 this.showError(response.message || 'Failed to load group messages');
    //             }
    //         },
    //         error: (xhr, status, error) => {
    //             this.showError('Failed to load group messages: ' + error);
    //         }
    //     });
    // }

    // Function to re-bind events after group switching
    rebindGroupEvents() {
        // console.log('Rebinding group events...');
        
        // Re-bind header button events
        this.bindHeaderButtonEvents();
        
        // Re-bind input events
        this.bindInputEvents();
        
        // Re-bind file panel events
        this.bindFilePanelEvents();
        
        // Re-bind search events
        this.bindSearchEvents();
        
        // Re-bind options menu events
        this.bindOptionsMenuEvents();
        
        // console.log('Group events rebound successfully');
    }

    // Function to bind header button events
    bindHeaderButtonEvents() {
        // Search button
        // const searchBtn = document.getElementById('searchBtn');
        // if (searchBtn) {
        //     searchBtn.onclick = () => this.toggleGroupSearch();
        // }
        
        // Files button
        const filesBtn = document.getElementById('filesBtn');
        if (filesBtn) {
            filesBtn.onclick = () => this.openFilesPanel();
        }
        
        // Options button
        const optionsBtn = document.getElementById('optionsBtn');
        if (optionsBtn) {
            optionsBtn.onclick = (e) => {
                e.stopPropagation();
                this.toggleOptionsMenu();
            };
        }
        
        
    }

    // Function to bind input events
    bindInputEvents() {
        // Send button
        const sendBtn = document.getElementById('sendBtn');
        if (sendBtn) {
            sendBtn.onclick = () => {
                this.sendMessage();
                // Stop typing when message is sent
                this.handleTypingStop(this.groupId);
            };
        }

        
        // Message input
        const messageInput = document.getElementById('messageInput');
        if (messageInput) {
            messageInput.onkeypress = (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                    // Stop typing when message is sent
                    this.handleTypingStop(this.groupId);
                }
            };
            
            messageInput.oninput = (e) => {
                this.handleTypingWithDebounce(e.target.value, this.groupId);
            };
            
            messageInput.onkeydown = (e) => {
                if (e.key === 'Escape') {
                    const editMessageId = document.getElementById('edit_message_id')?.value;
                    if (editMessageId) {
                        e.preventDefault();
                        this.cancelEditMode();
                    }
                }
                if (e.target.value == '') {
                    const editMessageId = document.getElementById('edit_message_id')?.value;
                    if (editMessageId) {    
                        this.clearEditMode();
                    }
                    // Stop typing when input is empty
                    this.handleTypingStop(this.groupId);
                }
            };
            
            messageInput.onblur = (e) => {
                if (e.target.value == '') {
                    const editMessageId = document.getElementById('edit_message_id')?.value;
                    if (editMessageId) {    
                        this.clearEditMode();
                    }
                    // Stop typing when input is empty
                    this.handleTypingStop(this.groupId);
                }
            };
        }
    }

    // Function to bind file panel events
    bindFilePanelEvents() {
        // File input change
        const fileInput = document.getElementById('attachment');
        if (fileInput) {
            fileInput.onchange = (e) => {
                this.handleFileSelection(e.target.files);
            };
        }

        // Attachment button
        const attachBtn = document.getElementById('attachBtn');
        if (attachBtn) {
            attachBtn.onclick = () => this.openFileUploadModal();
        }
    }

    // Function to bind search events
    bindSearchEvents() {
        // Search input
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.oninput = (e) => {
                this.handleGroupSearch(e.target.value);
            };
        }

        // Search button
        // const searchBtn = document.getElementById('searchBtn');
        // if (searchBtn) {
        //     searchBtn.onclick = () => this.toggleGroupSearch();
        // }
    }

    // Function to bind options menu events
    bindOptionsMenuEvents() {
        // Options menu toggle
        const optionsBtn = document.getElementById('optionsBtn');
        if (optionsBtn) {
            optionsBtn.onclick = (e) => {
                e.stopPropagation();
                this.toggleOptionsMenu();
            };
        }

        // Close options menu when clicking outside
        document.addEventListener('click', (e) => {
            const optionsMenu = document.querySelector('.options-menu');
            if (optionsMenu && !optionsMenu.contains(e.target)) {
                this.hideOptionsMenu();
            }
        });
    }

    // Helper functions for group switching
    leaveGroup() {
        if (this.presenceChannel && this.channelReady) {
            this.presenceChannel.unsubscribe();
            this.presenceChannel = null;
            this.channelReady = false;
            // console.log('Left group presence channel');
        }
    }

    updateSidebarActiveState(groupUniqueId) {
        // Remove active class from all group items
        $('.group-item').removeClass('active');
        
        // Add active class to current group
        $(`.group-item[data-group-id="${groupUniqueId}"]`).addClass('active');
    }

    clearMessageInput() {
        const messageInput = document.getElementById('messageInput');
        if (messageInput) {
            messageInput.value = '';
            messageInput.style.height = 'auto';
        }
    }

    // hideReplyTo() {
    //     const replyContainer = document.querySelector('.group-messages-input-area');
    //     if (replyContainer) {
    //         replyContainer.style.display = 'none';
    //     }
        
    //     const editMessageId = document.getElementById('grp_edit_message_id');
    //     if (editMessageId) {
    //         editMessageId.value = '';
    //     }
    // }

    showGroupLoading() {
        // Show loading indicator in the main content area
        const mainContainer = document.querySelector('#messagesContainer');
        if (mainContainer) {
            mainContainer.innerHTML = '<div class="text-center p-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
        }
    }

    loadGroupFiles() {
        // Load files for the new group
        // This can be implemented based on your file loading logic
        // console.log('Loading files for group:', this.groupUniqueId);
    }

    toggleGroupSearch() {
        // Toggle group search functionality
        const searchContainer = document.querySelector('.group-search-container');
        if (searchContainer) {
            searchContainer.style.display = searchContainer.style.display === 'none' ? 'block' : 'none';
        }
    }

    openFilesPanel() {
        // Open files panel for the group
        // console.log('Opening files panel for group:', this.groupUniqueId);
    }

    toggleOptionsMenu() {
        // Toggle options menu
        const optionsMenu = document.querySelector('.options-menu');
        if (optionsMenu) {
            optionsMenu.style.display = optionsMenu.style.display === 'none' ? 'block' : 'none';
        }
    }

    hideOptionsMenu() {
        // Hide options menu
        const optionsMenu = document.querySelector('.options-menu');
        if (optionsMenu) {
            optionsMenu.style.display = 'none';
        }
    }

    handleGroupInfoButton() {
        // Handle group info button click
        // console.log('Group info button clicked for group:', this.groupUniqueId);
    }

    handleGroupSearch(searchTerm) {
        // Handle group search functionality
        // console.log('Searching in group:', this.groupUniqueId, 'Term:', searchTerm);
    }

    // Remove file by name from array
    removeFileByName(fileArray, fileName) {
        return fileArray.filter(file => file.name !== fileName);
    }

    // Handle file upload progress
    handleFileUploadProgress(fileIndex, progress) {
        const progressBar = document.querySelector(`.file-preview[data-index="${fileIndex}"] .progress-bar`);
        if (progressBar) {
            progressBar.style.width = progress + '%';
        }
    }

    // Validate file type
    validateFileType(file) {
        const allowedTypes = [
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 'image/svg+xml',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/pdf', 'text/plain', 'audio/mpeg', 'video/mp4', 'video/mpeg'
        ];
        
        return allowedTypes.includes(file.type);
    }

    // Validate file size (default 10MB)
    validateFileSize(file, maxSize = 10 * 1024 * 1024) {
        return file.size <= maxSize;
    }

    // Get file icon based on type
    getFileIcon(file) {
        if (file.type.startsWith('image/')) {
            return '🖼️';
        } else if (file.type === 'application/pdf') {
            return '📄';
        } else if (file.type.includes('excel') || file.type.includes('spreadsheet')) {
            return '📊';
        } else if (file.type.includes('word') || file.type.includes('document')) {
            return '📝';
        } else if (file.type.startsWith('audio/')) {
            return '🎵';
        } else if (file.type.startsWith('video/')) {
            return '🎬';
        } else if (file.type === 'text/plain') {
            return '📄';
        } else {
            return '📁';
        }
    }

    // Format file size
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Show file upload error
    showFileUploadError(message, file = null) {
        let errorMessage = message;
        if (file) {
            errorMessage += `: ${file.name}`;
        }
        this.showToast(errorMessage, 'error');
    }

    // Handle file validation before upload
    validateFilesForUpload(files) {
        const maxFileSize = 10 * 1024 * 1024; // 10MB
        const maxFiles = 6;
        
        if (files.length > maxFiles) {
            this.showToast(`Maximum ${maxFiles} files allowed`, 'error');
            return false;
        }

        for (let file of files) {
            if (!this.validateFileType(file)) {
                this.showFileUploadError('Invalid file type', file);
                return false;
            }
            
            if (!this.validateFileSize(file, maxFileSize)) {
                this.showFileUploadError(`File size exceeds ${this.formatFileSize(maxFileSize)}`, file);
                return false;
            }
        }
        
        return true;
    }

    // Enhanced upload files function with validation
    // File upload is now handled by FileUploadManager in chat-container.blade.php
    // The old uploadFiles method has been removed

    // Initialize FileUploadManager for message attachments
    initializeFileUploadManager() {
        // Check if FileUploadManager is available
        if (typeof FileUploadManager === 'undefined') {
            console.warn('FileUploadManager not found. Make sure custom-file-upload.js is loaded.');
            return;
        }
        // Initialize the file upload manager for message attachments
        this.messageUploader = new FileUploadManager('#uploadMediaFiles', {
            maxFileSize: 10 * 1024 * 1024, // 10MB
            maxFiles: 5, // Maximum 5 files
            allowedTypes: [
                'image/jpeg', 
                'image/png', 
                'image/gif',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/plain',
                'audio/mpeg',
                'video/mp4',
                'video/mpeg'
            ],
            onFileAdded: (fileData) => {
                console.log('File added:', fileData.name);
            },
            onFileRemoved: (fileData) => {
                console.log('File removed:', fileData.name);
            },
            onError: (message) => {
                // Custom error handling
                if (typeof this.showToast !== 'undefined') {
                    this.showToast(message, 'error');
                } else if (typeof showNotification !== 'undefined') {
                    showNotification(message, 'error');
                } else {
                    errorMessage(message);
                }
            }
        });
        
        // Initialize the uploader
        if (this.messageUploader && typeof this.messageUploader.init === 'function') {
            this.messageUploader.init();
        }

        // Bind file upload form submission
        $(document).on('submit', '#file-upload-form', (e) => {
            e.preventDefault();
            this.handleFileUploadFormSubmit();
        });

        // Clear file input when modal is closed
        $('#uploadModal').on('hidden.bs.modal', () => {
            // Reset the file uploader
            if (this.messageUploader && typeof this.messageUploader.reset === 'function') {
                this.messageUploader.reset();
            }
            
            // Clear the filesToUpload array
            this.filesToUpload = [];
        });

        // Handle close modal button clicks
        $(document).on('click', '#closemodal, .close-upload-modal', () => {
            this.resetFilePreview();
        });

        // Handle file preview remove button (for compatibility with old system)
        $(document).on('click', '.preview-remove-btn', (e) => {
            const index = $(e.currentTarget).closest('.file-preview').data('index');
            this.removeFilePreview(index);
        });
    }

    // Bind scroll event for infinite scroll
    bindScrollEvent() {
        const messagesContainer = $('#messagesContainer');
        if (messagesContainer.length > 0) {
            messagesContainer.off('scroll').on('scroll', (e) => {
                this.handleScrollForOlderMessages(e);
            });
            console.log('Scroll event bound to messages container');
        } else {
            console.log('Messages container not found, retrying in 1 second...');
            setTimeout(() => this.bindScrollEvent(), 1000);
        }
    }

    // Infinite Scroll Methods
    handleScrollForOlderMessages(event) {
        const container = event.target;
        const scrollTop = container.scrollTop;
       
        // Check if we're near the top and should load older messages
        if (scrollTop <= this.scrollThreshold && 
            !this.isLoadingOlderMessages && 
            this.hasMoreMessages && 
            this.firstMessageId) {
            this.loadOlderMessages();
        }
    }

    loadOlderMessages() {
        if (this.isLoadingOlderMessages || !this.hasMoreMessages || !this.firstMessageId) {
            return;
        }
        this.isLoadingOlderMessages = true;
        this.showLoadingIndicator();

        $.ajax({
            url: BASEURL+`/group-message/fetch-older-messages`,
            type: 'POST',
            data: {
                _token: this.csrfToken,
                group_id: this.groupId,
                first_msg_id: this.firstMessageId,
                user_id: this.currentUserId
            },
            success: (response) => {
                if (response.status) {
                    // Store current scroll position
                    const container = $('#messagesContainer')[0];
                    const currentScrollTop = container.scrollTop;
                    const currentScrollHeight = container.scrollHeight;

                    // Prepend older messages to the container
                    $('#messagesContainer').prepend(response.contents);

                    // Update first message ID for next fetch
                    this.firstMessageId = response.first_msg_id;
                    this.hasMoreMessages = response.has_more_messages;
                    this.hasPreviousMessages = response.has_more_messages;
                    // Adjust scroll position to maintain user's view
                    // setTimeout(() => {
                    //     const newScrollHeight = container.scrollHeight;
                    //     const scrollDifference = newScrollHeight - currentScrollHeight;
                    //     container.scrollTop = currentScrollTop + scrollDifference;
                    // }, 100);

                    // console.log(`Loaded ${response.message_count} older messages. Has more: ${this.hasMoreMessages}`);
                } else {
                    console.error('Failed to load older messages:', response.message);
                    this.hasMoreMessages = false;
                }
            },
            error: (xhr, status, error) => {
                console.error('Error loading older messages:', error);
                this.hasMoreMessages = false;
            },
            complete: () => {
                this.isLoadingOlderMessages = false;
                this.hideLoadingIndicator();
            }
        });
    }

    showLoadingIndicator() {
        $('#loadingIndicator').show();
    }

    hideLoadingIndicator() {
        $('#loadingIndicator').hide();
    }

    // Debug method to test scroll event binding
    testScrollEventBinding() {
        console.log('Testing scroll event binding...');
        const container = $('#messagesContainer');
        console.log('Messages container found:', container.length > 0);
        if (container.length > 0) {
            console.log('Container properties:', {
                height: container.height(),
                scrollHeight: container[0].scrollHeight,
                clientHeight: container[0].clientHeight,
                overflow: container.css('overflow'),
                overflowY: container.css('overflow-y')
            });
        }
        this.bindScrollEvent();
    }

    // Filter groups in sidebar
    filterGroups(searchTerm) {
        // Implement group filtering logic here
        console.log('Filtering groups with:', searchTerm);
        
        // Get all group items in the sidebar
        const groupItems = document.querySelectorAll('.group-messages-group-item');
        const searchTermLower = searchTerm.toLowerCase().trim();
        
        // If search term is empty, show all groups
        if (!searchTermLower) {
            groupItems.forEach(item => {
                item.style.display = 'flex';
                item.classList.remove('search-hidden');
            });
            return;
        }
        
        // Filter groups based on search term
        groupItems.forEach(item => {
            const groupName = item.getAttribute('data-group-name') || '';
            const groupNameLower = groupName.toLowerCase();
            
            // Check if group name contains search term
            if (groupNameLower.includes(searchTermLower)) {
                item.style.display = 'flex';
                item.classList.remove('search-hidden');
                
                // Highlight the matching text in the group name
                this.highlightSearchTerm(item, searchTerm);
            } else {
                item.style.display = 'none';
                item.classList.add('search-hidden');
            }
        });
        // Show "no results" message if no groups match
        this.showNoSearchResults(searchTermLower, groupItems);
    }

    // Highlight search term in group names
    highlightSearchTerm(groupItem, searchTerm) {
        const groupNameElement = groupItem.querySelector('.group-messages-group-name');
        if (!groupNameElement) return;
        
        const originalText = groupNameElement.getAttribute('data-original-text') || groupNameElement.textContent;
        
        // Store original text if not already stored
        if (!groupNameElement.getAttribute('data-original-text')) {
            groupNameElement.setAttribute('data-original-text', originalText);
        }
        
        // Create highlighted version
        const regex = new RegExp(`(${searchTerm})`, 'gi');
        const highlightedText = originalText.replace(regex, '<mark class="search-highlight">$1</mark>');
        
        groupNameElement.innerHTML = highlightedText;
    }

    // Remove search highlighting
    removeSearchHighlighting() {
        const groupItems = document.querySelectorAll('.group-messages-group-item');
        groupItems.forEach(item => {
            const groupNameElement = item.querySelector('.group-messages-group-name');
            if (groupNameElement && groupNameElement.getAttribute('data-original-text')) {
                groupNameElement.textContent = groupNameElement.getAttribute('data-original-text');
                groupNameElement.removeAttribute('data-original-text');
            }
        });
    }

    // Show "no results" message
    showNoSearchResults(searchTerm, groupItems) {
        const groupsList = document.getElementById('groupsList');
        const noResultsElement = groupsList.querySelector('.no-search-results');
        
        // Count visible groups
        const visibleGroups = Array.from(groupItems).filter(item => 
            !item.classList.contains('search-hidden')
        );
        
        if (visibleGroups.length === 0 && searchTerm) {
            // Show no results message
            if (!noResultsElement) {
                const noResultsHtml = `
                    <div class="no-search-results">
                        <div class="no-results-icon">
                            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                                <line x1="8.5" y1="8.5" x2="15.5" y2="15.5"></line>
                            </svg>
                        </div>
                        <h3 class="no-results-title">No Groups Found</h3>
                        <p class="no-results-description">
                            No groups found matching "<strong>${searchTerm}</strong>"
                        </p>
                        <button class="clear-search-btn" onclick="clearGroupSearch()">
                            Clear Search
                        </button>
                    </div>
                `;
                groupsList.insertAdjacentHTML('beforeend', noResultsHtml);
            }
        } else {
            // Remove no results message if it exists
            if (noResultsElement) {
                noResultsElement.remove();
            }
        }
    }

    // Clear search and show all groups
    clearSearch() {
        const searchInput = document.getElementById('sidebarSearch');
        if (searchInput) {
            searchInput.value = '';
        }
        
        this.removeSearchHighlighting();
        this.filterGroups('');
        
        // Focus back to search input
        if (searchInput) {
            searchInput.focus();
        }
    }

    // Enhanced search with debouncing
    initializeSearchWithDebouncing() {
        const searchInput = document.getElementById('sidebarSearch');
        if (!searchInput) return;
        
        let searchTimeout;
        
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value;
            
            // Clear previous timeout
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }
            
            // Set new timeout for debounced search
            if (searchTerm.trim()) {
                searchTimeout = setTimeout(() => {
                    this.filterGroups(searchTerm);
                }, 300); // 300ms delay
            } else {
                // If search is empty, clear immediately
                this.filterGroups('');
            }
        });
        
        // Handle Enter key to perform immediate search
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                }
                this.filterGroups(e.target.value);
            }
        });
        
        // Handle Escape key to clear search
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.clearSearch();
            }
        });
        
        // Clear search when input loses focus and is empty
        searchInput.addEventListener('blur', (e) => {
            if (!e.target.value.trim()) {
                this.clearSearch();
            }
        });
    }
}

// Global function to remove group reaction - Referenced in HTML
window.removeGroupReaction = function(element) {
    if (window.groupMessagesManager) {
        window.groupMessagesManager.handleRemoveReaction({ 
            preventDefault: () => {}, 
            currentTarget: element 
        });
    }
};

// Global function to copy group message - Referenced in HTML
window.groupCopyMessage = function(messageId) {
    if (window.groupMessagesManager) {
        window.groupMessagesManager.handleCopyMessage({ 
            preventDefault: () => {}, 
            currentTarget: $(`#${messageId}`) 
        });
    }
};

// Global function to delete group message - Referenced in HTML
window.deleteGroupMessage = function(e) {
    if (window.groupMessagesManager) {
        var uniqueId = $(e).attr('data-message-id');
        window.groupMessagesManager.handleDeleteMessage({ 
            preventDefault: () => {}, 
            currentTarget: $("#message-"+uniqueId) 
        });
    }
};
// Global function to delete group message - Referenced in HTML
window.deleteGroupMessageForAll = function(e) {
    if (window.groupMessagesManager) {
        var uniqueId = $(e).attr('data-message-id');
        window.groupMessagesManager.handleDeleteMessageForAll({ 
            preventDefault: () => {}, 
            currentTarget: $("#message-"+uniqueId) 
        });
    }
};



// Global function to edit group message - Referenced in HTML
window.editGroupMessage = function(uniqueId) {
    if (window.groupMessagesManager) {
        window.groupMessagesManager.handleEditMessage({ 
            preventDefault: () => {}, 
            currentTarget: $(`#message-${uniqueId}`) 
        });
    }
};

// Global function to reply to group message - Referenced in HTML
window.groupReplyTo = function(element, groupId, messageId) {
    if (window.groupMessagesManager) {
        const messageElement = $(element).closest('.group-messages-message');
        const messageText = messageElement.find('.group-messages-chat-message').text().trim();
        
        // Set data attributes for the reply function
        $(element).data('message-id', messageId);
        $(element).data('group-id', groupId);
        $(element).data('message-text', messageText);
        
        window.groupMessagesManager.handleReply({ 
            preventDefault: () => {}, 
            currentTarget: element 
        });
    }
};

// Global function to close reply - Similar to individual-chat.js
window.closeGroupReplyTo = function() {
    if (window.groupMessagesManager) {
        window.groupMessagesManager.hideReplyTo();
    }
};

// Global function to scroll to group message - Referenced in HTML
window.scrollToGrpMessage = function(element, groupId, messageId) {
    if (window.groupMessagesManager) {
        const targetMessage = $(`#message-${messageId}`);
        if (targetMessage.length > 0) {
            targetMessage[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            targetMessage.addClass('group-messages-message-highlight');
            setTimeout(() => {
                targetMessage.removeClass('group-messages-message-highlight');
            }, 2000);
        }
    }
};

// Global function to load group messages
window.loadGroupMessages = function(groupUniqueId, lastMessageId = 0) {
    if (window.groupMessagesManager) {
        groupMessagesManager.loadGroupMessages(groupUniqueId, lastMessageId);
    } else {
        console.error('GroupMessagesManager not initialized');
    }
};



    // Global function to focus message input - Referenced in HTML
    window.focusMessageInput = function() {
    if (window.groupMessagesManager) {
        const messageInput = document.getElementById('messageInput');
        if (messageInput) {
            messageInput.focus();
            messageInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
};

// Global function to show group info - Referenced in HTML
window.showGroupInfo = function() {
    if (window.groupMessagesManager) {
        window.groupMessagesManager.handleGroupInfoButton();
    }
};

// Global function to switch to a different group - Referenced in HTML
window.switchToGroup = function(groupUniqueId, groupId) {
    if (window.groupMessagesManager) {
        window.groupMessagesManager.switchToGroup(groupUniqueId, groupId);
    } else {
        console.error('GroupMessagesManager not initialized');
    }
};

// Global function to stop typing - Referenced in HTML
window.stopGroupTyping = function() {
    if (groupMessagesManager) {
        groupMessagesManager.stopTyping();
    } else {
        console.error('GroupMessagesManager not initialized');
    }
};

// Global function to handle file upload
window.handleGroupFileUpload = function() {
    if (groupMessagesManager) {
        groupMessagesManager.openFileUploadModal();
    }
};

// Global function to remove file from preview
window.removeGroupFilePreview = function(index) {
    if (groupMessagesManager) {
        groupMessagesManager.removeFilePreview(index);
    }
};

// Global function to clear file previews
window.clearGroupFilePreviews = function() {
    if (groupMessagesManager) {
        groupMessagesManager.resetFilePreview();
    }
};

// Global function to clear search
window.clearGroupSearch = function() {
    if (groupMessagesManager) {
        groupMessagesManager.clearSearch();
    }
};

// Initialize when DOM is ready
$(document).ready(function() {
    // Initialize the group messages manager
    window.groupMessagesManager = new GroupMessagesManager();
    
    // Additional initialization for file handling
    if (window.groupMessagesManager) {
        // Initialize emoji picker for modal if available
        if (typeof EmojiPicker !== 'undefined') {
            new EmojiPicker(".message-emoji-icon-modal", {
                targetElement: "#messagenew"
            });
        }
    }
});

// Handle typing stop when page is unloaded
$(window).on('beforeunload', function() {
    if (window.groupMessagesManager) {
        window.groupMessagesManager.stopTyping();
    }
});

// Handle typing stop when page is hidden
$(document).on('visibilitychange', function() {
    if (document.hidden && window.groupMessagesManager) {
        window.groupMessagesManager.stopTyping();
    }
});

// Handle reply cancel button click
$(document).on('click', '.group-messages-reply-cancel', function() {
    if (window.groupMessagesManager) {
        window.groupMessagesManager.cancelReply();
    }
});

// Export for global access
if (typeof module !== 'undefined' && module.exports) {
    module.exports = GroupMessagesManager;
}

