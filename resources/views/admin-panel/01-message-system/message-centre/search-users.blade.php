<div class="search-users">
    <ul class="user-lists">
        @foreach($users as $user)
        <div>
            <div class="chat-item">
                <div class="mediaLeft">
                    <div class="chat-avatar">
                        @if($user->profile_image)
                        <img src="{{ $user->profile_image ? userDirUrl($user->profile_image, 't') : 'assets/images/default.jpg' }}"
                            alt="Doris">
                        @else
                        <div class="group-icon" data-initial="{{ userInitial($user) }}"></div>
                        @endif
                        @if($user->is_login==1)
                        <span class="status-online"></span>
                        @else
                        <span class="status-offline"></span>
                        @endif
                    </div>
                    <div class="chat-info">
                        <div>{{$user->first_name." ".$user->last_name}}</div>
                        <div class="text-danger">[{{$user->email}}]</div>
                    </div>
                </div>                

                @if(isset($user->invitation_send))
                @if(isset($user->invitation_accepted))
                @if($user->invitation_accepted==1)
                <div class="send-invite">
                    <button type="button" class="btn btn-success btn-sm">Invitation Accepted </button>
                </div>
                @elseif($user->receiver_id==auth()->user()->id)
                <div class="send-invite">
                    <button onclick="confirmAnyAction(this)" data-action="Accept Invitation"
                        data-href="{{ baseUrl('accept-chat-request/'.$user->chat_reqst_id) }}" type="button"
                        class="btn btn-success btn-sm">Accept</button>
                </div>
                <div class="send-invite">
                    <button onclick="confirmAnyAction(this)" data-action="Decline Invitation"
                        data-href="{{ baseUrl('decline-chat-request/'.$user->chat_reqst_id) }}" type="button"
                        class="CdsTYButton-btn-primary CdsTYButton-border-thick btn-sm">Decline</button>
                </div>
                @elseif($user->invitation_accepted==0)
                <div class="send-invite">
                    <button onclick="confirmAction(this)"
                        data-href="{{ baseUrl('connections/invitations/delete/'.$user->invitation_id) }}" type="button"
                        class="CdsTYButton-btn-primary CdsTYButton-border-thick btn-sm">Remove</button>
                </div>
                @elseif($user->invitation_accepted==1)
                <div class="send-invite">
                    <button type="button" class="btn btn-secondary btn-sm">Invitation Sent</button>
                </div>
                @endif
                @endif

                @else
                <div class="send-invite">
                    <button onclick="sendInvitation('{{$user->email}}')" type="button"
                        class="CdsTYButton-btn-primary btn-sm">Send Invitation</button>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </ul>
</div>