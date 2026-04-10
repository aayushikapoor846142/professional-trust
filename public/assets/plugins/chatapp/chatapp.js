let openChats = {};
let openChatBot = [];
var cbDraftTimer = 0;
// chatbot variable
var cbChatBotContent = [];
var cbFileUploadArea = [];
var cbModal = [];
var cdPreviewContainer = [];
var cbUploadButton = [];
var cbSelectedFiles = [];
var cbFormFiles = [];
var cbFiles = [];
var cbMessageInput = [];
var cbPreviewItem = "";
var cbFileExtension = "";
var cbFilePreview = "";

var typingTimers = {};
var currentlyTyping = {};
// end
$(document).ready(async function () {
    // localStorage.removeItem("openBots_"+currentUserId);
    getMininmizedBots =
        JSON.parse(localStorage.getItem("minimizedBots_" + currentUserId)) || [];
    localStorage.setItem(
        "minimizedBots_" + currentUserId,
        JSON.stringify(getMininmizedBots)
    );
    openBots =
        JSON.parse(localStorage.getItem("openBots_" + currentUserId)) || [];
    // alert(localStorage.getItem("openBots_" + currentUserId));
    if (openBots.length > 0) {
        $.each(openBots, async function (index, botId) {
            var bot = botId.split("_");
            if (bot[0] == "userChat") {
                await checkChatExists(bot[1], currentUserId);
            } else if (bot[0] == "groupChat") {
                await checkGroupExists(bot[1], currentUserId);
            }
        });
        $.each(openBots, async function (index, botId) {
            var bot = botId.split("_");
            if (bot[0] == "userChat") {
                // window.Echo.leave(`chatBot.${bot[1]}`);
                openUserChat(bot[1], "onLoad");
                if (getMininmizedBots.indexOf("userChat_" + bot[1]) !== -1) {
                    $("#chatbot-" + bot[1]).addClass("minimizedBot");
                    toggleBots($("#chatbot-" + bot[1]), true);
                }
            } else if (bot[0] == "groupChat") {
                openGroupChat(bot[1], "onLoad");
                if (getMininmizedBots.indexOf("groupChat_" + bot[1]) !== -1) {
                    $("#group-chatbot-" + bot[1]).addClass("minimizedBot");
                    toggleBots($("#group-chatbot-" + bot[1]), true);
                }
                // window.Echo.leave(`groupChatBot.${bot[1]}`);
            }
        });
    }

    // Websocket Initialize
    window.Echo.leave(`user.` + currentUserId);
    window.Echo.private(`user.${currentUserId}`).listen("UserSocket", (e) => {
        const response = e.data;
        if (response.action == "update_messaging_box") {
            if (response.sender_id == currentUserId || response.receiver_id == currentUserId) {
               //  // refreshMessaging();
            }
            if (response.sender_id == currentUserId) {
                if (response.sender_total_unread_count > 0) {
                    $(".total-messages-count").html(
                        response.total_unread_count
                    );
                } else {
                    $(".total-messages-count").html("");
                }
            }
            if (response.receiver_id == currentUserId) {
                if (response.receiver_total_unread_count > 0) {
                    $(".total-messages-count").html(
                        response.receiver_total_unread_count
                    );
                } else {
                    $(".total-messages-count").html("");
                }
            }
        }
        if (response.action == "auto_cancellation_booking") {
            if (response.receiver_id == currentUserId) { 
                window.location.href=BASEURL+"/appointments/appointment-booking/";
            }
        }
        if (response.action == "new_chat_message") {
            if (response.receiver_id == currentUserId) {
                if (response.chat_id != ACTIVE_CHAT) {
                    newMessageNotif(response.message);
                    $(".messages-count").css("opacity", "1"); // Set opacity to 50%
                    $(".messages-count").html(response.unread_count);
                }
                if (ACTIVE_CHAT != 0) {
                    conversationList();
                }
                var index = openBots.indexOf("userChat_" + response.chat_id);
                if (response.chat_id != ACTIVE_CHAT) {
                    if (index !== -1) {
                        fetchChatBotMessages(response.chat_id, response.last_message_id, "user socket new_chat_message");
                    }
                }
            }
        }
        if (response.action == "new_group_message") {
            //  alert('notif group')
            if (response.receiver_id == currentUserId) {
                if (response.group_id != ACTIVE_GROUP_CHAT) {
                    newMessageNotif(response.message);
                    if (response.total_unread_count > 0) {
                        $(".group-messages-count").css("opacity", "1"); // Set opacity to 50%
                        $(".group-messages-count").html(response.unread_count);
                    }
                }
                if (ACTIVE_GROUP_CHAT != 0) {
                    // updateConversationList();
                }
            }
        }
        if (response.action == "new_chat_request") {
            if (response.receiver_id == currentUserId) {
                if (document.getElementsByClassName("chatrequest-count")) {
                    $(".chatrequest-count").css("opacity", "1"); // Set opacity to 50%
                    $(".chatrequest-count").text(response.count);
                }
            }
            if (window.location.href == (BASEURL + "/connect")) {
                toConnectList(1);
                pendingConversationList();
                connectConversationList('followers', this, 'onload');
            }
        }
        if (response.action == "new_notification") {
            if (response.receiver_id == currentUserId) {
                if (document.getElementsByClassName("notification-count")) {
                    //     alert(response.message);
                    $(".notification-count").css("opacity", "1"); // Set opacity to 50%
                    $(".notification-count").text(response.count);
                    newMessageNotif(response.message);
                }
            }
        }
        if (response.action == "user_online") {
            if (response.receiver_id == currentUserId) {
                if (document.getElementsByClassName("chatrequest-count")) {
                    $(".chatrequest-count").text(response.count);
                }
                if (ACTIVE_CHAT != 0) {
                    conversationList();
                }
                if (ACTIVE_GROUP_CHAT != 0) {
                    updateConversationList()
                }
                if (ACTIVE_CHAT != 0) {
                    $(".user-chat-item[data-chat-id=" + response.chat_id + "]")
                        .find(".login-status")
                        .removeClass("status-offline");
                    $(".user-chat-item[data-chat-id=" + response.chat_id + "]")
                        .find(".login-status")
                        .addClass("status-online");
                }
                $(
                    "#chatMessages" + response.chat_id + " .login-status"
                ).removeClass("status-offline");
                $(
                    "#chatMessages" + response.chat_id + " .login-status"
                ).addClass("status-online");

                $(".message-header .login-status").removeClass(
                    "status-offline"
                );
                $(".message-header .login-status").addClass("status-online");

                $(".chat-profile-status").html("Active");
                $(".chat-profile-status").removeClass("text-danger");
                $(".chat-profile-status").addClass("text-success");
            }
        }
        if (response.action == "user_logout") {
            if (response.receiver_id == currentUserId) {
                if (ACTIVE_CHAT != 0) {
                    conversationList();
                }
                if (ACTIVE_GROUP_CHAT != 0) {
                    updateConversationList()
                }
                if (ACTIVE_CHAT != 0) {
                    $(".user-chat-item[data-chat-id=" + response.chat_id + "]")
                        .find(".login-status")
                        .removeClass("status-online");
                    $(".user-chat-item[data-chat-id=" + response.chat_id + "]")
                        .find(".login-status")
                        .addClass("status-offline");
                }
                $(
                    "#chatMessages" + response.chat_id + " .login-status"
                ).removeClass("status-online");
                $(
                    "#chatMessages" + response.chat_id + " .login-status"
                ).addClass("status-offline");

                $(".message-header .login-status").removeClass("status-online");
                $(".message-header .login-status").addClass("status-offline");

                $(".chat-profile-status").html("Inactive");
                $(".chat-profile-status").removeClass("text-success");
                $(".chat-profile-status").addClass("text-danger");
            }
        }

        if (response.action == "chat_deleted") {
            closeBot(response.chat_id, "userChat");
            //if (response.receiver_id == currentUserId) {
            if (ACTIVE_CHAT != 0) {
                if (ACTIVE_CHAT == response.chat_id) {
                    window.location.href = BASEURL + "/message-centre";
                } else {
                    conversationList();
                }
            }
            // }
        }
        if (response.action == "deleted_msg_for_everyone") {
            if (ACTIVE_CHAT != 0) {
                conversationList();
            }
            if (ACTIVE_GROUP_CHAT != 0) {
                updateConversationList();
            }
            // // refreshMessaging();
        }
        if (response.action == "new_case_request_comment") {
            if ((response.professional_id == currentUserId) || (response.client_id == currentUserId)) {
                $.ajax({
                    type: "POST",
                    url: BASEURL +
                        "/case-with-professionals/view-request-comments/" +
                        response.case_request_id,
                    dataType: "json",
                    data: {
                        _token: csrf_token,
                    },
                    success: function (response) {
                        $("#case-comments").html(response.contents);

                    },
                    error: function (xhr, status, error) {
                        console.error("Error in reaction:", error);
                    },
                });

            }



        }
    });

     // Websocket Initialize
     window.Echo.leave(`user-notif.` + currentUserId);
     window.Echo.private(`user-notif.${currentUserId}`).listen("UserNotification", (e) => {
         const response = e.data;
          
         if (response.action == "appointment_booking") {
           if (response.receiver_id == currentUserId) {
                newMessageNotif(response.message);
             }
         }
 

        if (response.action == "award_case") {
            console.log(response);
            if (response.receiver_id == currentUserId) {
                    newMessageNotif(response.message);
                    renderNotificationMessage();
                    if (document.getElementsByClassName("posting-case-notification-count")) {
                        $(".posting-case-notification-count").text("["+response.count+"]");
                    }
              }
        }

        if (response.action == "accept_retain_agreement") {
            if (response.receiver_id == currentUserId) {
                newMessageNotif(response.message);
                renderNotificationMessage();
                if (document.getElementsByClassName("accept-agreement-notification-count")) {
                    $(".accept-agreement-notification-count").text("["+response.count+"]");
                }
              }
        }
     });
     $(document).on("click", ".chatbot-header", function (e) {
        // Ignore clicks on .chatbot-user or .chatbot-close
        if ($(e.target).closest('.chatbot-user').length || $(e.target).closest('.chatbot-close').length) {
            return;
        }
        var cid = $(this).attr("data-chatId");
        toggleChatbot(cid);
    });
    $(document).on("input", ".chatInput,#chatMessages"+ACTIVE_CHAT+" #sendmsgg", function () {
        var chat_id = $(this).data("id");
        var inputLength = $(this).val().length;

        // Clear existing typing timer
        clearTimeout(typingTimers[chat_id]);
        
        // Check if chat is initialized
        if (!openChats[chat_id]) {
            console.log('Chat not initialized yet:', chat_id);
            return;
        }
        
        // Handle typing indicator
        if (inputLength > 0) {
            if (!currentlyTyping[chat_id]) {
                currentlyTyping[chat_id] = true;
                // Try to send typing status
                whisperTyping(chat_id, true);
            }
            
            typingTimers[chat_id] = setTimeout(function() {
                if (currentlyTyping[chat_id]) {
                    currentlyTyping[chat_id] = false;
                    whisperTyping(chat_id, false);
                }
            }, 1500);
        } else {
            if (currentlyTyping[chat_id]) {
                currentlyTyping[chat_id] = false;
                whisperTyping(chat_id, false);
            }
        }
        if (inputLength > 0 && inputLength % 5 === 0) {
            saveChatToDraft($(this).val(), chat_id);
        }
        
        // Optional: Add debounce/throttle to avoid excessive calls
        clearTimeout(cbDraftTimer);
        cbDraftTimer = setTimeout(() => {
            if (inputLength > 0 && inputLength % 5 === 0) {
                saveChatToDraft($(this).val(), chat_id);
                 // Save any remaining message
            }
        }, 3000);
    });
    $(document).on("keypress", ".chatInput", function (e) {
        var chat_id = $(this).data("id");
        var key = e.which;
        if (key == 13) {
            // the enter key code
            sendChatBotMessage(chat_id);
            return false;
        }
    });
    // Handle blur event to stop typing
    $(document).on("blur", ".chatInput", function () {
        var chat_id = $(this).data("id");
        
        // Stop typing indicator
        if (currentlyTyping[chat_id]) {
            currentlyTyping[chat_id] = false;
            whisperTyping(chat_id, false);
        }
        clearTimeout(typingTimers[chat_id]);
        
        // Your existing blur logic
        handleBlur(this,chat_id);
    });
});

