<div class="chat-list">
    <div class="chat-title">
        <h2>Chat Request</h2>
    </div>
    <div class="chat-header">
        <div class="group-search">
            <a href="javascript:;" class="search-icon"><i class="fa-sharp fa-regular fa-magnifying-glass"></i></a>
            <input type="text" placeholder="Search Members.." />
            <a href="javascript:;" class="clear-text"><i class="fa-times fa-regular fa-magnifying-glass"></i></a>
        </div>
    </div>
    <div class="recent-chats">
        <h3>Recent</h3>
        @if($chat_requests)
        @foreach($chat_requests as $rqst)
        <div class="chat-request" id="chat-{{ $rqst->unique_id }}">
            <div class="chat-item">                
                <div class="chat-avatar">
                    @php
                    $initial = userInitial($rqst->sender);
                    $imageUrl = $rqst->sender->profile_image ? userDirUrl($rqst->sender->profile_image, 't') : null;
                        @endphp
    
                    @if($imageUrl)
                     <img src="{{ $imageUrl }}" alt="User Image">
                        @else
                    <div class="group-icon">{{ $initial }}</div>
                @endif 
                    <span class="status-online"></span>
                </div>
                <div class="chat-info ">
                    <p class="chat-name">{{$rqst->sender->first_name." ".$rqst->sender->last_name}}</p>
                    @if($rqst->is_accepted==0)
                    <div class="chat-request-action-btn">
                        <li>
                            <a href="javascript:;" onclick="acceptChatRequest({{$rqst->unique_id}})">
                                <i class="tio-edit"></i> Accept
                            </a>
                        <li>
                            <a href="javascript:;" onclick="decllineChatRequest({{$rqst->unique_id}})">
                                 Decline
                            </a>
                        </li>
    
                    </div>
                    @else
                    <div class="request-status">
                        @if($rqst->is_accepted==1)
                        <p class="accepted">
                            {{'Accepted'}}
                        </p>
                        @elseif($rqst->is_accepted==2)
                        <p class="declined">
                            {{'Declined'}}
                        </p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
        @else
        <!-- Empty chat request -->
        <div class="empty-chat-request">
            <h5>No Request Available</h5>
        </div>
        @endif
    </div>
</div>

<script>
    function acceptChatRequest(request_id) {
   
        $.ajax({
            type: 'get',
            url: "{{ baseUrl('/accept-chat-request') }}/" + request_id,
            dataType: 'json',
            success: function(response) {
                if (response.status == true) {
                    successMessage(response.message);
                    window.location.href = response.redirect_back;
                    $('#chat-' + response.unique_id).remove();
    
                } else {
                    errorMessage(response.message);
                }
                // console.log(data.contents);
                // $('.to-connet-div').html(data.contents);
            },
            error: function(xhr, status, error) {
                console.log('Error fetching messages:', error);
            }
        });
    }
    </script>

<script>
    function decllineChatRequest(request_id) {
   
        $.ajax({
            type: 'get',
            url: "{{ baseUrl('/decline-chat-request') }}/" + request_id,
            dataType: 'json',
            success: function(response) {
                if (response.status == true) {
                    successMessage(response.message);
                    $('#chat-' + response.unique_id).remove();
    
                } else {
                    errorMessage(response.message);
                }
                // console.log(data.contents);
                // $('.to-connet-div').html(data.contents);
            },
            error: function(xhr, status, error) {
                console.log('Error fetching messages:', error);
            }
        });
    }
    </script>