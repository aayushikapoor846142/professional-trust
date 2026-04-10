@if($chat_msg!=NULL)
<div class="sent-block" id="message-{{ $chat_msg->unique_id }}">
    {{-- <input type="checkbox" style="display: none;" name="clear_msg[]" value="{{$chat_msg->id}}" class="select-message" id="clear-msg{{$chat_msg->unique_id}}" /> --}}
    <div class="cdsclearBox">
        <div class="message-block">
            <div class="text-message-block">
                <div class="message sent {{$chat_msg->checkReceiverRead($chat_msg->chat_id,$chat_msg->id,$receiver_id)}}">
                    <div class="msg-reactions">
                        @if($chat_msg->messageReactions)
                            @foreach($chat_msg->messageReactions as $reaction)
                                <span class="" id="MsgReaction{{$reaction->unique_id}}">{{$reaction->reaction}}</span>
                            @endforeach
                        @endif
                    </div>
                    <span class="chat-time">
                        <i class="fa-sharp fa-regular fa-clock"></i>
                        @php $timezone=getUserTimezone(); $checkTimezone = isValidTimezone($timezone); @endphp @if($checkTimezone) {{ $chat_msg->created_at->timezone($timezone)->format('H:i') }} @else {{ date('H:i', strtotime($chat_msg->created_at)) }}
                        @endif
                        <span class="readtrack">
                            @if($chat_msg->deleted_at==NULL) @if($chat_msg->checkReceiverRead($chat_msg->chat_id,$chat_msg->id,$receiver_id) == 'unread')
                            <i class="fa-sharp-duotone fa-solid fa-check"></i>
                            @else
                            <i class="fa-sharp fa-solid fa-check-double"></i>
                            @endif @endif
                        </span>
                    </span>
                    <div class="cdsshowmsg w-100">
                        @if($chat_msg->reply_to!=NULL) @if(!empty($chat_msg->replyTo))
                        <div class="reply-to cds-cursor-pointer" onclick="scrollToMessage(this,'{{$chat_msg->chat_id}}','{{$chat_msg->replyTo->unique_id}}')">
                            @if($chat_msg->replyTo->message!=NULL)
                            <span class="username">{{$chat_msg->replyTo->message}}</span>
                            @endif @if($chat_msg->replyTo->attachment!=NULL)
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
                            {{-- @include('admin-panel.01-message-system.message-centre.partials.attachments_common', [ 'get_attachments' => $chat_msg->replyTo->attachment, 'chat_msg_id' => $chat_msg->replyTo->id, 'chat_msg_Unique_id' =>
                            $chat_msg->replyTo->unique_id ]) --}} @endif

                            <p class="quoted-message">Reply quoted message</p>
                        </div>
                        @endif @endif @if($chat_msg->deleted_at!=NULL )
                        <p class="deleted-message chat-message">This message was deleted.</p>
                        @else
                        <span style="font-size: 10px;" id="editedMsg{{$chat_msg->unique_id}}">
                            @if($chat_msg->edited_at) edited @endif
                        </span>
                        <p class="chat-message" id="cpMsg{{$chat_msg->unique_id}}">{{ $chat_msg->message }}</p>
                        @if($chat_msg->attachment)
                        <div class="{{(count(explode(',',$chat_msg->attachment)) > 1)?'files-uploaded':''}}">
                            @include('admin-panel.01-message-system.message-centre.partials.attachments_common', [ 'get_attachments' => $chat_msg->attachment, 'chat_msg_id' => $chat_msg->id, 'chat_msg_Unique_id' => $chat_msg->unique_id ])
                        </div>
                        @endif @endif
                    </div>
                </div>
                <div class="dropdown chat-dropdown">
                    @if($chat_msg->deleted_at==NULL && $chat->blocked_chat!=1)
                    <button class="btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-ellipsis"></i>
                    </button>

                    <ul class="dropdown-menu">
                        @if($chat_msg->message!=NULL)
                        <li>
                            <a class="dropdown-item" href="javascript:;" onclick="copyChatBotMessage('cpMsg{{$chat_msg->unique_id}}')">Copy <i class="fa fa-copy"></i></a>
                        </li>
                        @if ($chat_msg->created_at >= now()->subHour())
                        <li>
                            <a class="dropdown-item" href="javascript:;" onclick="editUserMessage(this,'{{ $chat_msg->chat_id }}','{{ $chat_msg->unique_id }}')">Edit <i class="fa fa-edit"></i></a>
                        </li>
                        @endif @endif
                        <li>
                            <a class="dropdown-item replyTo" href="javascript:;" onclick="replyTo(this,'{{$chat_msg->chat_id}}','{{$chat_msg->id}}')">Reply <i class="fa fa-reply"></i></a>
                        </li>
                        {{-- @if($chat_msg->checkReceiverRead($chat_msg->chat_id,$chat_msg->id,$receiver_id) == 'unread') --}} @if ($chat_msg->created_at >= now()->subHour())
                        <li id="del_msg_for_all{{$chat_msg->unique_id}}">
                            <a class="dropdown-item" href="javascript:;" onclick="deleteChatBotMessageforAll('{{ $chat_msg->id }}','{{ $chat_msg->unique_id }}')">Delete for Everyone <i class="fa fa-trash"></i></a>
                        </li>
                        @endif
                        <li>
                            <a class="dropdown-item" href="javascript:;" onclick="deleteChatBotMessage('{{ $chat_msg->id }}','{{ $chat_msg->unique_id }}')">Delete for me <i class="fa fa-trash"></i></a>
                        </li>
                    </ul>
                    @endif
                </div>
            </div>
            <div class="sender-name">
                {{--
                <p class="">{{ auth()->user()->first_name." ".auth()->user()->last_name }}</p>
                --}}
            </div>  
        </div>
        <div style="display: none;" class="clear-checkbox">
            <label class="cds-checkbox">
                <input type="checkbox" name="clear_msg[]" value="{{$chat_msg->id}}" class="select-message" id="clear-msg{{$chat_msg->unique_id}}" />
                <span class="checkmark"></span>
            </label>
        </div>
    </div>
</div>
@endif
