  @php
  $totalGroupUnread=0;
  @endphp
  @if(!empty($currentUserGroupsList))
  @foreach($currentUserGroupsList->sortByDesc(function ($grp) {
  return max(strtotime($grp->created_at), strtotime($grp->last_message_date ?? '0000-00-00
  00:00:00'));
  }) as $grp)
  <div>
      <div class="chatbot-userlist"
            onclick="openGroupChatforMobileDesktop('{{ $grp->unique_id }}','{{ $grp->id }}')"
            data-group-id="{{$grp->id}}" 
            data-unique-id="{{$grp->unique_id}}"
            href="{{ baseUrl('group/chat/'.$grp->unique_id) }}">
          <div class="chat-avatar">
              @if($grp->group_image)
              <img src="{{ groupChatDirUrl($grp->group_image, 't') }}" alt="Doris">
              @else
              @php

              $initial = strtoupper(substr($grp->name, 0, 1)); // Extracts the first letter and
              @endphp
              <div class="group-icon" data-initial="{{$initial}}">

              </div>
              @endif
              @if($grp->type == 'Private')
              <div class="group-badge private-group">
                  {{ $grp->type }}
              </div>
              @else
              <div class="group-badge public-group">
                  {{ $grp->type }}
              </div>
              @endif

          </div>

          <div class="chat-info">
              <p class="chat-name">{{substr($grp->name, 0, 20)}} </p>


              <p class="chat-preview">
                  @if ($grp->lastMessage)
                  @if ($grp->lastMessage->attachment)
                  <span>Attachment</span>
                  @else
                  {!! makeBoldBetweenAsterisks(substr($grp->lastMessage->message, 0, 30) . "..." )
                  !!}
                  @endif
                  @else
                  No chat yet
                  @endif

              </p>

          </div>
          <div class="group-chat-time">
              <span class="chat-time">
                  @php
                  $timezone=getUserTimezone();
                  $checkTimezone = isValidTimezone($timezone);
                  $lastMessageTime = optional($grp->lastMessage)->created_at;
                  $formattedTime = $lastMessageTime ? $lastMessageTime->format('H:i') : '';

                  @endphp
                  @if($lastMessageTime)
                  @if($checkTimezone)
                  {{$lastMessageTime->timezone($timezone)->format('H:i');}}
                  @else
                  {{$formattedTime}}
                  @endif
                  @endif




              </span>
              <div class="count-conatiner">
                  @if(count($grp->groupRequest) > 0)
                  <span class="unread-message request-count">{{count($grp->groupRequest)}}</span>
                  @endif
                  @if($grp->unreadMessage($grp->id,auth()->user()->id) > 0)
                  @php
                  $unreadGroupCount = $grp->unreadMessage($grp->id, auth()->user()->id);
                  $totalGroupUnread += $unreadGroupCount;
                  @endphp

                  <span class="unread-message unread-count">{{$totalGroupUnread}}</span>

                  @endif
              </div>
          </div>
    </div>
  </div>
  @endforeach
  @else
  <div class="empty-chat-request">
      <h5>No Groups Available</h5>
  </div>
  @endif