function openChatforMobileDesktop(chatUniqueId, chatId) {
    if (window.innerWidth <= 768) {
        console.log("Mobile Screen");
        window.location.href =
            BASEURL +
            "/message-centre/chat/" +
            chatUniqueId +
            "?openfor=mobile";
    } else {
        openUserChat(chatId);
    }
}

async function openUserChat(chatId, openOn = "click") {
    const index = openBots.indexOf("userChat_" + chatId);
    if (openOn == "click") {
        if (index !== -1) return;
    }

    if (openBots.length >= 3) {
        if (index === -1) {
            var first_index = openBots[0];
            var split = first_index.split("_");
            openBots.shift();
            localStorage.setItem(
                "openBots_" + currentUserId,
                JSON.stringify(openBots)
            );

            if (split[0] == "userChat") {
                closeBot(split[1], "userChat");
            } else {
                closeBot(split[1], "groupChat");
            }
        }
    }
    if (index === -1) {
        openBots.push("userChat_" + chatId);
    }

    localStorage.setItem("openBots_" + currentUserId, JSON.stringify(openBots));
    // var totalOpenBotCount = $(".chatbot-header").length;

    let chatBox = $(`
        <div class="chatbot-window userbot-window" data-id="${chatId}" id="chatbot-${chatId}">
           <div class="chatbot-header ">
                <div class="chatbot-user">
                    <div class="chat-avatar">
                        <div class="chatbot-user-icon" data-initial=" "></div>
                    </div>
                    <div class="chatbot-name " >
                        &nbsp;
                    </div>
                </div>
            </div>
            ${CHAT_LOADER}
        </div>
    `);
    $(".chat-box-area").append(chatBox);

    $("#chatbot-" + chatId).show();

    initializeChatSocket(chatId);
    fetchChatBot(chatId);
}

function checkChatExists(chatId, user_id) {
    $.ajax({
        type: "post",
        url: BASEURL + "/message-centre/check-chat-exists",
        data: {
            _token: csrf_token,
            chat_id: chatId,
            user_id: user_id,
        },
        dataType: "json",
        success: function (data) {
            if (data.status) {
                if (!data.exists) {
                    closeBot(chatId, "userChat");
                }
            }
        },
    });
}

