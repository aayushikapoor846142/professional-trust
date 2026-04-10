@if(count($discussion_comments) > 0)
    @foreach($discussion_comments as $discussion_comm)
        @if(!empty($discussion_comm))
            <div class="CdsDiscussionThread-comment" id="comment-{{ $discussion_comm->unique_id }}">

                <!-- Header -->
                <div class="CdsDiscussionThread-comment-header">
                    @php
                        $user = $discussion_comm->user;
                        $avatarInitial = userInitial($user);
                        $avatarStyle = 'background: linear-gradient(135deg, #f59e0b, #ef4444);';
                    @endphp

                    @if($user->profile_image != '')
                        <img class="CdsDiscussionThread-comment-avatar" src="{{ userDirUrl($user->profile_image, 't') }}" alt="">
                    @else
                        <div class="CdsDiscussionThread-comment-avatar" style="{{ $avatarStyle }}">{{ $avatarInitial }}</div>
                    @endif

                    <div class="CdsDiscussionThread-comment-info">
                        <div class="CdsDiscussionThread-comment-author">{{ $user->first_name }} {{ $user->last_name }}</div>
                        <div class="CdsDiscussionThread-comment-meta">
                            <span class="CdsDiscussionThread-comment-badge">{{$discussion_comm->discussion->category->name}}</span>
                            <span>
                                @php
                                    $timezone = getUserTimezone();
                                    $checkTimezone = isValidTimezone($timezone);
                                    $time = $checkTimezone 
                                        ? $discussion_comm->created_at->timezone($timezone)->format('F d, Y H:i') 
                                        : date('F d, Y H:i', strtotime($discussion_comm->created_at));
                                @endphp
                                {{ $time }}
                            </span>

                            @if($discussion_comm->potentialAnswer->where('user_id', auth()->user()->id)->count())
                                <span>You marked as Potential Answer</span>
                            @endif
                            @if($discussion_comm->added_by != auth()->user()->id)
                                @if($discussion_comm->flaggedByUsers->where("user_id", auth()->user()->id)->count() == 0)
                                    <a href="javascript:;" onclick="showPopup('{{ baseUrl('manage-discussion-threads/flag-comment/' . $discussion_comm->unique_id) }}')">
                                        <i class="fa-regular fa-flag"></i> Flag Comment
                                    </a>
                                @else
                                    <a href="javascript:;">
                                        <i class="fa-solid fa-flag text-danger"></i>
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="CdsDiscussionThread-comment-content">
                    {!! $discussion_comm->comments !!}

                    @if($discussion_comm->reply_to != NULL && !empty($discussion_comm->replyTo))
                    <div class="CdsDiscussionThread-comment-content-placeholder" >
                        {!! makeBoldBetweenAsterisks($discussion_comm->replyTo->comments) ?? 'Reply another message' !!}
                    </div>
                    @endif

                    {{-- Attachments --}}
                    @if($discussion_comm->files)
                    <div class="CdsDiscussionThread-comment-attachments">
                        @foreach(explode(',', $discussion_comm->files) as $key => $file)
                            @php
                                $ext = pathinfo($file, PATHINFO_EXTENSION);
                                $imgExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                            @endphp

                            <div class="attachment">
                                @if(in_array($ext, $imgExt))
                                    <img src="{{ discussionCommentDirUrl($file, 's') }}" 
                                         class="img-fluid comment-preview-image" 
                                         alt="Image" 
                                         onclick="openCommentImagePreview(this)" 
                                         data-href="{{ baseUrl('manage-discussion-threads/view-comment-media/'.$discussion_comm->unique_id.'/'.$file) }}"
                                         style="cursor: pointer; border-radius: 8px; transition: transform 0.3s ease;"
                                         onmouseover="this.style.transform='scale(1.05)'"
                                         onmouseout="this.style.transform='scale(1)'">
                                @else
                                    <a href="javascript:;" onclick="showFilePreview('{{ baseUrl('discussion-threads/preview-file?file_name=' . $file . '&index=' . $key) }}')">
                                        @if($ext == 'pdf')
                                            <img src="{{ discussionCommentDirUrl($file, 'r', true) }}" class="pdf-thumbnail" alt="PDF">
                                            <p class="file-name"><img src="{{ url('assets/images/chat-icons/pdf-icon.png') }}"> {{ $file }}</p>
                                        @elseif(in_array($ext, ['doc', 'docx']))
                                            <img src="{{ url('assets/images/chat-icons/doc-icon.png') }}" alt="DOC"> {{ $file }}
                                        @elseif(in_array($ext, ['xls', 'xlsx']))
                                            <img src="{{ url('assets/images/chat-icons/xls-icon.png') }}" alt="XLS"> {{ $file }}
                                        @else
                                            <img src="{{ url('assets/images/chat-icons/file-icon.png') }}" alt="File"> {{ $file }}
                                        @endif
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                <div id="CDSDiscussion-reply-{{ $discussion_comm->unique_id }}"></div>

                <!-- Actions -->
                <div class="CdsDiscussionThread-comment-actions">
                    {{-- Reactions --}}
                    <div class="CdsDiscussionThread-comment-action">
                        <div class="dropdown">
                            <a class="dropdown-toggle" href="javascript:;" data-bs-toggle="dropdown" aria-expanded="false">
                                @if($discussion_comm->commentLikes->where('user_id', auth()->user()->id)->count())
                                    @php 
                                        $comment_like = $discussion_comm->commentLikes->where('user_id', auth()->user()->id)->first();
                                        echo commentEmojis($comment_like->comment_icon)['emoji']." (You)";
                                    @endphp
                                @else
                                    <i class="fa-solid fa-thumbs-up"></i>
                                @endif
                            </a>
                            <ul class="dropdown-menu">
                                @if(isset($comment_like))
                                    <li class="bg-danger bg-opacity-25">
                                        <a class="dropdown-item" href="javascript:;" onclick="unlikeComment('{{ $comment_like->id }}','{{ $discussion_comm->id }}','{{ $discussion_comm->discussion_boards_id }}')">
                                            {!! commentEmojis($comment_like->comment_icon)['emoji'] !!} - Remove
                                        </a>
                                    </li>
                                @endif
                                @foreach(commentEmojis() as $emoji)
                                    <li>
                                        <a class="dropdown-item" href="javascript:;" onclick="likeComment('{{ $emoji['keyword'] }}','{{ $discussion_comm->id }}','{{ $discussion_comm->discussion_boards_id }}')">
                                            {!! $emoji['emoji'] !!} - {{ $emoji['name'] }}
                                            @if($discussion_comm->commentLikes->where('comment_icon', $emoji['keyword'])->count() > 0)
                                                ({{ $discussion_comm->commentLikes->where('comment_icon', $emoji['keyword'])->count() }})
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    {{-- Reply --}}
                    <button class="CdsDiscussionThread-comment-action" onclick="toggleReplyForm('{{ $discussion_comm->unique_id }}','show')">
                        Reply
                    </button>

                    {{-- Edit --}}
                    @if($discussion_comm->added_by == auth()->user()->id)
                        @if($discussion_comm->comments != NULL && $discussion_comm->created_at >= now()->subMinutes(30))
                            <button class="CdsDiscussionThread-comment-action" onclick="editDiscussionComment('{{ $discussion_comm->unique_id }}')">
                                Edit
                            </button>
                        @endif

                        {{-- Delete --}}
                        <button class="CdsDiscussionThread-comment-action" onclick="deleteDiscussionComment('{{ $discussion_comm->id }}','{{ $discussion_comm->unique_id }}')">
                            Delete
                        </button>
                    @endif

                    {{-- Mark/Unmark as Answer --}}
                    @if($discussion_comm->discussion->added_by == auth()->user()->id && $discussion_comm->added_by != auth()->user()->id)
                        @if($discussion_comm->mark_as_answer == 1)
                            <button class="CdsDiscussionThread-comment-action">Marked as Answer ✓</button>
                            <button class="CdsDiscussionThread-comment-action" onclick="removeCommentAsAnswer('{{ $discussion_comm->unique_id }}')">Remove as Answer</button>
                        @else
                            <button class="CdsDiscussionThread-comment-action" onclick="markCommentAsAnswer('{{ $discussion_comm->unique_id }}')">Mark as Answer</button>
                        @endif
                    @endif

                    {{-- Mark/Unmark as Potential Answer --}}
                    @if($discussion_comm->added_by != auth()->user()->id)
                        @if($discussion_comm->potentialAnswer->where('user_id', auth()->user()->id)->count() == 0)
                            <button class="CdsDiscussionThread-comment-action" onclick="markPotentialAnswer('{{ $discussion_comm->unique_id }}')">Mark as Potential Answer</button>
                        @else
                            <button class="CdsDiscussionThread-comment-action" onclick="removePotentialAnswer('{{ $discussion_comm->unique_id }}')">Remove as Potential Answer</button>
                        @endif
                    @endif
                </div>
            </div>
        @endif
    @endforeach
@else
    <div class="no-comments">No comments found.</div>
@endif
