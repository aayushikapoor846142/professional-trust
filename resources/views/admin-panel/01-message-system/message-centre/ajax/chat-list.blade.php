@if(count($chat_user_list)>0)
    @foreach($chat_user_list as $chat)
        @php
            if($chat->user1_id == auth()->user()->id){
                $chat_with = $chat->chatWith;
            }else{
                $chat_with = $chat->addedBy;
            }
        @endphp
        <div class="user-chat-item chatdiv{{$chat->unique_id}}" data-href="{{ baseUrl('message-centre/chat/'.$chat->unique_id) }}" data-chat-unique-id="{{$chat->unique_id}}" data-chat-id="{{$chat->id}}">
            <div class="chat-avatar">
                @if($chat_with->profile_image != '')
                <img src="{{ $chat_with->profile_image ? userDirUrl($chat_with->profile_image, 't') : 'assets/images/default.jpg' }}"
                    alt="Doris">
                @else
                <div class="group-icon" data-initial="{{ userInitial($chat_with) }}"></div>
                @endif
                @if(loginStatus($chat_with) == 1)
                <span class="status-online"></span>
                @else
                <span class="status-offline"></span>
                @endif
            </div>
            <div class="chat-info">
                <div class="chat-name">
                    <h4>{{$chat_with->first_name." ".$chat_with->last_name}}</h4>
                </div>
                <div class="chat-message">
                    @if($chat->lastMessage)
                        @if($chat->lastMessage->deleted_at == NULL)
                            <p>{{ $chat->lastMessage->message }}</p>
                        @else
                            <p class="deleted-message">This message was deleted.</p>
                        @endif
                    @else
                        <p>No messages yet</p>
                    @endif
                </div>
            </div>
            <div class="chat-time">
                @if($chat->lastMessage)
                    <span>{{ $chat->lastMessage->created_at->diffForHumans() }}</span>
                @endif
            </div>
        </div>
    @endforeach
@else
    <div class="no-chats">
        <p>No conversations yet</p>
    </div>
@endif 