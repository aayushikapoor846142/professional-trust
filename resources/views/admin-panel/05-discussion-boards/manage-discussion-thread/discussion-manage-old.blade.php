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
@section("styles")
<link rel="stylesheet" href="{{ url('assets/css/18-CDS-discussion-threads.css') }}">
<link rel="stylesheet" href="{{ url('assets/css/custom-file-upload.css') }}">
<style>
/* Custom styling for discussion board file upload */
#discussionFileUploadContainer {
    margin-top: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

#discussionFileUploadContainer .CDSFeed-upload-container {
    margin-top: 0;
}

#discussionFileUploadContainer .CDSFeed-upload-area {
    background: white;
    border: 2px dashed #dee2e6;
    transition: all 0.2s ease;
}

#discussionFileUploadContainer .CDSFeed-upload-area:hover {
    border-color: #007bff;
    background: #f8f9ff;
}

#discussionFileUploadContainer .CDSFeed-upload-area.drag-over {
    border-color: #007bff;
    background: #e7f3ff;
    transform: scale(1.01);
}

#discussionFileUploadContainer .CDSFeed-file-list {
    margin-top: 0.75rem;
}

#discussionFileUploadContainer .CDSFeed-file-item {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    margin-bottom: 0.5rem;
}

/* Animation for showing/hiding upload container */
#discussionFileUploadContainer {
    transition: all 0.3s ease;
    opacity: 0;
    max-height: 0;
    overflow: hidden;
}

#discussionFileUploadContainer.show {
    opacity: 1;
    max-height: 500px;
}

/* Media upload trigger button styling */
#mediaUploadTrigger {
    cursor: pointer;
    transition: all 0.3s ease;
    border-radius: 8px;
    padding: 8px 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    font-weight: 500;
}

#mediaUploadTrigger:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

/* Enhanced Modal Styling */
.modal-content {
    border-radius: 16px;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    overflow: hidden;
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 16px 16px 0 0;
    padding: 1.5rem 2rem;
    border-bottom: none;
    position: relative;
}

.modal-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
    pointer-events: none;
}

.modal-title {
    font-weight: 700;
    font-size: 1.25rem;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    position: relative;
    z-index: 1;
}

.modal-title i {
    font-size: 1.1rem;
    opacity: 0.9;
}

