                        @foreach($chatUsersList as $chat)
                        @if(isset($chat->addedBy) && $chat->addedBy->id!=auth()->user()->id)
                        @php
                        $chat_with=$chat->addedBy;
                        @endphp
                        @else
                        @php
                        $chat_with=$chat->chatWith;
                        @endphp
                        @endif
                        <div>
                            <div class="chatbot-userlist"
                                onclick="openChatforMobileDesktop('{{ $chat->unique_id }}','{{ $chat->id }}')"
                                href="javascript:;" data-chat-unique-id="{{$chat->unique_id}}"
                                data-chat-id="{{$chat->id}}"
                                data-href="{{ baseUrl('message-centre/chat/'.$chat->unique_id) }}">
                                <div class="msg-media">
                                    @if(!empty($chat_with))
                                    <div class="chat-avatar">
                                        @if($chat_with->profile_image != '')
                                        <img src="{{ $chat_with->profile_image ? userDirUrl($chat_with->profile_image, 't') : 'assets/images/default.jpg' }}"
                                            alt="Doris">
                                        @else
                                        <div class="group-icon" data-initial="{{ userInitial($chat_with) }}"></div>
                                        @endif
                                        @if(loginStatus($chat_with) == 1)
                                        <span class="status-online login-status chatOnlineStatus{{$chat->id}}"></span>
                                        @else
                                        <span class="status-offline login-status chatOnlineStatus{{$chat->id}}"></span>
                                        @endif
                                    </div>
                                    <div class="chat-info">
                                        <p class="chat-name">{{$chat_with->first_name." ".$chat_with->last_name}}</p>
                                        <p class="chat-preview">
                                            @if ($chat->lastMessage)
                                            @if ($chat->lastMessage->attachment)
                                            <span>Attachment</span>
                                            @else
                                            {{ substr($chat->lastMessage->message, 0, 30) . "..." }}
                                            @endif
                                            @else
                                            No chat yet
                                            @endif
                                        </p>
                                    </div>
                                    @endif
                                </div>

                                <div class="msg-rightSide">
                                    <span class="chat-time">
                                        @php
                                        $timezone=getUserTimezone();
                                        $checkTimezone = isValidTimezone($timezone);
                                        $lastMessageTime = optional($chat->lastMessage)->created_at;
                                        $formattedTime = $lastMessageTime ? $lastMessageTime->format('H:i') : '';
                                        $totalUnread=0;
                                        @endphp
                                        @if($lastMessageTime)
                                        @if($checkTimezone)
                                        {{$lastMessageTime->timezone($timezone)->format('H:i');}}
                                        @else
                                        {{$formattedTime}}
                                        @endif
                                        @endif
                                        @if($chat->unreadMessage($chat->id,auth()->user()->id) > 0)
                                        @php
                                        $unreadCount = $chat->unreadMessage($chat->id, auth()->user()->id);
                                        $totalUnread += $unreadCount;
                                        @endphp

                                        <div>
                                            <span
                                                class="unread-message">{{$chat->unreadMessage($chat->id,auth()->user()->id)}}</span>
                                        </div>
                                        @endif
                                    </span>
                                    <div class="msg-openChat d-none">
                                        <a href="javascript:;" class="btn-click"><i
                                                class="fa-light fa-message-lines fa-xl"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach