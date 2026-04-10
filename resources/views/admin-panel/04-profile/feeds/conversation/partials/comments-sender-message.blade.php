@if($feed_comm!=NULL)
<div class="sent-block" id="comment-{{ $feed_comm->unique_id }}">
    <input style="display:none" type="checkbox" name="clear_msg[]" value="{{$feed_comm->id}}" class="select-message"
        id="clear-msg{{$feed_comm->unique_id}}">
    <div class="message-block">
        <div class="text-message-block">

            <div class="message sent">
                <div class="feed-contents">
                    <div class="w-100">
                        @if($feed_comm->reply_to!=NULL && !empty($feed_comm->replyTo))
                        <div class="reply-to" onclick="scrollToMessage('{{$feed_comm->replyTo->unique_id}}')">
                            @if($feed_comm->replyTo->comment!=NULL)
                            <span class="username">{!! makeBoldBetweenAsterisks($feed_comm->replyTo->comment) !!}</span>
                            @endif
                            @if($feed_comm->replyTo->media)
                            <div class="{{(count(explode(',',$feed_comm->replyTo->media)) > 1)?'files-uploaded':''}}">
                                @include('admin-panel.04-profile.feeds.conversation.partials.attachments_common', [
                                    'get_attachments' => $feed_comm->replyTo->media,
                                    'comment_id' => $feed_comm->replyTo->unique_id
                                    ])
                            </div>
                            @endif

                            <p class="quoted-message">Reply quoted message</p>
                        </div>
                        @endif

                        @if($feed_comm->deleted_at!=NULL )
                        <p class="deleted-message chat-message">This message was deleted.</p>
                        @else
                        <span style="font-size:10px;" id="editedMsg{{$feed_comm->unique_id}}">
                            @if($feed_comm->edited_at)
                            edited
                            @endif
                        </span>
                        <p class="chat-message" id="cpMsg{{$feed_comm->unique_id}}">{{ $feed_comm->comment}}</p>
                        @if($feed_comm->media)
                        <div class="{{(count(explode(',',$feed_comm->media)) > 1)?'files-uploaded':''}}">
                                @include('admin-panel.04-profile.feeds.conversation.partials.attachments_common', [
                                    'get_attachments' => $feed_comm->media,
                                    'comment_id' => $feed_comm->unique_id
                                ])    
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
                        {{ $feed_comm->created_at->timezone($timezone)->format('H:i') }}
                        @else
                        {{ date('H:i', strtotime($feed_comm->created_at)) }}
                        @endif

                    </span>
                </div>


            </div>
            <div class="dropdown chat-dropdown">
                <button class="btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="fa-solid fa-ellipsis"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="javascript:;"
                            onclick="showFeedReplies('{{ $feed_comm->unique_id }}','show')">Reply </a>
                    </li>
                    @if($feed_comm->comment!=NULL && $feed_comm->created_at >= now()->subMinutes(30))
                    <li>
                        <a class="dropdown-item" href="javascript:;"
                            onclick="editFeedComment('{{ $feed_comm->id }}','{{ $feed_comm->unique_id }}')">Edit</a>
                    </li>
                    @endif
                    <li>
                        <a class="dropdown-item" href="javascript:;"
                            onclick="deleteComment('{{ $feed_comm->id }}','{{ $feed_comm->unique_id }}')">Delete</a>
                    </li>

                </ul>
            </div>


        </div>
        <div class="sender-name">
            {{-- <p class="">{{ auth()->user()->first_name." ".auth()->user()->last_name }}</p>
            --}}
        </div>
        <div class="reply-counts">
            <a onclick="showFeedReplies('{{ $feed_comm->unique_id }}','show')" id="comment-count-{{ $feed_comm->unique_id }}" href="javascript:;">{{ $feed_comm->replyComments->count() }} comment(s)</a>
        </div> 
        <div class="parent-replies" style="display:none" id="pr-{{ $feed_comm->unique_id }}"></div>
    </div>

</div>
@endif