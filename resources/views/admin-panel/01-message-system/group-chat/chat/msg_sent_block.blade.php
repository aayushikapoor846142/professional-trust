@if($chat_msg!=NULL)
<div class="sent-block" id="message-{{ $chat_msg->unique_id }}">
    <input style="display: none;" type="checkbox" name="clear_msg[]" value="{{$chat_msg->id}}" class="select-message" id="clear-msg{{$chat_msg->unique_id}}" />
    <!-- <div class="sent-icon">
        <img src="{{ auth()->user()->profile_image ? userDirUrl(auth()->user()->profile_image, 't') : 'assets/images/default.jpg' }}" alt="Doris">
    </div> -->
    <div class="cdsclearBox">
    <div class="message-block {{$chat_msg->checkMemberStatus($chat_msg->group_id,$chat_msg->user_id)}}">
        <div class="text-message-block">
            <div class="message sent">
                <div class="msg-reactions">
                    @if($chat_msg->messageReactions)
                    <div class="@if($chat_msg->messageReactions->count()>0){{'tooltip-container remove-icon'}}@endif"
                        id="reactionsList{{$chat_msg->unique_id}}">
                        <span class="small-thum-icon">
                            @if($chat_msg->messageReactions->count()>0)
                            <i class="fa-regular fa-face-smile-plus"></i>
                            <span class="number-counter">{{$chat_msg->messageReactions->count()}}</span></span>
                        @endif
                        <div class="tooltip-content">
                            <div class="users">
                                @foreach($chat_msg->messageReactions as $msgReaction)
                                <div class="reactions-user">
                                    <div class="circle {{userInitial($msgReaction->userAdded)}}">
                                        {{userInitial($msgReaction->userAdded)}}</div>
                                    <span
                                        class="user-name">{{$msgReaction->userAdded?$msgReaction->userAdded->first_name:''}}
                                    </span>
                                    <div class="more-emojis">
                                        {{$msgReaction->reaction}}
                                    </div>
                                </div>
                                @endforeach
                              
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="w-100">
                    @if($chat_msg->reply_to!=NULL && !empty($chat_msg->replyTo))
                    <div class="reply-to cds-cursor-pointer" onclick="scrollToGrpMessage(this,'{{$chat_msg->group_id}}','{{$chat_msg->replyTo->unique_id}}')">
                        @if($chat_msg->replyTo->message!=NULL)
                        <span class="username">{!! makeBoldBetweenAsterisks($chat_msg->replyTo->message) !!}</span>
                        @endif
                        @if($chat_msg->replyTo->attachment!=NULL)
                        <div class="attachment cds-cursor-pointer" onclick="scrollToGrpMessage(this,'{{$chat_msg->group_id}}','{{$chat_msg->replyTo->unique_id}}')">
                            <a href="javascript:;" class="popup-link img-fluid">
                                <div class="file-icon">
                                    <i class="fa-solid fa-paperclip"></i>
                                </div>
                                <div class="file-info">
                                    <span class="file-name">Attachment</span>
                                </div>
                            </a>
                        </div>
                        @endif
                        <p class="quoted-message">Reply quoted message</p>
                    </div>
                    @endif

                    @if($chat_msg->deleted_at!=NULL )
                    <p class="deleted-message chat-message">This message was deleted.</p>
                    @else
                    <span style="font-size:10px;" id="editedMsg{{$chat_msg->unique_id}}">
                        @if($chat_msg->edited_at)
                        edited
                        @endif
                    </span>
                    <p class="chat-message" id="cpMsg{{$chat_msg->unique_id}}">{!!
                        makeBoldBetweenAsterisks($chat_msg->message) !!}</p>
                    @if($chat_msg->attachment)
                    <div class="{{(count(explode(',',$chat_msg->attachment)) > 1)?'files-uploaded':''}}">

                        @include('admin-panel.01-message-system.group-chat.chat.partials.attachments_common',['get_attachments'=>$chat_msg->attachment,
                        'chat_msg_id' => $chat_msg->id,
                        'chat_msg_Unique_id' => $chat_msg->unique_id])

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
                </span>
            </div>  
            @if($chat_msg->deleted_at==NULL )
            <div class="dropdown chat-dropdown">
                <button class="btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="fa-solid fa-ellipsis"></i>
                </button>
                    <ul class="dropdown-menu">
                        @if($chat_msg->message!=NULL)
                        <li>
                            <a class="dropdown-item" href="javascript:;" onclick="groupCopyMessage('cpMsg{{$chat_msg->unique_id}}')">Copy <i class="fa fa-copy"></i> </a>
                        </li>
                        @if ($chat_msg->created_at >= now()->subHour())
                        <li>
                            <a class="dropdown-item" href="javascript:;" onclick="editGroupMessage(this,'{{ $chat_msg->group_id }}', '{{ $chat_msg->unique_id }}')">Edit <i class="fa fa-edit"></i></a>
                        </li>
                        @endif @endif
                        <li>
                            <a class="dropdown-item" href="javascript:;" onclick="groupReplyTo(this,'{{ $chat_msg->group_id }}','{{$chat_msg->id}}')">Reply <i class="fa fa-reply"></i> </a>
                        </li>

                        @if ($chat_msg->created_at >= now()->subHour())
                        <li>
                            <a class="dropdown-item" href="javascript:;" onclick="deleteGroupMessageforAll('{{ $chat_msg->id }}','{{ $chat_msg->unique_id }}')">Delete for Everyone <i class="fa fa-trash"></i></a>
                        </li>
                        @endif

                        <li>
                            <a class="dropdown-item" href="javascript:;" onclick="deleteGroupMessage('{{ $chat_msg->id }}','{{ $chat_msg->unique_id }}')">Delete <i class="fa fa-trash"></i></a>
                        </li>
                </ul>
            </div>
            @endif
        </div>
        </div>
        <div style="display: none;" class="clear-checkbox">
            <label class="cds-checkbox">
                <input type="checkbox" name="clear_msg[]" value="{{$chat_msg->id}}"  class="select-message" id="clear-msg{{$chat_msg->unique_id}}" />
                <span class="checkmark"></span>
            </label>
        </div>
    </div>
    <div class="sender-name">
        {{-- <p class="">{{ auth()->user()->first_name." ".auth()->user()->last_name }}</p>
        --}}
    </div>
</div>
@endif