<!-- Active Chat State (Hidden when no chat is selected) -->
@if(isset($chat->addedBy) && $chat->addedBy->id!=auth()->user()->id)
    @php
    $chat_with=$chat->addedBy;
    @endphp
@else
    @php
    $chat_with=$chat->chatWith;
    @endphp
@endif
<div class="CdsIndividualChat-active-chat">
    <div class="CdsIndividualChat-chat-header">
        <div class="CdsIndividualChat-header-left">
            <div class="CdsIndividualChat-header-avatar">
                 {!! getProfileImage($chat_with->unique_id,'s',52) !!}
                <!-- <div class="user-icon" data-initial="{{ userInitial($chat_with) }}"></div> -->
            </div>
            <div class="CdsIndividualChat-header-info">
                <div class="CdsIndividualChat-header-name">{{$chat_with->first_name." ".$chat_with->last_name}}</div>
                <div class="CdsIndividualChat-header-status">
                    @if(loginStatus($chat_with) == 1)
                    <span class="chatOnlineStatus{{$chat->id}}">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                            <line x1="9" y1="9" x2="9.01" y2="9"></line>
                            <line x1="15" y1="9" x2="15.01" y2="9"></line>
                        </svg>
                        Online
                    </span>
                    @else
                    <span class="status-offline login-status chatOnlineStatus{{$chat->id}}">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                            <line x1="9" y1="9" x2="9.01" y2="9"></line>
                            <line x1="15" y1="9" x2="15.01" y2="9"></line>
                        </svg>
                        Offline
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="CdsIndividualChat-header-actions">
            <button class="CdsIndividualChat-header-btn" id="searchToggleBtn" title="Search messages">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
            </button>
            <button class="CdsIndividualChat-header-btn" id="userBtn" onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('individual-chats/get-user-profile/'.$chat->unique_id) }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </button>
            <button class="CdsIndividualChat-header-btn" id="filesBtn" onclick="showRightSlidePanel(this)" data-href="{{ baseUrl('individual-chats/chat-files/'.$chat->unique_id) }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
            </button>
            <div class="CdsIndividualChat-header-options-wrapper">
                <button class="CdsIndividualChat-header-btn" id="optionsBtn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="1"></circle>
                        <circle cx="12" cy="5" r="1"></circle>
                        <circle cx="12" cy="19" r="1"></circle>
                    </svg>
                </button>
                <!-- Options Menu -->
                <div class="CdsIndividualChat-options-menu" id="optionsMenu">

                    @if($chat->blocked_chat==1 && $chat->blocked_by==auth()->user()->id)
                        <div class="CdsIndividualChat-option-item" onclick="unblockChat('{{$chat->id}}')">
                            <span class="CdsIndividualChat-option-icon">🚫</span>
                            Unblock Chat
                        </div>
                    @else
                     @if($chat->blocked_chat==1 && $chat->blocked_by!=auth()->user()->id )
                        @else
                    <div class="CdsIndividualChat-option-item" onclick="blockChat('{{$chat->id}}')">
                        <span class="CdsIndividualChat-option-icon">🚫</span>
                        Block Chat
                    </div>
                    @endif
                    @endif
                    <div class="CdsIndividualChat-option-item" id="CdsIndividualChat-clear-chat" onclick="toggleClearChat()">
                        <span class="CdsIndividualChat-option-icon">🔄</span>
                        Clear Chat
                    </div>
                    <div class="CdsIndividualChat-option-item danger" onclick="confirmAnyAction(this)" data-href="{{ baseUrl('individual-chats/delete-chat/'.$chat->unique_id) }}" data-action="delete chat">
                        <span class="CdsIndividualChat-option-icon">🗑️</span>
                        Delete Chat
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Search Bar -->
    <div class="CdsIndividualChat-chat-search" id="searchBar" style="display: none;">
        <div class="CdsIndividualChat-chat-search-wrapper">
            <input 
                type="text" 
                class="CdsIndividualChat-chat-search-input" 
                id="searchInput" 
                placeholder="Search messages..."
                autocomplete="off"
            >
            <div class="CdsIndividualChat-search-controls">
                <button class="CdsIndividualChat-header-btn" id="searchPrevBtn" title="Previous result" disabled>
                    <i class="fa-solid fa-chevron-up"></i>
                </button>
                <button class="CdsIndividualChat-header-btn" id="searchNextBtn" title="Next result" disabled>
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
                <button class="CdsIndividualChat-search-btn" id="searchBtn" title="Search">
                    <i class="fa-solid fa-search"></i>
                </button>
                <button class="CdsIndividualChat-search-close-btn" id="searchCloseBtn" title="Close search">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
        </div>
        <div class="CdsIndividualChat-search-results" id="searchResults" style="display: none;">
            <span id="searchResultCount">0 results</span>
        </div>
    </div>
    <!-- Messages Container -->
    <div class="CdsIndividualChat-messages-container" id="messagesContainer">
        <form id="clear-messages">
            @csrf
            <div style="display:none" id="selectAllDiv" class="select-all-checkbox">
                <div class="cds-clearBox">
                    <label class="cds-checkbox ">
                        <input type="checkbox" id="selectAll" class="checkbox" />
                        <span class="checkmark"></span>
                        <span class="selectAll">Select All</span>
                    </label>
                </div>

                
                <div class="cds-action-btn">
                    <button id="cancelClear" type="button" class="btn btn-dark btn-sm">Cancel Clear</button>
                    <button id="clearChatBtn" type="submit" class="btn btn-primary btn-sm">Clear Selected Messages</button>
                </div>
                
            </div>

            <div class="messages_read" id="messages_read">
                @include('admin-panel.01-message-system.individual-chats.messages')
            </div>
        </form>
    </div>

    <!-- Blocked Chat Message -->
