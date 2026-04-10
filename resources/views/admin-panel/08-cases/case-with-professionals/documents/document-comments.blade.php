@foreach($comments as $comment)
<div class="CdsDocumentPreview-comment" id="case-document-comment-{{ $comment->unique_id }}">
    <div class="CdsDocumentPreview-commentAvatar" style="background-color: ${avatarColor}">
        {!! getProfileImage($comment->user->unique_id) !!}
    </div>
    <div class="CdsDocumentPreview-commentContent">
        <div class="CdsDocumentPreview-commentHeader">
            <span class="CdsDocumentPreview-commentAuthor">{{ $comment->user->first_name.' '.$comment->user->last_name }}</span>
            <span class="CdsDocumentPreview-commentDate">{{ $comment->created_at->diffForHumans()}}</span>
            @if($comment->commentRead)
                @if($comment->commentRead->user_id == auth()->user()->id)
                    @if($comment->commentRead->is_read == 0)
                        <i class="fa-sharp-duotone fa-solid fa-check"></i>
                    @else
                        <i class="fa-sharp fa-solid fa-check-double"></i>
                    @endif
                @endif
            @endif
            <div class="CDSDocument-dropdown">
                <button class="CDSDocument-dropdown-toggle" onclick="cdsDocumentToggleDropdown({{ $comment->id }})">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
                <ul class="CDSDocument-dropdown-menu" id="dropdown-{{ $comment->id }}">
                    @if($comment->added_by == auth()->user()->id)
                        <li>
                            <a class="CDSDocument-dropdown-item" href="javascript:;" data-id="{{ $comment->unique_id }}" onclick="confirmCommentDelete(this)" data-href="{{ baseUrl('case-with-professionals/case-document/comment-delete/' . $comment->unique_id.'/'.$comment->caseDocument->unique_id) }}">
                                <i class="fa-regular fa-trash"></i> Delete
                            </a>
                        </li>
                    @endif
                    <li>
                        <a class="CDSDocument-dropdown-item" href="javascript:;" data-id="{{ $comment->unique_id }}" onclick="replyComment(this)" >
                            <i class="fa-regular fa-reply"></i> Reply
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="CdsDocumentPreview-commentArea">
            <!-- <div class="CdsDocumentPreview-commentText">
                @if ($comment->commentReply)
                    <b>Reply:{{$comment->commentReply->message}}</b>
                    {{ $comment->message}}
                @else
                    {{ $comment->message}}
                @endif
            </div> -->

            @if ($comment->commentReply)
    <div class="CdsDocumentPreview-comment-quoted-reply">
        @if ($comment->commentReply->message)
            <div class="CdsDocumentPreview-comment-quoted-message">
                {{ $comment->commentReply->message }}
            </div>
        @elseif ($comment->commentReply->attachments)
            <div class="CdsDocumentPreview-comment-quoted-message">
                📎 Attachment: 
                <a href="{{url('download-media-file?dir='.caseDocumentCommentDir($comment->caseDocument->unique_id).'&file_name='.comment->commentReply->attachments)}}"  download>
                {{ $comment->commentReply->attachments }}
                </a>
            </div>
        @endif
        <div class="CdsDocumentPreview-comment-quoted-label">Reply quoted message</div>
    </div>
@endif

@if($comment->message)
    <div class="CdsDocumentPreview-commentText">
        {{ $comment->message }}
    </div>
@endif

@if($comment->attachments)
    <div class="CdsDocumentPreview-commentAttachment">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
        </svg>
        <div class="CdsDocumentPreview-attachmentInfo">
          
            <div class="CdsDocumentPreview-attachmentFileName">
                <a href="{{url('download-media-file?dir='.caseDocumentCommentDir($comment->caseDocument->unique_id).'&file_name='.$comment->attachments)}}"  download>
                {{ $comment->attachments }}
                </a>
            </div>
        </div>
    </div>
@endif

            <!-- @if($comment->message != "")
                @if ($comment->commentReply)
                    <div class="CdsDocumentPreview-comment-quoted-reply">
                        <div class="CdsDocumentPreview-comment-quoted-message">{{ $comment->commentReply->message }}</div>
                        <div class="CdsDocumentPreview-comment-quoted-label">Reply quoted message</div>
                    </div>
                @endif
                <div class="CdsDocumentPreview-commentText">
                    {{ $comment->message }}
                </div>
            @endif
           
            @if($comment->attachments)
                 @if ($comment->commentReply)
                    <div class="CdsDocumentPreview-comment-quoted-reply">
                        <div class="CdsDocumentPreview-comment-quoted-message">{{ $comment->commentReply->message }}</div>
                        <div class="CdsDocumentPreview-comment-quoted-label">Reply quoted messageasasasasasasasasasas</div>
                    </div>
                @endif
                <div class="CdsDocumentPreview-commentAttachment" onclick="cdsDocumentDownloadAttachment()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <div class="CdsDocumentPreview-attachmentInfo">
                        <div class="CdsDocumentPreview-attachmentFileName">{{ $comment->attachments }}</div>
                    </div>
                </div>
            @endif -->
        </div>
    </div>
</div>


@endforeach
