@extends('admin-panel.layouts.app')
@section('page-submenu')
@php 
$page_arr = [
    'page_title' => 'Discussion Centre',
    'page_description' => 'Manage individual and group messages via message centre.',
    'page_type' => 'discussion-board-details',
];
@endphp
{!! pageSubMenu('all-threads',$page_arr) !!}
@endsection

@section('content')
<link rel="stylesheet" href="{{ url('assets/css/18-CDS-discussion-threads.css') }}">
<link rel="stylesheet" href="{{ url('assets/css/custom-file-upload.css') }}">
<link rel="stylesheet" href="{{ url('assets/css/edit-discussion-modal.css') }}">

<div class="CDSDashboardContainer-container CDSDashboardContainer-has-sidebar" id="CDSDashboardContainer-dashboardContainer">
<div class="CDSDashboardContainer-main-content">


 <div class="CdsDiscussionThread-main-content">
 <div class="CdsDiscussionThread-main-content-header"> </div>
<div class="CdsDiscussionThread-main-content-body">
<div class="CdsDiscussionThread-wrapper">
    <div class="CdsDiscussionThread-detail-layout">
            <!-- Main Content -->
            <div class="CdsDiscussionThread-detail-main">
                <!-- Thread Detail -->
                <article class="CdsDiscussionThread-detail-card">
                    <div class="CdsDiscussionThread-detail-header"> <div class="CdsDiscussionThread-detail-meta">
                            <div class="CdsDiscussionThread-detail-author">
                                {!! getProfileImage($discussion->user->unique_id) !!}
                                <!-- <div class="CdsDiscussionThread-detail-avatar">D</div> -->
                                <div class="CdsDiscussionThread-detail-author-info">
                                    <div class="CdsDiscussionThread-detail-author-name">{{$discussion->user->first_name ?? ''}}{{$discussion->user->last_name ?? ''}}</div>
                                    <div class="CdsDiscussionThread-detail-timestamp">Immigration Fraud • 2 weeks ago</div>
                                </div>
                            </div>
                        </div>
                        <div class="CdsDiscussionThread-detail-header-top">
                          
                            <div class="CdsDiscussionThread-dropdown">
                                <button class="CdsDiscussionThread-dropdown-trigger">
                                    <svg class="CdsDiscussionThread-icon" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" stroke="currentColor"/>
                                    </svg>
                                </button>
                                <div class="CdsDiscussionThread-dropdown-menu">
                                    @if($discussion->added_by == auth()->user()->id)
                                    <div class="CdsDiscussionThread-dropdown-item">
                                        <svg class="CdsDiscussionThread-icon" viewBox="0 0 24 24" fill="none">
                                            <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke="currentColor"/>
                                        </svg>
                                        <span>Edit</span>
                                    </div>
                                    @endif
                                    <div class="CdsDiscussionThread-dropdown-divider"></div>
                                    <div class="CdsDiscussionThread-dropdown-item danger">
                                        <svg class="CdsDiscussionThread-icon" viewBox="0 0 24 24" fill="none">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke="currentColor"/>
                                        </svg>
                                        <span>Delete</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                       
                    </div>
  <h3 class="CdsDiscussionThread-detail-title">{{$discussion->topic_title}}</h3>
                    <div class="CdsDiscussionThread-detail-content">
                        <h3 class="CdsDiscussionThread-detail-description-label">Description:</h3>
                        <p class="CdsDiscussionThread-detail-description">
                           {!! html_entity_decode($discussion->description) !!}
                        </p>
                    </div>
                    @if($discussion->files)
                        <div class="CdsDiscussionThread-images-preview">
                            @foreach(array_slice(explode(',', $discussion->files), 0, 3) as $index => $file)
                                <img src="{{ $file ? discussionDirUrl($file, 's') : asset('assets/images/default.jpg') }}" 
                                        alt="Attachment {{ $index + 1 }}"
                                        class="CdsDiscussionThread-preview-image" onclick="cdsDiscussionDetailOpenPreview(this)" data-href="{{ baseUrl('manage-discussion-threads/view-media/'.$discussion->unique_id.'/'.$file) }}">
                            @endforeach
                            @if(count(explode(',', $discussion->files)) > 3)
                                <span class="CdsDiscussionThread-more-images">
                                    +{{ count(explode(',', $discussion->files)) - 3 }} more
                                </span>
                            @endif
                        </div>
                    @endif
                  
                </article>

                <!-- Comment Editor -->
                <div class="CdsDiscussionThread-comment-editor-card">
                    <input type="hidden" value="" id="edit_comment_id">
                    <input type="hidden" value="{{baseUrl('manage-discussion-threads/save-comment') . '/' . $discussion_id}}"
                            id="geturl">
                    <input type="hidden" value="{{$discussion_id}}" id="get_discussion_id">
                    <div class="cdsTYDashboard-discussion-panel-view-editor-custom-container-inner-wrap CdsDiscussionThread-comment-input">
                                {!! FormHelper::formTextarea([
                                'name'=>"comment",
                                "id" => "duscussionCommentBox",
                                "required"=>true,
                                "textarea_class" => "CDS_Thread_textarea",
                                'value'=>'',
                                ]) !!}
                        <div class="cdsTYDashboard-discussion-panel-view-editor-custom-container-action-buttons">
                            <div class="message-emoji-icon emoji-icon CdsDiscussionThread-emoji-btn">
                                <i class="fa-sharp fa-solid fa-face-smile"></i>
                            </div>

                            <div class="message-upload-file" id="discussionFileUploadTrigger">
                                <i class="fas fa-upload"></i>
                            </div>
                            <!-- <button id="sendBtn1">
                                <i class="fa-solid fa-send"></i>
                            </button> -->
                        </div>
                        <div class="reply-message " id="reply_quoted_msg" style="display: none">
                            <div class="reply-icons">
                                <i class="fa-solid fa-turn-up"></i>
                                <i class="fa-solid fa-xmark" onclick="closeReplyto()"></i>
                            </div>
                            <p class="quoted-message">Reply quoted message</p><span class="username" id="myreply">MY
                                Reply</span>
                        </div>
                    </div>
                    <div class="CdsDiscussionThread-editor-footer">
                        <button id= "CdsDiscussionThread-comment-post" class="CdsDiscussionThread-post-comment-btn">Post Comment</button>
                    </div>
                    
                    <!-- File Upload Section -->
                    <div class="CDSFeed-form-group" id="discussionFileUploadContainer" style="display: none;">
                        <div class="CDSFeed-upload-container" id="discussionMediaUpload">
                            <div class="CDSFeed-upload-area">
                                <input type="file" class="CDSFeed-file-input" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.mp3,.mp4,.mpeg" style="display: none;">
                                <div class="CDSFeed-upload-icon">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                </div>
                                <p class="CDSFeed-upload-text">Drag and drop files here or click to browse</p>
                                <p class="CDSFeed-upload-hint">Supports: Images, PDF, DOC, XLS, CSV, TXT, MP3, MP4 (Max 10MB per file)</p>
                            </div>
                            
                            <!-- File Preview Area -->
                            <div class="CDSFeed-file-list" style="display: none;">
                                <!-- Files will be dynamically added here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="CdsDiscussionThread-comments-section">
                   <div class="CdsDiscussionThread-comments-header">
                        <h2 class="CdsDiscussionThread-comments-title">Comments</h2>
                        <span class="CdsDiscussionThread-comments-filter">Older Comments</span>
                    </div>
                    <div class="CdsDiscussionThread-comments-load">

                    </div>
                </div>
            </div>

               </div>
      







 </div> </div>

 </div>

 <div class="CDSDashboardContainer-sidebar" id="sidebar">
                    <!-- Drag Handle (visible only on desktop) -->
                    <div class="CDSDashboardContainer-drag-handle" id="dragHandle"></div>

                    <!-- Collapse Button (visible only on desktop) -->
                    <button class="CDSDashboardContainer-collapse-btn" id="collapseBtn" aria-label="Toggle Sidebar">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                        </svg>
                    </button>
  <div class="CDSDashboardContainer-sidebar-inner">
                  @include("admin-panel.05-discussion-boards.manage-discussion-thread.right-side-panel-detail") </div>
                </div>

                <!-- Mobile Menu Toggle -->
                <button class="CDSDashboardContainer-menu-toggle" id="menuToggle" aria-label="Toggle Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <!-- Overlay -->
                <div class="CDSDashboardContainer-overlay" id="overlay"></div>
                
                <!-- Image Preview Overlay -->
                <div class="CdsCaseDocumentPreview-overlay" id="cdsDiscussionPreviewOverlay"></div>
 </div>


