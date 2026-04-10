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
        this.addMessageOptionsStyles();
        this.initializeFilePanel();
        
        // Bind chat-specific events
        this.rebindChatEvents();
        
        this.scrollToBottom();
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
            
            console.log('Sidebar click detected:', {
                element: e.currentTarget,
                chatUniqueId: chatUniqueId,
                dataAttributes: $(e.currentTarget).data()
            });
            
            if (chatUniqueId) {
                this.switchToChat(chatUniqueId,chatId);
            } else {
                console.warn('No chat ID found for clicked element');
            }
        });

        // Message options dropdown events
        // $(document).on('click', '.message-options-dropdown .message-options-toggle', (e) => {
        //     e.preventDefault();
        //     e.stopPropagation();
        //     this.toggleMessageOptions(e.currentTarget);
        // });

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

        // Initialize emoji picker for message reactions
        if(typeof EmojiPicker !== 'undefined'){
            $("#messagesContainer .message-reaction").each(function () {
                var ele_id = $(this).attr("id");
                var message_id = $(this).data("message-id"); // Use data-message-id for individual chat
                var e = $(this);

                if (ele_id && message_id) {
                    new EmojiPicker("#" + ele_id, {
                        onEmojiSelect: (selectedEmoji) => {
                            $.ajax({
                                url: BASEURL + "/individual-chats/add-reaction",
                                type: "POST",
                                data: {
                                    _token: this.csrfToken,
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
                }
            });
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
                    
                    // this.loadChatSidebar();
                    // this.loadChatMessages(this.chatUniqueId);
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
        // Clear file preview container if it exists
        const previewContainer = document.getElementById('filePreviewContainer');
        if (previewContainer) {
            previewContainer.innerHTML = '';
        }
    }

    // File Management Methods
    initializeFilePanel() {
        this.currentFilePage = 1;
        this.fileSearchQuery = '';
        this.loadChatFiles();
    }

    // Load chat files
    loadChatFiles(page = 1, search = '') {
        if (!this.chatId) return;

        const url = BASEURL + '/individual-chats/chat-files/' + this.chatId;
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
        this.loadChatFiles();
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
        this.loadChatFiles();
    }

    // Search files
    searchFiles() {
        const searchInputElement = document.getElementById('search-file-input');
        if (searchInputElement) {
            const searchQuery = searchInputElement.value.trim();
            this.fileSearchQuery = searchQuery;
            this.currentFilePage = 1;
            this.loadChatFiles(1, searchQuery);
        }
    }

    // Load more files
    loadMoreFiles() {
        this.currentFilePage++;
        this.loadChatFiles(this.currentFilePage, this.fileSearchQuery);
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
            
            console.log('Message IDs initialized from template:', {
                lastMessageId: this.lastMessageId,
                firstMessageId: this.firstMessageId
            });
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
    switchToChat(chatUniqueId,chatId) {
        if (!chatUniqueId || chatUniqueId === this.chatUniqueId) {
            console.log('Same chat or invalid ID, not switching');
            return; // Same chat or invalid ID
        }

        console.log('Switching to chat:', chatUniqueId, 'Chat ID:', chatId);

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
            const newUrl = window.location.pathname.replace(/\/[^\/]+$/, '') + '/' + chatUniqueId;
            window.history.pushState({chatId: chatUniqueId}, '', newUrl);
        }

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
                    
                    // Scroll to bottom
                    this.scrollToBottom();
                    this.loadChatFiles()
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
    loadChatMessages(chatUniqueId) {
        $.ajax({
            url: BASEURL+'/individual-chats/load-messages/'+chatUniqueId,
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
                    $("#messagesContainer").html(response.message);
                    
                    // Re-bind events for new chat elements
                    this.rebindChatEvents();
                    
                    // Scroll to bottom
                    this.scrollToBottom();
                    
                    console.log('Chat switched successfully:', {
                        chatUniqueId: chatUniqueId,
                        lastMessageId: this.lastMessageId,
                        firstMessageId: this.firstMessageId
                    });
                } else {
                    this.showError(response.message || 'Failed to load chat messages');
                }
            },
            error: (xhr, status, error) => {
                this.showError('Failed to load chat messages: ' + error);
            }
        });
    }

    // Function to re-bind events after chat switching
    rebindChatEvents() {
        console.log('Rebinding chat events...');
        
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
        
        console.log('Chat events rebound successfully');
    }

    // Function to bind header button events
    bindHeaderButtonEvents() {
        // Search button
        const searchBtn = document.getElementById('searchBtn');
        if (searchBtn) {
            searchBtn.onclick = () => this.toggleChatSearch();
        }
        
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
        
        // User button (if needed)
        const userBtn = document.getElementById('userBtn');
        if (userBtn) {
            userBtn.onclick = () => this.handleUserButton();
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
            messageInput.onkeypress = (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            };
            
            messageInput.onkeydown = (e) => {
                if (e.key === 'Escape') {
                    const editMessageId = document.getElementById('edit_message_id')?.value;
                    if (editMessageId) {
                        e.preventDefault();
                        this.cancelEditMode();
                    }
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
        // Close options menu on outside click
        document.addEventListener('click', (e) => {
            const optionsMenu = document.getElementById('optionsMenu');
            if (optionsMenu && !e.target.closest('.CdsIndividualChat-header-options-wrapper')) {
                optionsMenu.classList.remove('active');
            }
        });
        
        // Options menu items
        const optionsMenu = document.getElementById('optionsMenu');
        if (optionsMenu) {
            optionsMenu.addEventListener('click', (e) => {
                const option = e.target.closest('.CdsIndividualChat-option-item');
                if (option) {
                    this.handleOptionClick(option.textContent.trim());
                }
            });
        }
    }

    // Toggle chat search
    toggleChatSearch() {
        console.log('Toggle chat search called');
        const chatSearchBar = document.getElementById('chatSearchBar');
        if (chatSearchBar) {
            chatSearchBar.classList.toggle('active');
            if (chatSearchBar.classList.contains('active')) {
                const searchInput = document.getElementById('chatSearchInput');
                if (searchInput) {
                    searchInput.focus();
                }
            }
            console.log('Chat search active:', chatSearchBar.classList.contains('active'));
        } else {
            console.log('Chat search bar not found');
        }
    }

    // Close chat search
    closeChatSearch() {
        const chatSearchBar = document.getElementById('chatSearchBar');
        if (chatSearchBar) {
            chatSearchBar.classList.remove('active');
        }
    }

    // Toggle options menu
    toggleOptionsMenu() {
        console.log('Toggle options menu called');
        const optionsMenu = document.getElementById('optionsMenu');
        if (optionsMenu) {
            optionsMenu.classList.toggle('active');
            console.log('Options menu active:', optionsMenu.classList.contains('active'));
        } else {
            console.log('Options menu element not found');
        }
    }

    // Open files panel
    openFilesPanel() {
        console.log('Open files panel called');
        const filesPanel = document.getElementById('filesPanel');
        const overlay = document.getElementById('overlay');
        if (filesPanel && overlay) {
            filesPanel.classList.add('active');
            overlay.classList.add('active');
            console.log('Files panel opened');
        } else {
            console.log('Files panel or overlay not found');
        }
    }

    // Close files panel
    closeFilesPanel() {
        const filesPanel = document.getElementById('filesPanel');
        const overlay = document.getElementById('overlay');
        if (filesPanel && overlay) {
            filesPanel.classList.remove('active');
            overlay.classList.remove('active');
        }
    }

    // Handle user button click
    handleUserButton() {
        console.log('User button clicked');
        // Add your user button functionality here
    }

    // Handle option click
    handleOptionClick(option) {
        console.log('Option clicked:', option);
        switch(option) {
            case 'Block Chat':
                if (confirm('Are you sure you want to block this chat?')) {
                    console.log('Chat blocked');
                }
                break;
            case 'Clear Chat':
                if (confirm('Are you sure you want to clear this chat? This action cannot be undone.')) {
                    const messagesContainer = document.getElementById('messagesContainer');
                    if (messagesContainer) {
                        messagesContainer.innerHTML = '';
                    }
                    console.log('Chat cleared');
                }
                break;
            case 'Delete Chat':
                if (confirm('Are you sure you want to delete this chat? This action cannot be undone.')) {
                    console.log('Chat deleted');
                }
                break;
        }
        this.toggleOptionsMenu();
    }

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
        if (!chatId) {
            console.warn('Chat ID not available for socket initialization');
            return;
        }

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
                // Leave any existing individual-chat channel
                window.Echo.leave(`individual-chat.${this.chatId}`);
                
                // Join the new individual-chat channel
                const individualChatChannel = window.Echo.private(`individual-chat.${chatId}`);
                 // Listen for ChatSocket events (same as chatapp.js)
                individualChatChannel.listen('ChatSocket', (e) => {
                    console.log('ChatSocket event received on individual-chat channel:', e);
                    this.handleChatSocketEvent(e);
                });
                window.Echo.leave(`chatMessageReaction.` + chatId);
                const messageReaction = window.Echo.private(`individual-chat-reaction.${chatId}`);
                messageReaction.listen('MessageReactionAdded', (e) => {
                    this.handleMessageReaction(e); 
                });
               

                console.log('Successfully joined individual-chat channel:', `individual-chat.${chatId}`);
            } catch (error) {
                console.error('Error joining individual-chat channel:', error);
            }
        } else {
            console.warn('Echo not available for socket initialization');
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
                reactionsContainer.append(messageHtml);
            } else {
                // Create reactions container if it doesn't exist
                const newReactionsHtml = `<div class="CdsIndividualChat-message-reactions">${messageHtml}</div>`;
                messageElement.find('.CdsIndividualChat-message').append(newReactionsHtml);
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
    // Handle ChatSocket events (same flow as chatapp.js)
    handleChatSocketEvent(e) {
        const response = e.data;
        const chatId = this.chatId;
        // Check if this is the active chat
        if (this.chatId == chatId) {
            console.log('Processing ChatSocket event for active chat:', response);
            if (response.action == "new_message") {
                this.handleNewMessageAction(response);
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
        }
    }

    // Handle new message action
    handleNewMessageAction(response) {
        if (response.last_message_id !== this.lastMessageId) {
            // Fetch only new messages instead of reloading all
            this.fetchNewMessages(this.chatUniqueId, this.lastMessageId);
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
    fetchNewMessages(chatUniqueId, lastMessageId) {
        $.ajax({
            url: BASEURL+'/individual-chats/load-messages/'+chatUniqueId,
            type: 'POST',
            data: {
                _token: this.csrfToken,
                chat_id: chatUniqueId,
                last_msg_id: lastMessageId, // Only fetch messages after this ID
            },
            success: (response) => {
                if (response.status === true) {
                    // Update message IDs
                    this.lastMessageId = response.last_msg_id || 0;
                    this.firstMessageId = response.first_msg_id || 0;
                    
                    // Append new messages to the container instead of replacing
                    if (response.message && response.message.trim() !== '') {
                        $("#messagesContainer").append(response.message);
                        
                        // Re-bind events for new message elements
                        this.rebindChatEvents();
                        
                        // Re-initialize emoji pickers for new messages
                        this.initializeEmojiPicker();
                        
                        // Scroll to bottom to show new messages
                        this.scrollToBottom();
                        
                        console.log('New messages appended successfully:', {
                            chatUniqueId: chatUniqueId,
                            lastMessageId: this.lastMessageId,
                            firstMessageId: this.firstMessageId
                        });
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
            const message_ids = response.message_id.split(",");
            
            for (let i = 0; i < message_ids.length; i++) {
                $(`#messagesContainer #message-${message_ids[i]}`)
                    .find(".readtrack")
                    .html('<i class="fa-sharp fa-solid fa-check-double text-primary"></i>');
            }
        }
        
        if (response.unread_count > 0) {
            $(".chat-message-count").html(response.unread_count);
        } else {
            $(".chat-message-count").html("");
        }
        
        // Refresh chat sidebar
        this.loadChatSidebar();
    }

    // Handle message edited action
    handleMessageEditedAction(response) {
        const messageUniqueId = response.messageUniqueId;
        
        $(`#messagesContainer #editedMsg${messageUniqueId}`).html("edited");
        $(`#messagesContainer #cpMsg${messageUniqueId}`).html(response.editedMessage);
        
        // Refresh chat sidebar
        this.loadChatSidebar();
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
        if (confirm('Are you sure you want to delete this message for yourself?')) {
            $.ajax({
                url: BASEURL + '/individual-chats/delete-message-for-me/'+this.chatId,
                type: 'POST',
                data: {
                    _token: this.csrfToken,
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
        
        if (confirm('Are you sure you want to delete this message for everyone? This action cannot be undone.')) {
            $.ajax({
                url: BASEURL + '/individual-chats/delete-message-for-all/'+messageId,
                type: 'POST',
                data: {
                    _token: this.csrfToken,
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
                <button class="message-options-toggle" data-option-id="${messageUniqueId}" type="button" onclick="toggleMessageOptions(this)">
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
        
        // Close all other dropdowns first
        this.closeAllMessageOptions();
        
        // Toggle current dropdown
        if (menu.is(':visible')) {
            menu.hide();
        } else {
            menu.show();
            
            // Position the dropdown properly
            this.positionDropdown(dropdown, toggleButton);
        }
    }

    // Close all message options dropdowns
    closeAllMessageOptions() {
        $('.message-options-menu').hide();
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

    // Add custom styles for message options dropdown
    addMessageOptionsStyles() {
        const styleId = 'message-options-styles';
        if (document.getElementById(styleId)) {
            return; // Styles already added
        }

        const style = document.createElement('style');
        style.id = styleId;
        style.textContent = `
            .message-options-dropdown {
                position: relative;
                display: inline-block;
            }

            .message-options-toggle {
                background: #007bff;
                color: white;
                border: none;
                border-radius: 4px;
                padding: 4px 8px;
                cursor: pointer;
                font-size: 12px;
                transition: background-color 0.2s;
            }

            .message-options-toggle:hover {
                background: #0056b3;
            }

            .message-options-menu {
                position: absolute;
                background: white;
                border: 1px solid #ddd;
                border-radius: 4px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                padding: 5px 0;
                margin: 0;
                list-style: none;
                min-width: 150px;
                z-index: 1000;
            }

            .message-option-item {
                display: block;
                padding: 8px 15px;
                color: #333;
                text-decoration: none;
                transition: background-color 0.2s;
                font-size: 14px;
            }

            .message-option-item:hover {
                background-color: #f8f9fa;
                color: #333;
                text-decoration: none;
            }

            .message-option-item i {
                margin-left: 8px;
                float: right;
            }

            .CdsIndividualChat-message-options {
                position: absolute;
                top: 5px;
                right: 5px;
                opacity: 0;
                transition: opacity 0.2s;
            }

            .CdsIndividualChat-message-wrapper:hover .CdsIndividualChat-message-options {
                opacity: 1;
            }

            /* Edit mode styles */
            .messageInput.editing {
                border-color: #007bff;
                background-color: #f8f9fa;
            }

            .sendBtn.editing {
                background-color: #28a745 !important;
                border-color: #28a745 !important;
            }

            .sendBtn.editing:hover {
                background-color: #218838 !important;
                border-color: #1e7e34 !important;
            }

            /* File Panel Styles */
            .CdsIndividualChat-files-section-header {
                cursor: pointer;
                padding: 10px 0;
                border-bottom: 1px solid #e5e7eb;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .CdsIndividualChat-files-section-header:hover {
                background-color: #f9fafb;
            }

            .CdsIndividualChat-files-search {
                padding: 15px 0;
                border-bottom: 1px solid #e5e7eb;
            }

            .CdsIndividualChat-files-search-block {
                position: relative;
                display: flex;
                align-items: center;
            }

            .CdsIndividualChat-files-search-icon {
                position: absolute;
                left: 10px;
                color: #6b7280;
            }

            .CdsIndividualChat-files-search-input {
                padding-left: 35px;
                padding-right: 35px;
                border: 1px solid #d1d5db;
                border-radius: 6px;
                width: 100%;
            }

            .CdsIndividualChat-files-clear-text {
                position: absolute;
                right: 10px;
                color: #6b7280;
                text-decoration: none;
            }

            .CdsIndividualChat-files-action-btn {
                margin-top: 10px;
                text-align: right;
            }

            .CdsIndividualChat-files-loading {
                text-align: center;
                padding: 20px;
                color: #6b7280;
            }

            .CdsIndividualChat-files-loading-spinner {
                border: 2px solid #f3f3f3;
                border-top: 2px solid #3498db;
                border-radius: 50%;
                width: 20px;
                height: 20px;
                animation: spin 1s linear infinite;
                margin: 0 auto 10px;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            .CdsIndividualChat-files-empty {
                text-align: center;
                padding: 40px 20px;
                color: #6b7280;
            }

            .CdsIndividualChat-files-empty-icon {
                font-size: 48px;
                margin-bottom: 15px;
                opacity: 0.5;
            }

            .CdsIndividualChat-files-load-more {
                text-align: center;
                padding: 20px;
            }

            .CdsIndividualChat-files-load-more-btn {
                background-color: #3b82f6;
                color: white;
                border: none;
                padding: 8px 16px;
                border-radius: 6px;
                cursor: pointer;
            }

            .CdsIndividualChat-files-load-more-btn:hover {
                background-color: #2563eb;
            }

            .CdsIndividualChat-file-item {
                display: flex;
                align-items: center;
                padding: 10px;
                border-bottom: 1px solid #f3f4f6;
                transition: background-color 0.2s;
            }

            .CdsIndividualChat-file-item:hover {
                background-color: #f9fafb;
            }

            .CdsIndividualChat-file-preview {
                width: 50px;
                height: 50px;
                margin-right: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #f3f4f6;
                border-radius: 8px;
                overflow: hidden;
            }

            .CdsIndividualChat-file-image {
                width: 100%;
                height: 100%;
                object-fit: cover;
                cursor: pointer;
            }

            .CdsIndividualChat-file-pdf,
            .CdsIndividualChat-file-audio,
            .CdsIndividualChat-file-video,
            .CdsIndividualChat-file-excel,
            .CdsIndividualChat-file-generic {
                text-align: center;
                width: 100%;
            }

            .CdsIndividualChat-file-icon {
                width: 24px;
                height: 24px;
                margin-bottom: 5px;
            }

            .CdsIndividualChat-file-ext {
                font-size: 10px;
                color: #6b7280;
                text-transform: uppercase;
                font-weight: 500;
            }

            .CdsIndividualChat-file-info {
                flex: 1;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .CdsIndividualChat-file-name {
                font-size: 14px;
                color: #374151;
                max-width: 200px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .CdsIndividualChat-file-actions {
                display: flex;
                gap: 8px;
            }

            .CdsIndividualChat-file-preview-btn,
            .CdsIndividualChat-file-download-btn {
                background: none;
                border: none;
                padding: 5px;
                border-radius: 4px;
                cursor: pointer;
                color: #6b7280;
                text-decoration: none;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 30px;
                height: 30px;
            }

            .CdsIndividualChat-file-preview-btn:hover,
            .CdsIndividualChat-file-download-btn:hover {
                background-color: #f3f4f6;
                color: #374151;
            }
        `;
        document.head.appendChild(style);
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
                console.log('Successfully joined presence channel, users:', users);
                this.channelReady = true;
            })
            .joining((user) => {
                console.log('User joining:', user);
            })
            .leaving((user) => {
                console.log('User leaving:', user);
                this.hideTypingIndicator();
            })
            .listenForWhisper('typing', (e) => {
                console.log('Typing whisper received:', e);
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
            console.log('Channel not ready yet, retrying...');
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
