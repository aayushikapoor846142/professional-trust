@extends('admin-panel.layouts.app')
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/plugins/chatapp/group-messages/group-messages.css?v='.mt_rand()) }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/chatapp/individual-chats/individual-chat.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/custom-file-upload.css') }}">
<meta name="user-id" content="{{ auth()->user()->id }}">
<meta name="user-name" content="{{ auth()->user()->first_name . ' ' . auth()->user()->last_name }}">
@endsection
@section('page-submenu')
@php 
$page_arr = [
    'page_title' => 'Messages',
    'page_description' => 'Manage individual and group messages via message centre.',
    'page_type' => 'message-center',
];
@endphp
{!! pageSubMenu('message',$page_arr) !!}
@endsection
@section('content')
<div class="CdsIndividualChat-container">
        <!-- Sidebar -->
        <div class="CdsIndividualChat-sidebar" id="chatSidebar">
            <div class="CdsIndividualChat-sidebar-header">
                <h1 class="CdsIndividualChat-sidebar-title">Chats</h1>
                <div class="CdsIndividualChat-search-container">
                    <svg class="CdsIndividualChat-search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" class="CdsIndividualChat-search-input" placeholder="Search Messages Or Users" id="sidebarSearch">
                </div>
            </div>
            
            <div class="CdsIndividualChat-recent-section">
                <!-- <div class="CdsIndividualChat-recent-header">Recent</div> -->
                <div class="CdsIndividualChat-chat-list" id="chatList">
                @include('admin-panel.01-message-system.individual-chats.chat-sidebar',['chat_users' => $chat_users])
                </div>
            </div>
        </div>
        

        <!-- Main Chat Area -->
        <div class="CdsIndividualChat-main">
            @if(!$welcome_page)
            @include('admin-panel.01-message-system.individual-chats.message-container',['chat_id' => $chat_id,'chat' => $chat])
            @else
            @include('admin-panel.01-message-system.individual-chats.welcome-chat')
            @endif
           
        </div>
    </div>
@endsection

@section('javascript')
<script src="{{ asset('assets/js/custom-file-upload.js') }}"></script>
<script src="{{ asset('assets/plugins/chatapp/individual-chats/individual-chat.js') }}"></script>
<script>
        // Initialize chat functionality
        const cdsIndividualChatApp = {
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
                lastMessageId: {{$last_message_id??0}},
                firstMessageId: {{$first_message_id??0}}
            },

            // Initialize the chat application
            init() {
                this.cacheElements();
                this.bindEvents();
                this.initializeAutoResize();
                this.initializeTouchEvents();
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
                this.elements.optionsBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.toggleOptionsMenu();
                });

                // Files panel
                this.elements.filesBtn.addEventListener('click', () => this.openFilesPanel());
                // this.elements.closeFilesPanel.addEventListener('click', () => this.closeFilesPanel());
                // this.elements.overlay.addEventListener('click', () => this.closeFilesPanel());

                // Search functionality
                this.elements.searchBtn.addEventListener('click', () => this.toggleChatSearch());
                this.elements.closeChatSearch.addEventListener('click', () => this.closeChatSearch());

                // Sidebar search
                if (this.elements.sidebarSearch) {
                    this.elements.sidebarSearch.addEventListener('input', (e) => this.filterChats(e.target.value));
                }

                // Close menus on outside click
                document.addEventListener('click', () => {
                    if (this.elements.optionsMenu) {
                        this.elements.optionsMenu.classList.remove('active');
                    }
                });

                // Close files panel on Escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.elements.filesPanel.classList.contains('active')) {
                        this.closeFilesPanel();
                    }
                });

                // Option menu items
                if (this.elements.optionsMenu) {
                    this.elements.optionsMenu.addEventListener('click', (e) => {
                        const option = e.target.closest('.CdsIndividualChat-option-item');
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
                this.elements.filesPanel.classList.remove('active');
                this.elements.overlay.classList.remove('active');
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
                this.elements.chatSearchBar.classList.remove('active');
            },

            // Filter chats in sidebar
            filterChats(searchTerm) {
                // Implement chat filtering logic here
                console.log('Filtering chats with:', searchTerm);
            },

            // Handle option menu clicks
            handleOptionClick(option) {
                switch(option) {
                    case 'Block Chat':
                        if (confirm('Are you sure you want to block this chat?')) {
                            console.log('Chat blocked');
                        }
                        break;
                    case 'Clear Chat':
                        if (confirm('Are you sure you want to clear this chat? This action cannot be undone.')) {
                            this.elements.messagesContainer.innerHTML = '';
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
            }
        };

        // Initialize the application when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            cdsIndividualChatApp.init();
        });
    </script>
@endsection