</div>




@endsection
 
@section('javascript')
<!-- <script src="{{url('assets/js/custom-editor.js')}}"></script> -->
<script src="{{ url('assets/js/custom-file-upload.js') }}"></script>
<script src="{{ url('assets/js/new-discussion-thread.js?v='.mt_rand()) }}"></script>
   <script>
    // var editor = CustomEditor.init(".CDS_Thread_textarea");
    setTimeout(() => {
        initializeDiscussionSocket("{{$discussion_id}}");
    }, 2000);       
    // initializeDiscussionSocket("{{$discussion_id}}");
    new EmojiPicker(".message-emoji-icon", {
        targetElement: "#duscussionCommentBox",
        onEmojiSelect:function(emoji){
            if (editor) {
                editor.appendText(emoji);
            }
        }
    });
        // Initialize dropdown menus
        function cdsDiscussionThreadInitDetailDropdowns() {
            const dropdownTriggers = document.querySelectorAll('.CdsDiscussionThread-dropdown-trigger');
            
            dropdownTriggers.forEach(trigger => {
                trigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const dropdown = this.nextElementSibling;
                    
                    // Close all other dropdowns
                    document.querySelectorAll('.CdsDiscussionThread-dropdown-menu').forEach(menu => {
                        if (menu !== dropdown) {
                            menu.classList.remove('active');
                        }
                    });
                    
                    dropdown.classList.toggle('active');
                });
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', () => {
                document.querySelectorAll('.CdsDiscussionThread-dropdown-menu').forEach(menu => {
                    menu.classList.remove('active');
                });
            });

            // Handle dropdown item clicks
            const dropdownItems = document.querySelectorAll('.CdsDiscussionThread-dropdown-item');
            dropdownItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const action = this.querySelector('span').textContent;
                    console.log('Action clicked:', action);
                    
                    // Close the dropdown
                    this.closest('.CdsDiscussionThread-dropdown-menu').classList.remove('active');
                    
                    // Handle actions
                    if (action === 'Edit') {
                       
                    } else if (action === 'Delete') {
                        if (confirm('Are you sure you want to delete this thread?')) {
                            alert('Thread would be deleted');
                            // Redirect to list page after deletion
                        }
                    }
                });
            });
        }

        // Initialize comment actions
        function cdsDiscussionThreadInitCommentActions() {
            const likeButtons = document.querySelectorAll('.CdsDiscussionThread-comment-action:first-child');
            
            likeButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const count = this.querySelector('span');
                    const currentCount = parseInt(count.textContent);
                    
                    if (this.classList.contains('active')) {
                        count.textContent = currentCount - 1;
                        this.classList.remove('active');
                    } else {
                        count.textContent = currentCount + 1;
                        this.classList.add('active');
                    }
                });
            });
        }

        // Initialize editor tools
        function cdsDiscussionThreadInitEditor() {
            const editorTools = document.querySelectorAll('.CdsDiscussionThread-editor-tool');
            
            editorTools.forEach(tool => {
                tool.addEventListener('click', function() {
                    // Placeholder for editor functionality
                    console.log('Editor tool clicked');
                });
            });
        }

        // Initialize category tags
        function cdsDiscussionThreadInitDetailCategories() {
            const categoryTags = document.querySelectorAll('.CdsDiscussionThread-category-tag');
            
            categoryTags.forEach(tag => {
                tag.addEventListener('click', function() {
                    categoryTags.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        }

        // Initialize delete buttons
        function cdsDiscussionThreadInitDeleteButtons() {
            const deleteButtons = document.querySelectorAll('.CdsDiscussionThread-delete-btn');
            
            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to remove this member?')) {
                        const memberItem = this.closest('.CdsDiscussionThread-member-item');
                        memberItem.style.opacity = '0';
                        memberItem.style.transform = 'translateX(20px)';
                        setTimeout(() => memberItem.remove(), 300);
                    }
                });
            });
        }

        // Initialize post comment
        function cdsDiscussionThreadInitPostComment() {
            const postBtn = document.querySelector('.CdsDiscussionThread-post-comment-btn');
            const textarea = document.querySelector('.CdsDiscussionThread-editor-textarea');
            
            // postBtn.addEventListener('click', function() {
            //     const comment = textarea.value.trim();
            //     if (comment) {
            //         console.log('Posting comment:', comment);
            //         textarea.value = '';
            //         // Add animation or show success message
            //     }
            // });
        }

        // Initialize all detail page functions
        document.addEventListener('DOMContentLoaded', () => {
            cdsDiscussionThreadInitDetailDropdowns();
            cdsDiscussionThreadInitCommentActions();
            cdsDiscussionThreadInitEditor();
            cdsDiscussionThreadInitDetailCategories();
            cdsDiscussionThreadInitDeleteButtons();
            cdsDiscussionThreadInitPostComment();
        });


    function toggleReplyForm(commentId,action) {
        if(action == 'show'){
            $.ajax({
                url: "{{ baseUrl('manage-discussion-threads/reply-comment-form/') }}/"+commentId,
                type: "post",
                data:{
                    _token:'{{ csrf_token() }}',
                },
                dataType: "json",
                beforeSend: function() {
                    var loader = '<div id="feed-loader" class="CDSFeed-loader">';
                    loader += '<div class="spinner-border" role="status">';
                    loader += '<span class="sr-only"></span>';
                    loader += '</div>';
                    loader += '<div>Loading...</div>';
                    loader += '</div>';
                    $("#CDSDiscussion-reply-"+commentId).html(loader);
                },
                
                success: function(response) {
                    if (response.status == true) {
                        $("#CDSDiscussion-reply-"+commentId).html(response.contents);
                    } else {
                        $("#CDSDiscussion-reply-"+commentId).html('');
                       
                    }
                },
                error: function() {
                    hideLoader();
                    internalError();
                }
            });
        }else{
            $("#CDSDiscussion-reply-"+commentId).html('');
        }
    }

    </script>
