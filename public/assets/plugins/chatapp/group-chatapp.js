let openGroupChats = {};
let openGroupChatBot = [];
let groupMembers = [];
var gbDraftTimer = 0;
// group chatbot variable
var gbFileUploadArea = [];
var gbModal = [];
var gdPreviewContainer = [];
var gbUploadButton = [];
var gbSelectedFiles = [];
var gbFormFiles = [];
var gbFiles = [];
var gbMessageInput = [];
var gbPreviewItem = "";
var gbFileExtension = "";
var gbFilePreview = "";
var groupTypingTimers = {};
var groupCurrentlyTyping = {};
// end
$(document).ready(function () {
    $(document).on("click", ".group-chatbot-header", function (e) {
    // Ignore clicks on .chatbot-user or .chatbot-close
    if ($(e.target).closest('.chatbot-user').length || $(e.target).closest('.chatbot-close').length) {
                return;
            }
            var cid = $(this).attr("data-group-chatId");
            toggleGroupChatbot(cid);
    });
    $(document).on("keypress", ".groupChatInput", function (e) {
        var group_id = $(this).data("id");
        var key = e.which;
        if (key == 13) {
            // the enter key code
            sendGroupChatBotMessage(group_id);
            return false;
        }
    });
    $(document).on("keyup", ".groupChatInput", function (e) {
        var group_id = $(this).data("id");
        showGroupMemberList($(this), group_id, "memberSuggestions-" + group_id);
    });
    $(document).on("input", ".groupChatInput, #grpMessages"+ACTIVE_GROUP_CHAT+" #sendmsgg", function () {
        var group_id = $(this).data("id");
        var inputLength = $(this).val().length;
        // Clear existing typing timer
        clearTimeout(groupTypingTimers[group_id]);
        
        // Check if chat is initialized
        if (!openGroupChats[group_id]) {
            console.log('Group not initialized yet:', group_id);
            return;
        }
        
        // Handle typing indicator
        if (inputLength > 0) {
            if (!groupCurrentlyTyping[group_id]) {
                groupCurrentlyTyping[group_id] = true;
                // Try to send typing status
                groupWhisperTyping(group_id, true);
            }
            
            groupTypingTimers[group_id] = setTimeout(function() {
                if (groupCurrentlyTyping[group_id]) {
                    groupCurrentlyTyping[group_id] = false;
                    groupWhisperTyping(group_id, false);
                }
            }, 1500);
        } else {
            if (groupCurrentlyTyping[group_id]) {
                groupCurrentlyTyping[group_id] = false;
                groupWhisperTyping(group_id, false);
            }
        }
        if (inputLength > 0 && inputLength % 5 === 0) {
            saveGroupChatToDraft($(this).val(), group_id);
        }

        // Optional: Add debounce/throttle to avoid excessive calls
        clearTimeout(gbDraftTimer);
        gbDraftTimer = setTimeout(() => {
            if (inputLength > 0 && inputLength % 5 === 0) {
                saveGroupChatToDraft($(this).val(), group_id); // Save any remaining message
            }
        }, 3000);
    });
});

function openGroupChatforMobileDesktop(chatUniqueId, groupId) {
    if (window.innerWidth <= 768) {
        console.log("Mobile Screen");
        window.location.href =
            BASEURL + "/group/chat/" + chatUniqueId + "?openfor=mobile";
    } else {
        openGroupChat(groupId);
    }
}