function initializeChatSocket(chatId) {
    openChats[chatId] = {
        open: true,
        first_msg_id: 0,
        last_msg_id: 0,
        presenceChannel: null,
        channelReady: false 
    };

    $(document).on("blur", "#chatbot-" + chatId + "-sendmsg", function () {
        handleBlur(this,chatId);
    });
    $(document).on("click", ".cb-remove-file", function () {
        var index = $(this).data("index");
        var chat_id = $(this).data("id");
        cbFormFiles[chat_id].splice(index, 1);

        cbUpdatePreview(chat_id);
    });
    $(document).on("click", ".cb-close-uploader", function () {
        var chat_id = $(this).data("id");
        cbFormFiles[chat_id] = [];
        cbFiles[chat_id] = [];
        $("#chatbot-upload-file-" + chat_id).hide();
        cbUpdatePreview(chat_id);
    });
    // document.querySelector('.chatbot-body').addEventListener("dragleave", (e) => {
    //     e.preventDefault();
    //           var chatBotId = $(this).data("id");

    //     if (cbFormFiles[chatBotId].length == 0) {
    //         $("#chatbot-upload-file-"+chatBotId).hide();
    //     }
    // });

    $(document).on("mouseleave", ".chatbot-upload-file", function () {
        var chatBotId = $(this).data("id");
        console.log(cbFormFiles);
        // alert(chatBotId);

        if (cbFormFiles[chatBotId].length == 0) {
            $("#chatbot-upload-file-" + chatBotId).hide();
        }
        // $('.cb-close-uploader').click();
    });

    // Store the presence channel reference
    setTimeout(() => {
        initializePresenceChannel(chatId);
    }, 300);
    window.Echo.leave(`chatMessageReaction.` + chatId);
    window.Echo.private(`chatMessageReaction.` + chatId).listen(
        "MessageReactionAdded",
        (event) => {
            const messageUniqueId = event.messageUniqueId;
            const msgReaction = event.messageReaction;
            const reactionUniqueId = event.reactionUniqueId;
            if (event.action == "add_reaction") {
                // alert("skakkslaksla");
                if (event.sender_id == currentUserId) {
                    var messageHtml =
                        '<a class="remove-reaction reaction" data-id="' +
                        reactionUniqueId +
                        '" id="MsgReaction' +
                        reactionUniqueId +
                        '" href="javascript:;" onclick="removeChatReaction(this)">' +
                        msgReaction +
                        "</a>";
                } else {
                    var messageHtml =
                        '<a class=" reaction"  id="MsgReaction' +
                        reactionUniqueId +
                        '" href="javascript:;" >' +
                        msgReaction +
                        "</a>";
                }
                $(".chatbot-body #message-" + messageUniqueId)
                    .find(".msg-reactions")
                    .children()
                    .first()
                    .remove();
                $(".chatbot-body #message-" + messageUniqueId)
                    .find(".msg-reactions")
                    .append(messageHtml);
                $("#chatMessages" + chatId + " #message-" + messageUniqueId)
                    .find(".msg-reactions")
                    .children()
                    .first()
                    .remove();
                $("#chatMessages" + chatId + " #message-" + messageUniqueId)
                    .find(".msg-reactions")
                    .append(messageHtml);
            } else {
                $("#MsgReaction" + messageUniqueId).remove();
            }

            // Update the UI with the reaction
        }
    );
    window.Echo.leave(`chat_blocked.` + chatId);
    window.Echo.private(`chat_blocked.${chatId}`).listen(
        "ChatBlocked",
        (event) => {
            const blockedBy = event.blockedBy;
            const blockedUserId = event.blockedUserId;
            const blockStatus = event.status;

            // Update the UI with the reaction
            if (blockStatus == "blocked") {
                if (ACTIVE_CHAT == chatId) {
                    $(".sendmsgwindow" + chatId).hide();
                    $(".replyTo").hide();
                    $("#block_msg" + chatId).show();
                    $(".emoji-reaction-area").hide();
                }
                $("#chatbot-unblock-chat" + chatId).show();
                $("#chatbot-block-chat" + chatId).hide();
                $("#chatBotContent-" + chatId)
                    .find(".chatbot-msg-send")
                    .hide();
                $("#chatBotContent-" + chatId)
                    .find(".chatbot-blocked")
                    .show();

                if (blockedBy != currentUserId) {
                    $("#chatBotContent-" + chatId)
                        .find(".chatbot-blocked")
                        .html("You cannot send message");
                    $("#block_msg" + chatId).html(
                        "<h3>You cannot send message</h3>"
                    );
                } else {
                    $("#chatBotContent-" + chatId)
                        .find(".chatbot-blocked")
                        .html("Chat has been Blocked");
                    $("#block_msg" + chatId).html(
                        "<h3>Chat has been Blocked</h3>"
                    );
                }

                if (blockedBy == currentUserId) {
                    $("#chatbot_block_unblock_div" + chatId).show();
                    $("#chatbot_block_unblock_div" + chatId).html(
                        '<li class="chatbot-unblock-chat"><a href="javascript:void(0)" onclick="unblockChat(' +
                        chatId +
                        ')" id="unblock_chat">Unblock Chat <i class="fa-solid fa-unlock"></i></a></li>'
                    );

                    $("#block_unblock_div" + chatId).show();
                    $("#block_unblock_div" + chatId).html(
                        '<li class="cds-user-more-option"><a href="javascript:void(0)" onclick="unblockChat(' +
                        chatId +
                        ')" id="unblock_chat">Unblock Chat <i class="fa-solid fa-unlock"></i></a></li>'
                    );
                } else {
                    $("#chatbot_block_unblock_div" + chatId).hide();
                    $("#block_unblock_div" + chatId).hide();
                }
            } else {
                $("#chatbot-unblock-chat" + chatId).hide();
                $("#chatbot-block-chat" + chatId).show();
                $("#chatBotContent-" + chatId)
                    .find(".chatbot-msg-send")
                    .show();
                $("#chatBotContent-" + chatId)
                    .find(".chatbot-blocked")
                    .hide();
                $("#chatBotContent-" + chatId)
                    .find(".chatbot-blocked")
                    .html("");
                if (ACTIVE_CHAT == chatId) {
                    $(".emoji-reaction-area").show();
                    $(".sendmsgwindow" + chatId).show();
                    $("#block_msg" + chatId).hide();
                    $(".replyTo").show();
                }

                $("#chatbot_block_unblock_div" + chatId).show();
                $("#chatbot_block_unblock_div" + chatId).html(
                    '<li class="chatbot-block-chat"><a href="javascript:void(0)" onclick="blockChat(' +
                    chatId +
                    ')" id="chatbot-block-chat' +
                    chatId +
                    '">Block Chat <i class="fa-solid fa-circle-half-stroke"></i></a></li>'
                );

                $("#block_unblock_div" + chatId).show();
                $("#block_unblock_div" + chatId).html(
                    '<li class="cds-user-more-option"><a href="javascript:void(0)" onclick="blockChat(' +
                    chatId +
                    ')" id="block_chat">Block Chat <i class="fa-solid fa-circle-half-stroke"></i></a></li>'
                );
            }
        }
    );
    window.Echo.leave(`chat.` + chatId);
    window.Echo.private(`chat.${chatId}`).listen("ChatSocket", (e) => {
        const response = e.data;

        if (ACTIVE_CHAT == chatId || checkIfUserBotOpen(chatId) !== -1) {
            if (response.action == "new_message") {
                // Actions  for ChatWindow
                if (ACTIVE_CHAT == chatId) {
                    if (
                        response.last_message_id !==
                        openChats[chatId].last_msg_id
                    ) {
                        fetchChatBotMessages(
                            chatId,
                            "",
                            "chatbot socket new_message"
                        );
                    }
                }
                if (checkIfUserBotOpen(chatId) !== -1) {
                    if (
                        response.last_message_id !==
                        openChats[chatId].last_msg_id
                    ) {
                        fetchChatBotMessages(chatId, "", "chatbot socket new_message");
                    }
                }

                if (response.userActivityStatus == "Active") {
                    $(".chatOnlineStatus" + chatId).addClass("status-online");
                    $(".chatOnlineStatus" + chatId).removeClass(
                        "status-offline"
                    );
                    $(".sidebarOnlineStatus" + chatId).html("Active");
                } else {
                    $(".chatOnlineStatus" + chatId).addClass("status-offline");
                    $(".chatOnlineStatus" + chatId).removeClass(
                        "status-online"
                    );
                    $(".sidebarOnlineStatus" + chatId).html("Inactive");
                }
            }

            if (response.action == "delete_selected_attachments") {
                const messageUId = response.messageUniqueId;
                // alert(messageUId);
                if (response.attachments && response.attachments.length > 0) {
                    $(
                        '.attachment[data-file-name="' +
                        response.attachments +
                        '"]'
                    ).remove();
                } else {
                    // If no attachments left, remove message
                    $(
                        "#chatMessages" + chatId + " #message-" + messageUId
                    ).html(
                        '<div class="message-block"><div class="text-message-block"><div class="message sent"><p class="deleted-message chat-message">This message was deleted.</p></div></div></div>'
                    );
                    $("#chatbot-" + chatId)
                        .find("#message-" + messageUId)
                        .html(
                            '<div class="message-block"><div class="text-message-block"><div class="message sent"><p class="deleted-message chat-message">This message was deleted.</p></div></div></div>'
                        );
                }
                if (ACTIVE_CHAT != 0) {
                    conversationList();
                }
            }
            if (response.action == "delete_msg_for_me") {
                if (currentUserId == response.sender_id) {
                    const messageUniqueId = response.messageUniqueId;
                    $("#chatbot-" + chatId).find("#message-" + messageUniqueId).remove();
                    $("#chatMessages" + chatId).find("#message-" + messageUniqueId).remove();
                }
            }
            if (response.action == "deleted_msg_for_everyone") {
                const messageUId = response.messageUniqueId;
                $("#chatMessages" + chatId + " #message-" + messageUId).html(
                    '<div class="message-block"><div class="text-message-block"><div class="message sent"><p class="deleted-message chat-message">This message was deleted.</p></div></div></div>'
                );
                $("#chatbot-" + chatId).find("#message-" + messageUId).html('<div class="message-block"><div class="text-message-block"><div class="message sent"><p class="deleted-message chat-message">This message was deleted.</p></div></div></div>');
                if (ACTIVE_CHAT != 0) {
                    conversationList();
                }
                // // refreshMessaging();
            }

            if (response.action == "user_typing") {
                $(".typing-chat").hide();
                if (response.receiver_id == currentUserId) {
                    if (response.isTyping == 1) {
                        $(".typing-chat").show();
                    } else {
                        $(".typing-chat").hide();
                    }
                }
            }

            if (response.action == "message_read") {
                var message_ids = response.message_id.split(",");
                if (response.sender_id != currentUserId) {
                    // alert(response.unread_count);
                    for (var i = 0; i < message_ids.length; i++) {
                        $(
                                "#chatMessages" +
                                chatId +
                                " #message-" +
                                message_ids[i]
                            )
                            .find(".readtrack")
                            .html(
                                '<i class="fa-sharp fa-solid fa-check-double text-primary"></i>'
                            );
                        $("#chatbot-" + chatId)
                            .find("#message-" + message_ids[i])
                            .find(".readtrack")
                            .html(
                                '<i class="fa-sharp fa-solid fa-check-double text-primary"></i>'
                            );
                    }
                }
                if (response.unread_count > 0) {
                    $(".chat-message-count").html(response.unread_count);
                } else {
                    $(".chat-message-count").html("");
                }
                if (ACTIVE_CHAT != 0) {
                    conversationList();
                }
            }
            if (response.action == "message_edited") {
                const messageUniqueId = response.messageUniqueId;
                $(
                    "#chatMessages" + chatId + " #editedMsg" + messageUniqueId
                ).html("edited");
                $("#chatMessages" + chatId + " #cpMsg" + messageUniqueId).html(
                    response.editedMessage
                );

                $("#chatbot-" + chatId)
                    .find("#editedMsg" + messageUniqueId)
                    .html("edited");
                $("#chatbot-" + chatId)
                    .find("#cpMsg" + messageUniqueId)
                    .html(response.editedMessage);
                if (ACTIVE_CHAT != 0) {
                    conversationList();
                }
                // // refreshMessaging();
            }
        }
    });
}
function initializePresenceChannel(chatId, callback) {
    console.log('Initializing presence channel for chat:', chatId);
    
    // Leave any existing channel first
    try {
        window.Echo.leave(`presence-chat.${chatId}`);
    } catch (e) {
        // Ignore errors when leaving
    }
    
    // Join the presence channel
    const channel = window.Echo.join(`presence-chat.${chatId}`);
    
    // Store the channel reference immediately
    if (openChats[chatId]) {
        openChats[chatId].presenceChannel = channel;
    }
    
    channel
        .here((users) => {
            console.log('Successfully joined presence channel, users:', users);
            if (openChats[chatId]) {
                openChats[chatId].channelReady = true;
            }
            if (callback) callback();
        })
        .joining((user) => {
            console.log('User joining:', user);
        })
        .leaving((user) => {
            console.log('User leaving:', user);
            hideTypingIndicator(chatId);
        })
        .listenForWhisper('typing', (e) => {
            console.log('Typing whisper received:', e);
            if (e.userId != currentUserId) {
                if (e.typing) {
                    showTypingIndicator(chatId, e.userName || 'User');
                } else {
                    setTimeout(() => {
                        hideTypingIndicator(chatId);
                    }, 1500);
                }
            }
        })
        .error((error) => {
            console.error('Presence channel error:', error);
            if (openChats[chatId]) {
                openChats[chatId].channelReady = false;
            }
        });
    
    // Listen for subscription events on the Pusher channel
    setTimeout(() => {
        const pusherChannel = window.Echo.connector.pusher.channel(`presence-chat.${chatId}`);
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
function removeChatReaction(e) {
    var message_id = $(e).data("id");
    $.ajax({
        type: "post",
        url: BASEURL + "/message-centre/remove-reaction",
        data: {
            _token: csrf_token,
            message_id: message_id,
        },
        success: function (data) {
            $(e).remove();
        },
    });
}

function fetchChatBot(chatId) {
    $.ajax({
        type: "GET",
        url: BASEURL + "/message-centre/fetch-chat-bot/" + chatId,
        dataType: "json",
        data: {
            _token: csrf_token,
        },
        beforeSend: function () {},
        success: function (response) {
            if (response.status) {
                $("#chatbot-" + chatId).html(response.contents);
                fetchChatBotMessages(chatId, "", "fetchChatBot func");
            } else {
                $("#chatBody-" + chatId).html(
                    '<div class="welcome-chat"><h5>No message yet</h5></div>'
                );
            }
        },
        error: function (xhr, status, error) {
            console.log("Error fetching messages:", error);
        },
    });
}

function saveChatToDraft(message, chat_id) {
    $.ajax({
        type: "post",
        url: BASEURL + "/message-centre/save-message-to-draft",
        data: {
            _token: csrf_token,
            chat_id: chat_id,
            message: message,
        },
        success: function (data) {},
    });
}
//
function fetchChatBotMessages(chatId, prev_last_msg = "", action = "") {
    var call_ajax = 0;
    var cb_last_msg_id = openChats[chatId].last_msg_id;

    var cb_first_msg_id = (first_msg_id = openChats[chatId].first_msg_id);

    if (prev_last_msg != "") {
        if (prev_last_msg !== cb_last_msg_id) {
            call_ajax = 1;
        }
    } else {
        call_ajax = 1;
    }

    if (call_ajax == 1) {
        $.ajax({
            type: "GET",
            url: BASEURL + "/message-centre/fetch-chats/" + chatId + "/" + cb_last_msg_id,
            dataType: "json",
            data: {
                _token: csrf_token,
                first_msg_id: cb_first_msg_id,
                openfrom: "chatBot",
                action: action,
            },
            beforeSend: function () {
                if (cb_last_msg_id === 0) {
                    $("#chatBody-" + chatId).html(CHAT_LOADER);

                }
            },
            success: function (response) {
                $(".loader").hide();
                $(".welcome-chat").remove();
                if ($("#chatbot-" + chatId + "-sendmsg")) {
                    new EmojiPicker(
                        "#chatbot-" + chatId + " .chatbot-emoji-icon", {
                            targetElement: "#chatbot-" + chatId + "-sendmsg",
                        }
                    );
                }

                $("#chatBody-" + chatId)
                    .find(".empty-chat-message")
                    .remove();

                if (response.new_msg) {
                    if ($("#chatbot-" + chatId).find("#message-" + response.last_msg_unique_id).length === 0) {
                        if (cb_last_msg_id == 0) {
                            $("#chatBody-" + chatId).html(response.contents);
                        } else {
                            $("#chatBody-" + chatId).append(response.contents);
                        }
                        let displayedDates = new Set();
                        document.querySelectorAll("#chatBody-" + chatId + " .datenotification").forEach(function (element) {
                            let dateValue = element.getAttribute("data-date");

                            if (displayedDates.has(dateValue)) {
                                element.remove(); // Remove duplicate datenotifications
                            } else {
                                displayedDates.add(dateValue);
                            }
                        });
                        scrollChatToBottom("chatBody-" + chatId, response.last_msg_unique_id);
                        initUserChatEmoji("chatBody-" + chatId);
                    }
                    var getRcvdLength = $("#chatbot-" + chatId).find(".received-block").length;
                    var getSentLength = $("#chatbot-" + chatId).find(".sent-block").length;

                    if (getRcvdLength == 0 && getSentLength == 0) {
                        $("#chatBody-" + chatId).html(
                            '<div class="welcome-chat"><h5>No message yet</h5></div>'
                        );
                    }
                    if (ACTIVE_CHAT == chatId) {
                        if ($("#chatMessages" + chatId).find("#message-" + response.last_msg_unique_id).length === 0) {
                            if (cb_last_msg_id == 0) {
                                $("#messages_read").html(response.contents);
                            } else {
                                $("#messages_read").append(response.contents);
                            }
                            let displayedDates = new Set();
                            document.querySelectorAll("#messages_read .datenotification").forEach(function (element) {
                                let dateValue = element.getAttribute("data-date");

                                if (displayedDates.has(dateValue)) {
                                    element.remove(); // Remove duplicate datenotifications
                                } else {
                                    displayedDates.add(dateValue);
                                }
                            });
                        }
                        var getRcvdLength = $("#chatMessages" + chatId + " #messages_read").find(".received-block").length;
                        var getSentLength = $("#chatMessages" + chatId + " #messages_read").find(".sent-block").length;

                        if (getRcvdLength == 0 && getSentLength == 0) {
                            $("#chatMessages" + chatId + " #messages_read").html('<div class="welcome-chat"><h5>No message yet</h5></div>');
                        }
                        scrollChatToBottom("chatMessages" + chatId, response.last_msg_unique_id);
                        initUserChatEmoji("chatMessages" + chatId);
                        conversationList();
                    }
                    first_msg_id = response.first_msg_id;
                    openChats[chatId].last_msg_id = response.last_msg_id;
                    openChats[chatId].first_msg_id = first_msg_id;

                    //  alert(response.first_msg_id);
                    // var chatContainer = $("#chatbot-" + chatId);
                    // chatContainer[0].scrollIntoView({
                    //     behavior: "smooth",
                    // });
                } else {
                    $("#chatBody-" + chatId).append(response.contents);
                    if (ACTIVE_CHAT == chatId) {
                        $("#chatMessages" + chatId + " #messages_read").append(
                            response.contents
                        );
                    }
                    $("#sloader").remove();
                }

                if (response.last_msg_read) {
                    $(
                        "#del_msg_for_all" + response.last_msg_unique_id
                    ).remove();
                    $("#chatBody-" + chatId)
                        .find(".unread")
                        .each(function () {
                            $(this)
                                .find("i")
                                .removeClass(
                                    "fa-sharp-duotone fa-solid fa-check text-muted"
                                );
                            $(this)
                                .find("i")
                                .addClass(
                                    "fa-sharp fa-solid fa-check-double text-primary"
                                );
                            $(this).removeClass("unread");
                        });
                    if (ACTIVE_CHAT == chatId) {
                        $("#chatMessages" + chatId)
                            .find(".unread")
                            .each(function () {
                                $(this)
                                    .find("i")
                                    .removeClass(
                                        "fa-sharp-duotone fa-solid fa-check text-muted"
                                    );
                                $(this)
                                    .find("i")
                                    .addClass(
                                        "fa-sharp fa-solid fa-check-double text-primary"
                                    );
                                $(this).removeClass("unread");
                            });
                    }
                }
                // Append new messages
            },
            error: function (xhr, status, error) {
                console.log("Error fetching messages:", error);
            },
        });
    }
}

function initUserChatEmoji(parentId) {
    $("#" + parentId + " .message-reaction").each(function () {
        var ele_id = $(this).attr("id");
        var message_id = $(this).data("id");
        var e = $(this);

        var ele_id = "#" + parentId + " #" + ele_id;
        if ($(ele_id)) {
            new EmojiPicker(ele_id, {
                onEmojiSelect: (selectedEmoji) => {
                    $.ajax({
                        url: BASEURL + "/message-centre/add-reaction", // Replace with your Laravel route
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

function closeChatBotReplyto(chatId) {
    const replyModal = document.getElementById(
        "chatbot_reply_quoted_msg" + chatId
    );
    if (replyModal) {
        $("#chatbot_reply_quoted_msg" + chatId).hide();
        $("#chatbot_reply_to_id" + chatId).val("");
    } else {
        console.error("Element with ID 'reply_quoted_msg' not found.");
    }
}

function closeBot(botId, type) {
    if (type == "userChat") {
        if (currentlyTyping[botId]) {
            currentlyTyping[botId] = false;
            // Don't try to whisper on a closing channel
        }
        clearTimeout(typingTimers[botId]);
        delete currentlyTyping[botId];
        delete typingTimers[botId];
        
        // Properly leave presence channel
        try {
            window.Echo.leave(`presence-chat.${botId}`);
        } catch (e) {
            console.log('Error leaving channel:', e);
        }
        
        // Clean up openChats
        delete openChats[botId];
        
        $("#chatbot-" + botId).remove();
        window.Echo.leave(`chatBot.${botId}`);
        const index = openBots.indexOf("userChat_" + botId);
        if (index !== -1) {
            openBots.splice(index, 1);
        }
    } else {

        if(groupCurrentlyTyping[botId]) {
            groupCurrentlyTyping[botId] = false;
            // Don't try to whisper on a closing channel
        }
        clearTimeout(groupTypingTimers[botId]);
        delete groupCurrentlyTyping[botId];
        delete groupTypingTimers[botId];
        
        // Properly leave presence channel
        try {
            window.Echo.leave(`presence-group.${botId}`);
        } catch (e) {
            console.log('Error leaving channel:', e);
        }
        
        // Clean up openChats
        delete openGroupChats[botId];

        $("#group-chatbot-" + botId).remove();
        window.Echo.leave(`groupChatBot.${botId}`);
        const index = openBots.indexOf("groupChat_" + botId);
        if (index !== -1) {
            openBots.splice(index, 1);
        }
    }
    localStorage.setItem("openBots_" + currentUserId, JSON.stringify(openBots));
}
// function closeChatBot(chatId) {
//     $("#chatbot-" + chatId).remove();
//     window.Echo.leave(`chatBot.${chatId}`);
//     delete openChats[chatId];
//     const index = openBots.indexOf("userChat_" + chatId);
//     if (index !== -1) {
//         openBots.splice(index, 1);
//     }
//     localStorage.setItem("openBots_" + currentUserId, JSON.stringify(openBots));
// }

function uploadAtt(inputElement, chatId) {
    var myurl = `${BASEURL}/message-centre/send-msg/${chatId}`;
    var formData = new FormData();
    formData.append("message", "");
    formData.append("reply_to", "");
    let files = inputElement.files; // Get selected files

    for (var i = 0; i < files.length; i++) {
        //alert(files[i]);
        formData.append("attachment[]", files[i]);
    }
    $(".loader").show();
    $.ajax({
        url: myurl,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            if (data.status == true) {
                successMessage(data.message);
                $(".loader").hide();
                $("#closemodal").click();
                $("#file-upload-form")[0].reset();
                $("#chatbot_reply_to_id" + chatId).val("");
                $("#messagenew").val("");
                $("#reply_quoted_msg").hide();
                $("#fileName").html("");
                $(".file-name").html();
                // updateTypingStatus(0);
            } else {
                $(".loader").hide();
                errorMessage(data.message);
                validation(data.message);
            }
        },
        error: function (response) {
            $("#loader").hide();
            $("#message").html("<p>File upload failed</p>");
        },
    });
}

function sendChatBotMessage(chatId) {

    if (currentlyTyping[chatId]) {
        currentlyTyping[chatId] = false;
        whisperTyping(chatId, false);
    }
    clearTimeout(typingTimers[chatId]);

    var editMessageId = $("#chatbot_edit_msg_id" + chatId).val();
    //alert(editMessageId);
    var replyTo = $("#chatbot_reply_to_id" + chatId).val();

    var formData = new FormData();
    formData.append("_token", csrf_token);
    formData.append("reply_to", replyTo);
    formData.append("openfrom", "chatBot");
    formData.append("send_msg", $("#chatbot-" + chatId + "-sendmsg").val());
    if (editMessageId) {
        $.ajax({
            type: "post",
            url: BASEURL + "/message-centre/update-message/" + editMessageId,
            data: {
                _token: csrf_token,
                message: $("#chatbot-" + chatId + "-sendmsg").val(),
            },
            beforeSend: function () {
                $("#chatbot-" + chatId + "-sendmsg").attr(
                    "disabled",
                    "disabled"
                );
            },
            success: function (data) {
                $("#chatbot_edit_msg_id" + chatId).val("");
                $("#chatbot-" + chatId + "-sendmsg").removeAttr("disabled");
                if (data.status == true) {
                    $("#chatbot-" + chatId + "-sendmsg").val("");
                    $("#cpMsg" + editMessageId).html(data.updated_message);

                    document.querySelector("#sendmsgg").value = "";

                    $("#chatBody-" + chatId)
                        .find("#cpMsg" + editMessageId)
                        .html(data.updated_message);
                }
            },
        });
    } else {
        $.ajax({
            type: "post",
            url: BASEURL + "/message-centre/send-msg/" + chatId + "?sendmsg",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            beforeSend: function () {
                $("#chatbot-" + chatId + "-sendmsg").attr(
                    "disabled",
                    "disabled"
                );
            },
            success: function (data) {
                $("#chatbot-" + chatId + "-sendmsg").removeAttr("disabled");
                if (data.status == true) {
                    $("#chatbot-" + chatId + "-sendmsg").val("");
                    $("#chatbot_reply_quoted_msg" + chatId).hide();
                    $("#reply_quoted_msg").hide();
                    $("#chatbot_reply_to_id" + chatId).val("");
                    $("#chatbot_edit_msg_id" + chatId).val("");
                }
            },
        });
    }
}

function toggleBots(BotContainer, minimized) {
    if (minimized) {
        BotContainer.css("height", "56px");
        BotContainer.find(".toggle-btn").text("+");
    } else {
        BotContainer.css("height", "auto");
        BotContainer.find(".toggle-btn").text("-");
    }
}

function toggleChatbot(chatId) {
    const chatBotContent = $("#chatBotContent-" + chatId);
    const chatBotContainer = $("#chatbot-" + chatId);

    if (!chatBotContent.length) {
        console.error("Chatbot content not found!");
        return;
    }

    // chatBotContent.slideToggle();

    // Toggle minimized state
    chatBotContainer.toggleClass("minimizedBot");
    let dataId = chatBotContainer.data("id");

    if (chatBotContainer.hasClass("minimizedBot")) {
        // Change button text
        toggleBots(chatBotContainer, true);
        if (getMininmizedBots.indexOf("userChat_" + dataId) == -1) {
            getMininmizedBots.push("userChat_" + dataId);
        }
    } else {
        toggleBots(chatBotContainer, false);

        if (getMininmizedBots.indexOf("userChat_" + dataId) !== -1) {
            const getIndex = getMininmizedBots.indexOf("userChat_" + dataId);
            getMininmizedBots.splice(getIndex, 1);
        }
    }
    localStorage.setItem(
        "minimizedBots_" + currentUserId,
        JSON.stringify(getMininmizedBots)
    );
}

function editUserMessage(e, chatId, uniqueId) {
    // alert(chatId);
    const messageElement = $("#cpMsg" + uniqueId);
    let editMessageId;
    var index = openBots.indexOf("userChat_" + chatId);
    var editfrom;
    if ($(e).parents(".chat-messages").attr("id") === "chatMessages" + chatId) {
        editfrom = "chatWindow";
    } else {
        editfrom = "chatBot";
    }
    if (editfrom == "chatBot" && !$("#chatbot-" + chatId).hasClass("minimizedBot") && index !== -1) {
        editMessageId = $("#chatbot-" + chatId)
            .find("#chatbot_edit_msg_id" + chatId)
            .val(uniqueId);
        $("#chatbot-" + chatId + "-sendmsg").val(messageElement.text());
    } else {
        editMessageId = $("#edit_message_id").val(uniqueId);
        $("#sendmsgg").val(messageElement.text());
    }
}

function replyTo(e, chat_id, chat_msg_id) {
    // alert(chat_id);
    var msg = $(e).parents(".message-block").find(".chat-message").text();
    if (msg == '') {
        if ($(e).parents(".message-block").find(".files-uploaded .attachment").length > 0) {
            msg = "Attachment";
        }
    }
    if ($(e).parents(".chat-messages").attr("id") !== undefined) {
        $(e).parents(".chat-messages").find(".myChatReply" + chat_id).html(msg);
        $("#reply_to_id").val(chat_msg_id);
        $("#reply_quoted_msg").show();
    }
    if ($(e).parents(".chatbot-body").attr("id") !== undefined) {
        $("#chatbot_reply_to_id" + chat_id).val(chat_msg_id);
        $("#chatbot_reply_quoted_msg" + chat_id).show();
        $(e).parents(".chatbotmsgBody").find(".myChatReply" + chat_id).html(msg);
    }
}
//
function copyChatBotMessage(divId) {
    const chatMessage = document.getElementById(divId).innerText;
    var $temp = $("<div>");
    $("body").append($temp);
    $temp
        .attr("contenteditable", true)
        .html($("#" + divId).html())
        .select()
        .on("focus", function () {
            document.execCommand("selectAll", false, null);
        })
        .focus();
    document.execCommand("copy");
    $temp.remove();
    bottomMessage("Message copied");
}

function deleteChatBotMessage(chat_msg_id, chat_msg_uid) {
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
            var geturl = BASEURL + "/message-centre/delete-message-centre-msg/" + chat_msg_id;
            $.ajax({
                type: "get",
                url: geturl,
                data: {},
                success: function (data) {
                    $("#message-" + chat_msg_uid).remove();
                },
            });
        }
    });
}

function deleteChatBotMessageforAll(chat_msg_id, chat_msg_uid) {
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
            var geturl = BASEURL + "/message-centre/delete-message-for-all/" + chat_msg_id;
            $.ajax({
                type: "get",
                url: geturl,
                data: {},
                success: function (data) {
                    $("#message-" + chat_msg_uid).html(
                        '<div class="message-block"><div class="text-message-block"><div class="message sent"><p class="deleted-message chat-message">This message was deleted.</p></div></div></div>'
                    );
                    // conversationList();
                },
            });
        }
    });
}

function deleteSelectedAttachmentMessage(chat_msg_id, file_name) {
    var geturl =
        BASEURL + "/message-centre/delete-selected-attachments/" + chat_msg_id;

    $.ajax({
        type: "get",
        url: geturl,
        data: {
            filename: file_name,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            console.log(response);
            $(".modal").modal("hide");
        },
    });
}

function deleteSelectedGroupAttachmentMessage(chat_msg_id, file_name) {
    var geturl = BASEURL + "/group/delete-selected-attachments/" + chat_msg_id;

    $.ajax({
        type: "get",
        url: geturl,
        data: {
            filename: file_name,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            console.log(response);
            $(".modal").modal("hide");
        },
    });
}

function newMessageNotif(message) {
   
    const notificationTone = document.getElementById("notification-tone");
    notificationTone.play();
    Swal.fire({
        text: message,
        type: "success",
        icon: "success",
        position: "top-end", // Top-right corner
        showConfirmButton: false, // Hide the OK button
        toast: true, // Make it a toast-style alert
        timer: 13000, // Automatically close after 3 seconds
        width: "300px", // Adjust the width
    });
}

function scrollChatToBottom(parentId, divId) {
    // const div = document.getElementById(divId);
    // if (div) {
    //     div.scrollTop = div.scrollHeight;
    // }
    // document.getElementById('message-' + response.message_id).scrollIntoView({
    //     behavior: 'smooth'
    // });
    const parentDiv = document.getElementById(parentId);
    if (parentDiv) {
        const messageElement = parentDiv.querySelector("#message-" + divId);
        if (messageElement) {
            messageElement.scrollIntoView({
                behavior: "smooth",
            });
        }
    }
}

function checkIfUserBotOpen(id) {
    var index = openBots.indexOf("userChat_" + id);
    return index;
}

function cbUploadFiles(files, chat_id) {
    if (cbFormFiles[chat_id].length + files.length > 6) {
        errorMessage("You can only upload a maximum 6 files.");
        return;
    }

    cbSelectedFiles[chat_id] = Array.from(files); // Store selected files
    // previewContainer.innerHTML = ""; // Clear previous preview
    cbUploadButton[chat_id].disabled = cbSelectedFiles[chat_id].length === 0; // Enable upload button

    cbSelectedFiles[chat_id].forEach((file, index) => {
        if (file.size > 25 * 1024 * 1024) {
            // Check if file is larger than 1 MB
            errorMessage(
                "File " +
                file.name +
                " is too large. Maximum allowed size is 25 MB."
            );
            return; // Skip this file if it's too large
        }
        var reader = new FileReader();
        reader.onload = function (e) {
            cbFormFiles[chat_id].push(file);
            var previewItem = document.createElement("div");
            previewItem.classList.add("cb-preview-item");
            var fileName = file.name;
            var fileExtension = fileName.split(".").pop().toLowerCase();
            var filePreview = "";
            if (file.type.startsWith("image/")) {
                filePreview = `<img src="${e.target.result}" alt="Preview"><p>${file.name}</p>`;
            } else {
                if (fileExtension == "pdf") {
                    filePreview = `<img src='assets/images/chat-icons/pdf-icon.png' alt="Preview"><p>${file.name}</p>`;
                } else if (fileExtension == "xls" || fileExtension == "xlsx") {
                    filePreview = `<img src='assets/images/chat-icons/xls-icon.png' alt="Preview"><p>${file.name}</p>`;
                } else if (fileExtension == "doc" || fileExtension == "docx") {
                    filePreview = `<img src='assets/images/chat-icons/doc-icon.png' alt="Preview"><p>${file.name}</p>`;
                } else {
                    filePreview = `<img src='assets/images/chat-icons/file-icon.png' alt="Preview"><p>${file.name}</p>`;
                }
                //   filePreview = `<button data-index="${index}" type="button" class="remove-file">X</button> `;
            }
            previewItem.innerHTML = `
                ${filePreview}
                <button data-index="${index}" type="button" data-id="${chat_id}" class="cb-remove-file">X</button>
            `;
            cdPreviewContainer[chat_id].appendChild(previewItem);
        };
        reader.readAsDataURL(file);
    });
}

function cbResetPreview(chat_id) {
    cbSelectedFiles[chat_id] = [];
    cbFormFiles[chat_id] = [];
    cbFiles[chat_id] = [];
    cdPreviewContainer[chat_id].innerHTML = "";
    cbUploadButton[chat_id].disabled = true;
}

function unblockChat(getChatId) {
    var geturl = BASEURL + "/message-centre/unblock-message-centre/" + getChatId;
    $.ajax({
        type: "get",
        url: geturl,
        data: {},
        success: function (data) {},
    });
}

function blockChat(getChatId) {
    var geturl = BASEURL + "/message-centre/block-message-centre/" + getChatId;
    $.ajax({
        type: "get",
        url: geturl,
        data: {},
        success: function (data) {},
    });
}

function deleteChat(id) {
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
            var geturl =
                BASEURL + "/message-centre/delete-message-centre/" + id;
            $.ajax({
                type: "get",
                url: geturl,
                data: {},
                success: function (data) {
                    $("#messages_read").html("");
                    successMessage(data);
                    //  window.location.href = BASEURL + "/message-centre";
                },
            });
        }
    });
}

function clearChat(chatId) {
    var sentMsgCount = $("#chatBotContent-" + chatId + " .sent-block").length;
    var rcvdMsgCount = $(
        "#chatBotContent-" + chatId + " .received-block"
    ).length;
    if (sentMsgCount > 0 || rcvdMsgCount > 0) {
        $("#chatBotContent-" + chatId)
            .find("#chatbotSelectAllDiv" + chatId)
            .show();
        $("#chatBotContent-" + chatId)
            .find(".clear-checkbox")
            .show();
        $("#chatBotContent-" + chatId)
            .find("#clearChatBtnn")
            .show();
    }
}

function clearChatBtn(chatId) {
    var clear_msg = Array.from(
        $("#chatBody-" + chatId + ' input[name="clear_msg[]"]:checked').map(
            function () {
                return $(this).val(); // Get the value of the selected checkbox
            }
        )
    );
    var myurl = BASEURL + "/message-centre/clear-message-centre/" + chatId;

    $.ajax({
        type: "post",
        url: myurl,
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"), // CSRF Token
            clear_msg: clear_msg,
        },
        success: function (data) {
            var idsToRemove = data.msgIds;
            idsToRemove.forEach(function (id) {
                $("#chatMessages" + chatId + " #message-" + id).remove();
                $("#chatbot-" + chatId + " #message-" + id).remove();
            });
            $("#chatBotContent-" + chatId).find("#chatbotSelectAllDiv" + chatId).hide();
            $("#chatBotContent-" + chatId).find(".clear-checkbox").hide();
            $("#chatBotContent-" + chatId).find("#clearChatBtnn").hide();

            var message_count = data.message_count;
            if (message_count < 1) {
                $("#chatMessages" + chatId)
                    .find("#messages_read")
                    .html(
                        '<div class="welcome-chat"><h5>No message yet</h5></div>'
                    );
                $("#chatBody-" + chatId).html(
                    '<div class="welcome-chat"><h5>No message yet</h5></div>'
                );
            }

            conversationList();
            successMessage(data.message);
        },
    });
}

function selectAllChatbotCheckbox(chatId) {
    var chatContainer = document.getElementById("chatBody-" + chatId);
    var selectAllCheckbox = document.getElementById(
        "checkboxSelectAll" + chatId
    );

    if (chatContainer) {
        var messageCheckboxes =
            chatContainer.querySelectorAll(".select-message");
        Array.from(messageCheckboxes).forEach((checkbox) => {
            //  alert(checkbox);
            checkbox.checked = selectAllCheckbox.checked;
        });
    }
}

function cancelClear(chatId) {
    $("#chatBotContent-" + chatId)
        .find("#chatbotSelectAllDiv" + chatId)
        .hide();
    $("#chatBotContent-" + chatId)
        .find(".clear-checkbox")
        .hide();
    $("#chatBotContent-" + chatId)
        .find("#clearChatBtnnn")
        .hide();

    $("#chatBody-" + chatId)
        .find(".select-message,#checkboxSelectAll" + chatId)
        .prop("checked", false);
}

function cbUpdatePreview(chat_id) {
    cdPreviewContainer[chat_id].innerHTML = "";
    cbFormFiles[chat_id].forEach((file, index) => {
        var reader = new FileReader();
        reader.onload = function (e) {
            var previewItem = document.createElement("div");
            previewItem.classList.add("cb-preview-item");
            var fileExtension = file.name.split(".").pop().toLowerCase();
            var filePreview = "";
            if (file.type.startsWith("image/")) {
                filePreview = `<img src="${e.target.result}" alt="Preview"><p>${file.name}</p>`;
            } else {
                if (fileExtension == "pdf") {
                    filePreview = `<img src='assets/images/chat-icons/pdf-icon.png' alt="Preview"><p>${file.name}</p>`;
                } else if (fileExtension == "xls" || fileExtension == "xlsx") {
                    filePreview = `<img src='assets/images/chat-icons/xls-icon.png' alt="Preview"><p>${file.name}</p>`;
                } else if (fileExtension == "doc" || fileExtension == "docx") {
                    filePreview = `<img src='assets/images/chat-icons/doc-icon.png' alt="Preview"><p>${file.name}</p>`;
                } else {
                    filePreview = `<img src='assets/images/chat-icons/file-icon.png' alt="Preview"><p>${file.name}</p>`;
                }
            }
            previewItem.innerHTML = `
                    ${filePreview}
                    <button data-index="${index}" type="button" data-id="${chat_id}" class="cb-remove-file">X</button>
                `;
            cdPreviewContainer[chat_id].appendChild(previewItem);
        };
        reader.readAsDataURL(file);
    });
}

function scrollToMessage(e, chatId, messageId) {

    if ($(e).parents(".chat-messages").attr("id") !== undefined) {
        const selector = `#chatMessages${chatId} #message-${messageId}`;
        const messageElement = document.querySelector(selector);
        $("#chatMessages" + chatId)
            .find("#cpMsg" + messageId)
            .parents(".message")
            .addClass("highlight-message");

        if (messageElement) {
            messageElement.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });
        }
        setTimeout(() => {
            $("#chatMessages" + chatId)
                .find("#cpMsg" + messageId)
                .parents(".message")
                .removeClass("highlight-message");
        }, 1500);

    }
    if ($(e).parents(".chatbot-body").attr("id") !== undefined) {
        const selector2 = `#chatbot-${chatId} #message-${messageId}`;
        const messageElement2 = document.querySelector(selector2);
        $("#chatbot-" + chatId)
            .find("#cpMsg" + messageId)
            .parents(".message")
            .addClass("highlight-message");
        if (messageElement2) {
            messageElement2.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });
        }
        setTimeout(() => {
            $("#chatBody-" + chatId)
                .find("#cpMsg" + messageId)
                .parents(".message")
                .removeClass("highlight-message");
        }, 1500);
    }
}