<link href="{{ url('assets/css/16-CDS-case-document-preview.css') }}" rel="stylesheet" />
<script>
    function cdsDiscussionDetailOpenPreview(e) {
        var url = $(e).data("href");
        $.ajax({
            url:url,
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            beforeSend: function() {
                showLoader();
            },
            success: function(response) {
            
                if (response.status) {
                    hideLoader();
                    $("#cdsDiscussionPreviewOverlay").html(response.contents);
                } else {
                    hideLoader();
                    errorMessage(response.message);
                }
            },
            error: function() {
                internalError();
            }
        });
    }

    // Function to open comment image preview
    function openCommentImagePreview(element) {
        var url = $(element).data("href");
        $.ajax({
            url: url,
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            beforeSend: function() {
                showLoader();
            },
            success: function(response) {
                if (response.status) {
                    hideLoader();
                    $("#cdsDiscussionPreviewOverlay").html(response.contents);
                    $("#cdsDiscussionPreviewOverlay").addClass("CdsCaseDocumentPreview-active");
                } else {
                    hideLoader();
                    errorMessage(response.message);
                }
            },
            error: function() {
                hideLoader();
                internalError();
            }
        });
    }

    // Close image preview overlay
    $(document).on('click', '#cdsDiscussionPreviewOverlay', function(e) {
        if (e.target === this) {
            $(this).removeClass("CdsCaseDocumentPreview-active");
            $(this).html('');
        }
    });

    // Close image preview with escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#cdsDiscussionPreviewOverlay').hasClass('CdsCaseDocumentPreview-active')) {
            $('#cdsDiscussionPreviewOverlay').removeClass("CdsCaseDocumentPreview-active");
            $('#cdsDiscussionPreviewOverlay').html('');
        }
    });
</script>

@endsection