@if($chat->blocked_chat==1 )
<div id="block_msg{{$chat->id}}" class="blocked-chat">
    <div class="blocked-content">
        <div class="blocked-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="9" stroke="#DC2626" stroke-width="2"/>
                <line x1="7" y1="17" x2="17" y2="7" stroke="#DC2626" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <h3 class="blocked-title">Chat has been blocked</h3>
        <p class="blocked-message">You cannot send messages to this user.</p>
    </div>
</div>
 @endif
    <!-- Typing Indicator -->
    <div class="CdsIndividualChat-typing-area" id="typingArea" style="display: none;">
        <div class="CdsIndividualChat-typing-chat">
            <div class="CdsIndividualChat-typechat-message">
                <span id="typingUserName"></span> is typing...
            </div>
            <div class="CdsIndividualChat-typing-indicator">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>

    <!-- Reply Message -->
    <div class="CdsIndividualChat-reply-message" id="replyQuotedMsg" style="display: none;">
        <div class="CdsIndividualChat-reply-icons">
            <i class="fa-solid fa-turn-up"></i>
            <i class="fa-solid fa-xmark" onclick="closeReplyTo()"></i>
        </div>
        <p class="CdsIndividualChat-quoted-message">Reply quoted message</p>
        <span class="CdsIndividualChat-username myChatReply{{ $chat->id }}" id="myreply">MY Reply</span>
    </div>

    <!-- Input Container -->
    @if($chat->blocked_chat!=1)
    <div class="CdsIndividualChat-input-container" id="sendmsg">
        <div class="CdsIndividualChat-input-wrapper">
            <div class="CdsIndividualChat-input-actions-left">
                <div class="message-emoji-icon emoji-icon" id="emojiBtn">
                    <i class="fa-sharp fa-solid fa-face-smile"></i>
                </div>
            </div>
            <textarea class="CdsIndividualChat-input-field" placeholder="Enter Message" id="messageInput" name="send_msg" data-id="{{ $chat->id }}" rows="1">{{ isset($draft_message) ? $draft_message : '' }}</textarea>
            <button class="CdsIndividualChat-input-btn" id="attachBtn">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
                </svg>
            </button>
            <button class="CdsIndividualChat-input-btn CdsIndividualChat-send-btn" id="sendBtn">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
            </button>
        </div>
    </div>
    @endif
</div>



<!-- Overlay -->
<div class="CdsIndividualChat-overlay" id="overlay"></div>

<!-- Files Panel -->
{{-- @include('admin-panel.01-message-system.individual-chats.chat-file-shared') --}}

