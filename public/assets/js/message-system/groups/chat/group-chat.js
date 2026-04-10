const GroupChat = {
    config: null,
    currentGroupId: null,
    lastMessageId: 0,
    isFetching: false,
    
    init() {
        this.config = window.groupChatConfig || {};
        this.bindEvents();
        this.initializeChat();
    },
    
    bindEvents() {
        // Group chat item clicks
        $(document).on("click", ".group-chat-item", (e) => {
            this.handleGroupChatClick(e);
        });
        
        // Other group info clicks
        $(document).on("click", ".other-group-info", (e) => {
            this.handleOtherGroupClick(e);
        });
        
        // Member selection
        $(document).on("change", ".member-checkbox", (e) => {
            this.handleMemberSelection(e);
        });
    },
    
    handleGroupChatClick(e) {
        const $item = $(e.currentTarget);
        const url = $item.data("href");
        const conversationId = $item.data("unique-id");
        const groupId = $item.data("group-id");
        
        $(".loader").show();
        if (window.innerWidth < 991) {
            $(".message-container").addClass("active");
        }
        $(".group-chat-item").removeClass("active-chat");
        $item.addClass("active-chat");
        
        this.loadChatAjax(conversationId, groupId);
        history.pushState(null, '', url);
    },
    
    handleOtherGroupClick(e) {
        const $item = $(e.currentTarget);
        const url = $item.data("href");
        const conversationId = $item.data("unique-id");
        const groupId = $item.data("group-id");
        
        if (window.innerWidth < 991) {
            $(".message-container").addClass("active");
        }
        
        this.loadGroupInfo(conversationId, groupId);
        history.pushState(null, '', url);
    },
    
    handleMemberSelection(e) {
        const $checkbox = $(e.currentTarget);
        const $memberItem = $checkbox.closest('.member-item');
        
        if ($checkbox.is(':checked')) {
            $memberItem.addClass('selected');
        } else {
            $memberItem.removeClass('selected');
        }
    },
    
    loadChatAjax(conversationId, groupId) {
        $.ajax({
            url: this.config.baseUrl + 'group/chat-ajax/' + conversationId,
            method: 'GET',
            data: { group_id: groupId },
            success: (response) => {
                if (response.status) {
                    $(".message-container").html(response.contents);
                    this.currentGroupId = groupId;
                    if (typeof MessageHandler !== 'undefined') {
                        MessageHandler.initializeMessageHandlers();
                    }
                    if (typeof SocketHandler !== 'undefined') {
                        SocketHandler.initializeSocket(groupId);
                    }
                }
            },
            error: (xhr, status, error) => {
                console.error('Error loading chat:', error);
            }
        });
    },
    
    loadGroupInfo(conversationId, groupId) {
        $.ajax({
            url: this.config.baseUrl + 'group/get-group-info/' + groupId,
            method: 'GET',
            success: (response) => {
                if (response.status) {
                    $(".message-container").html(response.contents);
                }
            }
        });
    },
    
    initializeChat() {
        if (this.config.groupId) {
            this.initializeGroupChat(this.config.groupId);
            if (typeof SocketHandler !== 'undefined') {
                SocketHandler.initializeSocket(this.config.groupId);
            }
        }
    },
    
    initializeGroupChat(groupId) {
        this.currentGroupId = groupId;
        this.lastMessageId = 0;
        this.isFetching = false;
        
        // Initialize emoji picker if available
        if (typeof EmojiPicker !== 'undefined' && document.querySelector("#sendmsgg")) {
            new EmojiPicker(".message-emoji-icon", {
                targetElement: "#sendmsgg"
            });
        }
    },
    
    // Backward compatibility functions
    conversationList() {
        // Existing conversation list functionality
        if (typeof window.conversationList === 'function') {
            window.conversationList();
        }
    },
    
    openInMobile() {
        if (this.config.openfor === "mobile") {
            let url = window.location.pathname;
            let lastParam = url.split("/").filter(Boolean).pop();
            let attemptCount = 0;

            let checkExist = setInterval(function () {
                let $el = $('.groupchatdiv' + lastParam);
                if ($el.length) {
                    clearInterval(checkExist);
                    $el.trigger('click');
                }

                attemptCount++;
                if (attemptCount > 50) {
                    clearInterval(checkExist);
                    console.log("Element not found after multiple attempts.");
                }
            }, 300);
        }
    }
}; 