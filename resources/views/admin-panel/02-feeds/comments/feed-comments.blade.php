<div class="CDSFeed-comment-list">
    @foreach($comments as $comment)
    <!-- Parent Comment 1 -->
    <div class="CDSFeed-comment-block" id="comment-{{ $comment->unique_id }}">
        <div class="CDSFeed-comment">
            <div class="CDSFeed-comment-avatar">
                {!! getProfileImage($comment->user->unique_id) !!}
            </div>
            <div class="CDSFeed-comment-content">
                <div class="CDSFeed-comment-header">
                    <span class="CDSFeed-comment-author">{{ $comment->user->first_name." ".$comment->user->last_name }}</span>
                    <div>
                        @if(auth()->user()->id == $comment->added_by)
                            <span class="CDSFeed-comment-action">
                                <a href="javascript:;" onclick="showPopup('{{ baseUrl('my-feeds/edit-comment/'.$comment->unique_id) }}')"><i class="fa-regular fa-pen"></i> Edit</a>
                            </span>
                            <span class="CDSFeed-comment-action">
                                <a href="javascript:;" href="javascript:;" onclick="deleteComment(this)" data-comment-id="{{ $comment->unique_id }}" data-href="{{ baseUrl('my-feeds/delete-comment/'.$comment->unique_id) }}" class="text-danger"><i class="fa-regular fa-trash"></i> Delete</a>
                            </span>
                        @endif
                        <span class="CDSFeed-comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="CDSFeed-comment-text">
                    @if($comment->comment != '')
                    <p>
                        {!! $comment->comment !!}
                    </p>
                    @else
                        @foreach(explode(',',$comment->media) as $media)
                        <div class="CDSFeed-file-item" onclick="cdsFeedOpenPreview(this)" data-href="{{ baseUrl('my-feeds/view-comment-media/'.$comment->unique_id.'/'.$media) }}">
                            <div class="CDSFeed-file-preview">
                                <img src="{{commentDirUrl(trim($media), 't')}}">
                            </div>
                            <div class="CDSFeed-file-info">
                                <div class="CDSFeed-file-name">{{$media}}</div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                    @if($comment->edited_at != '')
                    <div class="CDSFeed-comment-edited">edited..</div>
                    @endif
                </div>
                <div class="CDSFeed-comment-footer">
                    <div class="CDSFeed-comment-actions">
                        <span class="CDSFeed-comment-action CDSFeed-like-action">
                            @if($comment->commentLiked->where("user_id",auth()->user()->id)->count() > 0)
                            <a href="javascript:;" class="comment-unlike liked"
                                data-comment-id="{{ $comment->unique_id }}"
                                data-href="{{ baseUrl('my-feeds/comment-unlike/'.$comment->unique_id) }}">
                                <i class="fa-solid fa-thumbs-up"></i> Liked ({{ $comment->commentLiked->count() }})
                            </a>
                            @else
                            <a href="javascript:;" class="comment-like" data-comment-id="{{ $comment->unique_id }}"
                                data-href="{{ baseUrl('my-feeds/comment-like/'.$comment->unique_id) }}">
                                <i class="fa-regular fa-thumbs-up"></i> Like ({{ $comment->commentLiked->count() }})
                            </a>
                            @endif

                        </span>
                        <span class="CDSFeed-comment-action CDSFeed-reply-action"
                            onclick="toggleReplyForm('{{ $comment->unique_id }}','show')">
                            <i class="fa-regular fa-comment"></i> Reply <span
                                id="reply-count-{{ $comment->unique_id }}">({{ $comment->replyComments->count() }})</span>
                        </span>
                    </div>
                    @if($comment->added_by != auth()->user()->id)
                    <div class="CDSFeed-comment-action-right">
                        <span class="CDSFeed-comment-action">
                            @if($comment->flaggedByUsers->where("user_id",auth()->user()->id)->count() == 0)
                            <a href="javascript:;"
                                onclick="showPopup('{{ baseUrl('my-feeds/flag-comment/' . $comment->unique_id) }}')">
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
        <div id="CDSFeed-reply-{{ $comment->unique_id }}"></div>
        <div class="CDSFeed-comment-reply-wrapper" id="CDSFeed-comment-{{ $comment->unique_id }}">
            @if($comment->replyComments->count() > 0)
            @include("admin-panel.02-feeds.comments.feed-comment-reply",['replyComments'=>$comment->replyComments,'parent_id'=>$comment->id])@endif
        </div>
    </div>
    @endforeach
</div>
