@if($comments && $comments->count() > 0)
    <div class="comments-header d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            <i class="fa-light fa-comments"></i> Comments
        </h5>
        <span class="text-muted">Older Comments</span>
    </div>
    
    <!-- First: Show all comments as separate items (main section) -->
    @foreach($comments as $comment)
        @if($comment->parent_id == '')
        <div class="main-comment mb-4" data-comment-id="{{ $comment->id }}">
            <div class="comment-card">
                <div class="comment-header d-flex align-items-start justify-content-between mb-3">
                    <div class="user-info d-flex align-items-center">
                        <div class="user-avatar me-3">
                            @if($comment->user && $comment->user->profile_photo_url)
                                <img src="{{ $comment->user->profile_photo_url }}" alt="{{ $comment->user->name }}" class="avatar-img">
                            @else
                                <div class="avatar-placeholder">
                                    {{ strtoupper(substr($comment->user->name ?? 'U', 0, 2)) }}
                                </div>
                            @endif
                        </div>
                        <div class="user-details">
                            <h6 class="user-name mb-1">{{ $comment->user->first_name .''.$comment->user->last_name ?? 'Unknown User' }}</h6>
                            <div class="user-meta d-flex align-items-center gap-2">
                                <span class="comment-time">{{ $comment->created_at->format('F d, Y H:i') }}</span>
                                @if($comment->parent_id)
                                    <span class="reply-indicator">↳ Reply to Comment</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="comment-actions">
                        @if(!$comment->parent_id)
                            <button class="btn btn-sm btn-outline-primary reply-comment-btn" data-comment-id="{{ $comment->id }}">
                                Reply
                            </button>
                        @endif
                        @if(auth()->id() == $comment->user_id)
                            <button class="btn btn-sm btn-outline-secondary edit-comment-btn" data-comment-id="{{ $comment->id }}">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-comment-btn" data-comment-id="{{ $comment->id }}">
                                Delete
                            </button>
                        @endif
                    </div>
                </div>
                
                <div class="comment-text mb-3">
                    <p class="mb-0">{{ $comment->comment }}</p>
                </div>

                <!-- Reply Input Section (only for main comments) -->
                @if(!$comment->parent_id)
                    <div class="reply-input-section" id="replyInput{{ $comment->id }}" style="display: none;">
                        <form class="replyCommentForm" data-comment-id="{{ $comment->id }}">
                            <div class="form-group">
                                <textarea class="form-control" name="reply" rows="2" placeholder="Write a reply..." required></textarea>
                            </div>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fa-light fa-reply"></i> Post Reply
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm cancel-reply-btn" data-comment-id="{{ $comment->id }}">
                                    <i class="fa-light fa-times"></i> Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Edit Comment Form -->
                <div class="edit-form mt-3" id="editForm{{ $comment->id }}" style="display: none;">
                    <form class="editCommentForm" data-comment-id="{{ $comment->id }}">
                        <div class="form-group">
                            <textarea class="form-control" name="comment" rows="3" placeholder="Write your comment here..." required>{{ $comment->comment }}</textarea>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fa-light fa-save"></i> Update
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm cancel-edit-btn" data-comment-id="{{ $comment->id }}">
                                <i class="fa-light fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @else
         <div class="main-comment mb-4" data-comment-id="{{ $comment->id }}">
            <div class="comment-card">
                <div class="comment-header d-flex align-items-start justify-content-between mb-3">
                    <div class="user-info d-flex align-items-center">
                        <div class="user-avatar me-3">
                            @if($comment->user && $comment->user->profile_photo_url)
                                <img src="{{ $comment->user->profile_photo_url }}" alt="{{ $comment->user->name }}" class="avatar-img">
                            @else
                                <div class="avatar-placeholder">
                                    {{ strtoupper(substr($comment->user->name ?? 'U', 0, 2)) }}
                                </div>
                            @endif
                        </div>
                        <div class="user-details">
                            <h6 class="user-name mb-1">{{ $comment->user->first_name .''.$comment->user->last_name ?? 'Unknown User' }}</h6>
                            <div class="user-meta d-flex align-items-center gap-2">
                                <span class="comment-time">{{ $comment->created_at->format('F d, Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="comment-actions">
                        @if(!$comment->parent_id)
                            <button class="btn btn-sm btn-outline-primary reply-comment-btn" data-comment-id="{{ $comment->id }}">
                                Reply
                            </button>
                        @endif
                        @if(auth()->id() == $comment->user_id)
                            <button class="btn btn-sm btn-outline-secondary edit-comment-btn" data-comment-id="{{ $comment->id }}">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-comment-btn" data-comment-id="{{ $comment->id }}">
                                Delete
                            </button>
                        @endif
                    </div>
                </div>
                
                <div class="comment-text mb-3">
                    <p class="mb-0">{{ $comment->comment }}</p>
                    <div class="reply-context" style="background-color: #f8f9fa; padding: 8px 12px; border-radius: 6px; margin-top: 8px; border-left: 3px solid #007bff;">
                        <b style="color: #6c757d;">Replied to you:</b>
                        <span style="color: #495057;">{{$comment->parent->comment}}</span>
                    </div>
                </div>

                <!-- Reply Input Section (only for main comments) -->
                @if(!$comment->parent_id)
                    <div class="reply-input-section" id="replyInput{{ $comment->id }}" style="display: none;">
                        <form class="replyCommentForm" data-comment-id="{{ $comment->id }}">
                            <div class="form-group">
                                <textarea class="form-control" name="reply" rows="2" placeholder="Write a reply..." required></textarea>
                            </div>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fa-light fa-reply"></i> Post Reply
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm cancel-reply-btn" data-comment-id="{{ $comment->id }}">
                                    <i class="fa-light fa-times"></i> Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Edit Comment Form -->
                <div class="edit-form mt-3" id="editForm{{ $comment->id }}" style="display: none;">
                    <form class="editCommentForm" data-comment-id="{{ $comment->id }}">
                        <div class="form-group">
                            <textarea class="form-control" name="comment" rows="3" placeholder="Write your comment here..." required>{{ $comment->comment }}</textarea>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fa-light fa-save"></i> Update
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm cancel-edit-btn" data-comment-id="{{ $comment->id }}">
                                <i class="fa-light fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endforeach

@else
    <div class="text-center text-muted py-5">
        <i class="fa-light fa-comments fa-4x mb-3 text-light"></i>
        <h5 class="mb-2">No comments yet</h5>
        <p class="mb-0">Be the first to start the discussion!</p>
    </div>
@endif