<!-- Upload Modal -->
<div class="modal fade cdsTYDashboardModal-container01" id="uploadModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form id="file-upload-form">
        @csrf
        <div class="modal-dialog modal-dialog-centered cdsTYDashboardModal-container01-inner">
            <div class="cdsTYDashboardModal-container01-inner-content modal-content">
                <div class="cdsTYDashboardModal-container01-inner-content-header modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Upload Document</h5>
                    <button type="button" id="closemodal" class="cdsTYDashboardModal-container01-btn-close"
                        data-bs-dismiss="modal" aria-label="Close">X</button>
                </div>
                <div class="cdsTYDashboardModal-container01-inner-content-body modal-body">
                    <div class="cds-modal-content">
                        <div class="CDSFeed-upload-container" id="uploadMediaFiles">
                            <div class="CDSFeed-upload-area">
                                <input type="file"  class="CDSFeed-file-input" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.mp3,.mp4,.mpeg" style="display: none;">
                                <div class="CDSFeed-upload-icon">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                </div>
                                <p class="CDSFeed-upload-text">Drag and drop files here or click to browse</p>
                                <p class="CDSFeed-upload-hint">Supports: JPG, PNG, GIF, PDF, DOC, DOCX, XLS, XLSX, TXT, MP3, MP4, MPEG (Max 10MB per file)</p>
                            </div>
                            
                            <!-- File Preview Area -->
                            <div class="CDSFeed-file-list" style="display: none;">
                                <!-- Files will be dynamically added here -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cdsTYDashboardModal-container01-inner-content-footer">
                    <div class="editor-container gap-2 d-flex p-2">
                        <input class="form-control" type="text" name="message" id="messagenew" placeholder="Write message here...">
                        <div class="cdsTYDashboard-discussion-panel-view-editor-custom-container-action-buttons">
                            <div class="message-emoji-icon-modal emoji-icon">
                                <i class="fa-sharp fa-solid fa-face-smile"></i>
                            </div>
                            <button type="submit" class="cdsTYDashboardbutton-style01 cdsTYDashboardbutton-purple"
                                id="sendBtnnew"><i class="fa-solid fa-send" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- File Upload Modal for Drag & Drop -->
<div class="modal fade" tabindex="-1" aria-labelledby="file-upload-modal-label" aria-hidden="true" id="file-upload-modal">
    <div class="modal-xl modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body w-100">
                <div id="preview-container"></div>
                <div class="drop-text">Drop files or paste the copied file here to upload</div>
            </div>

            <div class="modal-footer w-100">
                <button type="button" type="button" class="btn btn-primary me-2" id="upload-button">Upload</button>
                <button type="button" type="button" class="btn btn-dark close-upload-modal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden inputs for functionality -->
<input type="hidden" value="{{baseUrl('individual-chats/send-message/'.$chat->id)}}" id="geturl">
<input type="hidden" value="" id="edit_message_id">
<input type="hidden" value="" id="reply_to_id">
<input type="hidden" value="{{$chat->id}}" id="get_chat_id">
<input type="hidden" value="{{$chat->unique_id}}" id="get_chat_unique_id">
<input type="hidden" value="{{auth()->user()->id}}" id="current_user_id">
<input type="hidden" value="{{auth()->user()->first_name . ' ' . auth()->user()->last_name}}" id="current_user_name">

<!-- Search Functionality JavaScript -->
<script>
// Search functionality variables
var searchResults = [];
var currentSearchIndex = -1;
var searchQuery = '';

// DOM elements
var searchInput = document.getElementById('searchInput');
var searchPrevBtn = document.getElementById('searchPrevBtn');
var searchNextBtn = document.getElementById('searchNextBtn');
var searchBtn = document.getElementById('searchBtn');
var searchCloseBtn = document.getElementById('searchCloseBtn');
var searchResultsDiv = document.getElementById('searchResults');
var searchResultCount = document.getElementById('searchResultCount');
var messagesContainer = document.getElementById('messagesContainer');
var searchBar = document.getElementById('searchBar');
var searchToggleBtn = document.getElementById('searchToggleBtn');

// Search functionality
function performSearch() {
    var query = searchInput.value.trim();
    if (!query) {
        clearSearch();
        return;
    }
    
    searchQuery = query;
    searchResults = [];
    currentSearchIndex = -1;
    
    // Get all message elements - adapt to individual chat message structure
    var messageElements = messagesContainer.querySelectorAll('.CdsIndividualChat-message-wrapper');
    
    messageElements.forEach((element, index) => {
        // Look for the actual message text content - adapt to individual chat structure
        var messageTextElement = element.querySelector('.CdsIndividualChat-message-text');
        
        var messageText = '';
        if (messageTextElement) {
            messageText = messageTextElement.textContent || messageTextElement.innerText;
        }
        
        if (messageText && messageText.toLowerCase().includes(query.toLowerCase())) {
            searchResults.push({
                element: element,
                index: index,
                text: messageText
            });
        }
    });
    
    // Update UI
    updateSearchResults();
    
    // Highlight all search results with text highlights
    searchResults.forEach(result => {
        highlightTextInMessage(result.element, searchQuery);
    });
    
    // Update the results count to show total occurrences
    var totalOccurrences = searchResults.reduce((total, result) => {
        var messageTextElement = result.element.querySelector('.CdsIndividualChat-message-text');
        
        var count = 0;
        if (messageTextElement) {
            var text = messageTextElement.textContent || messageTextElement.innerText;
            var regex = new RegExp(escapeRegExp(searchQuery), 'gi');
            var matches = text.match(regex);
            count = matches ? matches.length : 0;
        }
        return total + count;
    }, 0);
    
    // Store total occurrences for display
    searchResults.totalOccurrences = totalOccurrences;
    
    // Highlight first result if any
    if (searchResults.length > 0) {
        currentSearchIndex = 0;
        highlightSearchResult(0);
    }
}