// Send typing status via whisper
function whisperTyping(chatId, isTyping) {
    console.log('Attempting to whisper typing:', chatId, isTyping);
    
    // First check if the chat is initialized
    if (!openChats[chatId]) {
        console.error('Chat not initialized:', chatId);
        return;
    }
    
    // Check if we have a presence channel reference
    if (!openChats[chatId].presenceChannel) {
        console.log('Presence channel not initialized, attempting to join...');
        // Initialize the presence channel if it doesn't exist
        initializePresenceChannel(chatId, () => {
            // Retry whisper after channel is initialized
            whisperTyping(chatId, isTyping);
        });
        return;
    }
    
    // Get the Pusher channel directly
    const channelName = `presence-chat.${chatId}`;
    const pusherChannel = window.Echo.connector.pusher.channel(channelName);
    console.log("channelName",channelName);
     try {
        openChats[chatId].presenceChannel.whisper('typing', {
            userId: currentUserId,
            userName: currentUserName || 'User',
            typing: isTyping
        });
        console.log('Whisper sent successfully');
    } catch (error) {
        console.error('Whisper error:', error);
    }
    
}
// Show typing indicator
function showTypingIndicator(chatId, userName) {
    // Remove this line: alert(chatId+" = "+userName);
    console.log('Show typing indicator:', chatId, userName);
    
    $("#typing-chatbot-"+chatId).show();
    $("#typing-chatbot-"+chatId).find(".typechat-message").html(userName+" typing");
    
    if (ACTIVE_CHAT == chatId) {
        $("#chatMessages"+chatId+" .typing-chat").fadeIn(200);
        $("#chatMessages"+chatId+" .typing-chat .typechat-message").html(userName+" typing");
    }
}
// Hide typing indicator
function hideTypingIndicator(chatId) {
    $("#typing-chatbot-"+chatId).hide();
    $("#typing-chatbot-"+chatId).find(".typechat-message").html("");

    
    
    if (ACTIVE_CHAT == chatId) {
        $("#chatMessages"+chatId+" .typing-chat").fadeOut(200);
        $("#chatMessages"+chatId+" .typing-chat .typechat-message").html('');
    }
}
function handleBlur(input,chatId) {
    if (input.value.trim() === "") {
        input.value = "";
        $("#chatbot_edit_msg_id" + chatId).val("");

        console.log("Blur function called!");
    }
}
// function debugEchoConnection() {
//     console.log('Echo instance:', window.Echo);
//     console.log('Pusher instance:', window.Echo.connector.pusher);
//     console.log('Connection state:', window.Echo.connector.pusher.connection.state);
    