function openGroupChat(groupId, openOn = "click") {
    var index = openBots.indexOf("groupChat_" + groupId);
    if (openOn == "click") {
        if (index !== -1) return;
    }

    // if (openGroupChats[groupId]) return;
    //alert(openBots.length);
    //  alert(groupId);
    if (openBots.length >= 3) {
        //        alert(index);
        if (index === -1) {
            var first_index = openBots[0];
            var split = first_index.split("_");
            openBots.shift();
            localStorage.setItem(
                "openBots_" + currentUserId,
                JSON.stringify(openBots)
            );
            if (split[0] == "userChat") {
                if (split[0] == "userChat") {
                    closeBot(split[1], "userChat");
                } else {
                    closeBot(split[1], "groupChat");
                    // closeGroupChatBot[split[1]];
                }
            } else {
                // closeGroupChatBot[split[1]];
                closeBot(split[1], "groupChat");
            }
        }
    }
    if (index === -1) {
        openBots.push("groupChat_" + groupId);
    }

    localStorage.setItem("openBots_" + currentUserId, JSON.stringify(openBots));

    let chatBox = $(`
        <div class="chatbot-window groupbot-window" data-id="${groupId}" id="group-chatbot-${groupId}">
            <div class="chatbot-header group-chatbot-header">
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

    $("#group-chatbot-" + groupId).show();

    // chatWindow.style.display = (chatWindow.style.display === "block") ? "none" : "block";
    initializeGroupSocket(groupId);
    fetchGroupChatBot(groupId);
}

function removeGroupReaction(e) {
    var message_id = $(e).data("id");
    $.ajax({
        type: "post",
        url: BASEURL + "/group/remove-reaction",
        data: {
            _token: csrf_token,
            message_id: message_id,
        },
        success: function (data) {
            $(e).remove();
        },
    });
}

function groupCopyMessage(divId, openfrom) {
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
    bottomMessage("Copied to Clipboard");
}

function fetchGroupChatBot(groupId) {
    $.ajax({
        type: "GET",
        url: BASEURL + "/group/fetch-chat-bot/" + groupId,
        dataType: "json",
        data: {
            _token: csrf_token,
        },
        beforeSend: function () {},
        success: function (response) {
            if (response.status) {
                $("#group-chatbot-" + groupId).html(response.contents);
                fetchGroupChatBotMessages(groupId);
            } else {
                $("#group-chatbot-" + groupId).html(
                    '<div class="welcome-chat"><h5>No message yet</h5></div>'
                );
            }
        },
        error: function (xhr, status, error) {
            console.log("Error fetching messages:", error);
        },
    });
}

function saveGroupChatToDraft(message, group_id) {
    $.ajax({
        type: "post",
        url: BASEURL + "/group/save-message-to-draft",
        data: {
            _token: csrf_token,
            group_id: group_id,
            message: message,
        },
        success: function (data) {},
    });
}
//
function fetchGroupChatBotMessages(groupId, prev_last_msg = "") {
    var call_ajax = 0;
    var gc_last_msg_id = openGroupChats[groupId].last_msg_id ;
    var first_msg_id = openGroupChats[groupId].first_msg_id ;
    if (prev_last_msg != "") {
        if (prev_last_msg !== gc_last_msg_id) {
            call_ajax = 1;
        }
    } else {
        call_ajax = 1;
    }

    if (call_ajax == 1) {
        $.ajax({
            type: "POST",
            url: BASEURL + "/group/fetch-chats/" + groupId + "/" + gc_last_msg_id,
            dataType: "json",
            data: {
                _token: csrf_token,
                first_msg_id: first_msg_id,
                openfrom: "groupChatBot",
            },
            beforeSend: function () {
                if (gc_last_msg_id === 0) {
                    $("#groupChatBody-" + groupId).html(CHAT_LOADER);
                    
                }
            },
            success: function (response) {
                
                $(".loader").hide();
                if ($("#group-chatbot-" + groupId + "-sendmsg")) {
                    new EmojiPicker(
                        "#group-chatbot-" + groupId + " .chatbot-emoji-icon", {
                            targetElement: "#group-chatbot-" + groupId + "-sendmsg",
                        }
                    );
                }

                // $(".welcome-chat").remove();
                if (response.new_msg) {
                    $(".welcome-chat").remove();
                    if ( $("#group-chatbot-" + groupId).find( "#message-" + response.last_msg_unique_id ).length < 1 ) {
                        if (gc_last_msg_id == 0) {
                            $("#groupChatBody-" + groupId).html( response.contents );
                        } else {
                            $("#groupChatBody-" + groupId).append( response.contents );
                        }
                        let displayedDates = new Set();
                        document.querySelectorAll("#groupChatBody-" + groupId+" .datenotification").forEach(function(element) {
                            let dateValue = element.getAttribute("data-date");

                            if (displayedDates.has(dateValue)) {
                                element.remove(); // Remove duplicate datenotifications
                            } else {
                                displayedDates.add(dateValue);
                            }
                        });
                    }
                    var getRcvdLength = $("#group-chatbot-" + groupId).find( ".received-block" ).length;
                    var getSentLength = $("#group-chatbot-" + groupId).find( ".sent-block" ).length;

                    if (getRcvdLength == 0 && getSentLength == 0) {
                        $("#groupChatBody-" + groupId).html(
                            '<div class="welcome-chat"><h5>No message yet</h5></div>'
                        );
                    }
                    scrollGroupChatToBottom(
                        "groupChatBody-" + groupId,
                        response.last_msg_unique_id
                    );
                    initGroupChatEmoji("groupChatBody-" + groupId);
                    if (ACTIVE_GROUP_CHAT == groupId) {
                        // updateConversationList();
                    }

                    if (ACTIVE_GROUP_CHAT == groupId) {
                        if ( $("#grpMessages" + groupId).find( "#message-" + response.last_msg_unique_id ).length === 0 ) {
                            if (gc_last_msg_id == 0) {
                                $("#messages_read").html(response.contents);
                            } else {
                                $("#messages_read").append(response.contents);
                            }
                            let displayedDates = new Set();
                            document.querySelectorAll("#messages_read .datenotification").forEach(function(element) {
                                let dateValue = element.getAttribute("data-date");

                                if (displayedDates.has(dateValue)) {
                                    element.remove(); // Remove duplicate datenotifications
                                } else {
                                    displayedDates.add(dateValue);
                                }
                            });
                        }
                        scrollGroupChatToBottom(
                            "grpMessages" + groupId,
                            response.last_msg_unique_id
                        );
                        initGroupChatEmoji("grpMessages" + groupId);
                        var getRcvdLength = $( "#grpMessages" + groupId + " #messages_read" ).find(".received-block").length;
                        var getSentLength = $( "#grpMessages" + groupId + " #messages_read" ).find(".sent-block").length;

                        if (getRcvdLength == 0 && getSentLength == 0) {
                            $( "#grpMessages" + groupId + " #messages_read" ).html( '<div class="welcome-chat"><h5>No message yet</h5></div>' );
                        }
                    }
                    openGroupChats[groupId].last_msg_id = response.last_msg_id;
                    openGroupChats[groupId].first_msg_id = response.first_msg_id;
                    // var chatContainer = $("#group-chatbot-" + groupId);
                } else {
                    $("#sloader").remove();
                }
                if (response.last_msg_read) {
                    $( "#del_msg_for_all" + response.last_msg_unique_id ).remove();
                    $("#groupChatBody-" + groupId) .find(".unread") .each(function () { $(this) .find("i") .removeClass( "fa-sharp-duotone fa-solid fa-check text-muted" );
                    $(this) .find("i") .addClass( "fa-sharp fa-solid fa-check-double text-primary" ); $(this).removeClass("unread"); });
                    var getGrpRcvdLength = $("#groupChatBody-" + groupId).find( ".received-block" ).length;
                    var getGrpSentLength = $("#groupChatBody-" + groupId).find( ".sent-block" ).length;
                    if (getGrpRcvdLength == 0 && getGrpSentLength == 0) { $("#groupChatBody-" + groupId).html( '<div class="welcome-chat"><h5>No message yet</h5></div>' ); }
                    if (ACTIVE_GROUP_CHAT == groupId) {
                        $("#grpMessages" + groupId) .find(".unread") .each(function () {
                            $(this) .find("i") .removeClass( "fa-sharp-duotone fa-solid fa-check text-muted" );
                            $(this) .find("i") .addClass( "fa-sharp fa-solid fa-check-double text-primary" );
                            $(this).removeClass("unread");
                        });
                        var getRcvdLength = $( "#grpMessages" + groupId + " #messages_read" ).find(".received-block").length;
                        var getSentLength = $( "#grpMessages" + groupId + " #messages_read" ).find(".sent-block").length;

                        if (getRcvdLength == 0 && getSentLength == 0) {
                            $(
                                "#grpMessages" + groupId + " #messages_read"
                            ).html(
                                '<div class="welcome-chat"><h5>No message yet</h5></div>'
                            );
                        }
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

function checkGroupExists(groupId,user_id) {
    $.ajax({
        type: "post",
        url: BASEURL + "/group/check-group-exists",
        data: {
            _token: csrf_token,
            group_id: groupId,
            user_id:user_id
        },
        dataType: "json",
        success: function (data) {
            if (data.status) {
                if (!data.exists) {
                    closeBot(groupId, "groupChat");
                }
            }
        },
    });
}

function initializeGroupSocket(groupId) {
    openGroupChats[groupId] = {
        open: true,
        first_msg_id: 0,
        last_msg_id: 0,
    };
    fetchGroupMembers(groupId);
    $(document).on(
        "blur",
        "#group-chatbot-" + groupId + "-sendmsg",
        function () {
            handleGroupBlur(this,groupId);
        }
    );
    $(document).on("click", ".gb-remove-file", function () {
        var index = $(this).data("index");
        var group_id = $(this).data("id");
        gbFormFiles[group_id].splice(index, 1);

        gbUpdatePreview(group_id);
    });
    $(document).on("click", ".gb-close-uploader", function () {
        var group_id = $(this).data("id");
        gbFormFiles[group_id] = [];
        gbFiles[group_id] = [];
        gbUpdatePreview(group_id);
        $("#group-chatbot-upload-file-{{ $chatId }}").hide();
    });

    $(document).on("mouseleave", ".group-chatbot-upload-file", function () {
        var groupChatBotId = $(this).data("id");

        // alert(chatBotId);
        if (gbFormFiles[groupChatBotId].length == 0) {
            $("#group-chatbot-upload-file-" + groupChatBotId).hide();
        }
        // $('.cb-close-uploader').click();
    });

    window.Echo.leave(`group-chat.${groupId}`);
    window.Echo.private(`group-chat.${groupId}`).listen(
        "GroupChatSocket",
        (e) => {
            const response = e.data;
            if (response.action == "group_member_removed") {
                if (response.receiver_id == currentUserId) {
                    closeBot(response.group_id, "groupChat");
                    if (ACTIVE_GROUP_CHAT != 0) {
                        if (ACTIVE_GROUP_CHAT == response.group_id) {
                            window.location.href = BASEURL + "/group/chat";
                        } else {
                            updateConversationList();
                        }
                    }
                }
                if (ACTIVE_GROUP_CHAT != 0) {
                    if (ACTIVE_GROUP_CHAT == response.group_id) {
                        window.location.href = BASEURL + "/group/chat";
                    } else {
                        updateConversationList();
                    }
                }
            }

            if (response.action == "group_deleted") {
                closeBot(response.group_id, "groupChat");
                if (ACTIVE_GROUP_CHAT != 0) {
                    if (ACTIVE_GROUP_CHAT == response.group_id) {
                        window.location.href = BASEURL + "/group/chat";
                    } else {
                        updateConversationList();
                    }
                }
            }
            if (
                ACTIVE_GROUP_CHAT == groupId ||
                checkIfGroupBotOpen(groupId) !== -1
            ) {
                if (response.action == "new_message") {
                        if (
                            response.last_message_id !==
                            openGroupChats[groupId].last_msg_id
                        ) {
                            fetchGroupChatBotMessages(groupId);
                            // fileSearchInputs(groupId, "clear");
                        }
                 
                }
                if (response.action == "group_message_read") {
                    if (response.receiver_id == currentUserId) {
                        if (response.unread_count == "0") {
                            $(".group-messages-count").css("opacity", "0"); // Set opacity to 50%
                        } else {
                            $(".group-messages-count").css("opacity", "1"); // Set opacity to 50%
                        }
                        $(".group-messages-count").html(response.unread_count);
                    }
                    if (ACTIVE_GROUP_CHAT == groupId) {
                        updateConversationList();
                    }
                }

                if (response.action == "user_typing") {
                    $(".typing-chat").hide();
                    if (response.sender_id != currentUserId) {
                        if (response.isTyping == 1) {
                            $(".membertyping").html(response.member_typing);
                            $(".typing-chat").show();
                        } else {
                            $(".typing-chat").hide();
                        }
                    }
                }
                if (response.action == "deleted_msg_for_everyone") {
                    const messageUId = response.messageUniqueId;
                    $(
                        "#grpMessages" + groupId + " #message-" + messageUId
                    ).html(
                        '<div class="message-block"><div class="text-message-block"><div class="message sent"><p class="deleted-message chat-message">This message was deleted.</p></div></div></div>'
                    );
                    $( "#group-chatbot-" + groupId + " #message-" + messageUId ).html( '<div class="message-block"><div class="text-message-block"><div class="message sent"><p class="deleted-message chat-message">This message was deleted.</p></div></div></div>' );

                    if (ACTIVE_GROUP_CHAT != 0) {
                        updateConversationList();
                    }
                    // // refreshMessaging();
                }

                if (response.action == "delete_selected_attachments") {
                    const messageUId = response.messageUniqueId;
                    // alert(messageUId);
                    if (
                        response.attachments &&
                        response.attachments.length > 0
                    ) {
                        $(
                            '.attachment[data-file-name="' +
                            response.attachments +
                            '"]'
                        ).remove();
                    } else {
                        // If no attachments left, remove message
                        $(
                            "#grpMessages" + groupId + " #message-" + messageUId
                        ).html(
                            '<div class="message-block"><div class="text-message-block"><div class="message sent"><p class="deleted-message chat-message">This message was deleted.</p></div></div></div>'
                        );
                        $("#group-chatbot-" + groupId)
                            .find("#message-" + messageUId)
                            .html(
                                '<div class="message-block"><div class="text-message-block"><div class="message sent"><p class="deleted-message chat-message">This message was deleted.</p></div></div></div>'
                            );
                    }
                    updateConversationList();
                }

                if (response.action == "delete_msg_for_me") {
                    if (currentUserId == response.sender_id) {
                        const messageUniqueId = response.messageUniqueId;
                        $("#group-chatbot-" + groupId)
                            .find("#message-" + messageUniqueId)
                            .remove();
                    }
                    if (ACTIVE_GROUP_CHAT != 0) {
                        updateConversationList();
                    }
                    // // refreshMessaging();
                }
                if (response.action == "message_edited") {
                    const messageUniqueId = response.messageUniqueId;
                    $(
                        "#grpMessages" +
                        groupId +
                        " #editedMsg" +
                        messageUniqueId
                    ).html("edited");
                    $(
                        "#grpMessages" + groupId + " #cpMsg" + messageUniqueId
                    ).html(response.editedMessage);

                    $("#groupChatBody-" + groupId)
                        .find("#editedMsg" + messageUniqueId)
                        .html("edited");
                    $("#groupChatBody-" + groupId)
                        .find("#cpMsg" + messageUniqueId)
                        .html(response.editedMessage);
                    if (ACTIVE_GROUP_CHAT != 0) {
                        updateConversationList();
                    }
                    // // refreshMessaging();
                }
                if (response.action == "new_member_joined") {
                    fetchGroupChatBotMessages(groupId);
                }
            }
        }
    );
    setTimeout(() => {
        initializeGroupPresenceChannel(groupId);
    }, 300);
    // window.Echo.private(`groupChatBot.${groupId}`).listen(
    //     "GroupChatSocket",
    //     (e) => {
    //         const response = e.data;
    //         if (response.action == "new_message") {
    //             if (
    //                 response.last_message_id !==
    //                 openGroupChats[groupId].last_msg_id
    //             ) {
    //                 fetchGroupChatBotMessages(groupId);
    //             }
    //             if (response.userActivityStatus == "Active") {
    //                 $(".chatOnlineStatus" + groupId).addClass("status-online");
    //                 $(".chatOnlineStatus" + groupId).removeClass(
    //                     "status-offline"
    //                 );
    //                 $(".sidebarOnlineStatus" + groupId).html("Active");
    //             } else {
    //                 $(".chatOnlineStatus" + groupId).addClass("status-offline");
    //                 $(".chatOnlineStatus" + groupId).removeClass(
    //                     "status-online"
    //                 );
    //                 $(".sidebarOnlineStatus" + groupId).html("Inactive");
    //             }
    //         }

    //         // if (response.action == 'user_typing') {
    //         //     $('.typing-chat').hide();
    //         //     if (response.receiver_id == currentUserId) {
    //         //         if (response.isTyping == 1) {
    //         //             $('.typing-chat').show();
    //         //         } else {
    //         //             $('.typing-chat').hide();
    //         //         }
    //         //     }
    //         // }

    //         // if (response.action == 'message_read') {
    //         //     var message_ids = response.message_id.split(",");
    //         //     if (response.sender_id != currentUserId) {
    //         //         for (var i = 0; i < message_ids.length; i++) {
    //         //             $("#message-" + message_ids[i]).find(".readtrack").html(
    //         //                 '<i class="fa-sharp fa-solid fa-check-double text-primary"></i>');
    //         //         }
    //         //     }
    //         //     $(".chat-request-count").html(response.unread_count);
    //         //     updateConversationList();
    //         // }
    //         var chatContainer = $("#group-chatbot-" + groupId);
    //         chatContainer[0].scrollIntoView({
    //             behavior: "smooth",
    //         });
    //         if (response.action == "message_edited") {
    //             const messageUniqueId = response.messageUniqueId;
    //             $("#groupChatBody-" + groupId)
    //                 .find("#editedMsg" + messageUniqueId)
    //                 .html("edited");
    //             $("#groupChatBody-" + groupId)
    //                 .find("#cpMsg" + messageUniqueId)
    //                 .html(response.editedMessage);
    //         }
    //     }
    // );
    window.Echo.leave(`groupMessageReaction.` + groupId);
    window.Echo.private(`groupMessageReaction.` + groupId).listen(
        "GroupMessageReactionChange",
        (event) => {
            const messageUniqueId = event.messageUniqueId;
            const status = event.status;
            $.ajax({
                type: "POST",
                url: BASEURL +
                    "/group/reacted-message/" +
                    groupId +
                    "/" +
                    messageUniqueId,
                dataType: "json",
                data: {
                    _token: csrf_token,
                },
                success: function (response) {
                    $("#grpMessages" + groupId + " #messages_read")
                        .find("#message-" + response.messageUniqueId)
                        .html(response.contents);
                    $("#group-chatbot-" + groupId)
                        .find("#message-" + messageUniqueId)
                        .html(response.contents);

                    if (status == "remove") {
                        $("#group-chatbot-" + groupId)
                            .find("#reactionsList" + messageUniqueId)
                            .hide();
                        $("#grpMessages" + groupId + " #messages_read")
                            .find("#reactionsList" + messageUniqueId)
                            .hide();
                    }
                    initGroupChatEmoji("groupChatBody-" + groupId);
                    if (ACTIVE_GROUP_CHAT == groupId) {
                        initGroupChatEmoji("grpMessages" + groupId);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error in reaction:", error);
                },
            });
        }
    );
}
function initializeGroupPresenceChannel(groupId, callback) {
    console.log('Initializing presence channel for group:', groupId);
    
    // Leave any existing channel first
    try {
        window.Echo.leave(`presence-group.${groupId}`);
    } catch (e) {
        // Ignore errors when leaving
    }
    
    // Join the presence channel
    const groupChannel = window.Echo.join(`presence-group.${groupId}`);
    
    // Store the channel reference immediately
    if (openGroupChats[groupId]) {
        openGroupChats[groupId].presenceChannel = groupChannel;
    }
    
    groupChannel
        .here((users) => {
            console.log('Successfully joined presence group channel, users:', users);
            if (openGroupChats[groupId]) {
                openGroupChats[groupId].channelReady = true;
            }
            if (callback) callback();
        })
        .joining((user) => {
            console.log('User joining group:', user);
        })
        .leaving((user) => {
            console.log('User leaving group:', user);
            hideGroupTypingIndicator(groupId);
        })
        .listenForWhisper('typing', (e) => {
            console.log('Typing whisper group received:', e);
            console.log("currentUserId",currentUserId);
            if (e.userId != currentUserId) {
                if (e.typing) {
                    showGroupTypingIndicator(groupId, e.userName || 'User');
                } else {
                    setTimeout(() => {
                        hideGroupTypingIndicator(groupId);
                    }, 1500);
                }
            }
        })
        .error((error) => {
            console.error('Presence channel error:', error);
            if (openGroupChats[groupId]) {
                openGroupChats[groupId].channelReady = false;
            }
        });
    
    // Listen for subscription events on the Pusher channel
    setTimeout(() => {
        const pusherChannel = window.Echo.connector.pusher.channel(`presence-group.${groupId}`);
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
// function closeGroupChatBot(groupId) {
//     alert("group "+groupId);
//     $("#group-chatbot-" + groupId).remove();
//     window.Echo.leave(`groupChatBot.${groupId}`);
//     // delete openGroupChats[groupId];
//     const index = openBots.indexOf("groupChat_" + groupId);
//     if (index !== -1) {
//         openBots.splice(index, 1);
//     }
//     localStorage.setItem("openBots_"+currentUserId,JSON.stringify(openBots));
// }

function uploadGroupAtt(inputElement, groupId) {
    var myurl = `${BASEURL}/group/send-msg/${groupId}`;
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
                $("#group_chatbot_reply_to_id" + groupId).val("");
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

function sendGroupChatBotMessage(groupId) {
    var editMessageId = $("#group-chatbot-" + groupId)
        .find("#grp_chatbot_edit_msg_id" + groupId)
        .val();
    var replyTo = $("#group-chatbot-" + groupId)
        .find("#group_chatbot_reply_to_id" + groupId)
        .val();
    var formData = new FormData();
    formData.append("_token", csrf_token);
    formData.append("reply_to", replyTo);
    formData.append("openfrom", "groupChatBot");
    formData.append(
        "send_msg",
        $("#group-chatbot-" + groupId + "-sendmsg").val()
    );
    if (editMessageId) {
        $.ajax({
            type: "post",
            url: BASEURL + "/group/update-message/" + editMessageId,
            data: {
                _token: csrf_token,
                message: $("#group-chatbot-" + groupId + "-sendmsg").val(),
            },
            beforeSend: function () {
                $("#group-chatbot-" + groupId + "-sendmsg").attr("disabled", "disabled");
            },
            success: function (data) {
                $("#group-chatbot-" + groupId + "-sendmsg").removeAttr("disabled");
                if (data.status == true) {
                    $("#group-chatbot-" + groupId + "-sendmsg").val("");
                    $("#cpMsg" + editMessageId).html(data.updated_message);

                    document.querySelector("#sendmsgg").value = "";

                    $("#groupChatBody-" + groupId)
                        .find("#cpMsg" + editMessageId)
                        .html(data.updated_message);
                    $("#grp_chatbot_edit_msg_id" + groupId).val("");
                }
            },
        });
    } else {
        $.ajax({
            type: "post",
            url: BASEURL + "/group/send-msg/" + groupId + "?sendmsg",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            beforeSend: function () {
                $("#group-chatbot-" + groupId + "-sendmsg").attr("disabled", "disabled");
            },
            success: function (data) {
                $("#group-chatbot-" + groupId + "-sendmsg").removeAttr("disabled");
                if (data.status == true) {
                    $("#group-chatbot-" + groupId + "-sendmsg").val("");
                    $("#group_chatbot_reply_quoted_msg" + groupId).hide();
                    $("#reply_quoted_msg").hide();
                    $("#grp_chatbot_edit_msg_id" + groupId).val("");
                    $("#group_chatbot_reply_to_id" + groupId).val("");
                }
            },
        });
    }
}

function toggleGroupChatbot(groupId) {
    const groupChatBotContent = $("#groupChatBotContent-" + groupId);
    const groupChatBotContainer = $("#group-chatbot-" + groupId);

    if (!groupChatBotContent.length) {
        console.error("Chatbot content not found!");
        return;
    }

    // groupChatBotContent.slideToggle();

    // Toggle minimized state
    groupChatBotContainer.toggleClass("minimizedBot");
    let dataId = groupChatBotContainer.data("id");

    if (groupChatBotContainer.hasClass("minimizedBot")) {
        toggleBots(groupChatBotContainer, true);
        if (getMininmizedBots.indexOf("groupChat_" + dataId) == -1) {
            getMininmizedBots.push("groupChat_" + dataId);
        }
    } else {
        toggleBots(groupChatBotContainer, false);
        if (getMininmizedBots.indexOf("groupChat_" + dataId) !== -1) {
            const getIndex = getMininmizedBots.indexOf("groupChat_" + dataId);
            getMininmizedBots.splice(getIndex, 1);
        }
    }
    localStorage.setItem(
        "minimizedBots_" + currentUserId,
        JSON.stringify(getMininmizedBots)
    );
}

function editGroupMessage(e, groupId, uniqueId) {
    const messageElement = $("#cpMsg" + uniqueId);
    let editMessageId;
    var index = openBots.indexOf("groupChat_" + groupId);
    if ($(e).parents(".grp-messages").attr("id") === "grpMessages" + groupId) {
        editfrom = "chatWindow";
    } else {
        editfrom = "groupChatBot";
    }
    if (
        editfrom == "groupChatBot" &&
        index !== -1 &&
        !$("#group-chatbot-" + groupId).hasClass("minimizedBot")
    ) {
        editMessageId = $("#grp_chatbot_edit_msg_id" + groupId).val(uniqueId);
        $("#group-chatbot-" + groupId + "-sendmsg").val(messageElement.text());
    } else {
        editMessageId = $("#grp_edit_message_id").val(uniqueId);
        $("#sendmsgg").val(messageElement.text());
    }
}

function groupReplyTo(e, grp_id, chat_msg_id) {
    var msg = $(e).parents(".message-block").find(".chat-message").text();
    if ($(e).parents(".chat-messages").attr("id") !== undefined) {
        $(e).parents(".chat-messages").find(".myChatReply" + grp_id).html(msg);
        $("#group_reply_to_id").val(chat_msg_id);
        $("#reply_quoted_msg").show();
    }
    if ($(e).parents(".chatbot-body").attr("id") !== undefined) {
        $("#group_chatbot_reply_to_id" + grp_id).val(chat_msg_id);
        $("#group_chatbot_reply_quoted_msg" + grp_id).show();
        $(e).parents(".chatbotmsgBody").find(".myChatReply" + grp_id).html(msg);
    }
}

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

function deleteGroupMessage(chat_msg_id, chat_msg_uid) {
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
                BASEURL + "/group/delete-message-centre-msg/" + chat_msg_id;
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

function deleteGroupMessageforAll(chat_msg_id, chat_msg_uid) {
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
                BASEURL + "/group/delete-message-for-all/" + chat_msg_id;
            $.ajax({
                type: "get",
                url: geturl,
                data: {},
                success: function (data) {
                    $("#message-" + chat_msg_uid).html(
                        '<div class="message-block"><div class="text-message-block"><div class="message sent"><p class="deleted-message chat-message">This message was deleted.</p></div></div></div>'
                    );
                    // updateConversationList();
                },
            });
        }
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

function closeGrpChatBotReplyto(grpId) {
    const replyModal = document.getElementById(
        "group_chatbot_reply_quoted_msg" + grpId
    );
    if (replyModal) {
        $("#group_chatbot_reply_quoted_msg" + grpId).hide();
        $("#group_chatbot_reply_to_id" + grpId).val("");
    } else {
        console.error("Element with ID 'reply_quoted_msg' not found.");
    }
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

function scrollGroupChatToBottom(parentId, divId) {
    // const div = document.getElementById(divId);
    // if (div) {
    //     div.scrollTop = div.scrollHeight;
    // }
    const parentDiv = document.getElementById(parentId);
    if (parentDiv) {
        const messageElement = parentDiv.querySelector("#message-" + divId);
        if (messageElement) {
            messageElement.scrollIntoView({
                behavior: "smooth"
            });
        }
    }
}

function checkIfGroupBotOpen(id) {
    var index = openBots.indexOf("groupChat_" + id);
    return index;
}

function initGroupChatEmoji(parentId) {
    $("#" + parentId + " .message-reaction").each(function () {
        var ele_id = $(this).attr("id");
        var message_id = $(this).data("id");
        var e = $(this);
        var ele_id = "#" + parentId + " #" + ele_id;
        if ($(ele_id)) {
            new EmojiPicker(ele_id, {
                onEmojiSelect: (selectedEmoji) => {
                    $.ajax({
                        url: BASEURL + "/group/add-reaction", // Replace with your Laravel route
                        type: "POST",
                        data: {
                            _token: csrf_token,
                            message_id: message_id,
                            // openfrom: openfrom,
                            reaction: selectedEmoji,
                        },
                        success: function (response) {
                            const msgBlock = $(e).parents(".message-block");
                            msgBlock.addClass("reaction-on");
                        },
                        error: function (xhr, status, error) {
                            console.log("Error adding reaction:", error);
                        },
                    });
                },
            });
        }
    });
}

function fetchGroupMembers(group_id) {
    $.ajax({
        url: BASEURL + "/group/group-members/search", // Replace with your Laravel route
        type: "GET",
        data: {
            _token: csrf_token,
            group_id: group_id,
        },
        dataType: "json",
        success: function (response) {
            if (response.status) {
                groupMembers[group_id] = response.members;
            }
        },
        error: function (xhr, status, error) {
            console.log("Error adding reaction:", error);
        },
    });
}
function groupWhisperTyping(groupId, isTyping) {
    console.log('Attempting to whisper group typing:', groupId, isTyping);
    
    // First check if the chat is initialized
    if (!openGroupChats[groupId]) {
        console.error('Chat not initialized:', groupId);
        return;
    }
    
    // Check if we have a presence channel reference
    if (!openGroupChats[groupId].presenceChannel) {
        console.log('Presence channel not initialized, attempting to join...');
        // Initialize the presence channel if it doesn't exist
        initializeGroupPresenceChannel(groupId, () => {
            // Retry whisper after channel is initialized
            groupWhisperTyping(groupId, isTyping);
        });
        return;
    }
    
    // Get the Pusher channel directly
     try {
        openGroupChats[groupId].presenceChannel.whisper('typing', {
            userId: currentUserId,
            userName: currentUserName || 'User',
            typing: isTyping
        });
        console.log('Whisper sent successfully');
    } catch (error) {
        console.error('Whisper error:', error);
    }
}
function showGroupMemberList(editorDiv, group_id, outputElement) {
    const editor = editorDiv[0]; // Get the native DOM element
    const editorId = editorDiv.attr("id");
    const cursorPosition = editor.selectionStart;
    const textBeforeCursor = editor.value.substring(0, cursorPosition);
    const atIndex = textBeforeCursor.lastIndexOf("@");

    if (atIndex !== -1) {
        const query = textBeforeCursor.substring(atIndex + 1);

        // Ensure groupMembers[group_id] is defined
        if (groupMembers[group_id]) {
            let matches;

            if (query.length === 0) {
                // If "@" is typed and no query, show all members
                matches = groupMembers[group_id];
            } else {
                // Filter members based on the query
                const regex = new RegExp("^" + query, "i");
                matches = groupMembers[group_id].filter((user) =>
                    regex.test(user)
                );
            }

            if (matches.length > 0) {
                const suggestions = matches
                    .map(
                        (user) =>
                        `<li  onclick="handleUserClick(this,'${editorId}','${outputElement}')" data-user="${user}">${user}</li>`
                    )
                    .join("");
                $("#" + outputElement)
                    .html(suggestions)
                    .show();
            } else {
                $("#" + outputElement).hide();
            }
        }
    } else {
        $("#" + outputElement).hide();
    }
}

function hideUserList() {
    userList.style.display = "none";
}

function handleUserClick(targetElement, inputElement, outputElement) {
    const selectedUser = targetElement.getAttribute("data-user");
    const editor = $("#" + inputElement);
    const cursorPosition = editor.selectionStart;
    const textBeforeCursor = editor.val().substring(0, cursorPosition);
    const atIndex = textBeforeCursor.lastIndexOf("@");
    const textAfterCursor = editor.val().substring(cursorPosition);

    const newText =
        "*" + textBeforeCursor.substring(0, atIndex + 1) + selectedUser + "*";
    editor.val(newText);

    // Move cursor to the end of the inserted text
    const newCursorPosition = atIndex + 1 + selectedUser.length + 1;
    // editor.setSelectionRange(newCursorPosition, newCursorPosition);

    $("#" + outputElement).hide();
    editor.focus();
}

function gbUploadFiles(files, group_id) {
    if (gbFormFiles[group_id].length + files.length > 6) {
        errorMessage("You can only upload a maximum 6 files.");
        return;
    }

    gbSelectedFiles[group_id] = Array.from(files); // Store selected files
    // previewContainer.innerHTML = ""; // Clear previous preview
    gbUploadButton[group_id].disabled = gbSelectedFiles[group_id].length === 0; // Enable upload button

    gbSelectedFiles[group_id].forEach((file, index) => {
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
            gbFormFiles[group_id].push(file);
            var previewItem = document.createElement("div");
            previewItem.classList.add("gb-preview-item");
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
                <button data-index="${index}" type="button" data-id="${group_id}" class="gb-remove-file">X</button>
            `;
            gdPreviewContainer[group_id].appendChild(previewItem);
        };
        reader.readAsDataURL(file);
    });
}