.btn-close {
    filter: invert(1);
    opacity: 0.8;
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-close:hover {
    opacity: 1;
    background: rgba(255,255,255,0.2);
    transform: rotate(90deg);
}

.modal-body {
    padding: 2rem;
    background: #fafbfc;
}

.modal-footer {
    padding: 1.5rem 2rem;
    border-top: 1px solid #e9ecef;
    background: white;
    border-radius: 0 0 16px 16px;
}

/* Enhanced Button Styling */
.btn {
    border-radius: 10px;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
    border: none;
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn:hover::before {
    left: 100%;
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
}

.btn-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

/* Insert button styling */
#insertMediaBtn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}

#insertMediaBtn:not(:disabled):hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

/* Enhanced Upload Area Styling */
.CDSFeed-upload-container {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    border: 2px dashed #e1e5e9;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.CDSFeed-upload-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.02) 0%, rgba(118, 75, 162, 0.02) 100%);
    pointer-events: none;
}

.CDSFeed-upload-container:hover {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.1);
}

.CDSFeed-upload-area {
    text-align: center;
    position: relative;
    z-index: 1;
}

.CDSFeed-upload-icon {
    margin-bottom: 1rem;
    color: #667eea;
}

.CDSFeed-upload-icon svg {
    width: 64px;
    height: 64px;
    opacity: 0.8;
}

.CDSFeed-upload-text {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.CDSFeed-upload-hint {
    font-size: 0.9rem;
    color: #718096;
    margin: 0;
}

/* File List Styling */
.CDSFeed-file-list {
    margin-top: 1.5rem;
}

.CDSFeed-file-item {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.CDSFeed-file-item:hover {
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
    transform: translateX(4px);
}

/* Responsive Design */
@media (max-width: 768px) {
    .modal-header {
        padding: 1rem 1.5rem;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .modal-footer {
        padding: 1rem 1.5rem;
    }
    
    .CDSFeed-upload-container {
        padding: 1.5rem;
    }
    
    .btn {
        padding: 0.6rem 1.2rem;
        font-size: 0.9rem;
    }
}

/* Animation for modal appearance */
.modal.fade .modal-dialog {
    transform: scale(0.8) translateY(-20px);
    transition: all 0.3s ease;
}

.modal.show .modal-dialog {
    transform: scale(1) translateY(0);
}

/* Custom scrollbar for modal body */
.modal-body::-webkit-scrollbar {
    width: 6px;
}

.modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.modal-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Discussion Image Preview Styling */
.CdsDiscussionThread-images-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 1rem;
}

.CdsDiscussionThread-preview-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.CdsDiscussionThread-preview-image:hover {
    transform: scale(1.05);
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

.CdsDiscussionThread-more-images {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.CdsDiscussionThread-more-images:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

/* Preview Modal Enhancements */
.cdsTYDashboardModal-preview-container01-modal-overlay {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    background: rgba(0, 0, 0, 0.8) !important;
    backdrop-filter: blur(8px) !important;
    z-index: 9999 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    opacity: 0 !important;
    visibility: hidden !important;
    transition: all 0.3s ease !important;
}

.cdsTYDashboardModal-preview-container01-modal-overlay.active {
    opacity: 1 !important;
    visibility: visible !important;
}

.cdsTYDashboardModal-preview-container01-modal-dialog {
    background: white !important;
    border-radius: 16px !important;
    max-width: 90vw !important;
    max-height: 90vh !important;
    width: 100% !important;
    overflow: hidden !important;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3) !important;
    transform: scale(0.8) !important;
    transition: transform 0.3s ease !important;
}

.cdsTYDashboardModal-preview-container01-modal-overlay.active .cdsTYDashboardModal-preview-container01-modal-dialog {
    transform: scale(1) !important;
}

.cdsTYDashboardModal-preview-container01-modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    padding: 1rem 1.5rem !important;
    border-radius: 16px 16px 0 0 !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
}

.cdsTYDashboardModal-preview-container01-modal-header h5 {
    margin: 0 !important;
    font-weight: 600 !important;
    font-size: 1.1rem !important;
}

.cdsTYDashboardModal-preview-container01-modal-header-toolbar {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.cdsTYDashboardModal-preview-container01-nav-buttons {
    display: flex;
    gap: 0.5rem;
}

.cdsTYDashboardModal-preview-container01-nav-button-prev,
.cdsTYDashboardModal-preview-container01-nav-button-next {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.cdsTYDashboardModal-preview-container01-nav-button-prev:hover,
.cdsTYDashboardModal-preview-container01-nav-button-next:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-1px);
}

#downloadButton {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    text-decoration: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    transition: all 0.3s ease;
}

#downloadButton:hover {
    background: rgba(255, 255, 255, 0.3);
    color: white;
    transform: translateY(-1px);
}

.cdsTYDashboardModal-container01-btn-close {
    background: rgba(255, 255, 255, 0.2) !important;
    border: none !important;
    color: white !important;
    width: 32px !important;
    height: 32px !important;
    border-radius: 50% !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 1.2rem !important;
    font-weight: bold !important;
}

.cdsTYDashboardModal-container01-btn-close:hover {
    background: rgba(255, 255, 255, 0.3) !important;
    transform: rotate(90deg) !important;
}

.cdsTYDashboardModal-preview-container01-modal-body {
    padding: 1.5rem;
    max-height: 70vh;
    overflow: auto;
}

.cdsTYDashboardModal-preview-container01-modal-body-preview-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 400px;
}

.cdsTYDashboardModal-preview-container01-modal-body-preview-container-inner {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
}

.cdsTYDashboardModal-preview-container01-modal-body-preview-container-inner img {
    max-width: 100%;
    max-height: 70vh;
    object-fit: contain;
    border-radius: 8px;
    cursor: zoom-in;
    transition: all 0.3s ease;
}

