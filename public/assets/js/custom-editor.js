const CustomEditor = (() => {
  // ... (keeping all the existing code before the init function)
  
  // Create styles for the editor
  function injectStyles() {
    if (document.getElementById('custom-editor-styles')) return;
    
    const style = document.createElement('style');
    style.id = 'custom-editor-styles';
    style.textContent = `
      .cds-editor-wrapper {
        background: #fff;
        position: relative;
      }

      .cds-editor-toolbar {
        display: flex;
        flex-wrap: wrap;
        padding: 10px;
        background: #f0f0f0;
        border-bottom: 1px solid rgba(0,0,0,0.1);
      }

     .cds-editor-toolbar button {
        background: white;
        border: 1px solid #ddd;
        padding: 6px 10px;
        margin: 2px;
        text-align: center;
        cursor: pointer;
        font-size: 18px;
        display: inline-flex;
        width: 36px;
        height: 36px;
        border-radius: 4px;
        transition: background 0.2s;
        align-items: center;
        justify-content: center;
    }

      .cds-editor-toolbar button:hover {
        background: #e0e0e0;
      }

      .cds-editor-toolbar button:active {
        background: #d0d0d0;
      }

     .cds-editor-content {
        padding: 20px;
        min-height: 180px;
        outline: none;
    }

      .cds-editor-content:empty::before {
        content: 'Start typing...';
        color: #aaa;
      }

      .cds-editor-content:focus {
      
      }

      /* Mention dropdown styles */
      .cds-mention-dropdown {
        position: absolute;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        max-height: 200px;
        overflow-y: auto;
        min-width: 250px;
        z-index: 1000;
        display: none;
      }

      .cds-mention-item {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s;
      }

      .cds-mention-item:last-child {
        border-bottom: none;
      }

      .cds-mention-item:hover,
      .cds-mention-item.selected {
        background-color: #f8f9fa;
      }

      .cds-mention-item.loading {
        opacity: 0.6;
        pointer-events: none;
      }

      .cds-mention-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        color: #666;
        font-size: 14px;
      }

      .cds-mention-error {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        color: #d32f2f;
        font-size: 14px;
      }

      .cds-mention-no-results {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        color: #666;
        font-size: 14px;
      }

      .cds-mention-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        margin-right: 12px;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
      }

      .cds-mention-info {
        flex: 1;
        min-width: 0;
      }

      .cds-mention-name {
        font-weight: 500;
        font-size: 14px;
        color: #333;
        margin-bottom: 2px;
      }

      .cds-mention-username {
        font-size: 12px;
        color: #666;
      }

      .cds-mention-tag {
        background-color: #e3f2fd;
        color: #1976d2;
        padding: 2px 6px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        text-decoration: none;
        display: inline-block;
      }

      /* Popup styles */
      .cds-editor-popup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #fff;
        border: 1px solid #ccc;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        z-index: 1000;
        border-radius: 8px;
        display: none;
      }

      .cds-editor-popup label {
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
      }

      .cds-editor-popup input[type="text"],
      .cds-editor-popup input[type="url"] {
        width: 300px;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 15px;
        font-size: 14px;
      }

      .cds-editor-popup input[type="color"] {
        width: 60px;
        height: 40px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 15px;
        cursor: pointer;
      }

      .cds-editor-popup-buttons {
        text-align: right;
      }

      .cds-editor-popup button {
        padding: 8px 16px;
        margin-left: 8px;
        border: 1px solid #ddd;
        background: #fff;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
      }

      .cds-editor-popup button:hover {
        background: #f0f0f0;
      }

      .cds-editor-popup button.primary {
        background: #4CAF50;
        color: white;
        border-color: #4CAF50;
      }

      .cds-editor-popup button.primary:hover {
        background: #45a049;
      }
    `;
    document.head.appendChild(style);
  }

  // Default users list
  const defaultUsers = [];

  // AJAX helper function
  function loadUsersAjax(config, query = '') {
    return new Promise((resolve, reject) => {
      const xhr = new XMLHttpRequest();
      let url = typeof config === 'string' ? config : config.url;
      
      // Add query parameter if needed
      if (query && config.queryParam) {
        const separator = url.includes('?') ? '&' : '?';
        url += `${separator}${config.queryParam}=${encodeURIComponent(query)}`;
      }
      
      xhr.open(config.method || 'GET', url);
      
      // Set headers
      if (config.headers) {
        Object.entries(config.headers).forEach(([key, value]) => {
          xhr.setRequestHeader(key, value);
        });
      }
      
      // Set content type for POST requests
      if ((config.method || 'GET').toUpperCase() === 'POST') {
        xhr.setRequestHeader('Content-Type', 'application/json');
      }
      
      xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
          try {
            const response = JSON.parse(xhr.responseText);
            let users = response;
            
            // Extract users from nested response if needed
            if (config.dataPath) {
              const path = config.dataPath.split('.');
              for (const key of path) {
                users = users[key];
              }
            }
            
            // Transform users if needed
            if (config.transform && typeof config.transform === 'function') {
              users = users.map(config.transform);
            }
            
            resolve(users);
          } catch (error) {
            reject(new Error('Failed to parse JSON response: ' + error.message));
          }
        } else {
          reject(new Error(`HTTP ${xhr.status}: ${xhr.statusText}`));
        }
      };
      
      xhr.onerror = function() {
        reject(new Error('Network error occurred'));
      };
      
      xhr.ontimeout = function() {
        reject(new Error('Request timeout'));
      };
      
      // Set timeout
      xhr.timeout = config.timeout || 10000;
      
      // Send request with data for POST
      if ((config.method || 'GET').toUpperCase() === 'POST') {
        let postData = '';
        if (config.data) {
          // Handle both object and function for data
          const dataToSend = typeof config.data === 'function' ? config.data() : config.data;
          
          // Add query parameter to POST data if needed
          if (query && config.queryParam) {
            dataToSend[config.queryParam] = query;
          }
          
          // Always send as JSON for cleaner Laravel handling
          postData = JSON.stringify(dataToSend);
        }
        xhr.send(postData);
      } else {
        xhr.send();
      }
    });
  }

  // Validate URL
  function isValidUrl(url) {
    return /^https?:\/\/[\w\-\.]+\.\w{2,}(\/\S*)?$/.test(url);
  }

  // Get cursor position for dropdown
  function getCursorPosition(element) {
    const selection = window.getSelection();
    if (selection.rangeCount === 0) return null;
    
    const range = selection.getRangeAt(0);
    const rect = range.getBoundingClientRect();
    const editorRect = element.getBoundingClientRect();
    
    return {
      x: rect.left - editorRect.left,
      y: rect.bottom - editorRect.top
    };
  }

  // Get text before cursor with better handling
  function getTextBeforeCursor() {
    const selection = window.getSelection();
    if (selection.rangeCount === 0) return '';
    
    const range = selection.getRangeAt(0);
    
    // Create a range from start of editor content to cursor
    const editorRange = document.createRange();
    const editor = range.startContainer.nodeType === Node.TEXT_NODE 
      ? range.startContainer.parentElement.closest('.cds-editor-content')
      : range.startContainer.closest('.cds-editor-content');
      
    if (!editor) return '';
    
    editorRange.setStart(editor, 0);
    editorRange.setEnd(range.startContainer, range.startOffset);
    
    return editorRange.toString();
  }

  // Save cursor position
  function saveCursorPosition(editor) {
    const selection = window.getSelection();
    if (selection.rangeCount === 0) return null;
    
    const range = selection.getRangeAt(0);
    const preCaretRange = range.cloneRange();
    preCaretRange.selectNodeContents(editor);
    preCaretRange.setEnd(range.endContainer, range.endOffset);
    
    return preCaretRange.toString().length;
  }

  // Restore cursor position
  function restoreCursorPosition(editor, position) {
    const selection = window.getSelection();
    const range = document.createRange();
    
    let charIndex = 0;
    let nodeStack = [editor];
    let node;
    let foundStart = false;

    while (!foundStart && (node = nodeStack.pop())) {
      if (node.nodeType === Node.TEXT_NODE) {
        const nextCharIndex = charIndex + node.textContent.length;
        if (position >= charIndex && position <= nextCharIndex) {
          range.setStart(node, position - charIndex);
          range.setEnd(node, position - charIndex);
          foundStart = true;
        }
        charIndex = nextCharIndex;
      } else {
        let i = node.childNodes.length;
        while (i--) {
          nodeStack.push(node.childNodes[i]);
        }
      }
    }

    if (foundStart) {
      selection.removeAllRanges();
      selection.addRange(range);
    }
  }

  // Insert mention at cursor - FIXED VERSION
  function insertMention(user, editor) {
    console.log('Inserting mention for user:', user);
    
    // Save current cursor position
    const cursorPos = saveCursorPosition(editor);
    
    // Get the text content and find @ position
    const textContent = editor.textContent || editor.innerText || '';
    let atPosition = -1;
    
    // Find the last @ before cursor position
    for (let i = cursorPos - 1; i >= 0; i--) {
      if (textContent[i] === '@') {
        atPosition = i;
        break;
      }
    }
    
    if (atPosition === -1) {
      console.log('No @ symbol found before cursor');
      return;
    }
    
    console.log('Found @ at position:', atPosition);
    
    // Get HTML content and find where to insert
    const htmlContent = editor.innerHTML;
    
    // Simple approach: replace the text content
    const beforeAt = textContent.substring(0, atPosition);
    const afterCursor = textContent.substring(cursorPos);
    
    // Create mention span
    const mentionHTML = `<span class="cds-mention-tag" data-user-id="${user.id}" data-user-name="${user.name}" contenteditable="false">@${user.name}</span>`;
    
    // Build new content
    const newContent = beforeAt + mentionHTML + '&nbsp;' + afterCursor;
    
    // Set the new content
    editor.innerHTML = newContent;
    
    // Restore cursor position after the mention
    const newCursorPos = beforeAt.length + user.name.length + 2; // +2 for @ and space
    
    setTimeout(() => {
      restoreCursorPosition(editor, newCursorPos);
      editor.focus();
    }, 10);
    
    console.log('Mention inserted successfully');
  }

  // Initialize the editor
  function init(selector, options = {}) {
    // Local variables for this instance
    let editor, textarea, container, mentionDropdown;
    let lastContent = '';
    let typingTimer;
    let isTyping = false;
    let selectedMentionIndex = 0;
    let filteredUsers = [];
    let isLoadingUsers = false;
    let loadUsersTimeout;
    let cachedUsers = [];
    let lastQuery = '';
    
    // ENHANCED: Variables for cursor position management
    let savedCursorPosition = null;
    let isTrackingCursor = true;
    let autoSaveTimer = null;
    
    // Event callbacks from options
    const {
      onChange = null,
      onType = null,
      onTypingStart = null,
      onTypingStop = null,
      onMention = null,
      onUsersLoad = null,
      onUsersError = null,
      onEmojiInsert = null,
      onCursorChange = null, // NEW: Callback for cursor position changes
      typeDelay = 500,
      usersLoadDelay = 300,
      autoSaveDelay = 100, // NEW: Delay for auto-saving cursor position
      users = defaultUsers
    } = options;
    
    textarea = document.querySelector(selector);
    if (!textarea) {
      console.error('Textarea not found:', selector);
      return null;
    }

    // Inject styles
    injectStyles();

    // Create container
    container = document.createElement('div');
    container.className = 'cds-editor-wrapper';

    // Create toolbar
    const toolbar = document.createElement('div');
    toolbar.className = 'cds-editor-toolbar';

    // Create editor
    editor = document.createElement('div');
    editor.className = 'cds-editor-content';
    editor.contentEditable = true;
    editor.innerHTML = textarea.value || '';
    lastContent = editor.innerHTML;

    // Create mention dropdown
    mentionDropdown = document.createElement('div');
    mentionDropdown.className = 'cds-mention-dropdown';
    container.appendChild(mentionDropdown);

    // ENHANCED: Cursor position management functions
    function saveCurrentCursorPosition() {
      if (isTrackingCursor && editor.contains(document.activeElement)) {
        const selection = window.getSelection();
        if (selection.rangeCount > 0 && editor.contains(selection.anchorNode)) {
          const newPosition = saveCursorPosition(editor);
          if (newPosition !== savedCursorPosition) {
            savedCursorPosition = newPosition;
            console.log('Cursor position saved:', savedCursorPosition);
            
            // Trigger callback if provided
            if (onCursorChange) {
              onCursorChange(savedCursorPosition);
            }
          }
        }
      }
    }

    function restoreCurrentCursorPosition() {
      if (savedCursorPosition !== null) {
        restoreCursorPosition(editor, savedCursorPosition);
        console.log('Cursor position restored:', savedCursorPosition);
      }
    }

    // ENHANCED: Auto-save cursor position with debouncing
    function autoSaveCursorPosition() {
      clearTimeout(autoSaveTimer);
      autoSaveTimer = setTimeout(() => {
        saveCurrentCursorPosition();
      }, autoSaveDelay);
    }

    function insertAtCursorPosition(content, isHTML = false) {
      // Focus editor first
      editor.focus();
      
      // If we have a saved position, restore it
      if (savedCursorPosition !== null) {
        restoreCurrentCursorPosition();
      }
      
      // Insert content
      if (isHTML) {
        document.execCommand('insertHTML', false, content);
      } else {
        document.execCommand('insertText', false, content);
      }
      
      // Update saved position after insertion
      setTimeout(saveCurrentCursorPosition, 10);
      
      // Trigger change event
      handleContentChange();
    }

    // ENHANCED: Setup cursor tracking with auto-save
    function setupCursorTracking() {
      // Track cursor on various events
      const trackingEvents = ['keyup', 'keydown', 'mouseup', 'focus', 'click', 'input'];
      trackingEvents.forEach(eventType => {
        editor.addEventListener(eventType, autoSaveCursorPosition);
      });
      
      // Save position when editor is about to lose focus
      editor.addEventListener('blur', saveCurrentCursorPosition);
      
      // Save position on mouse leave (for external interactions)
      editor.addEventListener('mouseleave', saveCurrentCursorPosition);
      
      // Track selection changes
      document.addEventListener('selectionchange', () => {
        if (editor.contains(document.activeElement)) {
          autoSaveCursorPosition();
        }
      });
    }

    // Mention dropdown functions
    function showMentionDropdown(query = '') {
      console.log('Showing mention dropdown with query:', query);
      
      // If users is a configuration object for AJAX, load users dynamically
      if (typeof users === 'object' && users.url) {
        loadUsersForQuery(query);
      } else {
        // Use static users list
        const usersList = Array.isArray(users) ? users : defaultUsers;
        filteredUsers = usersList.filter(user => 
          user.name.toLowerCase().includes(query.toLowerCase()) ||
          (user.username && user.username.toLowerCase().includes(query.toLowerCase()))
        );
        
        displayMentionResults();
      }
    }

    function loadUsersForQuery(query) {
      // Debounce AJAX requests
      clearTimeout(loadUsersTimeout);
      loadUsersTimeout = setTimeout(async () => {
        if (lastQuery === query && cachedUsers.length > 0 && !query) {
          // Use cached results for empty query
          filteredUsers = cachedUsers;
          displayMentionResults();
          return;
        }

        isLoadingUsers = true;
        showLoadingState();
        
        try {
          console.log('Loading users via AJAX with query:', query);
          const loadedUsers = await loadUsersAjax(users, query);
          console.log('Loaded users:', loadedUsers);
          
          // Cache users for empty queries
          if (!query) {
            cachedUsers = loadedUsers;
          }
          
          filteredUsers = loadedUsers;
          lastQuery = query;
          
          if (onUsersLoad) {
            onUsersLoad(loadedUsers, query);
          }
          
          displayMentionResults();
        } catch (error) {
          console.error('Failed to load users:', error);
          showErrorState(error.message);
          
          if (onUsersError) {
            onUsersError(error, query);
          }
        } finally {
          isLoadingUsers = false;
        }
      }, usersLoadDelay);
    }

    function showLoadingState() {
      mentionDropdown.innerHTML = '<div class="cds-mention-loading">Loading users...</div>';
      showDropdown();
    }

    function showErrorState(message) {
      mentionDropdown.innerHTML = `<div class="cds-mention-error">Error: ${message}</div>`;
      showDropdown();
    }

    function showNoResultsState() {
      mentionDropdown.innerHTML = '<div class="cds-mention-no-results">No users found</div>';
      showDropdown();
    }

    function showDropdown() {
      const cursorPos = getCursorPosition(editor);
      if (cursorPos) {
        mentionDropdown.style.left = `${cursorPos.x}px`;
        mentionDropdown.style.top = `${cursorPos.y + 5}px`;
        mentionDropdown.style.display = 'block';
      }
    }

    function displayMentionResults() {
      if (filteredUsers.length === 0) {
        showNoResultsState();
        return;
      }
      
      selectedMentionIndex = 0;
      renderMentionDropdown();
      showDropdown();
    }

    function hideMentionDropdown() {
      mentionDropdown.style.display = 'none';
      filteredUsers = [];
      selectedMentionIndex = 0;
      isLoadingUsers = false;
      clearTimeout(loadUsersTimeout);
    }

    function renderMentionDropdown() {
      mentionDropdown.innerHTML = '';
      
      filteredUsers.forEach((user, index) => {
        const item = document.createElement('div');
        item.className = `cds-mention-item ${index === selectedMentionIndex ? 'selected' : ''}`;
        
        item.innerHTML = `
          <div class="cds-mention-info">
            <div class="cds-mention-name">${user.name}</div>
          </div>
        `;
        
        item.addEventListener('click', (e) => {
          console.log('User clicked:', user);
          e.preventDefault();
          e.stopPropagation();
          
          insertMention(user, editor);
          hideMentionDropdown();
          handleContentChange();
          
          if (onMention) {
            onMention(user);
          }
        });
        
        // Also add mousedown event to prevent focus loss
        item.addEventListener('mousedown', (e) => {
          e.preventDefault();
        });
        
        mentionDropdown.appendChild(item);
      });
    }

    function selectMentionItem(direction) {
      if (filteredUsers.length === 0) return;
      
      if (direction === 'up') {
        selectedMentionIndex = selectedMentionIndex > 0 ? selectedMentionIndex - 1 : filteredUsers.length - 1;
      } else {
        selectedMentionIndex = selectedMentionIndex < filteredUsers.length - 1 ? selectedMentionIndex + 1 : 0;
      }
      
      renderMentionDropdown();
    }

    function confirmMentionSelection() {
      if (filteredUsers.length > 0 && selectedMentionIndex >= 0) {
        const selectedUser = filteredUsers[selectedMentionIndex];
        console.log('Confirming selection for user:', selectedUser);
        insertMention(selectedUser, editor);
        hideMentionDropdown();
        handleContentChange();
        
        if (onMention) {
          onMention(selectedUser);
        }
      }
    }

    // Check for @ mentions - IMPROVED VERSION
    function checkForMentions() {
      const textBefore = getTextBeforeCursor();
      console.log('Text before cursor:', textBefore);
      
      // Improved regex to match @ followed by any characters (including spaces for multi-word names)
      const atMatch = textBefore.match(/@([^@]*)$/);
      
      if (atMatch) {
        const query = atMatch[1].trim();
        console.log('Found @ mention with query:', query);
        showMentionDropdown(query);
      } else {
        hideMentionDropdown();
      }
    }

    // Event handlers
    function handleTypingStart() {
      if (!isTyping) {
        isTyping = true;
        if (onTypingStart) {
          onTypingStart(editor.innerHTML);
        }
      }
    }

    function handleTypingStop() {
      if (isTyping) {
        isTyping = false;
        if (onTypingStop) {
          onTypingStop(editor.innerHTML);
        }
      }
    }

    function handleContentChange() {
      const currentContent = editor.innerHTML;
      
      if (currentContent !== lastContent) {
        lastContent = currentContent;
        updateTextarea();
        
        if (onChange) {
          onChange(currentContent);
        }
        
        handleTypingStart();
        
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
          if (onType) {
            onType(currentContent);
          }
          handleTypingStop();
        }, typeDelay);
        
        // Check for mentions
        checkForMentions();
      }
    }

    // Update textarea value
    function updateTextarea() {
      if (textarea && editor) {
        textarea.value = editor.innerHTML;
        const event = new Event('change', { bubbles: true });
        textarea.dispatchEvent(event);
      }
    }

    // Apply formatting command
    function applyFormat(command, value = null) {
      try {
        editor.focus();
        document.execCommand(command, false, value);
        handleContentChange();
      } catch (err) {
        console.error(`Command "${command}" failed:`, err);
      }
    }

    // Create popup for this instance
    function createPopup() {
      const popup = document.createElement('div');
      popup.className = 'cds-editor-popup';
      popup.setAttribute('data-editor-id', selector);

      const label = document.createElement('label');
      label.textContent = 'Enter value:';

      const textInput = document.createElement('input');
      textInput.type = 'text';

      const colorInput = document.createElement('input');
      colorInput.type = 'color';
      colorInput.style.display = 'none';

      const buttonContainer = document.createElement('div');
      buttonContainer.className = 'cds-editor-popup-buttons';

      const confirmBtn = document.createElement('button');
      confirmBtn.textContent = 'Apply';
      confirmBtn.className = 'primary';

      const cancelBtn = document.createElement('button');
      cancelBtn.textContent = 'Cancel';

      buttonContainer.append(confirmBtn, cancelBtn);
      popup.append(label, textInput, colorInput, buttonContainer);
      document.body.appendChild(popup);

      return {
        show: (command, options = {}) => {
          const { placeholder = '', type = 'text', labelText = 'Enter value:' } = options;
          
          popup.style.display = 'block';
          label.textContent = labelText;
          
          if (type === 'color') {
            textInput.style.display = 'none';
            colorInput.style.display = 'block';
            colorInput.value = '#000000';
            colorInput.focus();
          } else {
            textInput.style.display = 'block';
            colorInput.style.display = 'none';
            textInput.type = type === 'url' ? 'url' : 'text';
            textInput.value = '';
            textInput.placeholder = placeholder;
            textInput.focus();
          }

          const handleConfirm = () => {
            popup.style.display = 'none';
            const value = type === 'color' ? colorInput.value : textInput.value;
            
            if (type === 'url' && value && !isValidUrl(value)) {
              alert('Please enter a valid URL (must start with http or https).');
              return;
            }
            
            if (value) {
              editor.focus();
              document.execCommand(command, false, value);
              handleContentChange();
            }
          };

          const handleCancel = () => {
            popup.style.display = 'none';
            editor.focus();
          };

          confirmBtn.onclick = handleConfirm;
          cancelBtn.onclick = handleCancel;
          
          textInput.onkeydown = (e) => {
            if (e.key === 'Enter') handleConfirm();
            if (e.key === 'Escape') handleCancel();
          };
        },
        destroy: () => {
          popup.remove();
        }
      };
    }

    // Create popup manager for this instance
    const popupManager = createPopup();

    // Define toolbar buttons
    const buttons = [
      { cmd: 'bold', icon: `<span class='icon-type-bold'></span>`, title: 'Bold' },
      { cmd: 'italic', icon: `<span class='icon-type-italic'></span>`, title: 'Italic' },
      { cmd: 'underline', icon: `<span class='icon-type-underline'></span>`, title: 'Underline' },
      { cmd: 'strikeThrough', icon: `<span class='icon-type-strikethrough'></span>`, title: 'Strikethrough' },
      { 
        cmd: 'createLink', 
        icon: `<span class='icon-link-45deg'></span>`, 
        title: 'Insert Link',
        custom: true,
        handler: () => {
          popupManager.show('createLink', {
            type: 'url',
            placeholder: 'https://example.com',
            labelText: 'Enter URL:'
          });
        }
      },
      { cmd: 'insertOrderedList', icon: `<span class='icon-list-ol'></span>`, title: 'Ordered List' },
      { cmd: 'insertUnorderedList', icon: `<span class='icon-list-ul'></span>`, title: 'Bullet List' },
      { cmd: 'justifyLeft', icon: `<span class='icon-justify-left'></span>`, title: 'Align Left' },
      { cmd: 'justifyCenter', icon: `<span class='icon-text-center'></span>`, title: 'Align Center' },
      { cmd: 'justifyRight', icon: `<span class='icon-justify-right'></span>`, title: 'Align Right' },
      { cmd: 'justifyFull', icon: `<span class='icon-justify'></span>`, title: 'Justify' },
      { 
        cmd: 'hiliteColor', 
        icon: '🖍️', 
        title: 'Highlight Color',
        custom: true,
        handler: () => {
          popupManager.show('hiliteColor', {
            type: 'color',
            labelText: 'Choose highlight color:'
          });
        }
      },
      { cmd: 'removeFormat', icon: '🧹', title: 'Clear Formatting' },
      { cmd: 'undo', icon: '↩️', title: 'Undo' },
      { cmd: 'redo', icon: '↪️', title: 'Redo' }
    ];

    // Create buttons
    buttons.forEach(({ cmd, icon, title, custom, handler }) => {
      const btn = document.createElement('button');
      btn.innerHTML = icon;
      btn.title = title;
      btn.type = 'button';
      
      btn.onclick = () => {
        if (custom && handler) {
          handler();
        } else {
          applyFormat(cmd);
        }
      };
      
      toolbar.appendChild(btn);
    });

    // Enhanced event listeners
    const events = ['input', 'keyup', 'mouseup', 'focus'];
    events.forEach(eventType => {
      editor.addEventListener(eventType, handleContentChange);
    });
    
    // Handle paste
    editor.addEventListener('paste', (e) => {
      e.preventDefault();
      const text = e.clipboardData.getData('text/plain');
      document.execCommand('insertText', false, text);
      handleContentChange();
    });

    // Handle keyboard events
    editor.addEventListener('keydown', (e) => {
      // Handle mention dropdown navigation
      if (mentionDropdown.style.display === 'block') {
        if (e.key === 'ArrowUp') {
          e.preventDefault();
          selectMentionItem('up');
          return;
        }
        if (e.key === 'ArrowDown') {
          e.preventDefault();
          selectMentionItem('down');
          return;
        }
        if (e.key === 'Enter') {
          e.preventDefault();
          confirmMentionSelection();
          return;
        }
        if (e.key === 'Escape') {
          e.preventDefault();
          hideMentionDropdown();
          return;
        }
      }
      
      const nonContentKeys = [
        'Tab', 'Shift', 'Control', 'Alt', 'Meta', 'CapsLock',
        'Home', 'End', 'PageUp', 'PageDown', 'Insert'
      ];
      
      if (!nonContentKeys.includes(e.key)) {
        handleTypingStart();
      }
    });

    // Click outside to close dropdown
    document.addEventListener('click', (e) => {
      if (!container.contains(e.target)) {
        hideMentionDropdown();
      }
    });

    // ENHANCED: Setup cursor tracking with auto-save
    setupCursorTracking();

    // Hide original textarea
    textarea.style.display = 'none';

    // Assemble and insert
    container.append(toolbar, editor);
    textarea.parentNode.insertBefore(container, textarea);

    // Return public API with ENHANCED cursor methods
    return {
      getValue: () => editor.innerHTML,
      setValue: (html) => {
        editor.innerHTML = html;
        lastContent = html;
        updateTextarea();
      },
      clear: () => {
        editor.innerHTML = '';
        lastContent = '';
        updateTextarea();
        editor.focus();
      },
      reset: () => {
        const defaultValue = textarea.defaultValue || '';
        editor.innerHTML = defaultValue;
        lastContent = defaultValue;
        updateTextarea();
        editor.focus();
      },
      focus: () => {
        editor.focus();
        setTimeout(saveCurrentCursorPosition, 10);
      },
      destroy: () => {
        clearTimeout(typingTimer);
        clearTimeout(loadUsersTimeout);
        clearTimeout(autoSaveTimer);
        container.remove();
        textarea.style.display = '';
        popupManager.destroy();
      },
      
      // Event methods
      onChange: (callback) => options.onChange = callback,
      onType: (callback) => options.onType = callback,
      onTypingStart: (callback) => options.onTypingStart = callback,
      onTypingStop: (callback) => options.onTypingStop = callback,
      onMention: (callback) => options.onMention = callback,
      onUsersLoad: (callback) => options.onUsersLoad = callback,
      onUsersError: (callback) => options.onUsersError = callback,
      onEmojiInsert: (callback) => options.onEmojiInsert = callback,
      onCursorChange: (callback) => options.onCursorChange = callback, // NEW
      
      // Get current typing state
      isTyping: () => isTyping,
      
      // Mention methods
      setUsers: (newUsers) => {
        options.users = newUsers;
        cachedUsers = [];
        lastQuery = '';
      },
      getUsers: () => Array.isArray(users) ? users : cachedUsers,
      hideMentions: () => hideMentionDropdown(),
      refreshUsers: () => {
        cachedUsers = [];
        lastQuery = '';
      },
      
      // ENHANCED: Emoji methods with auto-save
      insertEmoji: (emoji) => {
        console.log('Inserting emoji:', emoji);
        insertAtCursorPosition(emoji);
        
        if (onEmojiInsert) {
          onEmojiInsert(emoji, savedCursorPosition);
        }
        
        return true;
      },
      
      insertEmojiWithStyle: (emoji, style = {}) => {
        const styleString = Object.entries(style)
          .map(([key, value]) => `${key}: ${value}`)
          .join('; ');
        
        const emojiHTML = `<span class="emoji" style="${styleString}">${emoji}</span>`;
        insertAtCursorPosition(emojiHTML, true);
        
        if (onEmojiInsert) {
          onEmojiInsert(emoji, savedCursorPosition, style);
        }
        
        return true;
      },
      
      // ENHANCED: Advanced cursor position methods
      saveCursor: () => {
        saveCurrentCursorPosition();
        return savedCursorPosition;
      },
      
      restoreCursor: (position = null) => {
        if (position !== null) {
          savedCursorPosition = position;
        }
        restoreCurrentCursorPosition();
      },
      
      getCursorPosition: () => savedCursorPosition,
      
      // NEW: Get detailed cursor information
      getCursorInfo: () => {
        const selection = window.getSelection();
        if (selection.rangeCount === 0) return null;
        
        const range = selection.getRangeAt(0);
        const cursorPos = getCursorPosition(editor);
        
        return {
          position: savedCursorPosition,
          coordinates: cursorPos,
          textBefore: getTextBeforeCursor(),
          selectedText: selection.toString(),
          isCollapsed: range.collapsed,
          startOffset: range.startOffset,
          endOffset: range.endOffset
        };
      },
      
      // ENHANCED: Insertion methods with auto-save
      insertAtCursor: (content, isHTML = false) => {
        insertAtCursorPosition(content, isHTML);
      },
      
      insertAtPosition: (content, position, isHTML = false) => {
        savedCursorPosition = position;
        insertAtCursorPosition(content, isHTML);
      },
      
      // NEW: Append text at current cursor with auto-save
      appendText: (text) => {
        editor.focus();
        if (savedCursorPosition !== null) {
          restoreCurrentCursorPosition();
        }
        document.execCommand('insertText', false, text);
        saveCurrentCursorPosition();
        handleContentChange();
      },
      
      // NEW: Append HTML at current cursor with auto-save
      appendHTML: (html) => {
        editor.focus();
        if (savedCursorPosition !== null) {
          restoreCurrentCursorPosition();
        }
        document.execCommand('insertHTML', false, html);
        saveCurrentCursorPosition();
        handleContentChange();
      },
      
      // ENHANCED: Cursor tracking control
      enableCursorTracking: () => {
        isTrackingCursor = true;
        setupCursorTracking();
      },
      
      disableCursorTracking: () => {
        isTrackingCursor = false;
      },
      
      isCursorTracking: () => isTrackingCursor,
      
      // NEW: Auto-save control
      setAutoSaveDelay: (delay) => {
        options.autoSaveDelay = delay;
      },
      
      getAutoSaveDelay: () => options.autoSaveDelay || 100,
      
      // NEW: Method to connect with external buttons
      connectExternalButton: (buttonSelector, content, options = {}) => {
        const button = document.querySelector(buttonSelector);
        if (!button) {
          console.error('External button not found:', buttonSelector);
          return false;
        }
        
        const {
          isHTML = false,
          focusAfter = true,
          savePositionOnHover = true,
          insertMode = 'cursor' // 'cursor', 'end', 'start'
        } = options;
        
        // Save cursor position when button is hovered
        if (savePositionOnHover) {
          button.addEventListener('mouseenter', () => {
            if (editor.contains(document.activeElement)) {
              saveCurrentCursorPosition();
              console.log('Cursor saved on button hover');
            }
          });
        }
        
        // Handle button click
        button.addEventListener('click', (e) => {
          e.preventDefault();
          
          switch (insertMode) {
            case 'end':
              editor.focus();
              const endRange = document.createRange();
              endRange.selectNodeContents(editor);
              endRange.collapse(false);
              const endSelection = window.getSelection();
              endSelection.removeAllRanges();
              endSelection.addRange(endRange);
              break;
              
            case 'start':
              editor.focus();
              const startRange = document.createRange();
              startRange.selectNodeContents(editor);
              startRange.collapse(true);
              const startSelection = window.getSelection();
              startSelection.removeAllRanges();
              startSelection.addRange(startRange);
              break;
              
            default: // 'cursor'
              if (savedCursorPosition !== null) {
                restoreCurrentCursorPosition();
              }
          }
          
          // Insert content
          if (isHTML) {
            document.execCommand('insertHTML', false, content);
          } else {
            document.execCommand('insertText', false, content);
          }
          
          // Save new position
          saveCurrentCursorPosition();
          handleContentChange();
          
          // Focus editor if requested
          if (focusAfter) {
            editor.focus();
          }
          
          console.log('External content inserted:', content);
        });
        
        console.log('External button connected successfully');
        return true;
      },
      
      // NEW: Connect multiple external buttons
      connectExternalButtons: (buttonConfigs) => {
        const results = [];
        buttonConfigs.forEach(config => {
          const { selector, content, options } = config;
          const result = this.connectExternalButton(selector, content, options);
          results.push({ selector, success: result });
        });
        return results;
      },
      
      // Utility methods
      getMentions: () => {
        const mentions = [];
        const mentionElements = editor.querySelectorAll('.cds-mention-tag');
        mentionElements.forEach(el => {
          mentions.push({
            id: el.getAttribute('data-user-id'),
            name: el.getAttribute('data-user-name'),
            username: el.textContent.replace('@', '')
          });
        });
        return mentions;
      },
      
      getText: () => editor.innerText || editor.textContent || '',
      
      getPlainText: () => {
        let text = editor.innerHTML;
        text = text.replace(/<span[^>]*class="cds-mention-tag"[^>]*data-user-name="([^"]*)"[^>]*>@([^<]*)<\/span>/g, '@$2');
        text = text.replace(/<[^>]*>/g, '');
        const temp = document.createElement('div');
        temp.innerHTML = text;
        return temp.textContent || temp.innerText || '';
      },
      
      insertText: (text) => {
        insertAtCursorPosition(text);
      },
      
      insertHTML: (html) => {
        insertAtCursorPosition(html, true);
      },
      
      // Selection methods
      getSelection: () => {
        if (editor.contains(document.activeElement)) {
          const selection = window.getSelection();
          return selection.toString();
        }
        return '';
      },
      
      selectAll: () => {
        editor.focus();
        document.execCommand('selectAll');
      },
      
      // Formatting state methods
      isBold: () => document.queryCommandState('bold'),
      isItalic: () => document.queryCommandState('italic'),
      isUnderline: () => document.queryCommandState('underline'),
      
      // Enable/disable editor
      enable: () => {
        editor.contentEditable = true;
        toolbar.style.pointerEvents = 'auto';
        toolbar.style.opacity = '1';
        setupCursorTracking();
      },
      
      disable: () => {
        editor.contentEditable = false;
        toolbar.style.pointerEvents = 'none';
        toolbar.style.opacity = '0.5';
        hideMentionDropdown();
        isTrackingCursor = false;
      },
      
      // Configuration methods
      setOption: (key, value) => {
        options[key] = value;
      },
      
      getOption: (key) => options[key],
      
      // Editor state
      isDirty: () => editor.innerHTML !== (textarea.defaultValue || ''),
      
      // Event trigger methods
      triggerChange: () => handleContentChange(),
      
      // Plugin-like extensibility
      addButton: (buttonConfig) => {
        const { cmd, icon, title, handler } = buttonConfig;
        const btn = document.createElement('button');
        btn.innerHTML = icon;
        btn.title = title;
        btn.type = 'button';
        
        btn.onclick = () => {
          if (handler) {
            handler();
          } else {
            applyFormat(cmd);
          }
        };
        
        toolbar.appendChild(btn);
        return btn;
      },
      
      removeButton: (button) => {
        if (button && button.parentNode === toolbar) {
          toolbar.removeChild(button);
        }
      },
      
      // Get editor elements (advanced usage)
      getEditor: () => editor,
      getToolbar: () => toolbar,
      getContainer: () => container,
      getTextarea: () => textarea,
      
      // Debug methods
      debug: () => {
        console.log('=== EDITOR DEBUG INFO ===');
        console.log('Editor element:', editor);
        console.log('Current content:', editor.innerHTML);
        console.log('Text content:', editor.textContent);
        console.log('Saved cursor position:', savedCursorPosition);
        console.log('Is tracking cursor:', isTrackingCursor);
        console.log('Auto-save delay:', options.autoSaveDelay || 100);
        console.log('Filtered users:', filteredUsers);
        console.log('Cached users:', cachedUsers);
        console.log('Dropdown visible:', mentionDropdown.style.display === 'block');
        console.log('Current cursor position:', getCursorPosition(editor));
        console.log('Text before cursor:', getTextBeforeCursor());
        console.log('Cursor info:', this.getCursorInfo());
      },
      
      // Version info
      version: '1.2.0' // Updated version
    };
  }

  // ... (rest of the code remains the same)
  
  // Check browser support
  function isSupported() {
    return typeof document.execCommand === 'function';
  }

  // Utility function to create editor with common configurations
  function createSimple(selector, userConfig = {}) {
    const defaultConfig = {
      users: defaultUsers,
      typeDelay: 300,
      usersLoadDelay: 200,
      autoSaveDelay: 100
    };
    
    return init(selector, { ...defaultConfig, ...userConfig });
  }

  // Create editor with AJAX users
  function createWithAjax(selector, ajaxConfig, otherOptions = {}) {
    return init(selector, {
      users: ajaxConfig,
      typeDelay: 300,
      usersLoadDelay: 300,
      autoSaveDelay: 100,
      ...otherOptions
    });
  }

  // Create multiple editors at once
  function initMultiple(selectors, options = {}) {
    const editors = [];
    const selectorList = Array.isArray(selectors) ? selectors : [selectors];
    
    selectorList.forEach(selector => {
      const editor = init(selector, options);
      if (editor) {
        editors.push(editor);
      }
    });
    
    return editors;
  }

  // Global utility functions
  const utils = {
    // Clean HTML content
    cleanHTML: (html) => {
      const temp = document.createElement('div');
      temp.innerHTML = html;
      // Remove script tags and event handlers
      temp.querySelectorAll('script').forEach(el => el.remove());
      temp.querySelectorAll('*').forEach(el => {
        // Remove event attributes
        Array.from(el.attributes).forEach(attr => {
          if (attr.name.startsWith('on')) {
            el.removeAttribute(attr.name);
          }
        });
      });
      return temp.innerHTML;
    },
    
    // Extract mentions from HTML
    extractMentions: (html) => {
      const temp = document.createElement('div');
      temp.innerHTML = html;
      const mentions = [];
      temp.querySelectorAll('.cds-mention-tag').forEach(el => {
        mentions.push({
          id: el.getAttribute('data-user-id'),
          name: el.getAttribute('data-user-name'),
          username: el.textContent.replace('@', '')
        });
      });
      return mentions;
    },
    
    // Convert HTML to plain text with mentions
    htmlToText: (html) => {
      let text = html;
      // Replace mention spans with @username
      text = text.replace(/<span[^>]*class="cds-mention-tag"[^>]*>@([^<]*)<\/span>/g, '@$1');
      // Remove all other HTML tags
      text = text.replace(/<[^>]*>/g, '');
      // Decode HTML entities
      const temp = document.createElement('div');
      temp.innerHTML = text;
      return temp.textContent || temp.innerText || '';
    },
    
    // Validate user object
    validateUser: (user) => {
      return user && 
             typeof user === 'object' && 
             user.id && 
             user.name && 
             typeof user.name === 'string';
    },
    
    // Generate username from name
    generateUsername: (name) => {
      return name.toLowerCase()
        .replace(/[^a-z0-9\s]/g, '')
        .replace(/\s+/g, '');
    }
  };

  // Return public API
  return { 
    init, 
    isSupported,
    createSimple,
    createWithAjax,
    initMultiple,
    utils,
    version: '1.2.0' // Updated version
  };
})();