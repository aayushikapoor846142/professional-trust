@extends('admin-panel.layouts.app')
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/plugins/chatapp/group-messages/group-messages.css?v='.mt_rand()) }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/chatapp/group-messages/group-messages-dynamic.css?v='.mt_rand()) }}">
<meta name="user-id" content="{{ auth()->user()->id }}">
<meta name="user-name" content="{{ auth()->user()->first_name . ' ' . auth()->user()->last_name }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('page-submenu')
@php 
$page_arr = [
    'page_title' => 'Group Messages',
    'page_description' => 'Manage group conversations and team communications.',
    'page_type' => 'group-messages',
];
@endphp
{!! pageSubMenu('message',$page_arr) !!}
@endsection
@section('content')
<div class="group-messages-container">
    <!-- Sidebar -->
    
    <div class="group-messages-sidebar" id="groupSidebar">
        @include('admin-panel.01-message-system.group-messages.components.sidebar-header',['groups' => $groups])
        @include('admin-panel.01-message-system.group-messages.components.sidebar-content',['groups' => $groups,'group_id' => $group_id])
    </div>
    <!-- Main Chat Area -->
    <div class="group-messages-main">
        @if(!$welcome_page)
            @include('admin-panel.01-message-system.group-messages.components.chat-container',['group_id' => $group_id,'group' => $group,'group_messages' => $group_messages,'last_msg_id' => $last_message_id,'first_msg_id' => $first_message_id,'group_members' => $group_members])
        @else
            @include('admin-panel.01-message-system.group-messages.components.welcome-chat')
        @endif
    </div>
</div>
@endsection

@section('javascript')
<script src="{{url('assets/js/custom-file-upload.js')}}"></script>
<link rel="stylesheet" href="{{ url('assets/css/custom-file-upload.css') }}">

