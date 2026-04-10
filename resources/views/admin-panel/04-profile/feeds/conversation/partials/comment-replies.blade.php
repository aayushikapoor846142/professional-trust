<div class="replies w-100 bg-secondary bg-opacity-10 p-2" id="reply-for-{{ $parent_comment->unique_id }}">
    <div class="reply-comments">
        @foreach($comment_replies as $comment_reply)
            <div class="received-block" id="comment-{{$comment_reply->unique_id}}">
                <input style="display:none" type="checkbox" name="clear_msg[]" value="{{$comment_reply->id}}" class="select-message" id="clear-msg{{$comment_reply->unique_id}}">
                <div class="received-icon">
                    @if($comment_reply->user->profile_image != '')
                    <img src="{{ $comment_reply->user->profile_image ? userDirUrl($comment_reply->user->profile_image, 't') : 'assets/images/default.jpg' }}"
                        alt="Doris">
                    @else
                    <div class="group-icon" data-initial="{{ userInitial($comment_reply->user) }}"></div>
                    @endif
                </div>
                <div class="message-block  ">
                    <div class="textreceive-message-block">
                        <div class="message received">
                            <div class="msg-feed-comments">
                                    <span style="font-size:10px;" id="editedMsg{{$comment_reply->unique_id}}">
                                        @if($comment_reply->edited_at)
                                        edited
                                        @endif
                                    </span>
                                    <p class="chat-message" id="cpMsg{{$comment_reply->unique_id}}">{{$comment_reply->comment}}</p>
                                    @if($comment_reply->media)
                                    <div class="{{(count(explode(',',$comment_reply->media)) > 1)?'files-uploaded':''}}">
                                        @include('admin-panel.04-profile.feeds.conversation.partials.attachments_common', [
                                        'get_attachments' => $comment_reply->media,
                                        'comment_id' => $comment_reply->unique_id
                                        ])
                                    </div>
                                    @endif

                                </div>

                                <!-- reactions dialog -->
                                <span class="chat-time">

                                    <i class="fa-sharp fa-regular fa-clock"></i>
                                    @php
                                    $timezone=getUserTimezone();
                                    $checkTimezone = isValidTimezone($timezone);
                                    @endphp
                                    @if($checkTimezone)
                                    {{ $comment_reply->created_at->timezone($timezone)->format('H:i') }}
                                    @else
                                    {{ date('H:i', strtotime($comment_reply->created_at)) }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="align-items-start chat-dropdown d-flex dropdown gap-1">
                            <button class="btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fa-solid fa-ellipsis"></i>
                            </button>
                            <ul class="dropdown-menu">

                                <li>
                                    <a class="dropdown-item" href="javascript:;" onclick="showFeedReplies('{{ $comment_reply->unique_id }}','show','{{ $parent_comment->unique_id }}')" >Reply </a>
                                </li>

                            </ul>


                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="receiver-name">
                            <p class="">
                                {{ $comment_reply->user->first_name." ".$comment_reply->user->last_name }}
                            </p>
                            <p class="">
                                {{ $comment_reply->user->professionalLicense->title??'' }}
                            </p>
                        </div>
                        <div class="reply-counts">
                            <a onclick="showFeedReplies('{{ $comment_reply->unique_id }}','show')" id="comment-count-{{ $comment_reply->unique_id }}" href="javascript:;">{{ $comment_reply->replyComments->count() }} comment(s)</a>
                        </div> 
                    </div>
                    <div class="parent-replies" style="display:none" id="pr-{{ $comment_reply->unique_id }}"></div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="reply-comment-form">
        <form class="reply-form" id="reply-form-{{ $parent_comment->unique_id }}" action="{{ baseUrl('feeds/save-comment/'.$parent_comment->feed_id) }}">
            @csrf
            <input type="hidden" name="comment_type" value="reply" />
            <input type="hidden" name="parent_comment_id" value="{{ $parent_comment->unique_id }}" />
            <div class="message-input-box">
                <div class="send-message-input">
                    <textarea placeholder="Enter Comment" id="commentBox-{{ $parent_comment->unique_id }}" class="dynamic-textarea"
                        name="comment"></textarea>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <button type="submit" class="CdsTYButton-btn-primary btn-sm">Submit</button>
                <button type="button" onclick="showFeedReplies('{{ $parent_comment->unique_id }}','hide')" class="btn btn-dark btn-sm">Close</button>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function(){
        $("#reply-form-{{ $parent_comment->unique_id }}").submit(function(e){
            e.preventDefault();
            $.ajax({
                type: 'post',
                dataType:"json",
                url: $("#reply-form-{{ $parent_comment->unique_id }}").attr("action"),
                data:$("#reply-form-{{ $parent_comment->unique_id }}").serialize(),
                success: function (data) {
                    $("#commentBox-{{ $parent_comment->unique_id }}").val('');
                    // showReplies('{{ $parent_comment->unique_id }}','show');
                }
            });
        })
    })
</script>