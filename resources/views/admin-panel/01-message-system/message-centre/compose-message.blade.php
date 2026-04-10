@extends('components.custom-popup', ['modalTitle' => $pageTitle])

<style>
   
</style>

@section('custom-popup-content')
<div class="CdsDashboardCustomPopup-modal-form-wrapper">
    <div class="CdsDashboardCustomPopup-modal-form-grid">
        <div class="CdsDashboardCustomPopup-modal-left-column">
            <form id="compose-message-form" enctype="multipart/form-data" method="post">
                @csrf
                
                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label" for="recipient">
                        Select Recipients <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <div class="CdsDashboardCustomPopup-modal-search-wrapper">
                        <svg class="CdsDashboardCustomPopup-modal-search-icon" width="20" height="20"
                            viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <input type="text" class="CdsDashboardCustomPopup-modal-search-input"
                            id="searchRecipientInput" placeholder="Search Recipients..."
                            onkeyup="filterRecipients()">
                    </div>
                    <div class="CdsDashboardCustomPopup-modal-bulk-actions" style="margin: 10px 0; display: flex; gap: 10px;">
                        <button type="button" class="CdsDashboardCustomPopup-modal-btn-secondary" onclick="selectAllRecipients()">
                            <i class="fas fa-check-double"></i> Select All
                        </button>
                        <button type="button" class="CdsDashboardCustomPopup-modal-btn-secondary" onclick="clearAllRecipients()">
                            <i class="fas fa-times"></i> Clear All
                        </button>
                    </div>
                    @if($users->count() == 0)
                    <div class="CdsDashboardCustomPopup-modal-connection-hint">
                        <p>No users available in the list. Please <a href="{{ baseUrl('connections/connect') }}"
                                target="_blank">add connections</a> by clicking on the link.</p>
                    </div>
                    @endif
                    <div class="CdsDashboardCustomPopup-modal-recipient-list" id="recipientsList">
                        @foreach($users as $user)
                        <div class="CdsDashboardCustomPopup-modal-recipient-item recipient-item"
                            data-name="{{ strtolower($user->first_name.' '.$user->last_name) }}"
                            data-id="{{ $user->id }}" data-chat-id="{{ $user->chat_id }}">
                            <input type="checkbox" id="recipient-{{$user->id}}"
                                class="CdsDashboardCustomPopup-modal-recipient-checkbox recipients"
                                value="{{$user->id}}" name="recipient_ids[]">
                            <div class="CdsDashboardCustomPopup-modal-recipient-avatar">
                                @if($user->profile_image)
                                <img src="{{ userDirUrl($user->profile_image, 'm') }}"
                                    alt="{{ $user->first_name }} {{ $user->last_name }}">
                                @else
                                @php
                                $initial = strtoupper(substr($user->first_name, 0, 1)) .
                                strtoupper(substr($user->last_name, 0, 1));
                                @endphp
                                <div class="user-icon">
                                    {{ $initial }}
                                </div>
                                @endif
                                @if($user->is_login)
                                <span class="status-online"></span>
                                @else
                                <span class="status-offline"></span>
                                @endif
                            </div>
                            <div class="CdsDashboardCustomPopup-modal-recipient-info">
                                <p class="CdsDashboardCustomPopup-modal-recipient-name">
                                    {{$user->first_name." ".$user->last_name}}</p>
                                <p class="CdsDashboardCustomPopup-modal-recipient-email">{{$user->email}}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="CdsDashboardCustomPopup-modal-selected-count" id="selectedRecipientsCount" style="display: none;">
                        <span class="badge bg-primary" style="font-size: 12px; padding: 6px 12px; margin-top: 8px; display: inline-block;">
                            <i class="fas fa-users" style="margin-right: 5px;"></i>
                            <span id="recipientsCountText">0 recipients selected</span>
                        </span>
                    </div>
                </div>

                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label" for="message">
                        Message <span class="CdsDashboardCustomPopup-modal-required">*</span>
                    </label>
                    <textarea id="message" name="message" class="CdsDashboardCustomPopup-modal-textarea"
                        placeholder="Type your message here..." rows="6" required></textarea>
                </div>

                <div class="CdsDashboardCustomPopup-modal-form-group">
                    <label class="CdsDashboardCustomPopup-modal-label">Attachments</label>
                    <div class="CdsDashboardCustomPopup-modal-upload-container" id="messageMediaUpload">
                        <div class="CdsDashboardCustomPopup-modal-upload-area" onclick="document.getElementById('fileInput').click()">
                            <input type="file" id="fileInput" class="CdsDashboardCustomPopup-modal-file-input" multiple
                                accept="image/*,.pdf,.doc,.docx" style="display: none;" onchange="handleFileSelect(this)">
                            <div class="CdsDashboardCustomPopup-modal-upload-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                            </div>
                            <p class="CdsDashboardCustomPopup-modal-upload-text">Drag and drop files here or click to browse</p>
                            <p class="CdsDashboardCustomPopup-modal-upload-hint">Supports: JPG, PNG, GIF, PDF, DOC, DOCX (Max 10MB per file)</p>
                        </div>

                        <!-- File Preview Area -->
                        <div class="CdsDashboardCustomPopup-modal-file-list" id="fileList" style="display: none;">
                            <!-- Files will be dynamically added here -->
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('custom-popup-footer')
<div class="CdsDashboardCustomPopup-modal-submit-section">
    <button type="submit" form="compose-message-form" class="CdsDashboardCustomPopup-modal-submit-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20">
            <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"></path>
        </svg>
        <span>Send Message</span>
    </button>
