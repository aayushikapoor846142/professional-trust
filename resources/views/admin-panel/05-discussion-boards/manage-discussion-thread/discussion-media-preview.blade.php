<div class="CdsDocumentPreview-modal">
    <div class="CdsDocumentPreview-content">
        <div class="CdsDocumentPreview-fileInfo">
            <div class="CdsDocumentPreview-fileDetails">
                <span class="CdsDocumentPreview-fileName" id="cdsDocumentFileName">Document.pdf</span>
                <div class="CdsDocumentPreview-fileMeta">
                    <span class="CdsDocumentPreview-fileType" id="cdsDocumentFileType">PDF</span>
                </div>
            </div>
            <div class="CdsDocumentPreview-actions">
                <button class="CdsDocumentPreview-actionBtn" onclick="cdsDocumentDownload()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    <span>Download</span>
                </button>
            </div>
        </div>
        
        <div class="CdsDocumentPreview-previewWrapper">
            <div class="CdsDocumentPreview-previewContainer">
                <button class="CdsDocumentPreview-nav CdsDocumentPreview-prevBtn" 
                        onclick="cdsDocumentNavigate('prev')" 
                        id="cdsDocumentPrevBtn" 
                        disabled>
                    <svg viewBox="0 0 24 24">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                </button>
                
                <button class="CdsDocumentPreview-nav CdsDocumentPreview-nextBtn" 
                        onclick="cdsDocumentNavigate('next')" 
                        id="cdsDocumentNextBtn">
                    <svg viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </button>
                
                <div class="CdsDocumentPreview-previewContent" id="cdsDocumentPreviewContent">
                    <!-- Dynamic content -->
                </div>
                
                <div class="CdsDocumentPreview-loading" id="cdsDocumentLoading">
                    <div class="CdsDocumentPreview-spinner"></div>
                </div>
            </div>
            
            <div class="CdsDocumentPreview-thumbnailSection" id="cdsDocumentThumbnailSection">
                <button class="CdsDocumentPreview-thumbNav CdsDocumentPreview-thumbPrev" 
                        onclick="cdsDocumentScrollThumbs('left')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                </button>
                <button class="CdsDocumentPreview-thumbNav CdsDocumentPreview-thumbNext" 
                        onclick="cdsDocumentScrollThumbs('right')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </button>
                <div class="CdsDocumentPreview-thumbnailWrapper">
                    <div class="CdsDocumentPreview-thumbnailTrack" id="cdsDocumentThumbnailTrack">
                        <!-- Thumbnails will be generated here -->
                    </div>
                </div>
            </div>
        </div>
        
        <div class="CdsDocumentPreview-bottomControls">
            <button class="CdsDocumentPreview-toggleThumb" id="cdsDocumentToggleThumb" onclick="cdsDocumentToggleThumbnails()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
                <span>Thumbnails</span>
            </button>
            <button class="CdsDocumentPreview-close" onclick="cdsDocumentClosePreview()">
                Close Preview
            </button>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="CdsDocumentPreview-toast" id="cdsDocumentToast"></div>