<script src="{{ asset('assets/plugins/chatapp/group-messages/group-messages.js?v='.mt_rand()) }}"></script>
<script>
// Initialize group messages functionality
const cdsGroupMessagesApp = {
    // DOM elements
    elements: {
        messageInput: null,
        sendBtn: null,
        messagesContainer: null,
        optionsBtn: null,
        optionsMenu: null,
        filesBtn: null,
        filesPanel: null,
        overlay: null,
        closeFilesPanel: null,
        searchBtn: null,
        chatSearchBar: null,
        closeChatSearch: null,
        sidebarSearch: null,
        emojiBtn: null,
        attachBtn: null,
        hasPreviousMessages: {{$has_previous_messages??0}},
        lastMessageId: {{$last_message_id??0}},
        firstMessageId: {{$first_message_id??0}}
    },

    // Initialize the group messages application
    init() {
        // console.log('Initializing cdsGroupMessagesApp with message IDs:', {
        //     lastMessageId: this.lastMessageId,
        //     firstMessageId: this.firstMessageId
        // });
        
        this.cacheElements();
        this.bindEvents();
        this.initializeAutoResize();
        this.initializeTouchEvents();
        
        // Log final state after initialization
        this.logCurrentState();
    },

    // Cache DOM elements
    cacheElements() {
        this.elements.messageInput = document.getElementById('messageInput');
        this.elements.sendBtn = document.getElementById('sendBtn');
        this.elements.messagesContainer = document.getElementById('messagesContainer');
        this.elements.optionsBtn = document.getElementById('optionsBtn');
        this.elements.optionsMenu = document.getElementById('optionsMenu');
        this.elements.filesBtn = document.getElementById('filesBtn');
        this.elements.filesPanel = document.getElementById('filesPanel');
        this.elements.overlay = document.getElementById('overlay');
        this.elements.closeFilesPanel = document.getElementById('closeFilesPanel');
        this.elements.searchBtn = document.getElementById('searchBtn');
        this.elements.chatSearchBar = document.getElementById('chatSearchBar');
        this.elements.closeChatSearch = document.getElementById('closeChatSearch');
        this.elements.sidebarSearch = document.getElementById('sidebarSearch');
        this.elements.emojiBtn = document.getElementById('emojiBtn');
        this.elements.attachBtn = document.getElementById('attachBtn');
    },

    // Bind event listeners
    bindEvents() {
        // Options menu
        if (this.elements.optionsBtn) {
            this.elements.optionsBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleOptionsMenu();
            });
        }

        // Files panel
        if (this.elements.filesBtn) {
            this.elements.filesBtn.addEventListener('click', () => this.openFilesPanel());
        }
        if (this.elements.closeFilesPanel) {
            this.elements.closeFilesPanel.addEventListener('click', () => this.closeFilesPanel());
        }
        if (this.elements.overlay) {
            this.elements.overlay.addEventListener('click', () => this.closeFilesPanel());
        }

        // Search functionality
        if (this.elements.searchBtn) {
            this.elements.searchBtn.addEventListener('click', () => this.toggleChatSearch());
        }
        if (this.elements.closeChatSearch) {
            this.elements.closeChatSearch.addEventListener('click', () => this.closeChatSearch());
        }

        // Sidebar search
        if (this.elements.sidebarSearch) {
            this.elements.sidebarSearch.addEventListener('input', (e) => this.filterGroups(e.target.value));
        }

        // Close menus on outside click
        document.addEventListener('click', () => {
            if (this.elements.optionsMenu) {
                this.elements.optionsMenu.classList.remove('active');
            }
        });

        // Close files panel on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.elements.filesPanel && this.elements.filesPanel.classList.contains('active')) {
                this.closeFilesPanel();
            }
        });

        // Option menu items
        if (this.elements.optionsMenu) {
            this.elements.optionsMenu.addEventListener('click', (e) => {
                const option = e.target.closest('.group-messages-option-item');
                if (option) {
                    this.handleOptionClick(option.textContent.trim());
                }
            });
        }
    },

    // Toggle options menu
    toggleOptionsMenu() {
        console.log('Toggle options menu called');
        if (this.elements.optionsMenu) {
            this.elements.optionsMenu.classList.toggle('active');
            console.log('Options menu active:', this.elements.optionsMenu.classList.contains('active'));
        } else {
            console.log('Options menu element not found');
        }
    },

    // Open files panel
    openFilesPanel() {
        console.log('Open files panel called');
        if (this.elements.filesPanel && this.elements.overlay) {
            this.elements.filesPanel.classList.add('active');
            this.elements.overlay.classList.add('active');
            console.log('Files panel opened');
        } else {
            console.log('Files panel or overlay not found');
        }
    },

    // Close files panel
    closeFilesPanel() {
        if (this.elements.filesPanel && this.elements.overlay) {
            this.elements.filesPanel.classList.remove('active');
            this.elements.overlay.classList.remove('active');
        }
    },

    // Toggle chat search
    toggleChatSearch() {
        console.log('Toggle chat search called');
        if (this.elements.chatSearchBar) {
            this.elements.chatSearchBar.classList.toggle('active');
            if (this.elements.chatSearchBar.classList.contains('active')) {
                const searchInput = document.getElementById('chatSearchInput');
                if (searchInput) {
                    searchInput.focus();
                }
            }
            console.log('Chat search active:', this.elements.chatSearchBar.classList.contains('active'));
        } else {
            console.log('Chat search bar not found');
        }
    },

    // Close chat search
    closeChatSearch() {
        if (this.elements.chatSearchBar) {
            this.elements.chatSearchBar.classList.remove('active');
        }
    },

    // Filter groups in sidebar
    filterGroups(searchTerm) {
        // Implement group filtering logic here
        console.log('Filtering groups with:', searchTerm);
    },

    // Handle option menu clicks
    handleOptionClick(option) {
        switch(option) {
            case 'Leave Group':
                if (confirm('Are you sure you want to leave this group?')) {
                    console.log('Group left');
                }
                break;
            case 'Clear Chat':
                if (confirm('Are you sure you want to clear this chat? This action cannot be undone.')) {
                    if (this.elements.messagesContainer) {
                        this.elements.messagesContainer.innerHTML = '';
                    }
                    console.log('Chat cleared');
                }
                break;
            case 'Group Info':
                console.log('Show group info');
                break;
        }
        this.toggleOptionsMenu();
    },

    // Initialize auto-resize for textarea
    initializeAutoResize() {
        if (this.elements.messageInput) {
            this.elements.messageInput.addEventListener('input', () => {
                this.elements.messageInput.style.height = 'auto';
                this.elements.messageInput.style.height = Math.min(this.elements.messageInput.scrollHeight, 120) + 'px';
            });
        }
    },

    // Initialize touch events for mobile slide panel
    initializeTouchEvents() {
        if (!this.elements.filesPanel) return;
        
        let startX = 0;
        let currentX = 0;
        let isDragging = false;

        this.elements.filesPanel.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            isDragging = true;
        });

        this.elements.filesPanel.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            
            currentX = e.touches[0].clientX;
            const diffX = startX - currentX;
            
            // Only allow right-to-left swipe (closing the panel)
            if (diffX > 0) {
                const translateX = Math.max(-diffX, -500);
                this.elements.filesPanel.style.transform = `translateX(${translateX}px)`;
            }
        });

        this.elements.filesPanel.addEventListener('touchend', (e) => {
            if (!isDragging) return;
            
            const diffX = startX - currentX;
            
            // If swiped more than 100px to the left, close the panel
            if (diffX > 100) {
                this.closeFilesPanel();
            } else {
                // Reset position
                this.elements.filesPanel.style.transform = '';
            }
            
            isDragging = false;
        });
    },

    // Get current message IDs
    getMessageIds() {
        return {
            lastMessageId: this.lastMessageId,
            firstMessageId: this.firstMessageId
        };
    },

    // Update message IDs
    updateMessageIds(lastId, firstId) {
        if (lastId !== undefined) {
            this.lastMessageId = lastId;
        }
        if (firstId !== undefined) {
            this.firstMessageId = firstId;
        }
        console.log('Message IDs updated:', this.lastMessageId, this.firstMessageId);
    },

    // Log current state for debugging
    logCurrentState() {
        console.log('Current cdsGroupMessagesApp state:', {
            lastMessageId: this.lastMessageId,
            firstMessageId: this.firstMessageId,
            elements: this.elements
        });
    }
};

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    cdsGroupMessagesApp.init();
});
</script>
@endsection
