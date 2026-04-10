@props(['groupId'])

<div class="search-messages-container" id="search-messages-{{ $groupId }}" style="display: none;">
    <div class="search-header">
        <h4>Search Messages</h4>
        <button type="button" class="close-btn" onclick="closeSearchMessages()">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    
    <div class="search-input-container">
        <input type="text" 
               id="message-search-input" 
               class="form-control" 
               placeholder="Search in messages...">
        <button type="button" class="search-btn" onclick="searchMessages()">
            <i class="fa-solid fa-search"></i>
        </button>
    </div>
    
    <div class="search-results" id="search-results">
        <!-- Search results will be displayed here -->
    </div>
    
    <div class="search-navigation">
        <button type="button" class="nav-btn" onclick="previousResult()">
            <i class="fa-solid fa-chevron-up"></i> Previous
        </button>
        <span class="result-counter" id="result-counter">0 of 0</span>
        <button type="button" class="nav-btn" onclick="nextResult()">
            Next <i class="fa-solid fa-chevron-down"></i>
        </button>
    </div>
</div>

<script>
let searchResults = [];
let currentResultIndex = 0;

function openSearchMessages() {
    document.getElementById(`search-messages-${groupId}`).style.display = 'block';
    document.getElementById('message-search-input').focus();
}

function closeSearchMessages() {
    document.getElementById(`search-messages-${groupId}`).style.display = 'none';
    searchResults = [];
    currentResultIndex = 0;
}

function searchMessages() {
    const searchTerm = document.getElementById('message-search-input').value.trim();
    if (!searchTerm) return;
    
    $.ajax({
        url: baseUrl + 'group/search-messages',
        method: 'POST',
        data: {
            group_id: groupId,
            search: searchTerm,
            _token: csrfToken
        },
        success: (response) => {
            if (response.status) {
                searchResults = response.results;
                currentResultIndex = 0;
                displaySearchResults();
            }
        }
    });
}

function displaySearchResults() {
    const resultsContainer = document.getElementById('search-results');
    const counter = document.getElementById('result-counter');
    
    if (searchResults.length === 0) {
        resultsContainer.innerHTML = '<p class="no-results">No messages found</p>';
        counter.textContent = '0 of 0';
        return;
    }
    
    resultsContainer.innerHTML = searchResults.map((result, index) => `
        <div class="search-result ${index === currentResultIndex ? 'active' : ''}" 
             onclick="highlightMessage(${result.message_id})">
            <div class="result-sender">${result.sender_name}</div>
            <div class="result-message">${result.message}</div>
            <div class="result-time">${result.created_at}</div>
        </div>
    `).join('');
    
    counter.textContent = `${currentResultIndex + 1} of ${searchResults.length}`;
    highlightMessage(searchResults[currentResultIndex].message_id);
}

function nextResult() {
    if (currentResultIndex < searchResults.length - 1) {
        currentResultIndex++;
        displaySearchResults();
    }
}

function previousResult() {
    if (currentResultIndex > 0) {
        currentResultIndex--;
        displaySearchResults();
    }
}

function highlightMessage(messageId) {
    // Remove previous highlights
    document.querySelectorAll('.message-highlighted').forEach(el => {
        el.classList.remove('message-highlighted');
    });
    
    // Highlight current message
    const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
    if (messageElement) {
        messageElement.classList.add('message-highlighted');
        messageElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

// Search on Enter key
document.getElementById('message-search-input')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchMessages();
    }
});
</script> 