<script>
    // Initialize variables
    var cdsDocumentCurrentIndex = {{ $current_file_index ?? 0 }};
    var cdsDocumentFiles = {!! $files_arr !!};
    var cdsDocumentThumbnailsVisible = true;
    var cdsDocumentCaseId = "";
    var cdsDocumentCurrentAttachment = null;
    
    // Initialize on load
    cdsDocumentInit();
    
    // Initialize
    async function cdsDocumentInit() {
        await cdsDocumentLoadFile(cdsDocumentCurrentIndex);
       $("#cdsCaseDocumentPreviewOverlay").addClass("CdsCaseDocumentPreview-active");
        cdsDocumentGenerateThumbnails();
        cdsDocumentUpdateNavigation();
    }
    
    // Toggle thumbnails
    function cdsDocumentToggleThumbnails() {
        var section = document.getElementById('cdsDocumentThumbnailSection');
        var button = document.getElementById('cdsDocumentToggleThumb');
        
        cdsDocumentThumbnailsVisible = !cdsDocumentThumbnailsVisible;
        
        if (cdsDocumentThumbnailsVisible) {
            section.classList.remove('CdsDocumentPreview-hidden');
            button.classList.remove('CdsDocumentPreview-active');
        } else {
            section.classList.add('CdsDocumentPreview-hidden');
            button.classList.add('CdsDocumentPreview-active');
        }
    }
    
    // Close preview modal
    function cdsDocumentClosePreview() {
        
        // If loaded via AJAX in a modal
        $("#cdsDiscussionPreviewOverlay").html('');
        $("#cdsDiscussionPreviewOverlay").removeClass("CdsCaseDocumentPreview-active");
        // if (typeof closeModal === 'function') {
        //     closeModal();
        // } else if (window.parent !== window) {
        //     // If in iframe
        //     window.parent.postMessage('closePreview', '*');
        // } else {
        //     // Navigate back
        //     window.history.back();
        // }
    }

    // Load file
    function cdsDocumentLoadFile(index) {
        if (cdsDocumentFiles[index] === undefined) {
            return false;
        }
        
        var file = cdsDocumentFiles[index];
        var previewContent = document.getElementById('cdsDocumentPreviewContent');
        var loading = document.getElementById('cdsDocumentLoading');

        // Show loading
        loading.classList.add('CdsDocumentPreview-show');

        // Update file info
        document.getElementById('cdsDocumentFileName').textContent = file.name;
        document.getElementById('cdsDocumentFileType').textContent = file.type.toUpperCase();

        // Load content with a slight delay for better UX
        setTimeout(() => {
            var content = '';
            
            switch (file.type) {
                case 'image':
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                case 'bmp':
                case 'webp':
                case 'svg':
                    content = `<img src="${file.url}" class="CdsDocumentPreview-imagePreview" alt="${file.name}" onload="cdsDocumentHideLoading()" onerror="cdsDocumentHandleImageError(this)">`;
                    break;
                    
                case 'pdf':
                    content = `
                        <iframe src="${file.url}" class="CdsDocumentPreview-documentPreview" 
                                onload="cdsDocumentHideLoading()"
                                onerror="cdsDocumentHandlePdfError()"></iframe>
                    `;
                    break;
                    
                case 'doc':
                case 'docx':
                case 'xls':
                case 'xlsx':
                case 'ppt':
                case 'pptx':
                    // Try Office Online viewer
                    var officeViewerUrl = 'https://view.officeapps.live.com/op/embed.aspx?src=' + encodeURIComponent(file.url);
                    content = `
                        <iframe src="${officeViewerUrl}" class="CdsDocumentPreview-documentPreview" 
                                onload="cdsDocumentHideLoading()"
                                onerror="cdsDocumentHandleOfficeError('${file.type}')"></iframe>
                    `;
                    break;
                    
                default:
                    content = `
                        <div class="CdsDocumentPreview-errorPreview">
                            <div class="CdsDocumentPreview-errorIcon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                </svg>
                            </div>
                            <p class="CdsDocumentPreview-errorText">Preview not available for ${file.type.toUpperCase()} files</p>
                            <button class="CdsDocumentPreview-actionBtn" onclick="cdsDocumentDownload()">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                Download File
                            </button>
                        </div>
                    `;
                    cdsDocumentHideLoading();
            }

            previewContent.innerHTML = content;
            
            // For non-iframe content, hide loading immediately
            if (!content.includes('iframe') && !content.includes('img')) {
                cdsDocumentHideLoading();
            }

            // Update active thumbnail
            cdsDocumentUpdateActiveThumbnail(index);

            // Load comments
            // cdsDocumentLoadComments(file);
        }, 300);
    }
    
    // Hide loading
    function cdsDocumentHideLoading() {
        var loading = document.getElementById('cdsDocumentLoading');
        loading.classList.remove('CdsDocumentPreview-show');
    }
    
    // Handle image error
    function cdsDocumentHandleImageError(img) {
        cdsDocumentHideLoading();
        img.parentElement.innerHTML = `
            <div class="CdsDocumentPreview-errorPreview">
                <div class="CdsDocumentPreview-errorIcon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                </div>
                <p class="CdsDocumentPreview-errorText">Unable to load image</p>
            </div>
        `;
    }
    
    // Handle PDF error
    function cdsDocumentHandlePdfError() {
        cdsDocumentHideLoading();
        document.getElementById('cdsDocumentPreviewContent').innerHTML = `
            <div class="CdsDocumentPreview-errorPreview">
                <div class="CdsDocumentPreview-errorIcon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                </div>
                <p class="CdsDocumentPreview-errorText">Unable to load PDF preview</p>
                <button class="CdsDocumentPreview-actionBtn" onclick="cdsDocumentDownload()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Download PDF
                </button>
            </div>
        `;
    }
    
    // Handle Office error
    function cdsDocumentHandleOfficeError(fileType) {
        cdsDocumentHideLoading();
        document.getElementById('cdsDocumentPreviewContent').innerHTML = `
            <div class="CdsDocumentPreview-errorPreview">
                <div class="CdsDocumentPreview-errorIcon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                </div>
                <p class="CdsDocumentPreview-errorText">${fileType.toUpperCase()} preview requires document viewer</p>
                <button class="CdsDocumentPreview-actionBtn" onclick="cdsDocumentDownload()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Download ${fileType.toUpperCase()}
                </button>
            </div>
        `;
    }

    // Generate thumbnails
    function cdsDocumentGenerateThumbnails() {
        var track = document.getElementById('cdsDocumentThumbnailTrack');
        track.innerHTML = '';

        cdsDocumentFiles.forEach((file, index) => {
            var thumb = document.createElement('div');
            thumb.className = 'CdsDocumentPreview-thumbnail';
            thumb.onclick = () => {
                cdsDocumentCurrentIndex = index;
                cdsDocumentLoadFile(index);
                cdsDocumentUpdateNavigation();
            };

            // Check if file has a thumbnail (images and PDFs)
            if (file.thumbnail) {
                thumb.innerHTML = `
                    <img src="${file.thumbnail}" class="CdsDocumentPreview-thumbnailImage" alt="${file.name}">
                    <div class="CdsDocumentPreview-thumbnailLabel">${file.name}</div>
                `;
            } else {
                // Generate icon based on file type
                var iconSvg = cdsDocumentGetFileIcon(file.type);
                thumb.innerHTML = `
                    <div class="CdsDocumentPreview-thumbnailIcon">${iconSvg}</div>
                    <div class="CdsDocumentPreview-thumbnailLabel">${file.name}</div>
                `;
            }

            track.appendChild(thumb);
        });
    }
    
    // Get file icon
    function cdsDocumentGetFileIcon(type) {
        var icons = {
            'pdf': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
            'doc': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M9 13h6"/><path d="M9 17h6"/></svg>',
            'docx': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M9 13h6"/><path d="M9 17h6"/></svg>',
            'xls': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><rect x="8" y="12" width="8" height="6"/><line x1="12" y1="12" x2="12" y2="18"/><line x1="8" y1="15" x2="16" y2="15"/></svg>',
            'xlsx': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><rect x="8" y="12" width="8" height="6"/><line x1="12" y1="12" x2="12" y2="18"/><line x1="8" y1="15" x2="16" y2="15"/></svg>',
            'ppt': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
            'pptx': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
            'default': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>'
        };
        
        return icons[type] || icons['default'];
    }

    // Update active thumbnail
    function cdsDocumentUpdateActiveThumbnail(index) {
        var thumbs = document.querySelectorAll('.CdsDocumentPreview-thumbnail');
        thumbs.forEach((thumb, i) => {
            if (i === index) {
                thumb.classList.add('CdsDocumentPreview-active');
                // Scroll to center
                thumb.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            } else {
                thumb.classList.remove('CdsDocumentPreview-active');
            }
        });
    }

    // Navigate files
    function cdsDocumentNavigate(direction) {
        if (direction === 'prev' && cdsDocumentCurrentIndex > 0) {
            cdsDocumentCurrentIndex--;
        } else if (direction === 'next' && cdsDocumentCurrentIndex < cdsDocumentFiles.length - 1) {
            cdsDocumentCurrentIndex++;
        }

        cdsDocumentLoadFile(cdsDocumentCurrentIndex);
        cdsDocumentUpdateNavigation();
    }

    // Update navigation buttons
    function cdsDocumentUpdateNavigation() {
        var prevBtn = document.getElementById('cdsDocumentPrevBtn');
        var nextBtn = document.getElementById('cdsDocumentNextBtn');

        prevBtn.disabled = cdsDocumentCurrentIndex === 0;
        nextBtn.disabled = cdsDocumentCurrentIndex === cdsDocumentFiles.length - 1;
    }

    // Scroll thumbnails
    function cdsDocumentScrollThumbs(direction) {
        var wrapper = document.querySelector('.CdsDocumentPreview-thumbnailWrapper');
        var scrollAmount = 110; // thumbnail width + gap

        if (direction === 'left') {
            wrapper.scrollLeft -= scrollAmount;
        } else {
            wrapper.scrollLeft += scrollAmount;
        }
    }

    
    // Generate avatar color based on name
    function cdsDocumentGetAvatarColor(name) {
        const colors = [
            '#5b4be7', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6',
            '#ec4899', '#14b8a6', '#f97316', '#06b6d4', '#84cc16'
        ];
        let hash = 0;
        for (let i = 0; i < name.length; i++) {
            hash = name.charCodeAt(i) + ((hash << 5) - hash);
        }
        return colors[Math.abs(hash) % colors.length];
    }

    // Get initials from name
    function cdsDocumentGetInitials(name) {
        const parts = name.split(' ');
        if (parts.length >= 2) {
            return parts[0][0] + parts[parts.length - 1][0];
        }
        return name.substring(0, 2).toUpperCase();
    }

    // Format date
    function cdsDocumentFormatDate(dateString) {
        if (!dateString || dateString === 'Just now') return 'Just now';
        
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        
        // Less than a minute
        if (diff < 60000) return 'Just now';
        
        // Less than an hour
        if (diff < 3600000) {
            const minutes = Math.floor(diff / 60000);
            return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
        }
        
        // Less than a day
        if (diff < 86400000) {
            const hours = Math.floor(diff / 3600000);
            return `${hours} hour${hours > 1 ? 's' : ''} ago`;
        }
        
        // Less than a week
        if (diff < 604800000) {
            const days = Math.floor(diff / 86400000);
            return `${days} day${days > 1 ? 's' : ''} ago`;
        }
        
        // Format as date
        return date.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined 
        });
    }

    // function cdsDocumentLoadComments(comments) {
    //     var commentsBody = document.getElementById('cdsDocumentCommentsBody');
        
    //     if (!comments || comments.length === 0) {
    //         commentsBody.innerHTML = `
    //             <div class="CdsDocumentPreview-commentsEmpty">
    //                 <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
    //                     <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    //                 </svg>
    //                 <p>No comments yet</p>
    //                 <p style="font-size: 12px; margin-top: 8px;">Be the first to comment</p>
    //             </div>
    //         `;
    //         return;
    //     }
        
    //     commentsBody.innerHTML = comments.map(comment => {
    //         const initials = cdsDocumentGetInitials(comment.author);
    //         const avatarColor = cdsDocumentGetAvatarColor(comment.author);
    //         const formattedDate = cdsDocumentFormatDate(comment.created_at || comment.time);
            
    //         let attachmentHtml = '';
    //         if (comment.attachment) {
    //             attachmentHtml = `
    //                 <div class="CdsDocumentPreview-commentAttachment" onclick="cdsDocumentDownloadAttachment('${comment.attachment.url}', '${comment.attachment.name}')">
    //                     <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
    //                         <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
    //                         <polyline points="14 2 14 8 20 8"/>
    //                     </svg>
    //                     <div class="CdsDocumentPreview-attachmentInfo">
    //                         <div class="CdsDocumentPreview-attachmentFileName">${comment.attachment.name}</div>
    //                         <div class="CdsDocumentPreview-attachmentSize">${comment.attachment.size || 'File'}</div>
    //                     </div>
    //                 </div>
    //             `;
    //         }
            
    //         return `
    //             <div class="CdsDocumentPreview-comment">
    //                 <div class="CdsDocumentPreview-commentAvatar" style="background-color: ${avatarColor}">
    //                     ${comment.avatar ? `<img src="${comment.avatar}" alt="${comment.author}">` : initials}
    //                 </div>
    //                 <div class="CdsDocumentPreview-commentContent">
    //                     <div class="CdsDocumentPreview-commentHeader">
    //                         <span class="CdsDocumentPreview-commentAuthor">${comment.author}</span>
    //                         <span class="CdsDocumentPreview-commentDate">${formattedDate}</span>
    //                     </div>
    //                     <div class="CdsDocumentPreview-commentText">${comment.text}</div>
    //                     ${attachmentHtml}
    //                 </div>
    //             </div>
    //         `;
    //     }).join('');
    // }

    // Handle attachment selection
    function cdsDocumentHandleAttachment(event) {
        const file = event.target.files[0];
        if (!file) return;
        
        // Check file size (limit to 10MB)
        if (file.size > 10 * 1024 * 1024) {
            cdsDocumentShowToast('File size must be less than 10MB', 'error');
            return;
        }
        
        cdsDocumentCurrentAttachment = file;
        
        // Show preview
        const preview = document.getElementById('cdsDocumentAttachmentPreview');
        const nameSpan = preview.querySelector('.CdsDocumentPreview-attachmentName');
        nameSpan.textContent = file.name;
        preview.style.display = 'block';
    }

    // Remove attachment
    function cdsDocumentRemoveAttachment() {
        cdsDocumentCurrentAttachment = null;
        document.getElementById('cdsDocumentAttachmentInput').value = '';
        document.getElementById('cdsDocumentAttachmentPreview').style.display = 'none';
    }

    // Format file size
    function cdsDocumentFormatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }


  

    // Download attachment
    function cdsDocumentDownloadAttachment(url, filename) {
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        link.target = '_blank';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Enhanced toast notification
    function cdsDocumentShowToast(message, type = 'success') {
        var toast = document.getElementById('cdsDocumentToast');
        toast.textContent = message;
        toast.className = 'CdsDocumentPreview-toast';
        
        if (type === 'error') {
            toast.style.backgroundColor = '#ef4444';
        } else {
            toast.style.backgroundColor = '#10b981';
        }
        
        toast.classList.add('CdsDocumentPreview-show');
        
        setTimeout(() => {
            toast.classList.remove('CdsDocumentPreview-show');
        }, 3000);
    }

    // Download file
    function cdsDocumentDownload() {
        var file = cdsDocumentFiles[cdsDocumentCurrentIndex];
        // Create a temporary link and trigger download
        var link = document.createElement('a');
        link.href = file.download_url;
        link.download = file.name;
        link.target = '_blank';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        cdsDocumentShowToast(`Downloading ${file.name}...`);
    }

    // Share file
    function cdsDocumentShare() {
        var file = cdsDocumentFiles[cdsDocumentCurrentIndex];
        
        // Copy URL to clipboard
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(file.url).then(function() {
                cdsDocumentShowToast('File URL copied to clipboard!');
            }, function() {
                cdsDocumentShowToast('Unable to copy URL');
            });
        } else {
            // Fallback for older browsers
            var textArea = document.createElement("textarea");
            textArea.value = file.url;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            document.body.appendChild(textArea);
            textArea.select();
            try {
                document.execCommand('copy');
                cdsDocumentShowToast('File URL copied to clipboard!');
            } catch (err) {
                cdsDocumentShowToast('Unable to copy URL');
            }
            document.body.removeChild(textArea);
        }
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            cdsDocumentNavigate('prev');
        } else if (e.key === 'ArrowRight') {
            cdsDocumentNavigate('next');
        } else if (e.key === 'Escape') {
            cdsDocumentClosePreview();
        }
    });
</script>