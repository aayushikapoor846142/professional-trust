<div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{$pageTitle}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-0">

            <div class="preview-container">
                @switch($fileType)
                    @case('images')
                        <div class="text-center p-3">
                            <img src="{{ $previewUrl }}" class="img-fluid" alt="{{ $filename }}" style="max-height: 80vh; object-fit: contain;">
                        </div>
                        @break
                        
                    @case('documents')
                        @if($extension === 'pdf')
                            <!-- PDF Preview with multiple methods -->
                            <div class="pdf-preview" style="height: 80vh;">
                                <!-- Use stream URL instead of presigned URL -->
                                <iframe src="{{$previewUrl}}" width="100%" height="100%" style="border: none;" type="application/pdf">
                                    <!-- Fallback embed -->
                                    <embed src="{{$previewUrl}}" type="application/pdf" width="100%" height="100%" />
                                </iframe>
                            </div>
                            @php 
                            $file_data = awsFileEncoded(config('awsfilepath.cases') . "/" . $case_id . '/' . $filename);
                            $pdf_thumb = mediaUploadBaseCode("pdf-thumbnail",$file_data['data'],'pdf-images',$filename);
                            @endphp
                            <img src="{{ $pdf_thumb['thumbnail_base64'] }}" />
                        @endif
                        @break
                        
                    @case('videos')
                        <div class="text-center p-3">
                            <video controls style="max-width: 100%; max-height: 80vh;">
                                <source src="{{ $previewUrl }}" type="video/{{ $extension }}">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        @break
                        
                    @case('audio')
                        <div class="p-4">
                            <audio controls style="width: 100%;">
                                <source src="{{ $previewUrl }}" type="audio/{{ $extension === 'mp3' ? 'mpeg' : $extension }}">
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                        @break
                        
                    @case('text')
                        <iframe src="{{ $previewUrl }}" 
                                width="100%" 
                                height="600px" 
                                style="border: none; background: #f8f9fa;"></iframe>
                        @break
                        
                    @case('office')
                        @if(isset($viewerUrls['office']))
                            <div class="office-preview">
                                <ul class="nav nav-tabs" id="viewerTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="office-tab" data-bs-toggle="tab" 
                                           data-bs-target="#office-viewer" role="tab">
                                            <i class="bi bi-microsoft"></i> Microsoft Viewer
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="google-tab" data-bs-toggle="tab" 
                                           data-bs-target="#google-viewer" role="tab">
                                            <i class="bi bi-google"></i> Google Viewer
                                        </a>
                                    </li>
                                </ul>
                                
                                <div class="tab-content" id="viewerTabContent">
                                    <div class="tab-pane fade show active" id="office-viewer" role="tabpanel">
                                        <iframe src="{{ $viewerUrls['office'] }}" 
                                                width="100%" 
                                                height="600px" 
                                                frameborder="0"
                                                style="border: none;">
                                            <p>Your browser does not support iframes. 
                                               <a href="{{ $previewUrl }}" target="_blank">Click here to view the document</a>
                                            </p>
                                        </iframe>
                                    </div>
                                    <div class="tab-pane fade" id="google-viewer" role="tabpanel">
                                        <iframe src="{{ $viewerUrls['google'] }}" 
                                                width="100%" 
                                                height="600px" 
                                                frameborder="0"
                                                style="border: none;">
                                            <p>Your browser does not support iframes. 
                                               <a href="{{ $previewUrl }}" target="_blank">Click here to view the document</a>
                                            </p>
                                        </iframe>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info mt-2 mb-0 small">
                                    <i class="bi bi-info-circle"></i> 
                                    If the preview doesn't load, try switching viewers or 
                                    <a href="{{ $previewUrl }}" target="_blank">open in new tab</a>
                                </div>
                            </div>
                        @endif
                        @break
                        
                    @default
                        <div class="p-4 text-center">
                            <i class="bi bi-file-earmark-x" style="font-size: 4rem; color: #6c757d;"></i>
                            <p class="mt-3">Preview not available for this file type</p>
                            <a href="" 
                               class="CdsTYButton-btn-primary">
                                <i class="bi bi-download"></i> Download File
                            </a>
                        </div>
                @endswitch
            </div>
        </div>
        
        <div class="modal-footer">
            <a href="" 
               class="CdsTYButton-btn-primary">
                <i class="bi bi-download"></i> Download
            </a>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
</div>

<style>
.preview-container {
    min-height: 400px;
    background: #f8f9fa;
}

.pdf-preview {
    background: #525659;
    display: flex;
    align-items: center;
    justify-content: center;
}

.office-preview .nav-tabs {
    background: white;
    padding: 10px 10px 0 10px;
    border-bottom: 1px solid #dee2e6;
}

.office-preview .tab-content {
    background: white;
    min-height: 600px;
}

.modal-xl {
    max-width: 90%;
}

@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
    }
    
    .pdf-preview,
    .office-preview iframe {
        height: 60vh !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle tab switching for office documents
    const viewerTabs = document.getElementById('viewerTabs');
    if (viewerTabs) {
        // Ensure Bootstrap 5 tabs work properly
        const tabLinks = viewerTabs.querySelectorAll('a[data-bs-toggle="tab"]');
        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const tab = new bootstrap.Tab(link);
                tab.show();
            });
        });
    }
    
    // PDF fallback if primary method fails
    const pdfIframe = document.querySelector('.pdf-preview iframe');
    if (pdfIframe) {
        pdfIframe.addEventListener('error', function() {
            console.log('PDF iframe failed, trying alternative method');
            const embed = pdfIframe.querySelector('embed');
            if (embed) {
                embed.style.display = 'block';
            }
        });
    }
});
</script>