function gbResetPreview(group_id) {
    gbSelectedFiles[group_id] = [];
    gbFormFiles[group_id] = [];
    gbFiles[group_id] = [];
    gdPreviewContainer[group_id].innerHTML = "";
    gbUploadButton[group_id].disabled = true;
}

function clearGrpChat(grpId) {
    var sentMsgCount = $(
        "#groupChatBotContent-" + grpId + " .sent-block"
    ).length;
    var rcvdMsgCount = $(
        "#groupChatBotContent-" + grpId + " .received-block"
    ).length;
    if (sentMsgCount > 0 || rcvdMsgCount > 0) {
        $("#groupChatBotContent-" + grpId)
            .find("#grpbotSelectAllDiv" + grpId)
            .show();
        $("#groupChatBotContent-" + grpId)
            .find(".clear-checkbox")
            .show();
        $("#groupChatBotContent-" + grpId)
            .find("#clearChatBtnGrp")
            .show();
    }
}

function selectAllGrpbotCheckbox(grpId) {
    var chatContainer = document.getElementById("groupChatBody-" + grpId);
    var selectAllCheckbox = document.getElementById(
        "checkboxGrpSelectAll" + grpId
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

function cancelGrpClear(grpId) {
    $("#groupChatBotContent-" + grpId)
        .find("#grpbotSelectAllDiv" + grpId)
        .hide();
    $("#groupChatBotContent-" + grpId)
        .find(".clear-checkbox")
        .hide();
  
    $("#groupChatBotContent-" + grpId)
        .find("#clearChatBtnGrp")
        .hide();
    $("#groupChatBody-" + grpId)
        .find(".select-message,#checkboxGrpSelectAll" + grpId)
        .prop("checked", false);
}

function clearGrpBtn(grpId) {
    var clear_msg = Array.from(
        $("#groupChatBody-" + grpId + ' input[name="clear_msg[]"]:checked').map(
            function () {
                return $(this).val(); // Get the value of the selected checkbox
            }
        )
    );

    var myurl = BASEURL + "/group/clear-group-messages/" + grpId;

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
                $("#grpMessages" + grpId + " #message-" + id).remove();
                $("#group-chatbot-" + grpId + " #message-" + id).remove();
            });
            $("#groupChatBotContent-" + grpId)
                .find("#grpbotSelectAllDiv" + grpId)
                .hide();
            $("#groupChatBotContent-" + grpId)
                .find(".clear-checkbox")
                .hide();
            $("#groupChatBotContent-" + grpId)
                .find("#clearChatBtnGrp")
                .hide();

            var message_count = data.message_count;
            if (message_count < 1) {
                $("#grpMessages" + grpId)
                    .find("#messages_read")
                    .html(
                        '<div class="welcome-chat"><h5>No message yet</h5></div>'
                    );
                $("#groupChatBody-" + grpId).html(
                    '<div class="welcome-chat"><h5>No message yet</h5></div>'
                );
            }

            updateConversationList();
            successMessage(data.message);
        },
    });
}