</div>
@endsection



<script>
    $(document).ready(function () {
        // Add interactivity for recipient selection
        document.querySelectorAll('.CdsDashboardCustomPopup-modal-recipient-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const recipientItem = this.closest(
                    '.CdsDashboardCustomPopup-modal-recipient-item');
                
                if (this.checked) {
                    recipientItem.classList.add('CdsDashboardCustomPopup-modal-selected');
                } else {
                    recipientItem.classList.remove('CdsDashboardCustomPopup-modal-selected');
                }
                
                updateSelectedRecipientsCount();
            });
        });
        
        // Function to update selected recipients count
        function updateSelectedRecipientsCount() {
            const selectedCount = document.querySelectorAll('.CdsDashboardCustomPopup-modal-recipient-checkbox:checked').length;
            const countElement = document.getElementById('selectedRecipientsCount');
            const countTextElement = document.getElementById('recipientsCountText');
            
            if (selectedCount > 0) {
                countElement.style.display = 'block';
                countTextElement.textContent = selectedCount + ' recipient' + (selectedCount > 1 ? 's' : '') + ' selected';
            } else {
                countElement.style.display = 'none';
            }
        }

                // File upload functionality is now handled by custom functions
        console.log('File upload system initialized');

        $("#compose-message-form").submit(function (e) {
            e.preventDefault();
            var is_valid = formValidation("compose-message-form");
            if (!is_valid) {
                return false;
            }

            $(".CdsDashboardCustomPopup-modal-recipient-list .errmsg").remove();
            if ($(".recipients:checked").length == 0) {
                $(".CdsDashboardCustomPopup-modal-recipient-list").append(
                    "<div class='text-danger errmsg'>Please select at least one recipient</div>");
                return false;
            }

            var selectedRecipients = $(".recipients:checked");
            var recipientIds = selectedRecipients.map(function() {
                return $(this).val();
            }).get();
            var message = $("#message").val();

            // Get all selected recipients with their chat IDs
            var recipientsWithChatIds = [];
            selectedRecipients.each(function() {
                var recipientItem = $(this).closest('.CdsDashboardCustomPopup-modal-recipient-item');
                var chatId = recipientItem.data('chat-id');
                var recipientId = $(this).val();
                
                if (chatId) {
                    recipientsWithChatIds.push({
                        recipientId: recipientId,
                        chatId: chatId
                    });
                } else {
                    hideLoader();
                    errorMessage('Chat not found for one or more selected recipients');
                    return false;
                }
            });

            if (recipientsWithChatIds.length > 0) {
                // Send message to all selected recipients
                sendComposedMessageToMultipleRecipients(recipientsWithChatIds, message);
            }
        });
    });

    function sendComposedMessageToMultipleRecipients(recipients, message) {
        var totalRecipients = recipients.length;
        var successCount = 0;
        var failureCount = 0;
        var currentIndex = 0;

        function sendToNextRecipient() {
            if (currentIndex >= totalRecipients) {
                // All messages sent, show final result
                hideLoader();
                if (failureCount === 0) {
                    successMessage('Message sent successfully to all ' + totalRecipients + ' recipients!');
                    closeCustomPopup();
                    // Redirect to message centre page - it will automatically open the last chat
                    window.location.href = "{{ baseUrl('individual-chats') }}";
                } else if (successCount === 0) {
                    errorMessage('Failed to send message to any recipients');
                } else {
                    successMessage('Message sent to ' + successCount + ' out of ' + totalRecipients + ' recipients. ' + failureCount + ' failed.');
                    closeCustomPopup();
                    // Redirect to message centre page - it will automatically open the last chat
                    window.location.href = "{{ baseUrl('individual-chats') }}";
                }
                return;
            }

            var recipient = recipients[currentIndex];
            sendComposedMessage(recipient.chatId, message, function(success) {
                if (success) {
                    successCount++;
                } else {
                    failureCount++;
                }
                currentIndex++;
                sendToNextRecipient();
            });
        }

        // Start sending to first recipient
        sendToNextRecipient();
    }

    function sendComposedMessage(chatId, message, callback) {
        var formData = new FormData();
        formData.append('_token', "{{ csrf_token() }}");
        formData.append('send_msg', message);
        formData.append("openfrom", "composeModal");

        // Add attachments if any
        console.log('Files to send:', selectedFiles.length);
        selectedFiles.forEach((file, index) => {
            console.log('Adding file:', file.name, 'Size:', file.size);
            formData.append('attachment[]', file);
        });

        $.ajax({
            url: "{{ baseUrl('individual-chats/send-msg/') }}/" + chatId,
            type: "post",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (response) {
                if (response.status == true) {
                    console.log('Message sent successfully to chat ID:', chatId);
                    if (callback) callback(true);
                } else {
                    console.error('Failed to send message to chat ID:', chatId, response.message);
                    if (callback) callback(false);
                }
            },
            error: function (xhr) {
                console.error('Error sending message to chat ID:', chatId, xhr);
                if (callback) callback(false);
            }
        });
    }

        // Search functionality
    function filterRecipients() {
        const searchInput = document.getElementById("searchRecipientInput").value.toLowerCase();
        const recipientsList = document.getElementById("recipientsList");
        const recipients = recipientsList.getElementsByClassName("recipient-item");

        for (let recipient of recipients) {
            const recipientName = recipient.getAttribute("data-name");
            if (recipientName.includes(searchInput)) {
                recipient.style.display = "flex"; // Show matching recipients
            } else {
                recipient.style.display = "none"; // Hide non-matching recipients
            }
        }
    }

    // Select all recipients function
    function selectAllRecipients() {
        const checkboxes = document.querySelectorAll('.CdsDashboardCustomPopup-modal-recipient-checkbox');
        checkboxes.forEach(checkbox => {
            if (checkbox.closest('.recipient-item').style.display !== 'none') {
                checkbox.checked = true;
                checkbox.closest('.recipient-item').classList.add('CdsDashboardCustomPopup-modal-selected');
            }
        });
        updateSelectedRecipientsCount();
    }

    // Clear all recipients function
    function clearAllRecipients() {
        const checkboxes = document.querySelectorAll('.CdsDashboardCustomPopup-modal-recipient-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
            checkbox.closest('.recipient-item').classList.remove('CdsDashboardCustomPopup-modal-selected');
        });
        updateSelectedRecipientsCount();
    }

    // File handling functions
    let selectedFiles = [];

    function handleFileSelect(input) {
        const files = Array.from(input.files);
        files.forEach(file => {
            if (validateFile(file)) {
                selectedFiles.push(file);
                displayFile(file);
            }
        });
        updateFileList();
        input.value = ''; // Reset input
    }

    function validateFile(file) {
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = [
            'image/jpeg',
            'image/png', 
            'image/gif',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        if (file.size > maxSize) {
            errorMessage(`File ${file.name} is too large. Maximum size is 10MB.`);
            return false;
        }

        if (!allowedTypes.includes(file.type)) {
            errorMessage(`File type not supported for ${file.name}.`);
            return false;
        }

        return true;
    }

    function displayFile(file) {
        const fileList = document.getElementById('fileList');
        const fileItem = document.createElement('div');
        fileItem.className = 'CdsDashboardCustomPopup-modal-file-item';
        fileItem.dataset.fileName = file.name;
        
        const fileIcon = getFileIcon(file.type);
        const fileSize = formatFileSize(file.size);
        
        fileItem.innerHTML = `
            <div class="CdsDashboardCustomPopup-modal-file-icon">${fileIcon}</div>
            <div class="CdsDashboardCustomPopup-modal-file-info">
                <div class="CdsDashboardCustomPopup-modal-file-name">${file.name}</div>
                <div class="CdsDashboardCustomPopup-modal-file-size">${fileSize}</div>
            </div>
            <button type="button" class="CdsDashboardCustomPopup-modal-file-remove" onclick="removeFile('${file.name}')">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        fileList.appendChild(fileItem);
    }

    function removeFile(fileName) {
        selectedFiles = selectedFiles.filter(file => file.name !== fileName);
        const fileItem = document.querySelector(`[data-file-name="${fileName}"]`);
        if (fileItem) {
            fileItem.remove();
        }
        updateFileList();
    }

    function updateFileList() {
        const fileList = document.getElementById('fileList');
        if (selectedFiles.length > 0) {
            fileList.style.display = 'block';
        } else {
            fileList.style.display = 'none';
        }
    }

    function getFileIcon(fileType) {
        if (fileType.startsWith('image/')) {
            return '<i class="fas fa-image"></i>';
        } else if (fileType === 'application/pdf') {
            return '<i class="fas fa-file-pdf"></i>';
        } else if (fileType.includes('word')) {
            return '<i class="fas fa-file-word"></i>';
        } else {
            return '<i class="fas fa-file"></i>';
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Drag and drop functionality
    const uploadArea = document.querySelector('.CdsDashboardCustomPopup-modal-upload-area');
    
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.borderColor = '#2196f3';
        this.style.backgroundColor = '#f0f8ff';
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.style.borderColor = '#ddd';
        this.style.backgroundColor = 'transparent';
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.borderColor = '#ddd';
        this.style.backgroundColor = 'transparent';
        
        const files = Array.from(e.dataTransfer.files);
        files.forEach(file => {
            if (validateFile(file)) {
                selectedFiles.push(file);
                displayFile(file);
            }
        });
        updateFileList();
    });



</script>
