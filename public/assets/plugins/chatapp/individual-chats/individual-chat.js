/**
 * Individual Chat JavaScript Functionality
 * Handles emoji picker, file uploads, drag & drop, and message sending
 * Uses Laravel Echo with Pusher for real-time typing indicators
 */

class IndividualChatManager {
    constructor() {
        this.filesToUpload = [];
        this.typingTimer = null;
        this.typingDelay = 1500; // Match the delay from chatapp.js
        this.isTyping = false;
        this.chatId = null;
        this.chatUniqueId = null;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.currentUserId = this.getCurrentUserId();
        this.currentUserName = this.getCurrentUserName();
        this.presenceChannel = null;
        this.channelReady = false;
        
        // Global message ID variables accessible from any function
        this.lastMessageId = null;
        this.firstMessageId = null;
        
        this.init();
    }

    init() {
        this.chatId = document.getElementById('get_chat_id')?.value;
        this.chatUniqueId = document.getElementById('get_chat_unique_id')?.value;
        this.bindEvents();
        this.initializeEmojiPicker();
        // this.initializeFileUpload();
        this.initializeDragAndDrop();
        this.initializePresenceChannel(); // Initialize WebSocket connection
        this.initializeSocketForChat(this.chatId);
        // Initialize message IDs when chat loads
        this.initializeMessageIds();
        // this.addMessageOptionsStyles();
        // this.initializeFilePanel();
        
        // Bind chat-specific events
        this.rebindChatEvents();
        
        // Ensure options menu is in correct initial state
        this.initializeOptionsMenu();
        setTimeout(() => {
            this.scrollToBottom();
        }, 1000);       
        // this.scrollToBottom();
        this.bindHeaderButtonEvents();
    }

    // Initialize options menu to ensure proper state
    initializeOptionsMenu() {
        const optionsMenu = document.getElementById('optionsMenu');
        if (optionsMenu) {
            // Ensure menu starts in closed state
            optionsMenu.classList.remove('active');
            // Reset any inline styles
            optionsMenu.style.opacity = '';
            optionsMenu.style.transform = '';
            optionsMenu.style.pointerEvents = '';
            optionsMenu.style.visibility = '';
            console.log('Options menu initialized in closed state');
        }
        
        // Handle page visibility changes
        this.handlePageVisibilityChange();
        
        // Set up periodic state checking to catch race conditions
        setInterval(() => {
            this.handleMenuRaceCondition();
        }, 1000); // Check every second
    }

