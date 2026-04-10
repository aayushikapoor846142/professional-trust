@if(isset($groupdata))
@if($groupdata)
@foreach($groupdata->sortByDesc(function ($grp) {
    return max(strtotime($grp->created_at), strtotime($grp->last_message_date ?? '0000-00-00 00:00:00'));
}) as $grp)
<div>
    <a class="chat-item group-chat-item  groupchatdiv{{$grp->unique_id}}"  data-group-id="{{$grp->id}}" data-unique-id="{{$grp->unique_id}}" data-href="{{ baseUrl('group/chat/'.$grp->unique_id) }}"> @if($grp->type == 'Private')
                <div class="group-badge private-group">
                    {{ $grp->type }}
                </div>
            @else @endif
        <div class="chat-avatar"> 
            @if($grp->group_image)
            <img src="{{ groupChatDirUrl($grp->group_image, 's') }}" alt="Doris">
            @else
            @php            
            $initial = strtoupper(substr($grp->name, 0, 1)); // Extracts the first letter and converts to uppercase
            @endphp
                <div class="group-icon dark-tiber" data-initial="{{$initial}}">
                </div>
            @endif
          
            </div>       
            <div class="chat-info">
                <p class="chat-name">{{substr($grp->name, 0, 20)}} </p>
                <p class="chat-preview"> 
                   
                   @if(checkGroupPermission('can_see_messages', $grp->id))

                    @if ($grp->lastMessage)
                        @if ($grp->lastMessage->attachment)
                            <span>Attachment</span>
                        @else
                        {!! makeBoldBetweenAsterisks(substr($grp->lastMessage->message, 0, 30) . "..." ) !!}
                        @endif
                    @else
                        No chat yet
                    @endif
                    @endif
                </p>           
            </div>
            <div class="group-chat-time">
                <span class="chat-time">
                   @if(checkGroupPermission('can_see_messages', $grp->id))
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
                    @endif
            </span>
            <div class="count-conatiner">
                @if(count($grp->groupRequest) > 0)
                <span class="unread-message request-count">{{count($grp->groupRequest)}}</span>
                @endif
                @if($grp->unreadMessage($grp->id,auth()->user()->id) > 0)                
                    <span class="unread-message unread-count">{{$grp->unreadMessage($grp->id,auth()->user()->id)}}</span>                
                @endif
            </div>
        </div>
    </a>
</div>
@endforeach
  
@else
<div class="empty-chat-request">
    <h5>No Groups Available</h5>
</div>
@endif
@endif


<script>
$(document).ready(function() {
    // $(document).on("click", ".group-chat-item", function() {
    //     // Show loader immediately
    //     $(".loader").show();

    //     if (window.innerWidth < 991) {
    //         // Open message-container animation
    //         $(".message-container").stop(true, true).css({
    //             'display': 'block', 
    //             'width': '0', 
    //             'right': '0'
    //         }).animate({
    //             width: '100%' 
    //         }, 300).addClass("active");
    //     }

    //     var url = $(this).data("href");
    //     var conversation_id = $(this).data("unique-id");
    //     var chatid = $(this).data("group-id");

    //     // Load chat content and hide loader after completion
    //     loadChatAjax(conversation_id, chatid, function() {
    //         $(".loader").hide(); // Hide loader after chat loads
    //     });

    //     history.pushState(null, '', url);
    // });

    // Close message-container with animation
    // $(document).on("click", function(e) {
    //     if (!$(e.target).closest(".message-container, .chat-item").length) {
    //         $(".message-container").stop(true, true).animate({
    //             width: '0%'
    //         }, 300, function() {
    //             $(this).removeClass("active").css('display', 'none'); 
    //         });
    //     }
    // });

    // Close message-container when clicking .back-chats
    $(document).on("click", ".back-chats", function() {
        $(".message-container").stop(true, true).animate({
            width: '0%'
        }, 300, function() {
            $(this).removeClass("active").css('display', 'none');
        });
    });
});
    </script>
