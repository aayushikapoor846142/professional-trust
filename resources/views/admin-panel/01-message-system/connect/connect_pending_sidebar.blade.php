@if($pending_user_list->count() != 0)
<h6 class="font-weight">Pending</h6>
@foreach($pending_user_list as $chat)
    <div class="connect-pending">
        <div class="chat-item" href="javascript:;" data-chat-unique-id="{{$chat->unique_id}}" data-chat-id="{{$chat->id}}" data-href="{{ baseUrl('message-centre/chat/'.$chat->unique_id) }}">
            <div class="chat-avatar">
                @if($chat->profile_image != '')
                    <img src="{{ $chat->profile_image ? userDirUrl($chat->profile_image, 't') : 'assets/images/default.jpg' }}" alt="Doris">
                @else
                    <div class="group-icon" data-initial="{{ userInitial($chat) }}"></div>
                @endif
            </div>
            <div class="pending-connect-info">
                <p class="user-name">{{$chat->first_name." ".$chat->last_name}}</p>
                @if(!empty(checkInvitation(auth()->user()->id,$chat->id)) && checkInvitation(auth()->user()->id,$chat->id)->status == 0)
                <button class="cds-btn-unfollow cds-smallbtn" onclick="removeConnection('{{checkInvitation(auth()->user()->id,$chat->id)->unique_id??0}}')">Unconnect</button>
                @endif
          
            </div>
        </div>
    </div>
@endforeach
@endif