function updateSearchResults() {
    if (searchResults.length > 0) {
        var totalOccurrences = searchResults.totalOccurrences || searchResults.length;
        searchResultCount.textContent = `${currentSearchIndex + 1} of ${searchResults.length} result${searchResults.length > 1 ? 's' : ''} (${totalOccurrences} occurrence${totalOccurrences > 1 ? 's' : ''})`;
        searchResultsDiv.style.display = 'block';
        
        // Show search bar if it's hidden
        if (searchBar.style.display === 'none' || searchBar.style.display === '') {
            searchBar.style.display = 'block';
        }
        
        // Update toggle button state
        updateSearchToggleState(true);
        
        // Enable/disable navigation buttons
        searchPrevBtn.disabled = currentSearchIndex <= 0;
        searchNextBtn.disabled = currentSearchIndex >= searchResults.length - 1;
    } else {
        searchResultCount.textContent = 'No results found';
        searchResultsDiv.style.display = 'block';
        searchPrevBtn.disabled = true;
        searchNextBtn.disabled = true;
    }
}

function updateSearchToggleState(isActive) {
    if (isActive) {
        searchToggleBtn.classList.add('active');
        // searchToggleBtn.style.background = 'var(--CdsIndividualChat-success, #10B981)';
    } else {
        searchToggleBtn.classList.remove('active');
        // searchToggleBtn.style.background = 'var(--CdsIndividualChat-primary, #3B82F6)';
    }
}

function highlightSearchResult(index) {
    // Remove previous highlights
    searchResults.forEach(result => {
        result.element.classList.remove('CdsIndividualChat-search-highlight');
        result.element.classList.remove('CdsIndividualChat-search-highlight-current');
        // Remove text highlights
        removeTextHighlights(result.element);
    });
    
    if (index >= 0 && index < searchResults.length) {
        var result = searchResults[index];
        
        // Add highlight classes
        result.element.classList.add('CdsIndividualChat-search-highlight');
        result.element.classList.add('CdsIndividualChat-search-highlight-current');
        
        // Add text highlights
        highlightTextInMessage(result.element, searchQuery);
        
        // Scroll to the highlighted message
        result.element.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
        
        // Update navigation buttons
        searchPrevBtn.disabled = index <= 0;
        searchNextBtn.disabled = index >= searchResults.length - 1;
        
        // Update results display
        updateSearchResults();
    }
}

function navigateToNext() {
    if (currentSearchIndex < searchResults.length - 1) {
        currentSearchIndex++;
        highlightSearchResult(currentSearchIndex);
    }
}

function navigateToPrev() {
    if (currentSearchIndex > 0) {
        currentSearchIndex--;
        highlightSearchResult(currentSearchIndex);
    }
}

function clearSearch() {
    searchInput.value = '';
    searchResults = [];
    currentSearchIndex = -1;
    searchQuery = '';
    
    // Remove all highlights
    var highlightedElements = messagesContainer.querySelectorAll('.CdsIndividualChat-search-highlight, .CdsIndividualChat-search-highlight-current');
    highlightedElements.forEach(element => {
        element.classList.remove('CdsIndividualChat-search-highlight', 'CdsIndividualChat-search-highlight-current');
        // Remove text highlights
        removeTextHighlights(element);
    });
    
    // Hide results
    searchResultsDiv.style.display = 'none';
    searchBar.style.display = 'none';
    // Reset toggle button state
    updateSearchToggleState(false);
    
    // Disable navigation buttons
    searchPrevBtn.disabled = true;
    searchNextBtn.disabled = true;
}

function toggleSearch() {
    if (searchBar.style.display === 'none' || searchBar.style.display === '') {
        searchBar.style.display = 'block';
        searchInput.focus();
        searchInput.select();
    } else {
        searchBar.style.display = 'none';
        clearSearch();
    }
}

function highlightTextInMessage(messageElement, searchText) {
    if (!searchText) return;
    
    // Look for message text elements - adapt to individual chat structure
    var messageTextElement = messageElement.querySelector('.CdsIndividualChat-message-text');
    
    if (messageTextElement) {
        highlightTextInElement(messageTextElement, searchText);
    }
}