function gbUpdatePreview(group_id) {
    gdPreviewContainer[group_id].innerHTML = "";
    gbFormFiles[group_id].forEach((file, index) => {
        var reader = new FileReader();
        reader.onload = function (e) {
            var previewItem = document.createElement("div");
            previewItem.classList.add("gb-preview-item");
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
                    <button data-index="${index}" type="button" data-id="${group_id}" class="gb-remove-file">X</button>
                `;
            gdPreviewContainer[group_id].appendChild(previewItem);
        };
        reader.readAsDataURL(file);
    });
}

function scrollToGrpMessage(e, groupId, messageId) {
    if ($(e).parents(".chat-messages").attr("id") !== undefined) {
        const selector = `#grpMessages${groupId} #message-${messageId}`;
        const messageElement = document.querySelector(selector);
        $("#grpMessages" + groupId)
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
            $("#grpMessages" + groupId)
                .find("#cpMsg" + messageId)
                .parents(".message")
                .removeClass("highlight-message");
        }, 1500);

    }
    if ($(e).parents(".chatbot-body").attr("id") !== undefined) {
        const selector2 = `#group-chatbot-${groupId} #message-${messageId}`;
        const messageElement2 = document.querySelector(selector2);
        $("#group-chatbot-" + groupId)
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
            $("#groupChatBody-" + groupId)
                .find("#cpMsg" + messageId)
                .parents(".message")
                .removeClass("highlight-message");
        }, 1500);
    }
}