    // Handle page visibility changes to manage menu state
    handlePageVisibilityChange() {
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                // Page became visible again, ensure menu is closed
                const optionsMenu = document.getElementById('optionsMenu');
                if (optionsMenu && optionsMenu.classList.contains('active')) {
                    optionsMenu.classList.remove('active');
                    console.log('Options menu closed due to page visibility change');
                }
            }
        });
        
        // Also handle page focus/blur events
        window.addEventListener('focus', () => {
            const optionsMenu = document.getElementById('optionsMenu');
            if (optionsMenu && optionsMenu.classList.contains('active')) {
                optionsMenu.classList.remove('active');
                console.log('Options menu closed due to window focus');
            }
        });
    }

    bindEvents() {
        // Send message button
        $(document).on('click', '#sendBtn', () => {
            this.sendMessage();
        });

        // Enter key in message input
        $(document).on('keypress', '#messageInput', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // Escape key to cancel edit mode
        $(document).on('keydown', '#messageInput', (e) => {
            if (e.key === 'Escape') {
                const editMessageIdElement = document.getElementById('edit_message_id');
                if (editMessageIdElement && editMessageIdElement.value) {
                    e.preventDefault();
                    this.cancelEditMode();
                }
            }
            const messageInputElement = document.getElementById('messageInput');
            if (messageInputElement && messageInputElement.value == ''){
                const editMessageIdElement = document.getElementById('edit_message_id');
                if (editMessageIdElement && editMessageIdElement.value) {    
                    this.clearEditMode();
                }
            }
        });
        $(document).on('blur', '#messageInput', (e) => {
            const messageInputElement = document.getElementById('messageInput');
            if (messageInputElement && messageInputElement.value == ''){
                const editMessageIdElement = document.getElementById('edit_message_id');
                if (editMessageIdElement && editMessageIdElement.value) {    
                    this.clearEditMode();
                }
            }
        });
        // Attachment button
        $(document).on('click', '#attachBtn', () => {
            this.openFileUploadModal();
        });

        // Initialize FileUploadManager for message attachments
        this.initializeFileUploadManager();
        
        // Debug: Check if FileUploadManager is available
        if (typeof FileUploadManager !== 'undefined') {
            console.log('FileUploadManager class is available');
        } else {
            console.warn('FileUploadManager class is NOT available - check if custom-file-upload.js is loaded');
        }

        // Add paste event listener to main message input for FileUploadManager integration
        this.setupPasteEventListeners();

        // File input change - handle both main chat and modal uploads
        $(document).on('change', '#attachment', (e) => {
            this.handleFileSelection(e.target.files);
        });

        // Modal upload form submission - redirect to main chat system
        $(document).on('submit', '#file-upload-form', (e) => {
            e.preventDefault();
            // Get the message from modal
            const modalMessageElement = document.getElementById('messagenew');
            const replyToElement = document.getElementById('reply_to_id');
            const messageInputElement = document.getElementById('messageInput');
            
            
            if (modalMessageElement && messageInputElement) {
                const modalMessage = modalMessageElement.value;
                const replyTo = replyToElement ? replyToElement.value : '';
                
                // Set the message in main chat input
                messageInputElement.value = modalMessage;
                  if (!modalMessage && this.filesToUpload.length === 0) {
            this.showToast("Files or message required to upload", 'error');
            return false;
        }
                // Close modal and send via main chat system
                $('#uploadModal').modal('hide');
                
                // Send message with files if any
                if (modalMessage.trim() || this.filesToUpload.length > 0) {
                    this.sendMessage();
                }
            }
        });

        // Clear files when modal is closed
        $(document).on('hidden.bs.modal', '#uploadModal', () => {
            this.clearFileUploads();
            // Reset modal form
            const fileUploadForm = document.getElementById('file-upload-form');
            const messagenewElement = document.getElementById('messagenew');
            
            if (fileUploadForm) {
                fileUploadForm.reset();
            }
            if (messagenewElement) {
                messagenewElement.value = '';
            }
        });

        // File panel events
        $(document).on('click', '.CdsIndividualChat-files-section-header', () => {
            this.toggleFilesSection();
        });

        

        $(document).on('click', '#clearFileSearch', () => {
            this.clearFileSearch();
        });

        // Close message options dropdown when clicking outside
        $(document).on('click', (e) => {
            if (!$(e.target).closest('.message-options-dropdown').length) {
                this.closeAllMessageOptions();
            }
        });

        $(document).on('keyup', '#search-file-input', (e) => {
            if (e.key === 'Enter') {
                this.searchFiles();
            }
        });

        $(document).on('click', '#loadMoreFiles', () => {
            this.loadMoreFiles();
        });

        // Scroll to bottom
        $(document).on('click', '#scrollToBottom', () => {
            this.scrollToBottom();
        });

        // Reply functionality
        $(document).on('click', '.reply-to-message', (e) => {
            const messageId = $(e.currentTarget).data('message-id');
            const messageText = $(e.currentTarget).data('message-text');
            this.setReplyTo(messageId, messageText);
        });

        // Sidebar user click - handle chat switching
        $(document).on('click', '.chat-user-item', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            // Try multiple ways to get the chat ID
            let chatUniqueId = $(e.currentTarget).data('chat-unique-id');
            let chatId = $(e.currentTarget).data('chat-id');
            // If still no chat ID, try to get from href or other attributes
            if (!chatUniqueId) {
                const href = $(e.currentTarget).attr('href');
                if (href && href.includes('/')) {
                    chatUniqueId = href.split('/').pop();
                }
            }
            
            // console.log('Sidebar click detected:', {
            //     element: e.currentTarget,
            //     chatUniqueId: chatUniqueId,
            //     dataAttributes: $(e.currentTarget).data()
            // });
            
            if (chatUniqueId) {
                this.switchToChat(chatUniqueId,chatId);
            } else {
                console.warn('No chat ID found for clicked element');
            }
        });

        // Message options dropdown events
        $(document).on('click', '.message-options-dropdown .message-options-toggle', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.toggleMessageOptions(e.currentTarget);
        });

        // Copy message
        $(document).on('click', '.copy-message', (e) => {
            e.preventDefault();
            const messageId = $(e.currentTarget).data('message-id');
            this.copyMessage(messageId);
        });

        // Reply to message
        $(document).on('click', '.reply-to-message-option', (e) => {
            e.preventDefault();
            const messageId = $(e.currentTarget).data('message-id');
            const messageText = $(e.currentTarget).data('message-text');
            this.setReplyTo(messageId, messageText);
        });

        // Edit message
        $(document).on('click', '.edit-message', (e) => {
            e.preventDefault();
            const messageId = $(e.currentTarget).data('message-id');
            const messageText = $(e.currentTarget).data('message-text');
            this.editMessage(messageId, messageText);
        });

        // Delete message for me
        $(document).on('click', '.delete-message-for-me', (e) => {
            e.preventDefault();
            const messageId = $(e.currentTarget).data('message-id');
            const messageUniqueId = $(e.currentTarget).data('message-unique-id');
            this.deleteMessageForMe(messageId, messageUniqueId);
        });

        // Delete message for everyone
        $(document).on('click', '.delete-message-for-everyone', (e) => {
            e.preventDefault();
            const messageId = $(e.currentTarget).data('message-id');
            const messageUniqueId = $(e.currentTarget).data('message-unique-id');
            this.deleteMessageForEveryone(messageId, messageUniqueId);
        });

        // Close dropdown when clicking outside
        $(document).on('click', (e) => {
            if (!$(e.target).closest('.message-options-dropdown').length) {
                this.closeAllMessageOptions();
            }
        });

        // Typing indicator - handle input events
        $(document).on('input', '#messageInput', () => {
            this.handleTyping();
        });

        // Auto-resize textarea
        $(document).on('input', '#messageInput', () => {
            this.autoResizeTextarea();
        });

        // Handle blur event to stop typing
        $(document).on('blur', '#messageInput', () => {
            this.stopTyping();
        });
    }

    initializeEmojiPicker() {
        console.log('Initializing emoji picker...');
        
        // Initialize emoji picker for main input
        if (typeof EmojiPicker !== 'undefined') {
            console.log('EmojiPicker is defined, initializing main input picker...');
            new EmojiPicker("#emojiBtn", {
                targetElement: "#messageInput"
            });
        } else {
            console.error('EmojiPicker is not defined!');
        }

        // Initialize emoji picker for modal
        if (typeof EmojiPicker !== 'undefined') {
            console.log('Initializing modal emoji picker...');
            new EmojiPicker(".message-emoji-icon-modal", {
                targetElement: "#messagenew"
            });
        }

        // Initialize emoji picker for message reactions
        if(typeof EmojiPicker !== 'undefined'){
            const reactionElements = $("#messagesContainer .message-reaction");
            reactionElements.each(function () {
                var ele_id = $(this).attr("id");
                var message_id = $(this).data("message-id"); // Use data-message-id for individual chat
                var e = $(this);

                console.log('Processing reaction element:', { ele_id, message_id });

                if (ele_id && message_id) {
                    console.log('Initializing emoji picker for:', ele_id);
                    new EmojiPicker("#messagesContainer #" + ele_id, {
                        onEmojiSelect: (selectedEmoji) => {
                            // console.log('Emoji selected:', selectedEmoji, 'for message:', message_id);
                            $.ajax({
                                url: BASEURL + "/individual-chats/add-reaction",
                                type: "POST",
                                data: {
                                    _token: csrf_token,
                                    message_id: message_id,
                                    reaction: selectedEmoji,
                                },
                                success: function (response) {
                                    if (response.status) {
                                        console.log('Reaction added successfully');
                                    } else {
                                        console.error('Failed to add reaction:', response.message);
                                    }
                                },
                                error: function (xhr, status, error) {
                                    console.error("Error adding reaction:", error);
                                },
                            });
                        },
                    });
                } else {
                    console.warn('Missing ele_id or message_id:', { ele_id, message_id });
                }
            });
        } else {
            console.error('EmojiPicker is not defined for message reactions!');
        }
    }
    removeIndividualChatReaction(e){
        var message_id = $(e).data("message-id");
        $.ajax({
            type: "post",
            url: BASEURL + "/individual-chats/remove-reaction",
            data: {
                _token: this.csrfToken,
                message_id: message_id,
            },
            success: function (response) {
                if (response.status) {
                    $(e).remove();
                    console.log('Reaction removed successfully');
                } else {
                    console.error('Failed to remove reaction:', response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error removing reaction:", error);
            }
        });
    }
    initializeFileUpload() {
        // File input change handler
        const fileInput = document.getElementById('attachment');
        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                this.handleFileSelection(e.target.files);
            });
        }
    }

    initializeDragAndDrop() {
        const dropZone = document.getElementById('filePreviewContainer');
        if (dropZone) {
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropZone.style.borderColor = '#2563eb';
                dropZone.style.backgroundColor = '#eff6ff';
            });

            dropZone.addEventListener('dragleave', (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropZone.style.borderColor = '#d1d5db';
                dropZone.style.backgroundColor = '#f9fafb';
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropZone.style.borderColor = '#d1d5db';
                dropZone.style.backgroundColor = '#f9fafb';

                const files = e.dataTransfer.files;
                this.handleFileSelection(files);
            });
        }
    }

    handleFileSelection(files) {
        if (!files || files.length === 0) return;

        Array.from(files).forEach((file, index) => {
            this.filesToUpload.push(file);
            const previewDiv = this.createFilePreview(file, this.filesToUpload.length - 1);
            document.getElementById('filePreviewContainer').appendChild(previewDiv);
        });

        this.updateFileName();
    }

    createFilePreview(file, index) {
        const previewDiv = document.createElement('div');
        previewDiv.classList.add('file-preview');
        previewDiv.dataset.index = index;

        const icon = document.createElement('div');
        icon.style.cssText = `
            width: 40px; height: 40px; flex-shrink: 0; display: flex; 
            align-items: center; justify-content: center; overflow: hidden; 
            border-radius: 4px; background: #eee;
        `;

        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
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

        const fileNameSpan = document.createElement('span');
        fileNameSpan.textContent = file.name;
        fileNameSpan.style.flexGrow = '1';

        const closeBtn = document.createElement('span');
        closeBtn.textContent = '✖';
        closeBtn.classList.add('preview-remove-btn');
        closeBtn.addEventListener('click', () => {
            this.removeFilePreview(index);
        });

        previewDiv.appendChild(icon);
        previewDiv.appendChild(fileNameSpan);
        previewDiv.appendChild(closeBtn);

        return previewDiv;
    }

    removeFilePreview(index) {
        const previewDiv = document.querySelector(`.file-preview[data-index="${index}"]`);
        if (previewDiv) {
            previewDiv.remove();
        }

        this.filesToUpload.splice(index, 1);
        this.updateFilePreviews();
        this.updateFileName();
    }

    updateFilePreviews() {
        const allPreviews = document.querySelectorAll('.file-preview');
        allPreviews.forEach((preview, idx) => {
            preview.dataset.index = idx;
        });
    }

    updateFileName() {
        const fileNameDiv = document.getElementById('fileName');
        if (fileNameDiv) {
            if (this.filesToUpload.length > 0) {
                fileNameDiv.textContent = `${this.filesToUpload.length} file(s) selected`;
            } else {
                fileNameDiv.textContent = '';
            }
        }
    }

    openFileUploadModal() {
        $('#uploadModal').modal('show');
        
        // Ensure FileUploadManager is initialized after modal is shown
        setTimeout(() => {
            if (this.messageUploader && typeof this.messageUploader.init === 'function') {
                // Check if already initialized
                if (!this.messageUploader.container) {
                    const initResult = this.messageUploader.init();
                    if (initResult) {
                        console.log('FileUploadManager initialized successfully in modal');
                    } else {
                        console.error('FileUploadManager initialization failed in modal');
                    }
                }
            }
            
            // Check status after initialization attempt
            this.checkFileUploadManagerStatus();
        }, 100); // Small delay to ensure modal is fully rendered
    }



    sendMessage() {
        const messageInputElement = document.getElementById('messageInput');
        const replyToElement = document.getElementById('reply_to_id');
        const editMessageIdElement = document.getElementById('edit_message_id');
        
        if (!messageInputElement) {
            console.warn('Message input element not found');
            return;
        }
        
        const message = messageInputElement.value.trim();
        const replyTo = replyToElement ? replyToElement.value : '';
        const editMessageId = editMessageIdElement ? editMessageIdElement.value : '';
        
        // Check if we have files to upload
        if (this.filesToUpload.length > 0) {
            this.sendMessageWithFiles(message, replyTo);
            return;
        }
        
        // No files, send text message only
        if (!message) return;
        
        if(editMessageId){
            this.updateMessage(editMessageId, message);
        } else {
            this.sendNewMessage(message, replyTo);
        }
    }

    // Send message with files
    sendMessageWithFiles(message, replyTo) {
        const urlElement = document.getElementById('geturl');
        if (!urlElement) {
            console.warn('URL element not found');
            return;
        }
        
        const url = urlElement.value;
        const formData = new FormData();
        formData.append('_token', this.csrfToken);
        formData.append('send_msg', message);
        formData.append('reply_to', replyTo);

        // Add all files
        this.filesToUpload.forEach(file => {
            formData.append('attachment[]', file);
        });

        $("#sendBtn").prop('disabled', true);
        $("#attachBtn").prop('disabled', true);
        $("#messageInput").prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                if (response.status === true) {
                    this.showSuccess(response.message || 'Message with files sent successfully!');
                    
                    this.clearMessageInput();
                    this.hideReplyTo();
                    this.clearFileUploads();
                    // Refresh the file panel to show new files
                    this.refreshChatFiles();
                } else {
                    this.showError(response.message || 'Failed to send message with files');
                }
            },
            error: (xhr, status, error) => {
                this.showError('Failed to send message with files: ' + error);
            },
            complete: () => {
                $("#sendBtn").prop('disabled', false);
                $("#attachBtn").prop('disabled', false);
                $("#messageInput").prop('disabled', false);
            }
        });
    }

    // Send new message (same as reference)
    sendNewMessage(message, replyTo) {
        const urlElement = document.getElementById('geturl');
        const editMessageIdElement = document.getElementById('edit_message_id');
        
        if (!urlElement) {
            console.warn('URL element not found');
            return;
        }
        
        const url = urlElement.value;
        const editMessageId = editMessageIdElement ? editMessageIdElement.value : '';
        const formData = new FormData();
        formData.append('_token', this.csrfToken);
        formData.append('send_msg', message);
        formData.append('edit_message_id', editMessageId);
        formData.append('reply_to', replyTo);

        $("#sendBtn").prop('disabled', true);
        $("#attachBtn").prop('disabled', true);
        $("#messageInput").prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                if (response.status === true) {
                    this.clearMessageInput();
                    this.hideReplyTo();
                    
                } else {
                    this.showError(response.message || 'Failed to send message');
                }
            },
            error: (xhr, status, error) => {
                this.showError('Failed to send message: ' + error);
            },
            complete: () => {
                $("#sendBtn").prop('disabled', false);
                $("#attachBtn").prop('disabled', false);
                $("#messageInput").prop('disabled', false);
            }
        });
    }

    // Clear file uploads
    clearFileUploads() {
        this.filesToUpload = [];
        this.updateFileName();
        this.messageUploader.reset();
        // Clear file preview container if it exists
        const previewContainer = document.getElementById('filePreviewContainer');
        if (previewContainer) {
            previewContainer.innerHTML = '';
        }
    }

    // Upload files function
    uploadFiles(files) {
        const MAX_FILES = 6;
        if (files.length > MAX_FILES) {
            this.showToast(`You can only upload a maximum ${MAX_FILES} files.`, 'error');
            return;
        }
        
        // Validate each file
        const validFiles = [];
        const invalidFiles = [];
        
        Array.from(files).forEach((file, index) => {
            // Check file size
            // if (file.size > this.messageUploader?.options?.maxFileSize || 10 * 1024 * 1024) {
            //     invalidFiles.push({ file, reason: 'File size exceeds 10MB limit' });
            //     return;
            // }
            
            // Check file type
            const allowedTypes = this.messageUploader?.options?.allowedTypes || [
                'image/jpeg', 'image/png', 'image/gif', 'application/pdf',
                'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/plain', 'audio/mpeg', 'video/mp4', 'video/mpeg'
            ];
            
            if (!allowedTypes.includes(file.type)) {
                invalidFiles.push({ file, reason: 'File type not allowed' });
                return;
            }
            
            validFiles.push(file);
        });
        
        // Show errors for invalid files
        if (invalidFiles.length > 0) {
            invalidFiles.forEach(({ file, reason }) => {
                this.showToast(`${file.name}: ${reason}`, 'error');
            });
        }
        
        // Add valid files to upload queue
        if (validFiles.length > 0) {
            validFiles.forEach((file, index) => {
                this.filesToUpload.push(file);
            });
            
            // Update file name display
            this.updateFileName();
            
            // If FileUploadManager is available, add files to it for preview
            if (this.messageUploader && typeof this.messageUploader.handleFiles === 'function') {
                this.messageUploader.handleFiles(validFiles);
            }
            
            console.log('Valid files uploaded:', validFiles.length);
            this.showToast(`${validFiles.length} file(s) added successfully`, 'success');
        }
        
        // Show summary
        if (validFiles.length > 0 && invalidFiles.length > 0) {
            this.showToast(`Added ${validFiles.length} file(s), ${invalidFiles.length} file(s) rejected`, 'warning');
        }
    }

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
                this.filesToUpload.push(fileData.file);
            },
            onFileRemoved: (fileData) => {
                console.log('File removed:', fileData.name);
                // Remove from filesToUpload array
                const index = this.filesToUpload.findIndex(file => file.name === fileData.name);
                if (index > -1) {
                    this.filesToUpload.splice(index, 1);
                }
            },
            onError: (error) => {
                this.showToast(error, 'error');
            }
        });

        // Initialize the FileUploadManager
        if (this.messageUploader && typeof this.messageUploader.init === 'function') {
            const initResult = this.messageUploader.init();
            if (initResult) {
                console.log('FileUploadManager initialized successfully');
            } else {
                console.error('FileUploadManager initialization failed');
            }
        }
    }

    // Show toast message
    showToast(message, type = 'info') {
        // Use existing toast functionality if available, otherwise console log
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else {
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    }

    // Check FileUploadManager status
    checkFileUploadManagerStatus() {
        if (typeof FileUploadManager === 'undefined') {
            console.error('FileUploadManager class is not available');
            return false;
        }
        
        if (!this.messageUploader) {
            console.error('FileUploadManager instance is not created');
            return false;
        }
        
        if (!this.messageUploader.container) {
            console.error('FileUploadManager is not initialized');
            return false;
        }
        
        console.log('FileUploadManager is ready and initialized');
        return true;
    }

    // Setup paste event listeners for FileUploadManager integration
    setupPasteEventListeners() {
        const messageInput = document.getElementById('messageInput');
        if (messageInput) {
            messageInput.addEventListener('paste', (event) => {
                const items = (event.clipboardData || event.originalEvent.clipboardData).items;
                const files = [];
                let hasFiles = false;
                
                for (let item of items) {
                    if (item.kind === "file") {
                        files.push(item.getAsFile());
                        hasFiles = true;
                    }
                }
                
                // If files are found in clipboard, handle them
                if (hasFiles && files.length > 0) {
                    event.preventDefault(); // Prevent default paste behavior for files
                    
                    // Open modal and upload files
                    this.openFileUploadModal();
                    
                    // Small delay to ensure modal is fully open before processing files
                    setTimeout(() => {
                        this.uploadFiles(files);
                    }, 100);
                }
                // If no files, allow normal text paste behavior
            });
        }
        
        // Also add paste listener to modal message input
        const modalMessageInput = document.getElementById('messagenew');
        if (modalMessageInput) {
            modalMessageInput.addEventListener('paste', (event) => {
                const items = (event.clipboardData || event.originalEvent.clipboardData).items;
                const files = [];
                let hasFiles = false;
                
                for (let item of items) {
                    if (item.kind === "file") {
                        files.push(item.getAsFile());
                        hasFiles = true;
                    }
                }
                
                // If files are found in clipboard, handle them
                if (hasFiles && files.length > 0) {
                    event.preventDefault(); // Prevent default paste behavior for files
                    this.uploadFiles(files);
                }
                // If no files, allow normal text paste behavior
            });
        }
    }

    // File Management Methods
    initializeFilePanel() {
        this.currentFilePage = 1;
        this.fileSearchQuery = '';
        // this.loadChatFiles();
    }

    // Load chat files
    loadChatFiles(page = 1, search = '') {
        if (!this.chatId) return;

        const url = BASEURL + '/individual-chats/chat-files/' + this.chatUniqueId;
        const data = {
            _token: this.csrfToken,
            page: page,
            search: search
        };

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: (response) => {
                if (response.status === true) {
                    console.log('hello');
                    console.log(response);
                    if(response.files.length != 0){
                        this.displayChatFiles(response.files,response.attachments, response.hasMorePages);
                    }
                } else {
                    this.showNoFilesMessage();
                }
            },
            error: (xhr, status, error) => {
                console.error('Error loading chat files:', error);
                this.showNoFilesMessage();
            }
        });
    }

    // Refresh chat files (called when new messages are sent)
    refreshChatFiles() {
        this.currentFilePage = 1;
        this.fileSearchQuery = '';
        // this.loadChatFiles();
    }

    // Display chat files
    displayChatFiles(files,attachments, hasMorePages) {
        const container = document.getElementById('attachments-container');
        const noFilesMessage = document.getElementById('noFilesMessage');
        const loadMoreContainer = document.getElementById('loadMoreContainer');

        if (!files || files.length === 0) {
            this.showNoFilesMessage();
            return;
        }

        // Hide loading and no files message
        container.querySelector('.CdsIndividualChat-files-loading').style.display = 'none';
        noFilesMessage.style.display = 'none';

        // Generate files HTML
        let filesHtml = '';
        filesHtml += attachments;
        // files.forEach(file => {
        //     filesHtml += this.generateFileHtml(file);
        // });

        container.innerHTML = filesHtml;

        // Show/hide load more button
        if (hasMorePages) {
            loadMoreContainer.style.display = 'block';
        } else {
            loadMoreContainer.style.display = 'none';
        }
    }

    // Generate file HTML
    generateFileHtml(attachments) {
        // const attachment = file.attachment.split(',');
        let fileHtml = attachments;
        // for(let i = 0; i < attachment.length; i++){
        //     const fileName = attachment[i];
        //     const fileExt = fileName.split('.').pop().toLowerCase();
        //     const fileUrl = this.getFileUrl(fileName);
        //     const previewUrl = this.getFilePreviewUrl(fileName);

        //     fileHtml += `<div class="CdsIndividualChat-file-item" data-file-name="${fileName}">`;

        //     if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExt)) {
        //         // Image files
        //         fileHtml += `
        //             <div class="CdsIndividualChat-file-preview">
        //                 <img src="${previewUrl}" alt="${fileName}" class="CdsIndividualChat-file-image" 
        //                     onclick="window.individualChatManager.previewFile('${fileName}', '${fileUrl}')">
        //             </div>
        //         `;
        //     } else if (fileExt === 'pdf') {
        //         // PDF files
        //         fileHtml += `
        //             <div class="CdsIndividualChat-file-preview">
        //                 <div class="CdsIndividualChat-file-pdf">
        //                     <img src="${BASEURL}/assets/images/chat-icons/pdf-icon.png" alt="PDF" class="CdsIndividualChat-file-icon">
        //                     <span class="CdsIndividualChat-file-ext">PDF</span>
        //                 </div>
        //             </div>
        //         `;
        //     } else if (fileExt === 'mp3') {
        //         // Audio files
        //         fileHtml += `
        //             <div class="CdsIndividualChat-file-preview">
        //                 <div class="CdsIndividualChat-file-audio">
        //                     <img src="${BASEURL}/assets/images/chat-icons/headphone.svg" alt="Audio" class="CdsIndividualChat-file-icon">
        //                     <span class="CdsIndividualChat-file-ext">MP3</span>
        //                 </div>
        //             </div>
        //         `;
        //     } else if (fileExt === 'mp4') {
        //         // Video files
        //         fileHtml += `
        //             <div class="CdsIndividualChat-file-preview">
        //                 <div class="CdsIndividualChat-file-video">
        //                     <img src="${BASEURL}/assets/images/chat-icons/video.svg" alt="Video" class="CdsIndividualChat-file-icon">
        //                     <span class="CdsIndividualChat-file-ext">MP4</span>
        //                 </div>
        //             </div>
        //         `;
        //     } else if (['xls', 'xlsx'].includes(fileExt)) {
        //         // Excel files
        //         fileHtml += `
        //             <div class="CdsIndividualChat-file-preview">
        //                 <div class="CdsIndividualChat-file-excel">
        //                     <img src="${BASEURL}/assets/images/chat-icons/xls.svg" alt="Excel" class="CdsIndividualChat-file-icon">
        //                     <span class="CdsIndividualChat-file-ext">XLS</span>
        //                 </div>
        //             </div>
        //         `;
        //     } else {
        //         // Other files
        //         fileHtml += `
        //             <div class="CdsIndividualChat-file-preview">
        //                 <div class="CdsIndividualChat-file-generic">
        //                     <img src="${BASEURL}/assets/images/chat-icons/file.svg" alt="File" class="CdsIndividualChat-file-icon">
        //                     <span class="CdsIndividualChat-file-ext">${fileExt.toUpperCase()}</span>
        //                 </div>
        //             </div>
        //         `;
        //     }

        //     fileHtml += `
        //         <div class="CdsIndividualChat-file-info">
        //             <div class="CdsIndividualChat-file-name">${fileName}</div>
        //             <div class="CdsIndividualChat-file-actions">
        //                 <button class="CdsIndividualChat-file-preview-btn" onclick="window.individualChatManager.previewFile('${fileName}', '${fileUrl}')">
        //                     <i class="fa-solid fa-eye"></i>
        //                 </button>
        //                 <a href="${fileUrl}" class="CdsIndividualChat-file-download-btn" download>
        //                     <i class="fa-solid fa-download"></i>
        //                 </a>
        //             </div>
        //         </div>
        //     </div>`;
        // }

        return fileHtml;
    }

    // Get file URL
    getFileUrl(fileName) {
        return BASEURL + '/chat-files/' + fileName;
    }

    // Get file preview URL
    getFilePreviewUrl(fileName) {
        return BASEURL + '/chat-files/thumb/' + fileName;
    }

    // Preview file
    previewFile(fileName, fileUrl) {
        const modal = new bootstrap.Modal(document.getElementById('filePreviewModal'));
        const modalTitle = document.getElementById('filePreviewModalLabel');
        const modalContent = document.getElementById('filePreviewContent');
        const downloadBtn = document.getElementById('downloadFileBtn');

        modalTitle.textContent = fileName;
        downloadBtn.href = fileUrl;

        const fileExt = fileName.split('.').pop().toLowerCase();

        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExt)) {
            // Image preview
            modalContent.innerHTML = `<img src="${fileUrl}" class="img-fluid" alt="${fileName}">`;
        } else if (fileExt === 'pdf') {
            // PDF preview
            modalContent.innerHTML = `<iframe src="${fileUrl}" width="100%" height="500px"></iframe>`;
        } else if (fileExt === 'mp3') {
            // Audio preview
            modalContent.innerHTML = `<audio controls class="w-100"><source src="${fileUrl}" type="audio/mpeg">Your browser does not support the audio element.</audio>`;
        } else if (fileExt === 'mp4') {
            // Video preview
            modalContent.innerHTML = `<video controls class="w-100"><source src="${fileUrl}" type="video/mp4">Your browser does not support the video element.</video>`;
        } else {
            // Generic file info
            modalContent.innerHTML = `
                <div class="text-center p-4">
                    <i class="fa-solid fa-file fa-3x text-muted mb-3"></i>
                    <h5>${fileName}</h5>
                    <p class="text-muted">This file type cannot be previewed. Click download to save the file.</p>
                </div>
            `;
        }

        modal.show();
    }

    // Toggle files section
    toggleFilesSection() {
        const searchSection = document.getElementById('filesSearchSection');
        const icon = document.querySelector('.CdsIndividualChat-files-section-header i');
        
        if (searchSection.style.display === 'none') {
            searchSection.style.display = 'block';
            icon.className = 'fa-solid fa-chevron-up';
        } else {
            searchSection.style.display = 'none';
            icon.className = 'fa-solid fa-chevron-down';
        }
    }

    // Toggle file search
    toggleFileSearch() {
        const searchInput = document.getElementById('search-file-input');
        if (searchInput) {
            searchInput.focus();
        }
    }

    // Clear file search
    clearFileSearch() {
        const searchInputElement = document.getElementById('search-file-input');
        if (searchInputElement) {
            searchInputElement.value = '';
        }
        this.fileSearchQuery = '';
        this.currentFilePage = 1;
        // this.loadChatFiles();
    }

    // Search files
    searchFiles() {
        const searchInputElement = document.getElementById('search-file-input');
        if (searchInputElement) {
            const searchQuery = searchInputElement.value.trim();
            this.fileSearchQuery = searchQuery;
            this.currentFilePage = 1;
            // this.loadChatFiles(1, searchQuery);
        }
    }

    // Load more files
    loadMoreFiles() {
        this.currentFilePage++;
        // this.loadChatFiles(this.currentFilePage, this.fileSearchQuery);
    }

    // Show no files message
    showNoFilesMessage() {
        const container = document.getElementById('attachments-container');
        const noFilesMessage = document.getElementById('noFilesMessage');
        const loadMoreContainer = document.getElementById('loadMoreContainer');

        if (container) {
            container.innerHTML = '';
            const loadingElement = container.querySelector('.CdsIndividualChat-files-loading');
            if (loadingElement) {
                loadingElement.style.display = 'none';
            }
        }
        if (noFilesMessage) {
            noFilesMessage.style.display = 'block';
        }
        if (loadMoreContainer) {
            loadMoreContainer.style.display = 'none';
        }
    }

    // Update existing message (same as reference)
    updateMessage(messageId, newMessage) {
        $.ajax({
            url: BASEURL + '/individual-chats/update-message/' + messageId,
            type: 'POST',
            data: {
                _token: this.csrfToken,
                message: newMessage
            },
            beforeSend: () => {
                $("#sendBtn").prop('disabled', true);
                $("#attachBtn").prop('disabled', true);
                $("#messageInput").prop('disabled', true);
            },
            success: (response) => {
                if (response.status === true) {
                    // Clear edit mode
                    this.clearEditMode();
                    
                    // Update message in DOM (same as reference)
                    $(`#cpMsg${messageId}`).html(response.updated_message || newMessage);
                    
                    // Show edited indicator
                    const editedIndicator = document.getElementById(`editedMsg${messageId}`);
                    if (editedIndicator) {
                        editedIndicator.textContent = 'edited';
                    }
                    
                    this.showSuccess('Message updated successfully!');
                } else {
                    this.showError(response.message || 'Failed to update message');
                }
            },
            error: (xhr, status, error) => {
                this.showError('Failed to update message: ' + error);
            },
            complete: () => {
                $("#sendBtn").prop('disabled', false);
                $("#attachBtn").prop('disabled', false);
                $("#messageInput").prop('disabled', false);
            }
        });
    }

    // Show edit mode indicator
    showEditMode() {
        // Add edit mode visual indicator
        const messageInput = document.getElementById('messageInput');
        if (messageInput) {
            messageInput.classList.add('editing');
        }
        
        // Show edit mode info
        // this.showInfo('Editing message - Click "Update Message" to save or type new message to cancel');
    }

    // Clear edit mode
    clearEditMode() {
        // Clear hidden input
        const editMessageIdElement = document.getElementById('edit_message_id');
        if (editMessageIdElement) {
            editMessageIdElement.value = '';
        }
        
        // Reset send button
        // const sendBtn = document.getElementById('sendBtn');
        // sendBtn.textContent = 'Send';
        // sendBtn.classList.remove('editing');
        
        // Clear message input
        const messageInputElement = document.getElementById('messageInput');
        if (messageInputElement) {
            messageInputElement.value = '';
            messageInputElement.classList.remove('editing');
        }
        
        // Focus back to input
        // messageInput.focus();
    }

    // Cancel edit mode
    cancelEditMode() {
        this.clearEditMode();
        this.showInfo('Edit cancelled');
    }

    // Initialize message IDs when chat first loads
    initializeMessageIds() {
        // Get message IDs from the existing JavaScript object in the Blade template
        if (typeof cdsIndividualChatApp !== 'undefined' && cdsIndividualChatApp.elements) {
            this.lastMessageId = cdsIndividualChatApp.elements.lastMessageId || 0;
            this.firstMessageId = cdsIndividualChatApp.elements.firstMessageId || 0;
            
        } else {
            // Fallback: try to get from hidden inputs if available
            const lastMsgInput = document.getElementById('last_message_id');
            const firstMsgInput = document.getElementById('first_message_id');
            
            if (lastMsgInput) {
                this.lastMessageId = parseInt(lastMsgInput.value) || 0;
            }
            if (firstMsgInput) {
                this.firstMessageId = parseInt(firstMsgInput.value) || 0;
            }
            
            console.log('Message IDs initialized from hidden inputs:', {
                lastMessageId: this.lastMessageId,
                firstMessageId: this.firstMessageId
            });
        }
    }

    loadChatSidebar() {
        $.ajax({
            url: BASEURL+'/individual-chats/fetch-sidebar',
            type: 'POST',
            data: {
                _token: this.csrfToken,
                chat_unique_id: this.chatUniqueId
            },
            success: (response) => {
                if (response.status === true) {
                    $("#chatList").html(response.message);
                } else {
                    this.showError(response.message || 'Failed to fetch chat sidebar');
                }
            },
            error: (xhr, status, error) => {
                this.showError('Failed to fetch chat sidebar: ' + error);
            }
        });
    }

    // Function to switch to a different chat
    switchToChat(chatUniqueId,chatId,force = false) {
        if(!force){
            if (!chatUniqueId || chatUniqueId === this.chatUniqueId) {
                console.log('Same chat or invalid ID, not switching');
                return; // Same chat or invalid ID
            }
        }

        // console.log('Switching to chat:', chatUniqueId, 'Chat ID:', chatId);

        // Stop typing and leave previous chat's presence channel
        this.stopTyping();
        this.hideTypingIndicator();
        this.leaveChat();

        // Update the current chat unique ID
        this.chatUniqueId = chatUniqueId;
        this.chatId = chatId;
        // Update hidden input value
        const chatIdInput = document.getElementById('get_chat_unique_id');
        if (chatIdInput) {
            chatIdInput.value = chatUniqueId;
        }

        // Update URL without page reload (optional)
        if (window.history && window.history.pushState) {
            const newUrl = BASEURL+"/individual-chats/chat/"+chatUniqueId;
            window.history.pushState({chatId: chatUniqueId}, '', newUrl);
        }
        $(".chat-user-item").removeClass("active");
        $(`.chat-user-item[data-chat-unique-id="${chatUniqueId}"]`).addClass("active");
        // Reset message IDs for new chat
        this.lastMessageId = 0;
        this.firstMessageId = 0;

        // Initialize socket for the new chat
        this.initializeSocketForChat(chatId);

        // Load messages for the new chat
        this.switchChatWindow(chatUniqueId);

        // Update active state in sidebar
        this.updateSidebarActiveState(chatUniqueId);

        // Clear current message input and reply
        this.clearMessageInput();
        this.hideReplyTo();
        
        // Show loading indicator
        this.showChatLoading();
    }
    switchChatWindow(chatUniqueId) {
        $.ajax({
            url: BASEURL+'/individual-chats/switch-chat/'+chatUniqueId,
            type: 'POST',
            data: {
                _token: this.csrfToken,
                chat_id: chatUniqueId,
                last_msg_id: 0, // Start from beginning for new chat
            },
            success: (response) => {
                if (response.status === true) {
                    // Update message IDs
                    this.lastMessageId = response.last_msg_id || 0;
                    this.firstMessageId = response.first_msg_id || 0;
                    
                    // Update messages container
                    $(".CdsIndividualChat-main").html(response.message);
                    
                    // Reinitialize socket after content is loaded
                    this.initializeSocketForChat(this.chatId);
                    
                    // Re-bind events for new chat elements
                    this.rebindChatEvents();
                    setTimeout(() => {
                        this.initializeFileUploadManager();
                        this.initializeEmojiPicker();
                    }, 1500);
                    if (response.unread_count > 0) {
                        $("#chatList .CdsIndividualChat-chat-item[data-chat-unique-id='"+chatUniqueId+"'] .unread-message").html(response.unread_count);
                    } else {
                        $("#chatList .CdsIndividualChat-chat-item[data-chat-unique-id='"+chatUniqueId+"'] .unread-message").html("");
                    }
                    // Scroll to bottom
                    this.scrollToBottom();
                    // this.loadChatFiles()
                    console.log('Chat window switched successfully for:', chatUniqueId);
                
                } else {
                    this.showError(response.message || 'Failed to load chat messages');
                }
            },
            error: (xhr, status, error) => {
                this.showError('Failed to load chat messages: ' + error);
            }
        });
    }
    // Function to load messages for a specific chat
   

    // Function to re-bind events after chat switching
    rebindChatEvents() {
        console.log('Rebinding chat events...');
        
        // Re-bind header button events
        this.bindHeaderButtonEvents();
        
        // Re-bind input events
        // this.bindInputEvents();
        
        // Re-bind file panel events
        this.bindFilePanelEvents();
        
        // Re-bind search events
        this.bindSearchEvents();
        
        // Re-bind options menu events
        this.bindOptionsMenuEvents();
        
        console.log('Chat events rebound successfully');
    }

    // Function to bind header button events
    bindHeaderButtonEvents() {
        
        // Search button
        const searchBtn = document.getElementById('searchBtn');
        if (searchBtn) {
            searchBtn.onclick = () => this.toggleChatSearch();
        }
        
        
        // Options button
        const optionsBtn = document.getElementById('optionsBtn');
        if (optionsBtn) {
            // optionsBtn.onclick = (e) => {
            //     e.preventDefault();
            //     e.stopPropagation();
            //     this.toggleMainOptionsMenu();
            // };
            
            // Also add event listener for better compatibility
            optionsBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleMainOptionsMenu();
            });
        }
    }

    // Function to bind input events
    bindInputEvents() {
        // Send button
        const sendBtn = document.getElementById('sendBtn');
        if (sendBtn) {
            sendBtn.onclick = () => this.sendMessage();
        }
        
        // Message input
        const messageInput = document.getElementById('messageInput');
        if (messageInput) {
            // messageInput.onkeypress = (e) => {
            //     if (e.key === 'Enter' && !e.shiftKey) {
            //         e.preventDefault();
            //         this.sendMessage();
            //     }
            // };
            
            messageInput.onkeydown = (e) => {
                if (e.key === 'Escape') {
                    const editMessageId = document.getElementById('edit_message_id')?.value;
                    if (editMessageId) {
                        e.preventDefault();
                        this.cancelEditMode();
                    }
                }
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            };
            
            // Auto-resize functionality
            messageInput.oninput = () => {
                messageInput.style.height = 'auto';
                messageInput.style.height = Math.min(messageInput.scrollHeight, 120) + 'px';
            };
        }
        
        // Attachment button
        const attachBtn = document.getElementById('attachBtn');
        if (attachBtn) {
            attachBtn.onclick = () => this.openFileUploadModal();
        }
    }

    // Function to bind file panel events
    bindFilePanelEvents() {
        // Close files panel button
        const closeFilesPanel = document.getElementById('closeFilesPanel');
        if (closeFilesPanel) {
            closeFilesPanel.onclick = () => this.closeFilesPanel();
        }
        
        // Overlay click to close files panel
        const overlay = document.getElementById('overlay');
        if (overlay) {
            overlay.onclick = () => this.closeFilesPanel();
        }
        
        // Escape key to close files panel
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const filesPanel = document.getElementById('filesPanel');
                if (filesPanel && filesPanel.classList.contains('active')) {
                    this.closeFilesPanel();
                }
            }
        });
    }

    // Function to bind search events
    bindSearchEvents() {
        // Close chat search button
        const closeChatSearch = document.getElementById('closeChatSearch');
        if (closeChatSearch) {
            closeChatSearch.onclick = () => this.closeChatSearch();
        }
        
        // Sidebar search input
        const sidebarSearch = document.getElementById('sidebarSearch');
        if (sidebarSearch) {
            sidebarSearch.oninput = (e) => this.filterChats(e.target.value);
        }
    }

    // Filter chats in sidebar
    filterChats(searchTerm) {
        console.log('Filtering chats with:', searchTerm);
        const chatItems = document.querySelectorAll('.chat-user-item');
        
        chatItems.forEach(item => {
            const chatName = item.querySelector('.CdsIndividualChat-chat-name')?.textContent?.toLowerCase() || '';
            const chatPreview = item.querySelector('.CdsIndividualChat-chat-preview')?.textContent?.toLowerCase() || '';
            
            if (chatName.includes(searchTerm.toLowerCase()) || chatPreview.includes(searchTerm.toLowerCase())) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Function to bind options menu events
    bindOptionsMenuEvents() {
        // Close options menu on outside click with improved detection
        document.addEventListener('click', (e) => {
            const optionsMenu = document.getElementById('optionsMenu');
            if (optionsMenu && optionsMenu.classList.contains('active')) {
                if (this.isClickOutsideOptionsMenu(e.target)) {
                    this.closeOptionsMenu();
                    console.log('Options menu closed due to outside click');
                }
            }
        });
        
        // Prevent menu from closing when clicking inside the menu itself
        const optionsMenu = document.getElementById('optionsMenu');
        if (optionsMenu) {
            optionsMenu.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }
        
        // Close menu when pressing Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeOptionsMenu();
                console.log('Options menu closed due to Escape key');
            }
        });
        
        // Additional safety: close menu when clicking on any other header button
        const headerButtons = document.querySelectorAll('.CdsIndividualChat-header-btn:not(#optionsBtn)');
        headerButtons.forEach(button => {
            button.addEventListener('click', () => {
                this.closeOptionsMenu();
                console.log('Options menu closed due to other header button click');
            });
        });
        
        // Close menu when clicking on search bar or other chat elements
        const chatElements = document.querySelectorAll('#chatSearchBar, #messagesContainer, #sendmsg');
        chatElements.forEach(element => {
            element.addEventListener('click', () => {
                this.closeOptionsMenu();
                console.log('Options menu closed due to chat element click');
            });
        });
        
        // Close menu on window resize
        window.addEventListener('resize', () => {
            this.closeOptionsMenu();
            console.log('Options menu closed due to window resize');
        });
        
        // Close menu on scroll (in case the menu gets out of position)
        window.addEventListener('scroll', () => {
            this.closeOptionsMenu();
            console.log('Options menu closed due to scroll');
        });
        
        // Close menu when clicking on overlay if it exists
        const overlay = document.getElementById('overlay');
        if (overlay) {
            overlay.addEventListener('click', () => {
                this.closeOptionsMenu();
                console.log('Options menu closed due to overlay click');
            });
        }
    }

    // // Toggle chat search
    // toggleChatSearch() {
    //     console.log('Toggle chat search called');
    //     const chatSearchBar = document.getElementById('chatSearchBar');
    //     if (chatSearchBar) {
    //         chatSearchBar.classList.toggle('active');
    //         if (chatSearchBar.classList.contains('active')) {
    //             const searchInput = document.getElementById('chatSearchInput');
    //             if (searchInput) {
    //                 searchInput.focus();
    //             }
    //         }
    //         console.log('Chat search active:', chatSearchBar.classList.contains('active'));
    //     } else {
    //         console.log('Chat search bar not found');
    //     }
    // }

    // Close chat search
    closeChatSearch() {
        const chatSearchBar = document.getElementById('chatSearchBar');
        if (chatSearchBar) {
            chatSearchBar.classList.remove('active');
        }
        $('#chatSearchInput').val('');
        $('.CdsIndividualChat-message-text').removeClass('highlight');
    }

    // Verify menu state and positioning
    verifyMenuState() {
        const optionsMenu = document.getElementById('optionsMenu');
        if (optionsMenu) {
            const isActive = optionsMenu.classList.contains('active');
            const computedStyle = window.getComputedStyle(optionsMenu);
            const opacity = computedStyle.opacity;
            const visibility = computedStyle.visibility;
            const display = computedStyle.display;
            const pointerEvents = computedStyle.pointerEvents;
            
            console.log('Menu state verification:', {
                isActive,
                opacity,
                visibility,
                display,
                pointerEvents,
                hasActiveClass: optionsMenu.classList.contains('active')
            });
            
            return {
                isActive,
                opacity,
                visibility,
                display,
                pointerEvents
            };
        }
        return null;
    }

    // Toggle options menu
