
<div class="shared-files-container">
    <div class="shared-files-header">
        <div class="shared-files-title">
            <i class="fas fa-paperclip"></i>
            <span>Shared Files</span>
            <span class="file-count">({{ count($files) }})</span>
        </div>
    </div>
    
    <div class="shared-files-grid">
 
        @foreach($files as $getAttachment)
            @if($getAttachment != NULL)
                @php
                    $get_attachments = $getAttachment->attachment;
                    $attachments = explode(',', $get_attachments);
                @endphp
                
                @foreach($attachments as $attachment)
                    @if($attachment)
                        @php
                            $attachmentLower = strtolower(trim($attachment));
                            $fileExtension = pathinfo($attachment, PATHINFO_EXTENSION);
                            $fileName = pathinfo($attachment, PATHINFO_FILENAME);
                            $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            $isPdf = $fileExtension === 'pdf';
                            $isDocument = in_array($fileExtension, ['doc', 'docx']);
                            $isSpreadsheet = in_array($fileExtension, ['xls', 'xlsx']);
                            $isVideo = in_array($fileExtension, ['mp4', 'avi', 'mov', 'wmv']);
                            $isAudio = in_array($fileExtension, ['mp3', 'wav', 'ogg']);
                        @endphp
                        
                        <div class="shared-file-item" data-file-name="{{ $attachment }}" data-file-type="{{ $fileExtension }}">
                            <div class="file-preview">
                                @if($isImage)
                                    <div class="file-preview-image">
                                        <img src="{{ groupChatDirUrl($attachment, 's') }}" 
                                             alt="{{ $fileName }}" 
                                             class="file-thumbnail"
                                             onclick="previewFile('{{ baseUrl('group-message/preview-file?file_name='.$attachment.'&chat_id='.$getAttachment->unique_id) }}')" />
                                        <div class="file-overlay">
                                            <button class="btn-preview" onclick="previewFile('{{ baseUrl('group-message/preview-file?file_name='.$attachment.'&chat_id='.$getAttachment->unique_id) }}')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                @elseif($isPdf)
                                    <div class="file-preview-pdf">
                                        <div class="pdf-icon">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div class="file-overlay">
                                            <button class="btn-preview" onclick="previewFile('{{ baseUrl('group-message/preview-file?file_name='.$attachment.'&chat_id='.$getAttachment->unique_id) }}')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                @elseif($isDocument)
                                    <div class="file-preview-document">
                                        <div class="document-icon">
                                            <i class="fas fa-file-word"></i>
                                        </div>
                                        <div class="file-overlay">
                                            <button class="btn-preview" onclick="previewFile('{{ baseUrl('group-message/preview-file?file_name='.$attachment.'&chat_id='.$getAttachment->unique_id) }}')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                @elseif($isSpreadsheet)
                                    <div class="file-preview-spreadsheet">
                                        <div class="spreadsheet-icon">
                                            <i class="fas fa-file-excel"></i>
                                        </div>
                                        <div class="file-overlay">
                                            <button class="btn-preview" onclick="previewFile('{{ baseUrl('group-message/preview-file?file_name='.$attachment.'&chat_id='.$getAttachment->unique_id) }}')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                @elseif($isVideo)
                                    <div class="file-preview-video">
                                        <div class="video-icon">
                                            <i class="fas fa-file-video"></i>
                                        </div>
                                        <div class="file-overlay">
                                            <button class="btn-preview" onclick="previewFile('{{ baseUrl('group-message/preview-file?file_name='.$attachment.'&chat_id='.$getAttachment->unique_id) }}')">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </div>
                                    </div>
                                @elseif($isAudio)
                                    <div class="file-preview-audio">
                                        <div class="audio-icon">
                                            <i class="fas fa-file-audio"></i>
                                        </div>
                                        <div class="file-overlay">
                                            <button class="btn-preview" onclick="previewFile('{{ baseUrl('group-message/preview-file?file_name='.$attachment.'&chat_id='.$getAttachment->unique_id) }}')">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="file-preview-generic">
                                        <div class="generic-icon">
                                            <i class="fas fa-file"></i>
                                        </div>
                                        <div class="file-overlay">
                                            <button class="btn-preview" onclick="previewFile('{{ baseUrl('group-message/preview-file?file_name='.$attachment.'&chat_id='.$getAttachment->unique_id) }}')">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="file-info">
                                <div class="file-name" title="{{ $attachment }}">
                                    {{ strlen($fileName) > 20 ? substr($fileName, 0, 20) . '...' : $fileName }}
                                </div>
                                <div class="file-meta">
                                    <span class="file-extension">{{ strtoupper($fileExtension) }}</span>
                                    <span class="file-time">{{ \Carbon\Carbon::parse($getAttachment->created_at)->diffForHumans() }}</span>
                                </div>
                            </div>
                            
                            <div class="file-actions">
                                <button class="btn-download" onclick="downloadFile('{{ baseUrl('group-message/download-file?file_name='.$attachment.'&chat_id='.$getAttachment->unique_id) }}')" title="Download">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="btn-share" onclick="shareFile('{{ $attachment }}')" title="Share">
                                    <i class="fas fa-share-alt"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        @endforeach
    </div>
</div>

<script>
function previewFile(url) {
    // Open file preview in popup or modal
    if (typeof showPopup === 'function') {
        showPopup(url);
    } else {
        window.open(url, '_blank');
    }
}

function downloadFile(url) {
    // Download file
    const link = document.createElement('a');
    link.href = url;
    link.download = '';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function shareFile(fileName) {
    // Share file functionality
    if (navigator.share) {
        navigator.share({
            title: 'Shared File',
            text: 'Check out this file: ' + fileName,
            url: window.location.href
        });
    } else {
        // Fallback for browsers that don't support Web Share API
        const textArea = document.createElement('textarea');
        textArea.value = window.location.href;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        // Show success message
        showNotification('File link copied to clipboard!', 'success');
    }
}

function viewAllFiles() {
    // Show all files in a modal or expand the grid
    const grid = document.querySelector('.shared-files-grid');
    grid.classList.toggle('expanded');
    
    const btn = document.querySelector('.btn-view-all');
    if (grid.classList.contains('expanded')) {
        btn.innerHTML = '<i class="fas fa-compress"></i> Collapse';
    } else {
        btn.innerHTML = '<i class="fas fa-eye"></i> View All';
    }
}

function loadMoreFiles() {
    // Load more files functionality
    // This would typically make an AJAX call to load more files
    showNotification('Loading more files...', 'info');
}

function showNotification(message, type = 'info') {
    // Simple notification function
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>