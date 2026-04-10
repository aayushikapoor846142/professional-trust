
@if($groupdata->isNotEmpty() )
@foreach($groupdata->sortByDesc(function ($grp) {
    return max(strtotime($grp->created_at), strtotime($grp->last_message_date ?? '0000-00-00 00:00:00'));
}) as $grp)
<div class="other-group-tab">
    <a class="chat-item other-group-info" data-group-id="{{$grp->id}}" data-unique-id="{{$grp->unique_id}}">
        <div class="chat-avatar">
            @if($grp->group_image)
            <img src="{{ groupChatDirUrl($grp->group_image, 't') }}" alt="Doris">
            @else
            @php

            $initial = strtoupper(substr($grp->name, 0, 1)); // Extracts the first letter and converts to uppercase
            @endphp
            <div class="group-icon" data-initial="{{$initial}}">

            </div>
            @endif


        </div>

        <div class="chat-info">
            <p class="chat-name">{{substr($grp->name, 0, 20)}}</p>
           
            <div style="display: none;">
                @if(count($grp->groupRequest) > 0)
                <span class="request-count">    {{count($grp->groupRequest)}}</span>
                @endif
            </div>
        </div>
        @if($grp->type == 'Private')
        <div class="group-badge private-group">
            {{ $grp->type }}
        </div>
        @else
      
        @endif
      
    </a>
</div>
@endforeach
 
@else
@if(!$show_empty)
    <div class="empty-chat-request">
    <h5>No Groups Available</h5>
    </div>
@endif
@endif


<script>
$(document).ready(function() {
    $(document).on("click", ".other-group-info", function() {
        $(".loader").show(); // Show loader immediately

        if (window.innerWidth < 991) {
            $(".message-container").stop(true, true).css({
                'display': 'block', 
                'width': '0', 
                'right': '0'
            }).animate({
                width: '100%' 
            }, 300).addClass("active");
        }

        var url = $(this).data("href");
        var conversation_id = $(this).data("chat-unique-id");
        var chatid = $(this).data("chat-id");

        // Load chat content and hide loader after completion
        $.ajax({
            url: 'your-api-endpoint', // Replace with actual API endpoint
            method: 'GET',
            data: { conversation_id: conversation_id, chatid: chatid },
            success: function(response) {
                $(".message-container").html(response);
            },
            error: function() {
                console.error("Error loading chat");
            },
            complete: function() {
                $(".loader").hide(); // Ensure loader hides after request completes
            }
        });

        history.pushState(null, '', url);
    });

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