toggleMainOptionsMenu(e) {
    console.log('Toggle options menu called');
    const optionsMenu = document.getElementById('optionsMenu');
    const optionsBtn = document.getElementById('optionsBtn');

    if (!optionsMenu) {
        console.error('Options menu element not found');
        return;
    }

    const isActive = optionsMenu.classList.contains('active');
    console.log('Current menu state:', isActive ? 'active' : 'inactive');

    if (isActive) {
        // --- CLOSE ---
        optionsMenu.classList.remove('active');
        optionsMenu.style.opacity = '';
        optionsMenu.style.transform = '';
        optionsMenu.style.pointerEvents = '';
        optionsMenu.style.visibility = '';
        console.log('Options menu closed (toggle)');
        return;
    }

    // --- OPEN ---
    this.closeAllOtherMenus();
    optionsMenu.classList.add('active');
    console.log('Options menu opened');

    setTimeout(() => {
        this.ensureMenuPosition();
        this.verifyMenuState();
    }, 10);

    // === Outside click handler (with ignore for toggle button) ===
    const outsideClickHandler = (event) => {
        // Ignore if clicked inside menu OR on the button
        if (
            optionsMenu.contains(event.target) ||
            optionsBtn.contains(event.target)
        ) {
            return;
        }

        // Close the menu
        optionsMenu.classList.remove('active');
        optionsMenu.style.opacity = '';
        optionsMenu.style.transform = '';
        optionsMenu.style.pointerEvents = '';
        optionsMenu.style.visibility = '';
        console.log('Options menu closed by outside click');

        // Remove listener after closing
        document.removeEventListener('click', outsideClickHandler);
    };

    // 🔑 Delay binding by one tick so the same click doesn’t close it immediately
    setTimeout(() => {
        document.addEventListener('click', outsideClickHandler);
    }, 0);
}


    // Close all other menus to ensure only one is open at a time
    closeAllOtherMenus() {
        const allMenus = document.querySelectorAll('.CdsIndividualChat-options-menu, .message-options-menu');
        allMenus.forEach(menu => {
            if (menu.classList.contains('active')) {
                menu.classList.remove('active');
            }
        });
    }

    // Clean up options menu state
    cleanupOptionsMenu() {
        const optionsMenu = document.getElementById('optionsMenu');
        if (optionsMenu) {
            optionsMenu.classList.remove('active');
            console.log('Options menu cleaned up');
        }
    }

    // Method to be called when chat is destroyed or cleaned up
    destroy() {
        // Clean up options menu
        this.cleanupOptionsMenu();
        
        // Remove event listeners if needed
        // Note: Since we're using addEventListener, these will be cleaned up automatically
        // when the page is unloaded or the element is removed from DOM
    }

    // Open files panel
    // openFilesPanel() {
    //     console.log('Open files panel called');
    //     const filesPanel = document.getElementById('filesPanel');
    //     const overlay = document.getElementById('overlay');
    //     if (filesPanel && overlay) {
    //         filesPanel.classList.add('active');
    //         overlay.classList.add('active');
    //         console.log('Files panel opened');
    //     } else {
    //         console.log('Files panel or overlay not found');
    //     }
    // }

    // Close files panel
    closeFilesPanel() {
        const filesPanel = document.getElementById('filesPanel');
        const overlay = document.getElementById('overlay');
        if (filesPanel && overlay) {
            filesPanel.classList.remove('active');
            overlay.classList.remove('active');
        }
    }

    // // Handle user button click
    // handleUserButton() {
    //     console.log('User button clicked');
    //     // Add your user button functionality here
    // }

    // Handle option click
    // handleOptionClick(option) {
    //     console.log('Option clicked:', option);
    //     switch(option) {
    //         case 'Block Chat':
    //             if (confirm('Are you sure you want to block this chat?')) {
    //                 console.log('Chat blocked');
    //             }
    //             break;
    //         case 'Clear Chat':
    //             if (confirm('Are you sure you want to clear this chat? This action cannot be undone.')) {
    //                 const messagesContainer = document.getElementById('messagesContainer');
    //                 if (messagesContainer) {
    //                     messagesContainer.innerHTML = '';
    //                 }
    //                 console.log('Chat cleared');
    //             }
    //             break;
    //         case 'Delete Chat':
    //             if (confirm('Are you sure you want to delete this chat? This action cannot be undone.')) {
    //                 console.log('Chat deleted');
    //             }
    //             break;
    //     }
    //     this.toggleOptionsMenu();
    // }

    // Function to update sidebar active state
    updateSidebarActiveState(chatUniqueId) {
        // Remove active class from all chat items
        $('.chat-user-item, .chat-user, .chat-list-item').removeClass('active selected current');
        
        // Add active class to current chat (try multiple selectors)
        const activeSelectors = [
            `[data-chat-id="${chatUniqueId}"]`,
            `[data-chat-unique-id="${chatUniqueId}"]`,
            `[href*="${chatUniqueId}"]`
        ];
        
        let activeElement = null;
        for (const selector of activeSelectors) {
            activeElement = $(selector).first();
            if (activeElement.length > 0) {
                break;
            }
        }
        
        if (activeElement.length > 0) {
            activeElement.addClass('active selected current');
            // Scroll to active item if needed
            activeElement[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
        
        console.log('Sidebar active state updated for chat:', chatUniqueId);
    }
    
    // Function to show chat loading indicator
    showChatLoading() {
        const messagesContainer = document.getElementById('messagesContainer');
        if (messagesContainer) {
            // Add loading indicator
            const loadingHtml = `
                <div class="chat-loading-indicator" style="text-align: center; padding: 20px; color: #666;">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div style="margin-top: 10px;">Loading chat messages...</div>
                </div>
            `;
            messagesContainer.innerHTML = loadingHtml;
        }
    }

    // Function to initialize socket for a specific chat
    initializeSocketForChat(chatId) {
        

        console.log('Initializing socket for chat ID:', chatId);

        // Leave any existing presence channel first
        if (this.presenceChannel) {
            try {
                window.Echo.leave(`presence-chat.${this.chatId}`);
                console.log('Left previous presence channel:', `presence-chat.${this.chatId}`);
            } catch (e) {
                console.log('Error leaving previous channel:', e);
            }
        }

        // Initialize presence channel for the new chat
        this.initializePresenceChannel();

        // Listen for events on the individual-chat channel (same flow as chatapp.js)
        if (window.Echo) {
            try {
                
                window.Echo.leave(`user-individual-chat.${this.currentUserId}`);
                const userIndividualChatChannel = window.Echo.private(`user-individual-chat.${this.currentUserId}`);
                userIndividualChatChannel.listen('ChatMessageSocket', (e) => {
                    if(!this.chatId){
                        this.handleUserChatSocketEvent(e);
                    }
                });
                if (chatId) {
                    window.Echo.leave(`individual-chat.${this.chatId}`);
                    const individualChatChannel = window.Echo.private(`individual-chat.${chatId}`);
                    individualChatChannel.listen('ChatSocket', (e) => {
                        console.log('ChatSocket event received on individual-chat channel:', e);
                        this.handleChatSocketEvent(e);
                    });
                    window.Echo.leave(`individual-chat-reaction.` + this.chatId);
                    const messageReaction = window.Echo.private(`individual-chat-reaction.${chatId}`);
                    messageReaction.listen('MessageReactionAdded', (e) => {
                        this.handleMessageReaction(e); 
                    });
                }
                // window.Echo.leave(`chat_blocked.` + this.chatId);
                // window.Echo.private(`chat_blocked.${this.chatId}`).listen(
                //     "ChatBlocked",
                //     (event) => {
                //         // const blockedBy = event.blockedBy;
                //         // const blockedUserId = event.blockedUserId;
                //         // const blockStatus = event.status;
                //         // Update the UI with the reaction
                //         this.switchToChat(this.chatUniqueId,this.chatId);
                //     })

                // console.log('Successfully joined individual-chat channel:', `individual-chat.${chatId}`);
            } catch (error) {
                // console.error('Error joining individual-chat channel:', error);
            }
        } else {
            // console.warn('Echo not available for socket initialization');
        }
    }
    handleMessageReaction(e){
        const messageUniqueId = e.messageUniqueId;
        const msgReaction = e.messageReaction;
        const reactionUniqueId = e.reactionUniqueId;
        const senderId = e.sender_id;
        
        if (e.action == "add_reaction") {
            // Create reaction HTML
            let messageHtml = '';
            if (senderId == this.currentUserId) {
                // Own reaction - can be removed
                messageHtml = `<span class="CdsIndividualChat-reaction" onclick="removeIndividualChatReaction(this)" data-message-id="${reactionUniqueId}" id="MsgReaction${reactionUniqueId}">${msgReaction}</span>`;
            } else {
                // Other user's reaction - cannot be removed
                messageHtml = `<span class="CdsIndividualChat-reaction" id="MsgReaction${reactionUniqueId}">${msgReaction}</span>`;
            }
            
            // Add reaction to message
            const messageElement = $(`#messagesContainer #message-${messageUniqueId}`);
            const reactionsContainer = messageElement.find('.CdsIndividualChat-message-reactions');
            
            if (reactionsContainer.length > 0) {
                reactionsContainer.html(messageHtml);
            } else {
                // Create reactions container if it doesn't exist
                const newReactionsHtml = `<div class="CdsIndividualChat-message-reactions">${messageHtml}</div>`;
                messageElement.find('.CdsIndividualChat-message').html(newReactionsHtml);
            }
        } else if (e.action == "remove_reaction") {
            // Remove the reaction element
            $(`#MsgReaction${reactionUniqueId}`).remove();
            
            // If no reactions left, remove the reactions container
            const messageElement = $(`#MsgReaction${reactionUniqueId}`).closest('.CdsIndividualChat-message-wrapper');
            const reactionsContainer = messageElement.find('.CdsIndividualChat-message-reactions');
            if (reactionsContainer.children().length === 0) {
                reactionsContainer.remove();
            }
        }
    }

    handleUserChatSocketEvent(e){
        const response = e.data;
        this.loadChatSidebar();
        // if(response.action == "new_message"){
        //     this.loadChatSidebar();
        //     console.log('UserChatSocket event received on user-individual-chat channel:', response);
        // }
    }
    // Handle ChatSocket events (same flow as chatapp.js)
    handleChatSocketEvent(e) {
        const response = e.data;
        const chatId = response.chat_id;
        // Check if this is the active chat
        // alert("IN: "+response.action+" "+this.chatId+" "+chatId	); 
        if (this.chatId == chatId) {
            console.log('Processing ChatSocket event for active chat:', response);
            
            if (response.action == "new_message") {
                this.handleNewMessageAction(response);
            }
            if(response.action == 'blocked' || response.action == 'unblocked'){
                this.switchToChat(this.chatUniqueId,this.chatId,true);
            }

            if (response.action == "delete_selected_attachments") {
                this.handleDeleteAttachmentsAction(response);
            }

            if (response.action == "delete_msg_for_me") {
                this.handleDeleteMessageForMeAction(response);
            }

            if (response.action == "deleted_msg_for_everyone") {
                this.handleDeleteMessageForEveryoneAction(response);
            }

            if (response.action == "user_typing") {
                this.handleUserTypingAction(response);
            }
            if (response.action == "message_read") {
                this.handleMessageReadAction(response);
            }

            if (response.action == "message_edited") {
                this.handleMessageEditedAction(response);
            }

            if (response.action == "add_reaction") {
                this.handleMessageReactionAction(response);
            }

            if (response.action == "remove_reaction") {
                this.handleMessageReactionRemovedAction(response);
            }

            if (response.action == "chat_deleted") {
                closeBot(response.chat_id, "userChat");
                if (this.chatId == response.chat_id) {
                    window.location.href = BASEURL + "/individual-chats";
                }
            }
        }
    }

    // Handle new message action
    handleNewMessageAction(response) {
        console.log(response,"new message");
        // alert("New Message: "+response.last_message_id+" "+this.lastMessageId);  
        if (response.last_message_id !== this.lastMessageId) {
            // Fetch only new messages instead of reloading all
            this.loadChatMessages(this.chatUniqueId, this.lastMessageId);
            this.loadChatSidebar();
        }

        // Update user activity status
        if (response.userActivityStatus == "Active") {
            $(`.chatOnlineStatus${this.chatId}`).addClass("status-online");
            $(`.chatOnlineStatus${this.chatId}`).removeClass("status-offline");
            $(`.sidebarOnlineStatus${this.chatId}`).html("Active");
        } else {
            $(`.chatOnlineStatus${this.chatId}`).addClass("status-offline");
            $(`.chatOnlineStatus${this.chatId}`).removeClass("status-online");
            $(`.sidebarOnlineStatus${this.chatId}`).html("Inactive");
        }
    }

    // Function to fetch only new messages
  loadChatMessages(chatUniqueId, lastMessageId) {
     // alert("load new message: "+chatUniqueId+" "+lastMessageId);
    $.ajax({
        url: BASEURL + '/individual-chats/load-messages/' + chatUniqueId,
        type: 'POST',
        data: {
            _token: this.csrfToken,
            last_msg_id: lastMessageId,
        },
        success: (response) => {
            if (response.status === true) {
                // Update message IDs FIRST
                this.lastMessageId = response.last_msg_id || this.lastMessageId;
                this.firstMessageId = response.first_msg_id || this.firstMessageId;

                if (response.contents && response.contents.trim() !== '') {
                    if (lastMessageId == 0) {
                        // First load → full replace
                        $("#messagesContainer").html(response.contents);
                    } else {
                        // New messages → append
                        $("#messagesContainer").append(response.contents);
                    }

                    this.rebindChatEvents();

                    setTimeout(() => {
                        this.initializeEmojiPicker();
                    }, 500);

                    // Always scroll down for new messages
                    $("#messagesContainer").scrollTop(
                        $("#messagesContainer")[0].scrollHeight
                    );

                    // Update unread badge
                    let $chatItem = $("#chatList .CdsIndividualChat-chat-item[data-chat-unique-id='" + chatUniqueId + "']");
                    $chatItem.find(".unread-message").html(response.unread_count > 0 ? response.unread_count : "");
                }
            } else {
                this.showError(response.message || 'Failed to load new messages');
            }
        },
        error: (xhr, status, error) => {
            this.showError('Failed to load new messages: ' + error);
        }
    });
}

    // Handle delete attachments action
    handleDeleteAttachmentsAction(response) {
        const messageUId = response.messageUniqueId;
        
        if (response.attachments && response.attachments.length > 0) {
            // Remove files from the file panel
            response.attachments.forEach(fileName => {
                $(`.CdsIndividualChat-file-item[data-file-name="${fileName}"]`).remove();
            });
            
            // Also remove from message attachments
            $(`.attachment[data-file-name="${response.attachments}"]`).remove();
        } else {
            // If no attachments left, remove message
            $(`#messagesContainer #message-${messageUId}`).html(
                '<div class="message-block"><div class="text-message-block"><div class="message sent"><p class="deleted-message chat-message">This message was deleted.</p></div></div></div>'
            );
        }
        
        // Refresh chat sidebar and file panel
        this.loadChatSidebar();
        this.refreshChatFiles();
    }

    // Handle delete message for me action
    handleDeleteMessageForMeAction(response) {
        if (this.currentUserId == response.sender_id) {
            const messageUniqueId = response.messageUniqueId;
            $(`#messagesContainer`).find(`#message-${messageUniqueId}`).remove();
        }
    }

    // Handle delete message for everyone action
    handleDeleteMessageForEveryoneAction(response) {
        const messageUId = response.messageUniqueId;
        $(`#messagesContainer #message-${messageUId}`).html(
            '<div class="message-block"><div class="text-message-block"><div class="message sent"><p class="deleted-message chat-message">This message was deleted.</p></div></div></div>'
        );
        
        // Refresh chat sidebar
        this.loadChatSidebar();
    }

    // Handle user typing action
    handleUserTypingAction(response) {
        if (response.receiver_id == this.currentUserId) {
            if (response.isTyping == 1) {
                this.showTypingIndicator(response.sender_name || 'User');
            } else {
                this.hideTypingIndicator();
            }
        }
    }

    // Handle message read action
    handleMessageReadAction(response) {
        if (response.sender_id != this.currentUserId) {
            const message_ids = response.read_msg_ids.split(",");
            
            for (let i = 0; i < message_ids.length; i++) {
                $(`#messagesContainer #message-${message_ids[i]}`)
                    .find(".readtrack")
                    .html('<i class="fa-sharp fa-solid fa-check-double text-primary"></i>');
            }
        }
        
        // Refresh chat sidebar
        // this.loadChatSidebar();
    }

    // Handle message edited action
    handleMessageEditedAction(response) {
        const messageUniqueId = response.messageUniqueId;
        $(`#messagesContainer #cpMsg${messageUniqueId}`).html(response.editedMessage);
        $(`#messagesContainer #editedMsg${messageUniqueId}`).html("edited");
        this.loadChatSidebar();
    }

    // Handle message reaction action
    handleMessageReactionAction(response) {
        const messageUniqueId = response.messageUniqueId;
        const reaction = response.reaction;
        const reactionUniqueId = response.reactionUniqueId;
        const senderId = response.sender_id;
        
        // Create reaction HTML
        let reactionHtml = '';
        if (senderId == this.currentUserId) {
            // Own reaction - can be removed
            reactionHtml = `<span class="CdsIndividualChat-reaction" onclick="removeIndividualChatReaction(this)" data-message-id="${reactionUniqueId}" id="MsgReaction${reactionUniqueId}">${reaction}</span>`;
        } else {
            // Other user's reaction - cannot be removed
            reactionHtml = `<span class="CdsIndividualChat-reaction" id="MsgReaction${reactionUniqueId}">${reaction}</span>`;
        }
        
        // Add reaction to message
        const messageElement = $(`#messagesContainer #message-${messageUniqueId}`);
        const reactionsContainer = messageElement.find('.CdsIndividualChat-message-reactions');
        
        if (reactionsContainer.length > 0) {
            reactionsContainer.html(reactionHtml);
        } else {
            // Create reactions container if it doesn't exist
            const newReactionsHtml = `<div class="CdsIndividualChat-message-reactions">${reactionHtml}</div>`;
            messageElement.find('.CdsIndividualChat-message').html(newReactionsHtml);
        }
    }

    // Handle message reaction removed action
    handleMessageReactionRemovedAction(response) {
        const reactionUniqueId = response.reactionUniqueId;
        // Remove the reaction element
        $(`#MsgReaction${reactionUniqueId}`).remove();
        
        // If no reactions left, remove the reactions container
        // const messageElement = $(`#MsgReaction${reactionUniqueId}`).closest('.CdsIndividualChat-message-wrapper');
        // const reactionsContainer = messageElement.find('.CdsIndividualChat-message-reactions');
        // if (reactionsContainer.children().length === 0) {
        //     reactionsContainer.remove();
        // }
    }

    // Play notification sound
    playNotificationSound() {
        const audio = document.getElementById('message-notification');
        if (audio) {
            audio.play().catch(e => console.log('Could not play notification sound:', e));
        }
    }

    // Copy message to clipboard
    copyMessage(messageId) {
        const messageElement = document.getElementById(`cpMsg${messageId}`);
        if (messageElement) {
            const textToCopy = messageElement.textContent || messageElement.innerText;
            navigator.clipboard.writeText(textToCopy).then(() => {
                this.showSuccess('Message copied to clipboard!');
            }).catch(() => {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = textToCopy;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                this.showSuccess('Message copied to clipboard!');
            });
        }
    }

    // Edit message (same flow as reference)
    editMessage(messageId, messageText) {
        // Check if message is less than 1 hour old (same condition as reference)
        const messageElement = document.getElementById(`cpMsg${messageId}`);
        if (messageElement) {
            const messageContainer = messageElement.closest('[data-message-timestamp]');
            if (messageContainer) {
                const timestamp = messageContainer.dataset.messageTimestamp;
                const messageTime = new Date(timestamp);
                const oneHourAgo = new Date(Date.now() - 60 * 60 * 1000);
                
                if (messageTime < oneHourAgo) {
                    this.showError('Messages can only be edited within 1 hour of sending');
                    return;
                }
            }
            
            // Set edit mode (same as reference)
            const editMessageIdElement = document.getElementById('edit_message_id');
            const messageInputElement = document.getElementById('messageInput');
            
            if (editMessageIdElement && messageInputElement) {
                editMessageIdElement.value = messageId;
                messageInputElement.value = messageText;
                
                // Focus on input and select text
                messageInputElement.focus();
                messageInputElement.select();
                
                // Change send button text to indicate edit mode
                // const sendBtn = document.getElementById('sendBtn');
                // sendBtn.textContent = 'Update Message';
                // sendBtn.classList.add('editing');
                
                // Show edit indicator
                this.showEditMode();
            }
        }
    }



    // Delete message for me
    deleteMessageForMe(messageId,messageUniqueId) {
        Swal.fire({
            title: "Are you sure to delete?",
            text: "You won't be able to revert this!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
            confirmButtonClass: "btn btn-primary",
            cancelButtonClass: "btn btn-danger ml-1",
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: BASEURL + '/individual-chats/delete-message-for-me/'+messageId,
                    type: 'POST',
                    data: {
                        _token: csrf_token,
                        message_id: messageId,
                    },
                    success: (response) => {
                        if (response.status === true) {
                            // Remove message from DOM
                            $(`#message-${messageUniqueId}`).remove();
                            this.showSuccess('Message deleted successfully!');
                        } else {
                            this.showError(response.message || 'Failed to delete message');
                        }
                    },
                    error: (xhr, status, error) => {
                        this.showError('Failed to delete message: ' + error);
                    }
                });
            }
        });
        // if (confirm('Are you sure you want to delete this message for yourself?')) {
        //     $.ajax({
        //         url: BASEURL + '/individual-chats/delete-message-for-me/'+messageId,
        //         type: 'POST',
        //         data: {
        //             _token: this.csrfToken,
        //             message_id: messageId,
        //         },
        //         success: (response) => {
        //             if (response.status === true) {
        //                 // Remove message from DOM
        //                 $(`#message-${messageUniqueId}`).remove();
        //                 this.showSuccess('Message deleted successfully!');
        //             } else {
        //                 this.showError(response.message || 'Failed to delete message');
        //             }
        //         },
        //         error: (xhr, status, error) => {
        //             this.showError('Failed to delete message: ' + error);
        //         }
        //     });
        // }
    }

    // Delete message for everyone
    deleteMessageForEveryone(messageId, messageUniqueId) {
        // Check if message is less than 1 hour old (same condition as reference)
        const messageElement = document.getElementById(`cpMsg${messageUniqueId}`);
        if (messageElement) {
            const messageContainer = messageElement.closest('[data-message-timestamp]');
            if (messageContainer) {
                const timestamp = messageContainer.dataset.messageTimestamp;
                const messageTime = new Date(timestamp);
                const oneHourAgo = new Date(Date.now() - 60 * 60 * 1000);
                
                if (messageTime < oneHourAgo) {
                    this.showError('Messages can only be deleted for everyone within 1 hour of sending');
                    return;
                }
            }
        }
        Swal.fire({
            title: "Are you sure to delete?",
            text: "You won't be able to revert this!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
            confirmButtonClass: "btn btn-primary",
            cancelButtonClass: "btn btn-danger ml-1",
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: BASEURL + '/individual-chats/delete-message-for-all/'+messageId,
                    type: 'POST',
                    data: {
                        _token: csrf_token,
                    },
                    success: (response) => {
                        if (response.status === true) {
                            // Show deleted message indicator
                            const messageElement = document.getElementById(`cpMsg${messageUniqueId}`);
                            if (messageElement) {
                                messageElement.innerHTML = '<p class="deleted-message chat-message">This message was deleted.</p>';
                            }
                            this.showSuccess('Message deleted for everyone!');
                        } else {
                            this.showError(response.message || 'Failed to delete message');
                        }
                    },
                    error: (xhr, status, error) => {
                        this.showError('Failed to delete message: ' + error);
                    }
                });
            }
        });
        // if (confirm('Are you sure you want to delete this message for everyone? This action cannot be undone.')) {
        //     $.ajax({
        //         url: BASEURL + '/individual-chats/delete-message-for-all/'+messageId,
        //         type: 'POST',
        //         data: {
        //             _token: this.csrfToken,
        //         },
        //         success: (response) => {
        //             if (response.status === true) {
        //                 // Show deleted message indicator
        //                 const messageElement = document.getElementById(`cpMsg${messageUniqueId}`);
        //                 if (messageElement) {
        //                     messageElement.innerHTML = '<p class="deleted-message chat-message">This message was deleted.</p>';
        //                 }
        //                 this.showSuccess('Message deleted for everyone!');
        //             } else {
        //                 this.showError(response.message || 'Failed to delete message');
        //             }
        //         },
        //         error: (xhr, status, error) => {
        //             this.showError('Failed to delete message: ' + error);
        //         }
        //     });
        // }
    }

    // Helper method to check message conditions (same as reference)
    checkMessageConditions(messageId, messageUniqueId) {
        // Check if message is deleted
        const messageElement = document.getElementById(`cpMsg${messageUniqueId}`);
        if (!messageElement) {
            return { canEdit: false, canDeleteForEveryone: false, canCopy: false, reason: 'Message not found' };
        }

        // Check if message is deleted
        if (messageElement.classList.contains('deleted-message') || messageElement.textContent.includes('This message was deleted')) {
            return { canEdit: false, canDeleteForEveryone: false, canCopy: false, reason: 'Message is deleted' };
        }

        // Check if chat is blocked (you can add this check based on your chat state)
        if (this.isChatBlocked()) {
            return { canEdit: false, canDeleteForEveryone: false, canCopy: false, reason: 'Chat is blocked' };
        }

        // Check message age for edit and delete for everyone
        const messageContainer = messageElement.closest('[data-message-timestamp]');
        let canEdit = true;
        let canDeleteForEveryone = true;
        
        if (messageContainer) {
            const timestamp = messageContainer.dataset.messageTimestamp;
            const messageTime = new Date(timestamp);
            const oneHourAgo = new Date(Date.now() - 60 * 60 * 1000);
            
            if (messageTime < oneHourAgo) {
                canEdit = false;
                canDeleteForEveryone = false;
            }
        }

        // Check if message has content for copy
        const hasContent = messageElement.textContent && messageElement.textContent.trim().length > 0;

        return {
            canEdit,
            canDeleteForEveryone,
            canCopy: hasContent,
            reason: 'OK'
        };
    }

    // Check if chat is blocked
    isChatBlocked() {
        // You can implement this based on your chat state
        // For now, return false (not blocked)
        return false;
    }

    // Generate dropdown options based on message conditions (same as reference)
    generateMessageOptions(messageId, messageUniqueId, messageText, isOwnMessage = false) {
        const conditions = this.checkMessageConditions(messageId, messageUniqueId);
        let options = [];

        // Copy option - only if message has content and not deleted
        if (conditions.canCopy) {
            options.push(`
                <li>
                    <a class="dropdown-item copy-message" href="javascript:;" 
                       data-message-id="${messageUniqueId}">
                        Copy <i class="fa fa-copy"></i>
                    </a>
                </li>
            `);
        }

        // Edit option - only for own messages, less than 1 hour old, and not deleted
        if (isOwnMessage && conditions.canEdit) {
            options.push(`
                <li>
                    <a class="dropdown-item edit-message" href="javascript:;" 
                       data-message-id="${messageUniqueId}" 
                       data-message-text="${this.escapeHtml(messageText)}">
                        Edit <i class="fa fa-edit"></i>
                    </a>
                </li>
            `);
        }

        // Reply option - always available if not deleted
        if (conditions.reason === 'OK') {
            options.push(`
                <li>
                    <a class="dropdown-item reply-to-message-option" href="javascript:;" 
                       data-message-id="${messageId}" 
                       data-message-text="${this.escapeHtml(messageText)}">
                        Reply <i class="fa fa-reply"></i>
                    </a>
                </li>
            `);
        }

        // Delete for everyone - only for own messages, less than 1 hour old, and not deleted
        if (isOwnMessage && conditions.canDeleteForEveryone) {
            options.push(`
                <li id="del_msg_for_all${messageUniqueId}">
                    <a class="dropdown-item delete-message-for-everyone" href="javascript:;" 
                       data-message-id="${messageId}" 
                       data-message-unique-id="${messageUniqueId}">
                        Delete for Everyone <i class="fa fa-trash"></i>
                    </a>
                </li>
            `);
        }

        // Delete for me - always available if not deleted
        if (conditions.reason === 'OK') {
            options.push(`
                <li>
                    <a class="dropdown-item delete-message-for-me" href="javascript:;" 
                       data-message-id="${messageId}" 
                       data-message-unique-id="${messageUniqueId}">
                        Delete for me <i class="fa fa-trash"></i>
                    </a>
                </li>
            `);
        }

        return options.join('');
    }

    // Escape HTML to prevent XSS
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Create complete message options dropdown HTML
    createMessageOptionsDropdown(messageId, messageUniqueId, messageText, isOwnMessage = false) {
        const options = this.generateMessageOptions(messageId, messageUniqueId, messageText, isOwnMessage);
        
        if (options.length === 0) {
            return ''; // No options available
        }

        return `
            <div class="message-options-dropdown">
                <button class="message-options-toggle" data-option-id="${messageUniqueId}" type="button">
                    <i class="fa-solid fa-ellipsis"></i>
                </button>
                <ul id="msg-options-${messageUniqueId}" class="message-options-menu" style="display: none;">
                    ${options}
                </ul>
            </div>
        `;
    }

    // Toggle message options dropdown
   toggleMessageOptions(toggleButton) {
    if (!toggleButton) {
        console.error('toggleMessageOptions: toggleButton is undefined');
        return;
    }

    const option_id = $(toggleButton).data('option-id');
    const dropdown = $(toggleButton).closest(".message-options-dropdown");
    const menu = dropdown.find('.message-options-menu');

    if (!dropdown.length || !menu.length) {
        console.error('toggleMessageOptions: Could not find dropdown or menu elements');
        return;
    }

    console.log('toggleMessageOptions called:', { option_id, dropdown: dropdown.length, menu: menu.length });

    // Close all other dropdowns except the current one
    $('.message-options-menu.active').not(menu).removeClass('active');

    // Toggle current dropdown
    if (menu.hasClass('active')) {
        console.log('Removing active class');
        menu.removeClass('active');
    } else {
        console.log('Adding active class');
        menu.addClass('active');
        this.positionDropdown(dropdown, toggleButton);
    }
}

    // Close all message options dropdowns
    closeAllMessageOptions() {
        $('.message-options-menu').removeClass('active');
    }

    // Position dropdown to prevent it from going off-screen
    positionDropdown(dropdown, toggleButton) {
        const menu = dropdown.find('.message-options-menu');
        
        if (!menu.length || !toggleButton) {
            console.error('positionDropdown: Missing required elements', { menu: menu.length, toggleButton: !!toggleButton });
            return; // Safety check
        }
        
        try {
            const buttonRect = toggleButton.getBoundingClientRect();
            const menuRect = menu[0].getBoundingClientRect();
        
            // Check if dropdown goes off the right edge
            if (buttonRect.right + menuRect.width > window.innerWidth) {
                menu.css('right', '0');
                menu.css('left', 'auto');
            } else {
                menu.css('left', '0');
                menu.css('right', 'auto');
            }
            
            // Check if dropdown goes off the bottom edge
            if (buttonRect.bottom + menuRect.height > window.innerHeight) {
                menu.css('bottom', '100%');
                menu.css('top', 'auto');
                menu.css('margin-bottom', '5px');
            } else {
                menu.css('top', '100%');
                menu.css('bottom', 'auto');
                menu.css('margin-top', '5px');
            }
        } catch (error) {
            console.error('positionDropdown: Error positioning dropdown', error);
        }
    }

   
    setReplyTo(messageId, messageText) {
        const replyToIdElement = document.getElementById('reply_to_id');
        const myreplyElement = document.getElementById('myreply');
        const replyQuotedMsgElement = document.getElementById('replyQuotedMsg');
        
        if (replyToIdElement) {
            replyToIdElement.value = messageId;
        }
        if (myreplyElement) {
            myreplyElement.textContent = messageText;
        }
        if (replyQuotedMsgElement) {
            replyQuotedMsgElement.style.display = 'flex';
        }
    }

    hideReplyTo() {
        const replyToId = document.getElementById('reply_to_id');
        const replyQuotedMsg = document.getElementById('replyQuotedMsg');
        
        if (replyToId) {
            replyToId.value = '';
        }
        if (replyQuotedMsg) {
            replyQuotedMsg.style.display = 'none';
        }
    }

    clearMessageInput() {
        // Check if we're in edit mode
        const editMessageIdElement = document.getElementById('edit_message_id');
        const messageInputElement = document.getElementById('messageInput');
        
        if (editMessageIdElement && editMessageIdElement.value) {
            this.cancelEditMode();
        } else if (messageInputElement) {
            messageInputElement.value = '';
        }
        
        $("#sendBtn").prop('disabled', false);
        $("#attachBtn").prop('disabled', false);
        $("#messageInput").prop('disabled', false);
        this.autoResizeTextarea();
    }

    blockChat(chatId) {
        $.ajax({
            type: "get",
            url: BASEURL + "/individual-chats/block-message-centre/" + chatId,
            data: {
                _token: this.csrfToken,
            },
            dataType: 'json',
            success: function (data) {
                if(data.status === true) {
                    successMessage('Chat blocked successfully');
                } else {
                    errorMessage(data.message || 'Failed to block chat');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error blocking chat:', error);
                if (typeof errorMessage === 'function') {
                    errorMessage('Error blocking chat. Please try again.');
                }
            }
        });
    }

    unblockChat(chatId) {
        $.ajax({
            url: BASEURL + '/individual-chats/unblock-message-centre/' + chatId,
            type: 'POST',
            data: {
                _token: this.csrfToken,
            },
            dataType: 'json',
            success: (response) => {
                if (response.status === true) {
                    successMessage('Chat unblocked successfully');
                } else {
                    errorMessage(response.message || 'Failed to unblock chat');
                }
            },
            error: (xhr, status, error) => {
                errorMessage('Error unblocking chat. Please try again.');
            }
        });
    }

    autoResizeTextarea() {
        const textarea = document.getElementById('messageInput');
        if (textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
        }
    }

    // Initialize Laravel Echo presence channel for real-time typing
    initializePresenceChannel() {
        if (!this.chatId || !window.Echo) {
            console.warn('Chat ID or Echo not available for presence channel');
            return;
        }

        console.log('Initializing presence channel for chat:', this.chatId);
        
        // Leave any existing channel first
        try {
            window.Echo.leave(`presence-chat.${this.chatId}`);
        } catch (e) {
            // Ignore errors when leaving
        }
        
        // Join the presence channel
        this.presenceChannel = window.Echo.join(`presence-chat.${this.chatId}`);
        
        this.presenceChannel
            .here((users) => {
                // successMessage('Successfully joined presence channel, users:', users);
                this.channelReady = true;
            })
            .joining((user) => {
                // successMessage('User joining:', user);
            })
            .leaving((user) => {
                successMessage('User leaving:', user);
                this.hideTypingIndicator();
            })
            .listenForWhisper('typing', (e) => {
                // successMessage('Typing whisper received:', e);
                if (e.userId != this.currentUserId) {
                    if (e.typing) {
                        this.showTypingIndicator(e.userName || 'User');
                    } else {
                        // Hide typing indicator after a delay
                        setTimeout(() => {
                            this.hideTypingIndicator();
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
            const pusherChannel = window.Echo.connector.pusher.channel(`presence-chat.${this.chatId}`);
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

    // Handle typing with WebSocket whisper
    handleTyping() {
        clearTimeout(this.typingTimer);
        
        if (!this.channelReady) {
            // successMessage('Channel not ready yet, retrying...');
            setTimeout(() => this.handleTyping(), 500);
            return;
        }
        
        if (!this.isTyping) {
            this.startTyping();
        }
        
        // Set timer to stop typing indicator
        this.typingTimer = setTimeout(() => {
            this.stopTyping();
        }, this.typingDelay);
    }

    startTyping() {
        this.isTyping = true;
        
        this.whisperTyping(true);
    }

    stopTyping() {
        this.isTyping = false;
        this.whisperTyping(false);
        clearTimeout(this.typingTimer);
    }

    // Send typing status via WebSocket whisper
    whisperTyping(isTyping) {
        if (!this.presenceChannel || !this.channelReady) {
            console.log('Presence channel not ready, attempting to initialize...');
            this.initializePresenceChannel();
            return;
        }

        try {
            this.presenceChannel.whisper('typing', {
                userId: this.currentUserId,
                userName: this.currentUserName || 'User',
                typing: isTyping
            });
            console.log('Typing whisper sent:', isTyping);
        } catch (error) {
            console.error('Whisper error:', error);
        }
    }

    // Show typing indicator for the opposite user
    showTypingIndicator(userName) {
        const typingArea = document.getElementById('typingArea');
        const typingUserName = document.getElementById('typingUserName');
        
        if (typingArea && typingUserName) {
            typingUserName.textContent = userName;
            typingArea.style.display = 'block';
        }
    }

    // Hide typing indicator
    hideTypingIndicator() {
        const typingArea = document.getElementById('typingArea');
        if (typingArea) {
            typingArea.style.display = 'none';
        }
    }

    // Get current user ID from the page
    getCurrentUserId() {
        // Try to get user ID from meta tag or hidden input
        const userIdMeta = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
        if (userIdMeta) return userIdMeta;
        
        // Fallback: try to get from a hidden input if available
        const userIdInput = document.getElementById('current_user_id')?.value;
        if (userIdInput) return userIdInput;
        
        // If no user ID found, return null
        console.warn('Current user ID not found');
        return null;
    }

    // Get current user name
    getCurrentUserName() {
        // Try to get user name from meta tag or hidden input
        const userNameMeta = document.querySelector('meta[name="user-name"]')?.getAttribute('content');
        if (userNameMeta) return userNameMeta;
        
        // Fallback: try to get from a hidden input if available
        const userNameInput = document.getElementById('current_user_name')?.value;
        if (userNameInput) return userNameInput;
        
        // If no user name found, return default
        return 'User';
    }

    // Function to handle chat switching
    // switchChat(newChatUniqueId,newChatId) {
    //     // Clear typing status for the previous chat
    //     if (this.chatId && this.chatId !== newChatId) {
    //         this.stopTyping();
    //     }
        
    //     // Leave previous presence channel
    //     if (this.presenceChannel) {
    //         try {
    //             window.Echo.leave(`presence-chat.${this.chatId}`);
    //         } catch (e) {
    //             // Ignore errors when leaving
    //         }
    //     }
        
    //     // Update chat ID
    //     this.chatId = newChatId;
    //     this.chatUniqueId = newChatUniqueId;
        
    //     // Hide typing indicator when switching chats
    //     this.hideTypingIndicator();
        
    //     // Initialize new presence channel
    //     this.initializePresenceChannel();
    // }

    // Function to clear typing status when leaving chat
    leaveChat() {
        this.stopTyping();
        this.hideTypingIndicator();
        
        // Leave presence channel
        if (this.presenceChannel) {
            try {
                window.Echo.leave(`presence-chat.${this.chatId}`);
            } catch (e) {
                // Ignore errors when leaving
            }
        }
    }

    scrollToBottom() {
        const messagesContainer = document.getElementById('messagesContainer');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }

    showLoader() {
        // Implement loader display
        console.log('Loading...');
    }

    hideLoader() {
        // Implement loader hide
        console.log('Loading complete');
    }

    showSuccess(message) {
        // Implement success message display
        console.log('Success:', message);
        if (typeof successMessage === 'function') {
            successMessage(message);
        }
    }

    showError(message) {
        // Implement error message display
        console.log('Error:', message);
        if (typeof errorMessage === 'function') {
            errorMessage(message);
        }
    }

    showInfo(message) {
        // Implement info message display
        console.log('Info:', message);
        if (typeof infoMessage === 'function') {
            infoMessage(message);
        } else {
            // Fallback to alert if infoMessage function doesn't exist
            errorMessage(message);
        }
    }

    // Force open options menu (fallback method)
    forceOpenOptionsMenu() {
        const optionsMenu = document.getElementById('optionsMenu');
        if (optionsMenu) {
            // Remove any conflicting classes first
            optionsMenu.classList.remove('active');
            
            // Force the active state
            optionsMenu.classList.add('active');
            
            // Also set inline styles as backup
            optionsMenu.style.opacity = '1';
            optionsMenu.style.transform = 'translateY(0)';
            optionsMenu.style.pointerEvents = 'all';
            optionsMenu.style.visibility = 'visible';
            
            console.log('Options menu forced open with inline styles');
            this.verifyMenuState();
        }
    }

    // Handle potential race conditions in menu state
    handleMenuRaceCondition() {
        const optionsMenu = document.getElementById('optionsMenu');
        if (optionsMenu) {
            // Check if there's a mismatch between class and computed styles
            const hasActiveClass = optionsMenu.classList.contains('active');
            const computedStyle = window.getComputedStyle(optionsMenu);
            const isActuallyVisible = computedStyle.opacity === '1' && computedStyle.pointerEvents === 'all';
            
            if (hasActiveClass && !isActuallyVisible) {
                console.warn('Race condition detected: class active but not visible');
                // Force the styles to match the class
                this.forceOpenOptionsMenu();
            } else if (!hasActiveClass && isActuallyVisible) {
                console.warn('Race condition detected: visible but no active class');
                // Force the class to match the styles
                optionsMenu.classList.add('active');
            }
        }
    }

    // Close options menu
    closeOptionsMenu() {
        const optionsMenu = document.getElementById('optionsMenu');
        if (optionsMenu && optionsMenu.classList.contains('active')) {
            optionsMenu.classList.remove('active');
            // Reset inline styles
            optionsMenu.style.opacity = '';
            optionsMenu.style.transform = '';
            optionsMenu.style.pointerEvents = '';
            optionsMenu.style.visibility = '';
            console.log('Options menu closed');
        }
    }

    // Check if click is outside the options menu area
    isClickOutsideOptionsMenu(target) {
        const optionsMenu = document.getElementById('optionsMenu');
        const optionsBtn = document.getElementById('optionsBtn');
        const optionsWrapper = document.querySelector('.CdsIndividualChat-header-options-wrapper');
        
        // Check if target is inside any of these elements
        const isInsideMenu = optionsMenu && optionsMenu.contains(target);
        const isInsideButton = optionsBtn && optionsBtn.contains(target);
        const isInsideWrapper = optionsWrapper && optionsWrapper.contains(target);
        
        // Debug logging
        console.log('Click outside check:', {
            target: target,
            targetClass: target.className,
            targetId: target.id,
            isInsideMenu,
            isInsideButton,
            isInsideWrapper,
            isOutside: !isInsideMenu && !isInsideButton && !isInsideWrapper
        });
        
        // Return true if click is outside all of them
        return !isInsideMenu && !isInsideButton && !isInsideWrapper;
    }

    // Ensure menu is properly positioned
    ensureMenuPosition() {
        const optionsMenu = document.getElementById('optionsMenu');
        const optionsBtn = document.getElementById('optionsBtn');
        
        if (optionsMenu && optionsBtn) {
            // Ensure menu is positioned relative to the button
            const buttonRect = optionsBtn.getBoundingClientRect();
            const menuRect = optionsMenu.getBoundingClientRect();
            
            console.log('Menu positioning:', {
                buttonRect: {
                    top: buttonRect.top,
                    left: buttonRect.left,
                    width: buttonRect.width,
                    height: buttonRect.height
                },
                menuRect: {
                    top: menuRect.top,
                    left: menuRect.left,
                    width: menuRect.width,
                    height: menuRect.height
                }
            });
        }
    }
}

// Global functions for compatibility
function closeReplyTo() {
    if (window.individualChatManager) {
        window.individualChatManager.hideReplyTo();
    }
}

// Global function for message options dropdown
function toggleMessageOptions(toggleButton) {
    if (window.individualChatManager) {
        window.individualChatManager.toggleMessageOptions(toggleButton);
    }
}

// Global function for toggle clear chat
function toggleClearChat() {
    var sentMsgCount = $('.CdsIndividualChat-messages-container .sent').length;
    var rcvdMsgCount = $('.CdsIndividualChat-messages-container .received').length;
    if (sentMsgCount > 0 || rcvdMsgCount > 0) {
        $('#clear-messages').find('#selectAllDiv').show();
        $('#clear-messages').find('.clear-checkbox').show();
        $('#clear-messages').find('#clearChatBtn').show();
    }
}

function removeIndividualChatReaction(emojiICon) {
    if (window.individualChatManager) {
        window.individualChatManager.removeIndividualChatReaction(emojiICon);
    }
}

// Initialize when document is ready
$(document).ready(() => {
    // Add custom dropdown styles
    window.individualChatManager = new IndividualChatManager();
    
    // Clear typing status when user navigates away
    window.addEventListener('beforeunload', () => {
        if (window.individualChatManager) {
            window.individualChatManager.stopTyping();
            window.individualChatManager.hideTypingIndicator();
            window.individualChatManager.leaveChat();
        }
    });
});


function unblockChat(getChatId) {
    if (window.individualChatManager) {
        window.individualChatManager.unblockChat(getChatId);
    }   
}


function blockChat(getChatId) {
    if (window.individualChatManager) {
        window.individualChatManager.blockChat(getChatId);
    }   
}


$(document).on('click', '#CdsIndividualChat-clear-chat', function() {
        var sentMsgCount = $('.CdsIndividualChat-messages-container .sent').length;
        var rcvdMsgCount = $('.CdsIndividualChat-messages-container .received').length;
        if (sentMsgCount > 0 || rcvdMsgCount > 0) {
            $('#clear-messages').find('#selectAllDiv').show();
            $('#clear-messages').find('.clear-checkbox').show();
            $('#clear-messages').find('#clearChatBtn').show();
        }
    });

// Clear chat functionality
$(document).on('click', '#clearChatBtn', function(e) {
    e.preventDefault();
    var clear_msg = Array.from(
        $('#clear-messages input[name="clear_msg[]"]:checked').map(
            function () {
                return $(this).val();
            }
        )
    );
    
    if (clear_msg.length === 0) {
        errorMessage('Please select messages to clear');
        return;
    }
    
    var chatId = $('#get_chat_id').val();
    var myurl = BASEURL + "/individual-chats/clear-messages/" + chatId;

    $.ajax({
        type: "post",
        url: myurl,
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            clear_msg: clear_msg,
        },
        success: function (data) {
            var idsToRemove = data.msgIds;
            idsToRemove.forEach(function (id) {
                $('.CdsIndividualChat-messages-container #message-' + id).remove();
            });
            
            $('#clear-messages').find('#selectAllDiv').hide();
            $('#clear-messages').find('.clear-checkbox').hide();
            $('#clear-messages').find('#clearChatBtn').hide();

            var message_count = data.message_count;
            if (message_count < 1) {
                $('.CdsIndividualChat-messages-container').find('#messages_read').html(
                    '<div class="welcome-chat"><h5>No message yet</h5></div>'
                );
            }

            successMessage(data.message);
        },
        error: function(xhr, status, error) {
            // console.error('Error clearing messages:', error);
            errorMessage('Error clearing messages. Please try again.');
        }
    });
});

// Cancel clear functionality
$(document).on('click', '#cancelClear', function() {
    $('#clear-messages').find('#selectAllDiv').hide();
    $('#clear-messages').find('.clear-checkbox').hide();
    $('#clear-messages').find('#clearChatBtn').hide();

    $('#clear-messages').find('.select-message, #selectAll').prop("checked", false);
});

// Select all functionality
$(document).on('change', '#selectAll', function() {
    var isChecked = $(this).is(':checked');
    $('#clear-messages').find('.select-message').prop('checked', isChecked);
});