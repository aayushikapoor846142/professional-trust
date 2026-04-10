<div class="comment-item mb-3" data-comment-id="{{ $comment->id }}" style="margin-left: {{ $level * 30 }}px;">
    <div class="card comment-card">
        <div class="card-body">
            <!-- User Info Section -->
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex align-items-center">
                    <div class="avatar me-3">
                        @if($comment->user->profile_image)
                            <img src="{{ asset('storage/' . $comment->user->profile_image) }}" alt="Profile" class="rounded-circle" width="40" height="40">
                        @else
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px; font-size: 14px;">
                                {{ strtoupper(substr($comment->user->first_name, 0, 1)) }}{{ strtoupper(substr($comment->user->last_name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">{{ $comment->user->first_name }} {{ $comment->user->last_name }}</h6>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light-purple">{{ $comment->category ?? 'General' }}</span>
                        <small class="text-muted">
                                <i class="fa-light fa-clock"></i> {{ $comment->created_at->format('F d, Y H:i') }}
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
                    <button class="btn btn-sm btn-outline-primary reply-comment-btn" data-comment-id="{{ $comment->id }}" title="Reply to Comment">
                        Reply
                    </button>
                    @if(auth()->id() == $comment->user_id)
                        <button class="btn btn-sm btn-outline-secondary edit-comment-btn" data-comment-id="{{ $comment->id }}" title="Edit Comment">
                            Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-comment-btn" data-comment-id="{{ $comment->id }}" title="Delete Comment">
                            Delete
                        </button>
                    @else
                        <button class="btn btn-sm btn-outline-warning mark-answer-btn" data-comment-id="{{ $comment->id }}" title="Mark as Potential Answer">
                            Mark as Potential Answer
                        </button>
                        <button class="btn btn-sm btn-outline-info flag-comment-btn" data-comment-id="{{ $comment->id }}" title="Flag Comment">
                            Flag Comment
                        </button>
                    @endif
                </div>
            </div>
            
            <!-- Comment Text Section -->
            <div class="comment-text-section">
                <p class="comment-text mb-0">{{ $comment->comment }}</p>
            </div>

            <!-- Edit Comment Form (Hidden by default) -->
            <div class="edit-comment-form mt-3" id="editForm{{ $comment->id }}" style="display: none;">
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

            <!-- Reply Input Section (Hidden by default) -->
            <div class="reply-input-section mt-3" id="replyInput{{ $comment->id }}" style="display: none;">
                <form class="replyCommentForm" data-comment-id="{{ $comment->id }}">
                    <div class="form-group">
                        <textarea class="form-control" name="reply" rows="2" placeholder="i am professional" required></textarea>
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

            <!-- Replies section -->
                @if($comment->replies && $comment->replies->count() > 0)
                <div class="replies-to-comment">
                    <h6 class="reply-header">
                        <i class="fa-light fa-reply"></i> Replies ({{ $comment->replies->count() }})
                    </h6>
                    @foreach($comment->replies as $reply)
                        <div class="reply-item" data-reply-id="{{ $reply->id }}">
                            <div class="card reply-card">
                                <div class="card-body">
                                    <!-- Reply User Info Section -->
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3">
                                                @if($reply->user->profile_image)
                                                    <img src="{{ asset('storage/' . $reply->user->profile_image) }}" alt="Profile" class="rounded-circle" width="35" height="35">
                                                @else
                                                    <div class="bg-info rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 35px; height: 35px; font-size: 12px;">
                                                        {{ strtoupper(substr($reply->user->first_name, 0, 1)) }}{{ strtoupper(substr($reply->user->last_name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-info">{{ $reply->user->first_name }} {{ $reply->user->last_name }}</h6>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-light-purple">{{ $reply->category ?? 'General' }}</span>
                                                    <small class="text-muted">
                                                        <i class="fa-light fa-clock"></i> {{ $reply->created_at->format('F d, Y H:i') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="comment-actions">
                                            @if(auth()->id() == $reply->user_id)
                                                <button class="btn btn-sm btn-outline-secondary edit-comment-btn" data-comment-id="{{ $reply->id }}" title="Edit Reply">
                                                    <i class="fa-light fa-edit"></i> Edit
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger delete-comment-btn" data-comment-id="{{ $reply->id }}" title="Delete Reply">
                                                    <i class="fa-light fa-trash"></i> Delete
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-outline-warning mark-answer-btn" data-comment-id="{{ $reply->id }}" title="Mark as Potential Answer">
                                                    <i class="fa-light fa-check"></i> Mark as Potential Answer
                                                </button>
                                                <button class="btn btn-sm btn-outline-info flag-comment-btn" data-comment-id="{{ $reply->id }}" title="Flag Reply">
                                                    <i class="fa-light fa-flag"></i> Flag Comment
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Reply Text Section -->
                                    <div class="reply-text-section">
                                        <p class="reply-text mb-0">{{ $reply->comment }}</p>
                                    </div>

                                    <!-- Edit Reply Form (Hidden by default) -->
                                    <div class="edit-form mt-3" id="editForm{{ $reply->id }}" style="display: none;">
                                        <form class="editCommentForm" data-comment-id="{{ $reply->id }}">
                                            <div class="form-group">
                                                <textarea class="form-control" name="comment" rows="2" placeholder="Write your reply here..." required>{{ $reply->comment }}</textarea>
                                            </div>
                                            <div class="mt-2">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fa-light fa-save"></i> Update
                                                </button>
                                                <button type="button" class="btn btn-secondary btn-sm cancel-edit-btn" data-comment-id="{{ $reply->id }}">
                                                    <i class="fa-light fa-times"></i> Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif
        </div>
    </div>
</div>
