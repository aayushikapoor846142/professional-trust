<div class="CdsIndividualChat-message-wrapper sent" id="message-{{ $message->unique_id }}" data-message-timestamp="{{ $message->created_at->toISOString() }}">
    <!-- Clear Chat Checkbox -->
    <div class="clear-checkbox" style="display: none;">
        <label class="cds-checkbox">
            <input type="checkbox" name="clear_msg[]" value="{{ $message->id }}" class="select-message" />
            <span class="checkmark"></span>
        </label>
    </div>
    <div class="CdsIndividualChat-message sent">
        <!-- Message Reactions -->
        <div class="CdsIndividualChat-message-reactions">
        @if($message->messageReactions && count($message->messageReactions) > 0)
            @foreach($message->messageReactions as $reaction)
            <span class="CdsIndividualChat-reaction"  id="MsgReaction{{$reaction->unique_id}}">
                {{$reaction->reaction}}
            </span>
            @endforeach
        @endif
        </div>
        <!-- Reply to Message -->
        @if($message->reply_to != NULL && !empty($message->replyTo) && $message->replyTo->deleted_at == NULL)
        <div class="CdsIndividualChat-reply-to">
            @if($message->replyTo->message != NULL)
            <span class="CdsIndividualChat-reply-text">{{$message->replyTo->message}}</span>
            @endif
            @if($message->replyTo->attachment != NULL)
            <div class="CdsIndividualChat-reply-attachment">
                <i class="fa-solid fa-paperclip"></i>
                <span>Attachment</span>
            </div>
            @endif
            <p class="CdsIndividualChat-reply-label">Reply quoted message</p>
        </div>
        @endif
        
        <!-- Message Content -->
        @if($message->deleted_at)
        <p class="CdsIndividualChat-deleted-message">This message was deleted.</p>
        @else
        <span style="font-size: 10px;" id="editedMsg{{ $message->unique_id }}">
            @if($message->edited_at) edited @endif
        </span>
        <div class="CdsIndividualChat-message-text" id="cpMsg{{ $message->unique_id }}">{{ $message->message }}</div>
        
        <!-- Attachments -->
        @if($message->attachment)
        <div class="CdsIndividualChat-attachments {{(count(explode(',',$message->attachment)) > 1)?'CdsIndividualChat-files-uploaded':''}}">
            @include('admin-panel.01-message-system.individual-chats.attachment-common', [
            'get_attachments' => $message->attachment,
            'chat_msg_id' => $message->id,
            'chat_msg_Unique_id' => $message->unique_id
            ])
        </div>
        @endif
        @endif
        
        <div class="CdsIndividualChat-message-time">
            <span>
                @php
                $timezone = getUserTimezone();
                $checkTimezone = isValidTimezone($timezone);
                @endphp
                @if($checkTimezone)
                    {{ $message->created_at->timezone($timezone)->format('H:i') }}
                @else
                    {{ date('H:i', strtotime($message->created_at)) }}
                @endif
            </span>
            <span class="readtrack">
                @if($message->deleted_at == NULL)
                    @if($message->checkReceiverRead($message->chat_id, $message->id, $receiver_id) == 'unread')
                        <i class="fa-sharp-duotone fa-solid fa-check text-muted"></i>
                    @else
                        <i class="fa-sharp fa-solid fa-check-double text-primary"></i>
                    @endif
                @endif
            </span>
        </div>
        
        <!-- Message Options Dropdown -->
        @if($message->deleted_at == NULL && $chat->blocked_chat != 1)
        <div class="CdsIndividualChat-message-options">
            <div class="message-options-dropdown">
                <button class="message-options-toggle" type="button" data-option-id="{{ $message->unique_id }}" >
                    <i class="fa-solid fa-ellipsis"></i>
                </button>
                <ul id="msg-options-{{ $message->unique_id }}" class="message-options-menu">
                    @if($message->message != NULL)
                    <li>
                        <a class="message-option-item copy-message" href="javascript:;" 
                           data-message-id="{{ $message->unique_id }}">
                            Copy <i class="fa fa-copy"></i>
                        </a>
                    </li>
                    @if ($message->created_at >= now()->subHour())
                    <li>
                        <a class="message-option-item edit-message" href="javascript:;" 
                           data-message-id="{{ $message->unique_id }}" 
                           data-message-text="{{ $message->message }}">
                            Edit <i class="fa fa-edit"></i>
                        </a>
                    </li>
                    @endif
                    @endif
                    <li>
                        <a class="message-option-item reply-to-message-option" href="javascript:;" data-message-id="{{ $message->id }}"
                        data-message-text="{{ $message->message }}" 
                           onclick="replyTo(this,'{{ $message->chat_id }}','{{ $message->id }}')">
                            Reply <i class="fa fa-reply"></i>
                        </a>
                    </li>
                    @if ($message->created_at >= now()->subHour())
                    <li id="del_msg_for_all{{ $message->unique_id }}">
                        <a class="message-option-item delete-message-for-everyone" href="javascript:;" 
                           data-message-id="{{ $message->id }}" 
                           data-message-unique-id="{{ $message->unique_id }}">
                            Delete for Everyone <i class="fa fa-trash"></i>
                        </a>
                    </li>
                    @endif
                    <li>
                        <a class="message-option-item delete-message-for-me" href="javascript:;" 
                           data-message-id="{{ $message->id }}" 
                           data-message-unique-id="{{ $message->unique_id }}">
                            Delete for me <i class="fa fa-trash"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        @endif
    </div>
    <!-- <div class="message-reaction emoji-icon" data-message-id="{{ $message->unique_id }}" id="emojiBtn-{{ $message->unique_id }}">
        <i class="fa-sharp fa-solid fa-face-smile"></i>
    </div> -->
</div>