@php
$lastDisplayedDate = null;
$displayedToday = false;
$displayedYesterday = false;
@endphp

@if(count($chat_messages)>0)

    @foreach($chat_messages as $chat_msg)
    @php
    $created_date = $chat_msg->created_at->format('Y-m-d');
    $check_prev_msg = $chat_msg->checkBeforeMessage($chat_msg->id,$chat_msg->chat_id,$created_date,auth()->user()->id);
    $formattedDate = '';
    // Check if the message is from today and if "Today" has not been displayed yet
    if ($chat_msg->created_at->isToday() && $check_prev_msg == 0) {
    $formattedDate = 'Today';
    $displayedToday = true;
    } elseif ($chat_msg->created_at->isYesterday() && $check_prev_msg == 0){
    $formattedDate = 'Yesterday';
    $displayedToday = true;
    }else if($check_prev_msg == 0){
    $formattedDate = $chat_msg->created_at->format('F d, Y');
    }
    @endphp

    @if(!empty($chat_msg))
    @if($formattedDate != '')
    <div class="datenotification msg-notification" data-date="{{ date("Y-m-d",strtotime($chat_msg->created_at)) }}" id="date-{{ $created_date }}">
        <div class="datenotification-label">
            {{ $formattedDate ?? '' }}
        </div>
    </div>
    @endif
    @if(isset($unread_from_id) && $unread_from_id == $chat_msg->unique_id)
    <div class="unread-message-from msg-notification">
        Unread Message
    </div>
    @endif
    @if($chat_msg->sent_by != auth()->user()->id)
    @if(isset($chat->addedBy) && $chat->addedBy->id!=auth()->user()->id)
    @php
    $chat_with=$chat->addedBy;
    @endphp
    @else
    @php
    $chat_with=$chat->chatWith;
    @endphp
    @endif



    <div class="received-block" id="message-{{$chat_msg->unique_id}}">        
        <div class="received-icon chat-avatar">
            @if($chat_with->profile_image != '')
            <img src="{{ $chat_with->profile_image ? userDirUrl($chat_with->profile_image, 't') : 'assets/images/default.jpg' }}"
                alt="Doris">
            @else
            <div class="group-icon" data-initial="{{ userInitial($chat_with) }}"></div>
            @endif
        </div>
        <div class="cdsclearBox">
            <div style="display:none"  class="clear-checkbox">                    
                <label class="cds-checkbox">
                    <input type="checkbox" name="clear_msg[]" value="{{$chat_msg->id}}"  class="select-message" id="clear-msg{{$chat_msg->unique_id}}" />
                    <span class="checkmark"></span>
                </label>
            </div>
            <div class="message-block">
                <div class="textreceive-message-block">
                    <div class="message received">
                        <div class="msg-reactions">
                            @if($chat_msg->messageReactions)
                            @foreach($chat_msg->messageReactions as $reaction)
                            @if($reaction->added_by == auth()->user()->id)
                            <a class="{{$reaction->added_by == auth()->user()->id?'remove-reaction':''}} reaction"
                                onclick="removeChatReaction(this)" id="MsgReaction{{$reaction->unique_id}}"
                                data-id="{{$reaction->unique_id}}" href="javascript:;">{{$reaction->reaction}}</a>
                            @else
                            <span class="remove-reaction reaction"
                                id="MsgReaction{{$reaction->unique_id}}">{{$reaction->reaction}}</span>
                            @endif
                            @endforeach
                            @endif
                        </div>
                        <div>
                            <span style="font-size:10px;" id="editedMsg{{$chat_msg->unique_id}}">
                                @if($chat_msg->edited_at)
                                edited
                                @endif
                            </span>
                            @if($chat_msg->deleted_at==NULL)
                            @if($chat_msg->reply_to!=NULL)
                            @if(!empty($chat_msg->replyTo))
                            @if($chat_msg->replyTo->deleted_at==NULL)
                            <div class="reply-to-received cds-cursor-pointer" onclick="scrollToMessage(this,'{{$chat_msg->chat_id}}','{{$chat_msg->replyTo->unique_id}}')">
                                @if($chat_msg->replyTo->message!=NULL)
                                <span class="username">{{$chat_msg->replyTo->message}}</span>
                                @endif
                                @if($chat_msg->replyTo->attachment!=NULL)
                                <div class="attachment cds-cursor-pointer" onclick="scrollToMessage(this,'{{$chat_msg->chat_id}}','{{$chat_msg->replyTo->unique_id}}')">
                                    <a href="javascript:;" class="popup-link img-fluid">
                                        <div class="file-icon">
                                            <i class="fa-solid fa-paperclip"></i>
                                        </div>
                                        <div class="file-info">
                                            <span class="file-name">Attachment</span>
                                        </div>
                                    </a>
                                </div>
                                {{--  @include('admin-panel.01-message-system.message-centre.partials.attachments_common', [
                                'get_attachments' => $chat_msg->replyTo->attachment,
                                'chat_msg_id' => $chat_msg->replyTo->id,
                                'chat_msg_Unique_id' => $chat_msg->replyTo->unique_id
                                ])--}}
                                @endif

                                <p class="quoted-message">Reply quoted message</p>
                            </div>
                            @else
                            <p class="deleted-message chat-message">This message was deleted.</p>

                            @endif
                            @endif
                            @endif
                            @endif
                            @if($chat_msg->deleted_at )
                            <p class="deleted-message chat-message">This message was deleted.</p>
                            @else
                            <p class="chat-message" id="cpMsg{{$chat_msg->unique_id}}">{{ $chat_msg->message }}</p>
                            @if($chat_msg->attachment)
                            <div class="{{(count(explode(',',$chat_msg->attachment)) > 1)?'files-uploaded':''}}">
                                @include('admin-panel.01-message-system.message-centre.partials.attachments_common', [
                                'get_attachments' => $chat_msg->attachment,
                                'chat_msg_id' => $chat_msg->id,
                                'chat_msg_Unique_id' => $chat_msg->unique_id
                                ])


                            </div>
                            @endif
                            @endif



                        </div>
                        <span class="chat-time">
                            <i class="fa-sharp fa-regular fa-clock"></i>
                            @php
                            $timezone=getUserTimezone();
                            $checkTimezone = isValidTimezone($timezone);
                            @endphp
                            @if($checkTimezone)
                            {{ $chat_msg->created_at->timezone($timezone)->format('H:i') }}
                            @else
                            {{ date('H:i', strtotime($chat_msg->created_at)) }}
                            @endif
                            <span class="readtrack">
                                @if($chat_msg->deleted_at==NULL)
                                @if($chat_msg->checkReceiverRead($chat_msg->chat_id,$chat_msg->id,$receiver_id) == 'unread')
                                <i class="fa-sharp-duotone fa-solid fa-check text-muted"></i>
                                @else
                                <i class="fa-sharp fa-solid fa-check-double text-primary"></i>
                                @endif
                                @endif
                            </span>
                        </span>


                    </div>
                    @if($chat_msg->deleted_at==NULL && $chat->blocked_chat!=1)
                    <div class="align-items-start chat-dropdown d-flex dropdown gap-1">
                        <button class="btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-ellipsis"></i>
                        </button>
                        <ul class="dropdown-menu">
                            @if($chat_msg->message!=NULL)
                            <li>
                                <a class="dropdown-item" href="javascript:;"
                                    onclick="copyChatBotMessage('cpMsg{{$chat_msg->unique_id}}')">Copy <i class="fa fa-copy"></i>
                                </a>
                            </li>
                            @endif
                            <li>
                                <a class="dropdown-item replyTo" href="javascript:;" onclick="replyTo(this,'{{$chat_msg->chat_id}}','{{ $chat_msg->id }}')">Reply
                                    <i class="fa fa-reply"></i>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="javascript:;" onclick="deleteChatBotMessage('{{ $chat_msg->id }}','{{ $chat_msg->unique_id }}')">Delete for me  <i class="fa fa-trash"></i>
                                </a>
                            </li>
                        </ul>
                        <div class="emoji-reaction-area">
                            <div class='reaction-input'>
                                <div class="emoji-icon message-reaction" data-id="{{$chat_msg->unique_id}}"
                                    id="reaction-{{$chat_msg->unique_id}}">
                            
                                    <i class="fa-regular fa-face-smile cds-faceicon"></i>

                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @else
    @include('admin-panel.01-message-system.message-centre.msg_sent_block')
    <!-- old -->
    @endif
    @endif
    @endforeach