//     window.Echo.connector.pusher.connection.bind('state_change', states => {
//         console.log('Pusher state change:', states.previous, '->', states.current);
//     });
    
//     window.Echo.connector.pusher.connection.bind('error', error => {
//         console.error('Pusher error:', error);
//     });
// }
// $(document).ready(function() {
//     setTimeout(() => {
//         debugEchoConnection();
//     }, 1000);
// });

function checkChannelSubscription(chatId) {
    const channelName = `presence-chat.${chatId}`;
    const pusherChannel = window.Echo.connector.pusher.channel(channelName);
    
    if (pusherChannel) {
        console.log('Channel subscription state:', pusherChannel.subscribed);
        console.log('Channel members:', pusherChannel.members);
        
        if (!pusherChannel.subscribed) {
            console.log('Channel not subscribed, checking authorization...');
            
            // Force resubscribe
            pusherChannel.subscribe();
            
            // Listen for subscription success
            pusherChannel.bind('pusher:subscription_succeeded', (members) => {
                console.log('Subscription succeeded! Members:', members);
                openChats[chatId].channelReady = true;
            });
            
            // Listen for subscription error
            pusherChannel.bind('pusher:subscription_error', (status) => {
                console.error('Subscription error:', status);
                if (status === 403) {
                    console.error('Authorization failed - check your channels.php');
                }
            });
        } else {
            openChats[chatId].channelReady = true;
        }
    }
}