.cdsTYDashboardModal-preview-container01-modal-body-preview-container-inner img.zoomed {
    cursor: zoom-out;
    transform: scale(1.5);
}

.cdsTYDashboardModal-preview-container01-modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e9ecef;
    background: #f8f9fa;
    border-radius: 0 0 16px 16px;
}

.cdsTYDashboardModal-preview-container01-thumbnail-wrapper {
    margin-top: 1rem;
    transition: all 0.3s ease;
}

.cdsTYDashboardModal-preview-container01-thumbnail-wrapper.visible {
    max-height: 120px;
    overflow: hidden;
}

.cdsTYDashboardModal-preview-container01-thumbnail-wrapper:not(.visible) {
    max-height: 0;
    overflow: hidden;
}

.cdsTYDashboardModal-preview-container01-thumbnail-scroll {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    padding: 0.5rem 0;
}

.cdsTYDashboardModal-preview-container01-thumbnail-item {
    flex-shrink: 0;
    width: 60px;
    height: 60px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
}

.cdsTYDashboardModal-preview-container01-thumbnail-item:hover {
    border-color: #667eea;
    transform: scale(1.05);
}

.cdsTYDashboardModal-preview-container01-thumbnail-item.active {
    border-color: #667eea;
    background: #e8ecff;
}

.cdsTYDashboardModal-preview-container01-thumbnail-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 4px;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .CdsDiscussionThread-preview-image {
        width: 60px;
        height: 60px;
    }
    
    .CdsDiscussionThread-more-images {
        width: 60px;
        height: 60px;
        font-size: 0.8rem;
    }
    
    .cdsTYDashboardModal-preview-container01-modal-dialog {
        max-width: 95vw;
        max-height: 95vh;
    }
    
    .cdsTYDashboardModal-preview-container01-modal-header-toolbar {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .cdsTYDashboardModal-preview-container01-nav-buttons {
        order: 2;
    }
    
    #downloadButton {
        order: 1;
    }
    
    .cdsTYDashboardModal-container01-btn-close {
        order: 3;
    }
}
</style>

<!-- Include Edit Discussion Modal CSS -->
<link rel="stylesheet" href="{{ url('assets/css/edit-discussion-modal.css') }}">


@endsection
@section('content')
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
                                    <div class="CdsDiscussionThread-dropdown-item" onclick="openCustomPopup(this)" data-href="{{ baseUrl('manage-discussion-threads/edit/'.$discussion->unique_id.'/modal') }}">
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
                                        class="CdsDiscussionThread-preview-image" 
                                        onclick="openDiscussionImagePreview('{{ $file }}', {{ $index }}, '{{ discussionDirUrl($file, 'r') }}')" 
                                        data-href="{{ baseUrl('manage-discussion-threads/view-media/'.$discussion->unique_id.'/'.$file) }}">
                            @endforeach
                            @if(count(explode(',', $discussion->files)) > 3)
                                <span class="CdsDiscussionThread-more-images" onclick="openDiscussionImagePreview('{{ $discussion->files }}', 0, '{{ discussionDirUrl(explode(',', $discussion->files)[0], 'r') }}')">
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
                            
                            <div class="message-upload-file" id="mediaUploadTrigger" title="Upload Media Files">
                                <i class="fas fa-image"></i>
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
 </div>

<!-- Media Upload Popup Modal -->
<div class="modal fade" id="mediaUploadModal" tabindex="-1" role="dialog" aria-labelledby="mediaUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mediaUploadModalLabel">
                    <i class="fas fa-upload me-2"></i>Upload Media Files
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="CDSFeed-form-group">
                    <div class="CDSFeed-upload-container" id="popupMediaUpload">
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="CdsTYButton-btn-primary" id="insertMediaBtn" disabled>
                    <i class="fas fa-plus me-1"></i>Insert Media
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Media Preview Modal -->
<div class="modal fade" id="mediaPreviewModal" tabindex="-1" role="dialog" aria-labelledby="mediaPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mediaPreviewModalLabel">Media Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="mediaPreviewContent">
                    <!-- Media content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Discussion Preview Overlay -->