@endif

<script>
$(document).ready(function() {
    var openfrom = "{{ $openfrom }}";
    var chat_id = "{{ $chat_id }}";
   
});
</script>
<script>
$(document).ready(function() {
    var chatBox = $("#scrollDiv");
    var topBtn = $("#scrollToTop");
    var bottomBtn = $("#scrollToBottom");
    var hideTimeout;
    var scrollTimeout;
    var lastScrollTop = chatBox.scrollTop();

    function showButton(btn, delay) {
        setTimeout(function() {
            btn.addClass("show");

            // Clear previous hide timeout
            clearTimeout(hideTimeout);

            // Hide button after 3-5 seconds
            hideTimeout = setTimeout(function() {
                btn.removeClass("show");
            }, 3000);
        }, delay);
    }

    // Scroll to bottom function
    bottomBtn.click(function() {
        chatBox.animate({
            scrollTop: chatBox[0].scrollHeight
        }, 500);
        showButton(bottomBtn, 0);
    });

    // Scroll to top function
    topBtn.click(function(e) {
        e.preventDefault();
        chatBox.animate({
            scrollTop: 0
        }, 500);
        showButton(topBtn, 0);
    });

    // Detect scrolling
    chatBox.scroll(function() {
        var scrollPos = chatBox.scrollTop();
        var scrollHeight = chatBox[0].scrollHeight - chatBox.outerHeight();

        // Hide bottom button when at the bottom
        if (scrollPos >= scrollHeight - 5) {
            bottomBtn.removeClass("show");
        }
        // Check scrolling direction
        if (scrollPos > lastScrollTop) { // Scrolling down
            clearTimeout(scrollTimeout);
            showButton(topBtn, 0);
        } else { // Scrolling up
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(function() {
                showButton(bottomBtn, 0);
            }, 2000); // Show after 3 seconds
        }
        lastScrollTop = scrollPos;
    });
    // Auto-hide bottom button if already at bottom
    chatBox.scrollTop(chatBox[0].scrollHeight);
});
</script> 