function checkRemoveadmin(e)
{
    var url = $(e).attr("data-href");
    var action = $(e).attr("data-action");

    Swal.fire({
        title: "Are you sure to leave from group?",
        text: "Before you leaving make one of the existing member as admin",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#198754",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            // redirect(url);
            $.ajax({
                type: "GET",
                url: url,
                dataType: "json",
                beforeSend: function () {
                    $(".loader").show();
                },
                success: function (response) {
                    $(".loader").hide();
                    if (response.status == true) {
                        if(response.removed == "yes"){
                            window.location.href = response.redirect_back;
                        }else{
                            showPopup(response.redirect_back);
                        }
                    } else {
                        errorMessage(response.message);
                    }
                },
                error: function () {
                    internalError();
                },
            });
        }
    });
}

function markAsAdmin(e)
{
    var url = $(e).attr("data-href");
    var action = $(e).attr("data-action");

    Swal.fire({
        title: "Are you sure to make as admin of group?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#198754",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            // redirect(url);
            $.ajax({
                type: "GET",
                url: url,
                dataType: "json",
                beforeSend: function () {
                    $(".loader").show();
                },
                success: function (response) {
                    $(".loader").hide();
                    if (response.status == true) {
                        window.location.href = response.redirect_back;
                    } else {
                        errorMessage(response.message);
                    }
                },
                error: function () {
                    internalError();
                },
            });
        }
    });
}
// Show typing indicator
function showGroupTypingIndicator(groupId, userName) {
    // Remove this line: alert(chatId+" = "+userName);
    console.log('Show typing indicator:', groupId, userName); // Use console.log for debugging
    
    $("#typing-groupbot-"+groupId).show();
    $("#typing-groupbot-"+groupId).find(".typechat-message").html(userName);
    
    if (ACTIVE_GROUP_CHAT == groupId) {
        // alert("ddd");
        $("#grpMessages"+groupId+" .typing-chat").fadeIn(200);
        $("#grpMessages"+groupId+" .typing-chat .membertyping").html(userName);
    }
}
// Hide typing indicator
function hideGroupTypingIndicator(groupId) {
    $("#typing-groupbot-"+groupId).hide();
    $("#typing-groupbot-"+groupId).find(".typechat-message").html("");
    
    
    if (ACTIVE_GROUP_CHAT == groupId) {
        $("#grpMessages"+groupId+" .typing-chat").fadeOut(200);
        $("#grpMessages"+groupId+" .typing-chat .membertyping").html("");
    }
}

function handleGroupBlur(input,groupId) {
    if (input.value.trim() === "") {
        input.value = "";
        $("#grp_chatbot_edit_msg_id" + groupId).val("");

        console.log("Blur function called!");
    }
}
// function debugGroupEchoConnection() {
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
//         debugGroupEchoConnection();
//     }, 1000);
// });