<div class="CdsCaseDocumentPreview-overlay" id="cdsDiscussionPreviewOverlay"></div>


</div>




@endsection
 
@push('scripts')


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
                    if (action === 'Delete') {
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

    // Media Upload Popup Functionality
    let popupMediaUploader;
    
    // Initialize popup media uploader
    function initializePopupMediaUploader() {
        popupMediaUploader = new FileUploadManager('#popupMediaUpload', {
            maxFileSize: 10 * 1024 * 1024, // 10MB
            maxFiles: 5,
            allowedTypes: [
                'image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 'image/svg+xml',
                'application/pdf',
                'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/csv', 'application/csv',
                'audio/mpeg', 'video/mp4', 'video/mpeg'
            ],
            allowedExtensions: [
                '.jpg', '.jpeg', '.png', '.gif', '.bmp', '.webp', '.svg',
                '.pdf', '.doc', '.docx', '.xls', '.xlsx', '.csv', '.txt', '.mp3', '.mp4', '.mpeg'
            ],
            onFileAdded: function(fileData) {
                console.log('File added to popup uploader:', fileData.name);
                updateInsertButton();
            },
            onFileRemoved: function(fileData) {
                console.log('File removed from popup uploader:', fileData.name);
                updateInsertButton();
            },
            onError: function(message) {
                errorMessage(message);
            }
        });
        
        // Initialize the uploader
        if (popupMediaUploader.init()) {
            console.log('Popup media uploader initialized successfully');
        }
    }

    // Update insert button state
    function updateInsertButton() {
        const insertBtn = document.getElementById('insertMediaBtn');
        if (popupMediaUploader && popupMediaUploader.getFileCount() > 0) {
            insertBtn.disabled = false;
            insertBtn.innerHTML = `<i class="fas fa-plus me-1"></i>Insert Media (${popupMediaUploader.getFileCount()})`;
        } else {
            insertBtn.disabled = true;
            insertBtn.innerHTML = `<i class="fas fa-plus me-1"></i>Insert Media`;
        }
    }

    // Handle media upload trigger click
    $(document).on('click', '#mediaUploadTrigger', function() {
        $('#mediaUploadModal').modal('show');
    });

    // Handle modal events
    $('#mediaUploadModal').on('shown.bs.modal', function() {
        // Initialize uploader when modal is shown
        if (!popupMediaUploader) {
            initializePopupMediaUploader();
        }
        updateInsertButton();
    });

    $('#mediaUploadModal').on('hidden.bs.modal', function() {
        // Reset uploader when modal is hidden
        if (popupMediaUploader) {
            popupMediaUploader.reset();
        }
    });

    // Handle insert media button click
    $(document).on('click', '#insertMediaBtn', function() {
        if (popupMediaUploader && popupMediaUploader.getFileCount() > 0) {
            const files = popupMediaUploader.getFilesWithData();
            
            // Add files to the main discussion uploader
            files.forEach(fileData => {
                if (discussionFileUploader) {
                    // Add file to main uploader
                    discussionFileUploader.addFile(fileData.file);
                }
            });
            
            // Show the main upload container
            const container = document.getElementById('discussionFileUploadContainer');
            if (container) {
                container.style.display = 'block';
                setTimeout(() => container.classList.add('show'), 10);
            }
            
            // Close modal
            $('#mediaUploadModal').modal('hide');
            
            // Show success message
            successMessage(`${files.length} file(s) added to comment`);
        }
    });

    // Initialize popup uploader when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize popup uploader after a short delay
        setTimeout(() => {
            initializePopupMediaUploader();
        }, 1000);
    });

    // Media preview functionality
    function openMediaPreview(mediaUrl, mediaType) {
        const modal = document.getElementById('mediaPreviewModal');
        const content = document.getElementById('mediaPreviewContent');
        
        // Clear previous content
        content.innerHTML = '';
        
        if (mediaType.startsWith('image/')) {
            // Display image
            const img = document.createElement('img');
            img.src = mediaUrl;
            img.className = 'img-fluid';
            img.style.maxHeight = '70vh';
            img.style.maxWidth = '100%';
            content.appendChild(img);
        } else if (mediaType.startsWith('video/')) {
            // Display video
            const video = document.createElement('video');
            video.src = mediaUrl;
            video.controls = true;
            video.className = 'img-fluid';
            video.style.maxHeight = '70vh';
            video.style.maxWidth = '100%';
            content.appendChild(video);
        } else if (mediaType.startsWith('audio/')) {
            // Display audio player
            const audio = document.createElement('audio');
            audio.src = mediaUrl;
            audio.controls = true;
            audio.style.width = '100%';
            content.appendChild(audio);
        } else {
            // Display file info for other types
            const fileInfo = document.createElement('div');
            fileInfo.className = 'text-center p-4';
            fileInfo.innerHTML = `
                <i class="fas fa-file fa-3x text-muted mb-3"></i>
                <h5>File Preview Not Available</h5>
                <p class="text-muted">This file type cannot be previewed in the browser.</p>
                <a href="${mediaUrl}" class="CdsTYButton-btn-primary" target="_blank" download>
                    <i class="fas fa-download me-2"></i>Download File
                </a>
            `;
            content.appendChild(fileInfo);
        }
        
        // Show modal
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
    }

    // Add click handlers for media preview
    $(document).on('click', '.CdsDiscussionThread-preview-image', function() {
        const mediaUrl = $(this).attr('src');
        openMediaPreview(mediaUrl, 'image/jpeg');
    });

    // Enhanced Discussion Image Preview Function
    function openDiscussionImagePreview(fileName, index, fullImageUrl) {
        // Get all files from the discussion
        const allFiles = '{{ $discussion->files }}'.split(',');
        const currentIndex = index;
        
        // Create gallery items array
        const galleryItems = allFiles.map((file, idx) => ({
            name: file,
            url: '{{ discussionDirUrl("", "r") }}' + file,
            download_url: '{{ discussionDirUrl("", "r") }}' + file,
            type: getFileType(file)
        }));
        
        // Create preview modal HTML
        const modalHTML = `
            <div class="cdsTYDashboardModal-preview-container01-modal-overlay active" id="discussionPreviewModal">
                <div class="cdsTYDashboardModal-preview-container01-modal-dialog">
                    <div class="cdsTYDashboardModal-preview-container01-modal-header">
                        <h5>Preview <span id="imageDetails">${galleryItems[currentIndex].name}</span></h5>
                        <div class="cdsTYDashboardModal-preview-container01-modal-header-toolbar">
                            ${galleryItems.length > 1 ? `
                                <div class="cdsTYDashboardModal-preview-container01-nav-buttons">
                                    <button class="cdsTYDashboardModal-preview-container01-nav-button-prev" onclick="changeDiscussionSlide(-1)">← Previous</button>
                                    <button class="cdsTYDashboardModal-preview-container01-nav-button-next" onclick="changeDiscussionSlide(1)">Next →</button>
                                </div>
                            ` : ''}
                            <a id="downloadButton" href="${galleryItems[currentIndex].download_url}" download>Download</a>
                            <button class="cdsTYDashboardModal-container01-btn-close" onclick="closeDiscussionPreviewModal()">×</button>
                        </div>
                    </div>
                    <div class="cdsTYDashboardModal-preview-container01-modal-body" id="modalBody">
                        <div class="cdsTYDashboardModal-preview-container01-modal-body-preview-container">
                            <div class="cdsTYDashboardModal-preview-container01-modal-body-preview-container-inner" id="previewFileContainer"></div>
                        </div>
                    </div>
                    ${galleryItems.length > 1 ? `
                        <div class="cdsTYDashboardModal-preview-container01-modal-footer" id="footerRef">
                            <button class="cdsTYDashboardModal-preview-container01-mobile-thumbnail-toggle" onclick="toggleDiscussionMobileThumbs()">Show</button>
                            <div class="cdsTYDashboardModal-preview-container01-mobile-thumbnail-panel" id="mobileThumbPanel"></div>
                            <div class="cdsTYDashboardModal-preview-container01-thumbnail-toggle-wrapper">
                                <button onclick="toggleDiscussionThumbnails()">Hide</button>
                            </div>
                            <div class="cdsTYDashboardModal-preview-container01-thumbnail-wrapper visible" id="thumbnailPanel">
                                <div class="cdsTYDashboardModal-preview-container01-thumbnail-scroll" id="thumbnailContainer"></div>
                            </div>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
        
        // Add modal to overlay
        document.getElementById('cdsDiscussionPreviewOverlay').innerHTML = modalHTML;
        document.getElementById('cdsDiscussionPreviewOverlay').classList.add('CdsCaseDocumentPreview-active');
        
        // Activate the modal overlay
        setTimeout(() => {
            const modalOverlay = document.querySelector('.cdsTYDashboardModal-preview-container01-modal-overlay');
            if (modalOverlay) {
                modalOverlay.classList.add('active');
            }
        }, 10);
        
        // Initialize preview
        window.discussionGalleryItems = galleryItems;
        window.discussionCurrentIndex = currentIndex;
        renderDiscussionThumbnails();
        displayDiscussionItem(currentIndex);
        
        // Add touch/swipe support
        addDiscussionTouchSupport();
    }
    
    // Helper function to get file type
    function getFileType(fileName) {
        const ext = fileName.split('.').pop().toLowerCase();
        if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(ext)) return 'image';
        if (['pdf'].includes(ext)) return 'pdf';
        if (['mp4', 'avi', 'mov', 'wmv'].includes(ext)) return 'video';
        if (['mp3', 'wav', 'ogg'].includes(ext)) return 'audio';
        return 'document';
    }
    
    // Render thumbnails for discussion preview
    function renderDiscussionThumbnails() {
        const container = document.getElementById('thumbnailContainer');
        if (!container) return;
        
        container.innerHTML = '';
        window.discussionGalleryItems.forEach((item, index) => {
            const thumb = document.createElement('div');
            thumb.className = 'cdsTYDashboardModal-preview-container01-thumbnail-item';
            thumb.onclick = () => displayDiscussionItem(index);
            
            if (item.type === 'image') {
                const img = document.createElement('img');
                img.src = item.url;
                img.alt = item.name;
                thumb.appendChild(img);
            } else {
                const icon = document.createElement('i');
                icon.className = 'fas fa-file';
                icon.style.fontSize = '24px';
                icon.style.color = '#666';
                thumb.appendChild(icon);
            }
            
            container.appendChild(thumb);
        });
    }
    
    // Display discussion item
    function displayDiscussionItem(index) {
        window.discussionCurrentIndex = index;
        const container = document.getElementById('previewFileContainer');
        const nameSpan = document.getElementById('imageDetails');
        const downloadBtn = document.getElementById('downloadButton');
        
        if (!container) return;
        
        const item = window.discussionGalleryItems[index];
        nameSpan.textContent = item.name;
        downloadBtn.href = item.download_url;
        container.innerHTML = '';
        
        if (item.type === 'image') {
            const img = document.createElement('img');
            img.src = item.url;
            img.style.maxWidth = '100%';
            img.style.maxHeight = '70vh';
            img.style.objectFit = 'contain';
            img.onclick = () => img.classList.toggle('zoomed');
            img.ondblclick = () => openFullscreen(img);
            container.appendChild(img);
        } else if (item.type === 'pdf') {
            const embed = document.createElement('embed');
            embed.src = item.url;
            embed.type = 'application/pdf';
            embed.style.width = '100%';
            embed.style.height = '70vh';
            container.appendChild(embed);
        } else if (item.type === 'video') {
            const video = document.createElement('video');
            video.src = item.url;
            video.controls = true;
            video.style.maxWidth = '100%';
            video.style.maxHeight = '70vh';
            container.appendChild(video);
        } else if (item.type === 'audio') {
            const audio = document.createElement('audio');
            audio.src = item.url;
            audio.controls = true;
            audio.style.width = '100%';
            container.appendChild(audio);
        } else {
            const fileInfo = document.createElement('div');
            fileInfo.className = 'text-center p-4';
            fileInfo.innerHTML = `
                <i class="fas fa-file fa-3x text-muted mb-3"></i>
                <h5>File Preview Not Available</h5>
                <p class="text-muted">This file type cannot be previewed in the browser.</p>
                <a href="${item.download_url}" class="CdsTYButton-btn-primary" target="_blank" download>
                    <i class="fas fa-download me-2"></i>Download File
                </a>
            `;
            container.appendChild(fileInfo);
        }
        
        // Update thumbnail selection
        const thumbnails = document.querySelectorAll('.cdsTYDashboardModal-preview-container01-thumbnail-item');
        thumbnails.forEach((thumb, idx) => {
            thumb.classList.toggle('active', idx === index);
        });
    }
    
    // Change slide for discussion preview
    function changeDiscussionSlide(n) {
        const newIndex = (window.discussionCurrentIndex + n + window.discussionGalleryItems.length) % window.discussionGalleryItems.length;
        displayDiscussionItem(newIndex);
    }
    
    // Toggle thumbnails for discussion preview
    function toggleDiscussionThumbnails() {
        const panel = document.getElementById('thumbnailPanel');
        const btn = document.querySelector('.cdsTYDashboardModal-preview-container01-thumbnail-toggle-wrapper button');
        const isVisible = panel.classList.toggle('visible');
        btn.textContent = isVisible ? 'Hide' : 'Show';
    }
    
    // Toggle mobile thumbnails for discussion preview
    function toggleDiscussionMobileThumbs() {
        document.getElementById('mobileThumbPanel').classList.toggle('active');
    }
    
    // Close discussion preview modal
    function closeDiscussionPreviewModal() {
        const overlay = document.getElementById('cdsDiscussionPreviewOverlay');
        const modalOverlay = document.querySelector('.cdsTYDashboardModal-preview-container01-modal-overlay');
        
        if (modalOverlay) {
            modalOverlay.classList.remove('active');
            setTimeout(() => {
                if (overlay) {
                    overlay.classList.remove('CdsCaseDocumentPreview-active');
                    overlay.innerHTML = '';
                }
            }, 300);
        } else if (overlay) {
            overlay.classList.remove('CdsCaseDocumentPreview-active');
            overlay.innerHTML = '';
        }
        
        window.discussionGalleryItems = null;
        window.discussionCurrentIndex = null;
    }
    
    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('cdsTYDashboardModal-preview-container01-modal-overlay')) {
            closeDiscussionPreviewModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDiscussionPreviewModal();
        }
    });
    
    // Add touch support for discussion preview
    function addDiscussionTouchSupport() {
        const modalBody = document.getElementById('modalBody');
        if (!modalBody) return;
        
        let touchStartX = 0;
        let touchEndX = 0;
        
        modalBody.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        });
        
        modalBody.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            const dx = touchEndX - touchStartX;
            if (dx > 80) changeDiscussionSlide(-1);
            else if (dx < -80) changeDiscussionSlide(1);
        });
    }
    
    // Open fullscreen
    function openFullscreen(el) {
        if (el.requestFullscreen) el.requestFullscreen();
        else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();
        else if (el.msRequestFullscreen) el.msRequestFullscreen();
    }

    // Remove current file from edit form
    function removeCurrentFile(fileName, index) {
        if (confirm('Are you sure you want to remove this file?')) {
            var currentFiles = $('#edit-updated-files').val();
            var filesArray = currentFiles.split(',');
            
            // Remove the file at the specified index
            filesArray.splice(index, 1);
            
            // Update the hidden input
            $('#edit-updated-files').val(filesArray.join(','));
            
            // Remove the file element from DOM
            $('.current-file-item').eq(index).fadeOut(300, function() {
                $(this).remove();
            });
            
            successMessage('File removed successfully');
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
</script>

@endpush
