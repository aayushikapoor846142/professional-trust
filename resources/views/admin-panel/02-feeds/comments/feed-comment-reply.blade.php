<div class="CDSFeed-replies">
    @php
    $last_reply_id = 0;
    $results = $replyComments;
    @endphp
    @foreach($replyComments->take(2) as $replyComment)
    @php
    $last_reply_id = $replyComment->id;
    @endphp
    <div class="CDSFeed-reply" id="comment-{{ $replyComment->unique_id }}">
        <div class="CDSFeed-comment">
            <div class="CDSFeed-comment-avatar">
                {!! getProfileImage($replyComment->user->unique_id) !!}
            </div>
            <div class="CDSFeed-comment-content">
                <div class="CDSFeed-comment-header">
                    <span class="CDSFeed-comment-author">{{ $replyComment->user->first_name." ".$replyComment->user->last_name }}</span>
                    <div>
                        @if(auth()->user()->id == $replyComment->added_by)
                            <span class="CDSFeed-comment-action">
                                <a href="javascript:;" onclick="showPopup('{{ baseUrl('my-feeds/edit-comment/'.$replyComment->unique_id) }}')"><i class="fa-regular fa-pen"></i> Edit</a>
                            </span>
                            <span class="CDSFeed-comment-action">
                                <a href="javascript:;" href="javascript:;" onclick="deleteComment(this)" data-href="{{ baseUrl('my-feeds/delete-comment/'.$replyComment->unique_id) }}" class="text-danger"><i class="fa-regular fa-trash"></i> Delete</a>
                            </span>
                        @endif
                        <span class="CDSFeed-comment-time">{{ $replyComment->created_at->diffForHumans() }}</span>
                    </div>
                    
                </div>
                <div class="CDSFeed-comment-text">
                    <p>
                        {!! $replyComment->comment !!}
                    </p>
                    @if($replyComment->edited_at != '')
                    <div class="CDSFeed-comment-edited">edited..</div>
                    @endif
                </div>
                <div class="CDSFeed-comment-footer">
                    <div class="CDSFeed-comment-actions">
                        <span class="CDSFeed-comment-action CDSFeed-like-action">
                            @if($replyComment->commentLiked->where("user_id",auth()->user()->id)->count() > 0)
                            <a href="javascript:;" class="comment-unlike liked" data-comment-id="{{ $replyComment->unique_id }}" data-href="{{ baseUrl('my-feeds/comment-unlike/'.$replyComment->unique_id) }}">
                                <i class="fa-solid fa-thumbs-up"></i> Liked ({{ $replyComment->commentLiked->count() }})
                            </a>
                            @else
                            <a href="javascript:;" class="comment-like" data-comment-id="{{ $replyComment->unique_id }}" data-href="{{ baseUrl('my-feeds/comment-like/'.$replyComment->unique_id) }}">
                                <i class="fa-regular fa-thumbs-up"></i> Like ({{ $replyComment->commentLiked->count() }})
                            </a>
                            @endif
                        </span>
                        <span class="CDSFeed-comment-action CDSFeed-reply-action"
                            onclick="toggleReplyForm('{{ $replyComment->unique_id }}','show')">
                            <i class="fa-regular fa-comment"></i> Reply  <span id="reply-count-{{ $replyComment->unique_id }}">({{ $replyComment->replyComments->count() }})</span>
                        </span>
                    </div>
                    @if($replyComment->added_by != auth()->user()->id)
                    <div class="CDSFeed-comment-action-right">
                        <span class="CDSFeed-comment-action">
                            @if($replyComment->flaggedByUsers->where("user_id",auth()->user()->id)->count() == 0)
                                <a href="javascript:;" onclick="showPopup('{{ baseUrl('my-feeds/flag-comment/' . $replyComment->unique_id) }}')">
                                    <i class="fa-regular fa-flag"></i> Flag Comment
                                </a>
                            @else
                            <a href="javascript:;">
                                <i class="fa-solid fa-flag text-danger"></i>
                            </a>
                            @endif
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div id="CDSFeed-reply-{{ $replyComment->unique_id }}"></div>
        <div class="CDSFeed-comment-reply-wrapper" id="CDSFeed-comment-{{ $replyComment->unique_id }}">
            @if($replyComment->replyComments->count() > 0)
            @include("admin-panel.02-feeds.comments.feed-comment-reply",['replyComments'=>$replyComment->replyComments,'parent_id'=>$replyComment->id])
            @endif
        </div>
    </div>
    @endforeach
    @if($results->where('id','>',$last_reply_id)->where('reply_to',$parent_id)->count() > 0)
    <a href="javascript:void(0);" class=" CDS-reply-load-more" onclick="loadMoreReply('{{ $parent_id }}','{{ $last_reply_id }}')" class="CDS-Feed-reply-more" data-parent-id="{{ $parent_id ?? '' }}"
        data-last-comment-id="{{ $last_reply_id }}">
        Load More ({{ checkMoreComment($parent_id,$last_reply_id) }})<i class="fa fa-chevron-down"></i>
    </a>
    @endif
</div>