function highlightTextInElement(element, searchText) {
    var text = element.innerHTML;
    if (!text) return;
    
    // Store original content if not already stored
    if (!element.hasAttribute('data-original-html')) {
        element.setAttribute('data-original-html', text);
    }
    
    // Create a case-insensitive regex for the search text
    var regex = new RegExp(`(${escapeRegExp(searchText)})`, 'gi');
    
    // Replace the text with highlighted version
    var highlightedText = text.replace(regex, '<span class="CdsIndividualChat-text-highlight">$1</span>');
    element.innerHTML = highlightedText;
}

function removeTextHighlights(messageElement) {
    // Remove text highlights from message text elements
    var messageTextElement = messageElement.querySelector('.CdsIndividualChat-message-text');
    
    if (messageTextElement) {
        removeTextHighlightsFromElement(messageTextElement);
    }
}

function removeTextHighlightsFromElement(element) {
    // Restore original HTML content
    var originalHtml = element.getAttribute('data-original-html');
    if (originalHtml) {
        element.innerHTML = originalHtml;
        element.removeAttribute('data-original-html');
    } else {
        // Fallback: remove highlighted spans
        var highlightedSpans = element.querySelectorAll('.CdsIndividualChat-text-highlight');
        highlightedSpans.forEach(span => {
            span.replaceWith(span.textContent);
        });
    }
}

function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

// Event listeners
if (searchInput) {
    searchInput.addEventListener('input', function() {
        if (this.value.trim()) {
            // Add a small delay for better performance during typing
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                performSearch();
            }, 300);
        } else {
            clearSearch();
        }
    });
    
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (e.shiftKey) {
                navigateToPrev();
            } else {
                navigateToNext();
            }
        } else if (e.key === 'Escape') {
            clearSearch();
        }
    });
}

if (searchBtn) searchBtn.addEventListener('click', performSearch);
if (searchPrevBtn) searchPrevBtn.addEventListener('click', navigateToPrev);
if (searchNextBtn) searchNextBtn.addEventListener('click', navigateToNext);
if (searchCloseBtn) searchCloseBtn.addEventListener('click', clearSearch);
if (searchToggleBtn) searchToggleBtn.addEventListener('click', toggleSearch);

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+F or Cmd+F to show search bar
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        if (searchBar.style.display === 'none' || searchBar.style.display === '') {
            toggleSearch();
        } else {
            searchInput.focus();
            searchInput.select();
        }
    }
    
    // Escape to close search
    if (e.key === 'Escape' && searchBar.style.display !== 'none') {
        e.preventDefault();
        toggleSearch();
    }
});
</script>

<!-- Paste Functionality for File Uploads - Integrates with individual-chat.js -->
<script>
// Get DOM elements
var chatArea = document.querySelector('.CdsIndividualChat-active-chat');
var modal = document.getElementById('uploadModal');
var sendInput = document.getElementById('messageInput');
var modalMessageInput = document.getElementById('messagenew');

// Initialize Drag and Drop
if (chatArea) {
    // Open modal when file is dragged over chat area
    chatArea.addEventListener("dragover", (e) => {
        e.preventDefault();
        if (e.dataTransfer && e.dataTransfer.items) {
            for (let i = 0; i < e.dataTransfer.items.length; i++) {
                if (e.dataTransfer.items[i].kind === "file") {
                    // Use the existing openFileUploadModal method from individual-chat.js
                    if (typeof individualChatManager !== 'undefined') {
                        individualChatManager.openFileUploadModal();
                    } else {
                        $("#uploadModal").modal("show");
                    }
                    break; // Stop checking after finding a file
                }
            }
        }
    });
}

if (modal) {
    // Handle dragover on modal
    modal.addEventListener("dragover", function(e) {
        e.preventDefault();
    });
    
    // Detect file drop inside modal
    modal.addEventListener("drop", function(e) {
        e.preventDefault(); // Prevent browser default behavior
        // Use the existing uploadFiles method from individual-chat.js
        if (typeof individualChatManager !== 'undefined') {
            individualChatManager.uploadFiles(e.dataTransfer.files);
        }
    });
}

// Note: Paste event handling is now managed by individual-chat.js FileUploadManager integration
// This provides better integration and validation

// Handle attach button click to open upload modal
var attachBtn = document.getElementById('attachBtn');
if (attachBtn) {
    attachBtn.addEventListener('click', function() {
        // Use the existing openFileUploadModal method from individual-chat.js
        if (typeof individualChatManager !== 'undefined') {
            individualChatManager.openFileUploadModal();
        } else {
            $("#uploadModal").modal("show");
        }
    });
}
</script>