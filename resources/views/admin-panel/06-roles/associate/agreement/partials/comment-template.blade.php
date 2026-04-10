<!-- Main Comment Template -->
<div class="comment-item mb-3" data-comment-id="{{ $comment->id ?? '{{COMMENT_ID}}' }}">
    <div class="card comment-card">
        <div class="card-body">
            <!-- User Info Section -->
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex align-items-center">
                    <div class="avatar me-3">
                        @if(isset($comment->user) && $comment->user->profile_image)
                            <img src="{{ asset('storage/' . $comment->user->profile_image) }}" alt="{{ $comment->user->first_name ?? '{{USER_NAME}}' }}" class="rounded-circle" width="40" height="40">
                        @else
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px; font-size: 14px;">
                                {{ isset($comment->user) ? strtoupper(substr($comment->user->first_name, 0, 1)) . strtoupper(substr($comment->user->last_name, 0, 1)) : '{{USER_INITIALS}}' }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">{{ $comment->user->first_name ?? '{{USER_FIRST_NAME}}' }} {{ $comment->user->last_name ?? '{{USER_LAST_NAME}}' }}</h6>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light-purple">{{ $comment->category ?? 'General' }}</span>
                            <small class="text-muted">
                                <i class="fa-light fa-clock"></i> {{ $comment->created_at ? $comment->created_at->format('F d, Y H:i') : '{{COMMENT_TIME}}' }}
                            </small>
                        </div>
                    </div>
                </div>
                <div class="comment-actions">
                    <div class="dropdown d-inline-block me-2">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-light fa-thumbs-up"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fa-light fa-thumbs-up"></i> Like</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fa-light fa-heart"></i> Love</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fa-light fa-laugh"></i> Funny</a></li>
                        </ul>
                    </div>
                    <button class="btn btn-sm btn-outline-primary reply-comment-btn" data-comment-id="{{ $comment->id ?? '{{COMMENT_ID}}' }}" title="Reply to Comment">
                        Reply
                    </button>
                    @if(auth()->check() && auth()->id() == ($comment->user_id ?? '{{USER_ID}}'))
                        <button class="btn btn-sm btn-outline-secondary edit-comment-btn" data-comment-id="{{ $comment->id ?? '{{COMMENT_ID}}' }}" title="Edit Comment">
                            Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-comment-btn" data-comment-id="{{ $comment->id ?? '{{COMMENT_ID}}' }}" title="Delete Comment">
                            Delete
                        </button>
                    @else
                        <button class="btn btn-sm btn-outline-warning mark-answer-btn" data-comment-id="{{ $comment->id ?? '{{COMMENT_ID}}' }}" title="Mark as Potential Answer">
                            Mark as Potential Answer
                        </button>
                        <button class="btn btn-sm btn-outline-info flag-comment-btn" data-comment-id="{{ $comment->id ?? '{{COMMENT_ID}}' }}" title="Flag Comment">
                            Flag Comment
                        </button>
                    @endif
                </div>
            </div>
            
            <!-- Comment Text Section -->
            <div class="comment-text-section mt-2">
                <p class="comment-text mb-0">{{ $comment->comment ?? '{{COMMENT_TEXT}}' }}</p>
            </div>

            <!-- Edit Comment Form (Hidden by default) -->
            <div class="edit-form mt-3" id="editForm{{ $comment->id ?? '{{COMMENT_ID}}' }}" style="display: none;">
                <form class="editCommentForm" data-comment-id="{{ $comment->id ?? '{{COMMENT_ID}}' }}">
                    <div class="form-group">
                        <textarea class="form-control" name="comment" rows="2" placeholder="Write your comment here..." required>{{ $comment->comment ?? '{{COMMENT_TEXT}}' }}</textarea>
                    </div>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        <button type="button" class="btn btn-sm btn-secondary cancel-edit-btn" data-comment-id="{{ $comment->id ?? '{{COMMENT_ID}}' }}">Cancel</button>
                    </div>
                </form>
            </div>

            <!-- Reply Input Section (Hidden by default) -->
            <div class="reply-input-section mt-3" id="replyInput{{ $comment->id ?? '{{COMMENT_ID}}' }}" style="display: none;">
                <form class="replyCommentForm" data-comment-id="{{ $comment->id ?? '{{COMMENT_ID}}' }}">
                    <div class="form-group">
                        <textarea class="form-control" name="reply" rows="2" placeholder="i am professional" required></textarea>
                    </div>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fa-light fa-reply"></i> Post Reply
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary cancel-reply-btn" data-comment-id="{{ $comment->id ?? '{{COMMENT_ID}}' }}">
                            <i class="fa-light fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Replies section -->
            <div class="replies-section mt-3" id="replies{{ $comment->id ?? '{{COMMENT_ID}}' }}">
                @if(isset($comment->replies) && $comment->replies->count() > 0)
                    @foreach($comment->replies as $reply)
                        @include('admin-panel.24-others.professional-agreements.comments.comment-item', ['comment' => $reply, 'level' => ($level ?? 0) + 1])
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Reply Template -->
<div class="reply-item mb-2" data-reply-id="{{ $comment->id ?? '{{REPLY_ID}}' }}">
    <div class="card comment-card" style="border-left: 3px solid #007bff;">
        <div class="card-body py-2">
            <div class="d-flex align-items-start">
                <div class="avatar me-2">
                    @if(isset($comment->user) && $comment->user->profile_image)
                        <img src="{{ asset('storage/' . $comment->user->profile_image) }}" alt="{{ $comment->user->first_name ?? '{{USER_NAME}}' }}" class="rounded-circle" width="35" height="35">
                    @else
                        <div class="bg-info rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 35px; height: 35px; font-size: 12px;">
                            {{ isset($comment->user) ? strtoupper(substr($comment->user->first_name, 0, 1)) . strtoupper(substr($comment->user->last_name, 0, 1)) : '{{USER_INITIALS}}' }}
                        </div>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong class="text-info">{{ $comment->user->first_name ?? '{{USER_FIRST_NAME}}' }} {{ $comment->user->last_name ?? '{{USER_LAST_NAME}}' }}</strong>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light-purple">{{ $comment->category ?? 'General' }}</span>
                                <small class="text-muted">
                                    <i class="fa-light fa-clock"></i> {{ $comment->created_at ? $comment->created_at->format('F d, Y H:i') : '{{COMMENT_TIME}}' }}
                                </small>
                            </div>
                        </div>
                        <div class="comment-actions">
                            @if(auth()->check() && auth()->id() == ($comment->user_id ?? '{{USER_ID}}'))
                                <button class="btn btn-sm btn-outline-secondary edit-comment-btn" data-comment-id="{{ $comment->id ?? '{{REPLY_ID}}' }}">
                                    Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-comment-btn" data-comment-id="{{ $comment->id ?? '{{REPLY_ID}}' }}">
                                    Delete
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="comment-text-section mt-1">
                        <p class="comment-text mb-0">{{ $comment->comment ?? '{{COMMENT_TEXT}}' }}</p>
                    </div>
                    <div class="edit-form mt-2" id="editForm{{ $comment->id ?? '{{REPLY_ID}}' }}" style="display: none;">
                        <form class="editCommentForm" data-comment-id="{{ $comment->id ?? '{{REPLY_ID}}' }}">
                            <div class="form-group">
                                <textarea class="form-control" name="comment" rows="2" placeholder="Write your reply here..." required>{{ $comment->comment ?? '{{COMMENT_TEXT}}' }}</textarea>
                            </div>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                <button type="button" class="btn btn-sm btn-secondary cancel-edit-btn" data-comment-id="{{ $comment->id ?? '{{REPLY_ID}}' }}">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
