<!-- Selection Bar -->
<div class="CdsDashboardCaseDocuments-list-view-selection-bar">
    <span class="CdsDashboardCaseDocuments-list-view-selection-count">0 Selected</span>
    <div class="CdsDashboardCaseDocuments-list-view-selection-actions">
        <button class="CdsDashboardCaseDocuments-list-view-delete-btn" onclick="deleteSelectedFiles('{{ $folder_id }}')">
            <i class="fa fa-trash"></i> Delete
        </button>
        <!-- <button class="CdsDashboardCaseDocuments-list-view-upload-btn" 
                onclick="showPopup('{{ baseUrl('case-with-professionals/documents/upload/'.$case_id.'/'.$folder_id) }}')">
            <i class="fa fa-upload"></i> Upload
        </button> -->
    </div>
</div>
<div class="CDSCaseDocument-uploader" id="CDSCaseDocument-uploader-{{ $folder_id }}">
    <div class="CDSFeed-upload-container" id="feedMediaUpload-{{ $folder_id }}">
        <div class="CDSFeed-upload-area">
            <input type="file" class="CDSFeed-file-input" multiple accept="image/*,.pdf,.doc,.docx" style="display: none;">
            <div class="CDSFeed-upload-icon">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
            </div>
            <p class="CDSFeed-upload-text">Drag and drop files here or click to browse</p>
            <p class="CDSFeed-upload-hint">Supports: JPG, PNG, GIF, PDF, DOC, DOCX (Max 10MB per file)</p>
        </div>
        
        <!-- File Preview Area -->
        <div class="CDSFeed-file-list" style="display: none;">
            <!-- Files will be dynamically added here -->
        </div>
    </div>
</div>
<!-- Check All Row -->
<div class="CdsDashboardCaseDocuments-list-view-check-all-row">
    <label class="CdsDashboardCaseDocuments-list-view-check-all-label">
         {!! FormHelper::formCheckbox([
    'id' => 'check-all-' . $folder_id,
    'checkbox_class' => 'datatableCheckAll',
    'data_attr' => 'data-folder-id=' . $folder_id . ''
]) !!}
        Check All
    </label>
</div>

<!-- File List Container -->
<div class="file-sortable-container" data-group="{{ $folder_id }}" data-type="{{ $type }}">
    @if($case_documents && $case_documents->count() > 0)
        @foreach($case_documents as $document)
        <div class="CdsDashboardCaseDocuments-list-view-file-item sortable-item" data-id="{{ $document->unique_id }}">

                @if($document->added_by == auth()->user()->id)
            <div class="CdsDashboardCaseDocuments-list-view-file-checkbox">
                  @if($document->is_encrypted != 1)
                <input type="checkbox" class="CdsDashboardCaseDocuments-list-view-checkbox row-checkbox" value="{{ $document->unique_id }}">
                          @endif
            </div>
            @else
              <div class="CdsDashboardCaseDocuments-list-view-file-checkbox ps-1">
               
            </div>
            @endif

            <div class="CdsDashboardCaseDocuments-list-view-file-icon-small {{ getFileIconClass($document->file_name) }}">
                {{ getFileIcon($document->file_name) }}
            </div>
            
            <div class="CdsDashboardCaseDocuments-list-view-file-content">
                <div class="CdsDashboardCaseDocuments-list-view-file-name fileTitle">
                    {{ $document->file_name }}
                    
                </div>
                <div class="CdsDashboardCaseDocuments-list-view-file-meta">
                    Added on {{ \Carbon\Carbon::parse($document->created_at)->format('M d, Y') }}<br>
                    Added By: {{ $document->user->first_name ?? '' }} {{ $document->user->last_name ?? '' }}<br>
                     <span class="CdsDashboardCaseDocuments-unread-badge">{{documentUnreadComment($document->id)}}</span>
                    @if($document->file_size)
                    <br>Size: {{ formatFileSize($document->file_size) }}
                    @endif
                </div>
            </div>
              @if($document->is_encrypted != 1)
            <div style="position: relative;">
                <button class="CdsDashboardCaseDocuments-list-view-more-btn" onclick="toggleDropdown(this)">
                    More <i class="fa fa-chevron-down"></i>
                </button>
                <div class="CdsDashboardCaseDocuments-list-view-dropdown">
                    <div class="CdsDashboardCaseDocuments-list-view-dropdown-item" 
                         onclick="cdsCaseDocumentOpenPreview(this)" data-href="{{ baseUrl('case-with-professionals/view-case-document/'.$case_id.'/'.$folder_id.'/'.$document->unique_id) }}">
                        <i class="fa fa-eye"></i> View
                    </div>
                    <div class="CdsDashboardCaseDocuments-list-view-dropdown-item">
                         <a href="{{ baseUrl('case-with-professionals/download-documents') }}?case_id={{ $case_id }}&file={{  $document->file_name }}">
                        <i class="fa fa-download"></i> Download
                         </a>
                    </div>
                    @if($document->added_by == auth()->user()->id)
                    <div class="CdsDashboardCaseDocuments-list-view-dropdown-item" onclick="showPopupRename('{{ $document->unique_id }}', '{{ trim($document->file_name) }}')">
                        <i class="fa fa-edit"></i> Rename
                    </div>
                    <div class="CdsDashboardCaseDocuments-list-view-dropdown-divider"></div>
                    <div class="CdsDashboardCaseDocuments-list-view-dropdown-item" 
                         style="color: #dc3545;"
                         data-href="{{ baseUrl('case-with-professionals/delete-document/'.$document->unique_id) }}"
                         onclick="confirmAnyAction(this)"
                         data-action="Delete">
                        <i class="fa fa-trash"></i> Delete
                    </div>
                    @endif
                </div>
            </div>
             @else
             <span class="badge bg-warning text-dark">Encrypted</span>
             @endif
            
            <span class="drag-handle" style="cursor: move; margin-left: 10px;">
                <i class="fa fa-arrows"></i>
            </span>
        </div>
        @endforeach
    @else
        <div class="CdsDashboardCaseDocuments-list-view-empty-folder">
            No files in this folder. Click "Upload" to add files.
        </div>
    @endif
</div>
@push("document_scripts")
<script>
// Initialize file upload manager by ID
var feedUploader_{{ $folder_id }} = new FileUploadManager('#feedMediaUpload-{{ $folder_id }}', {
    maxFileSize: 10 * 1024 * 1024, // 10MB
    maxFiles: 5, // Maximum 5 files
    allowedTypes: [
        'image/jpeg', 
        'image/png', 
        'image/gif', 
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/csv',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'

    ],
    autoUpload: true,
    // Upload handler function
    onUpload: async function(file, progressCallback, fileData) {
        // This is your external upload function
        // You can use XMLHttpRequest, fetch, or any upload library
        
        const formData = new FormData();
        formData.append('file', file);
        formData.append("type","{{ $type }}");
        formData.append("case_id","{{ $case_id }}");
        formData.append('_token', csrf_token);
        formData.append('folder_id', '{{ $folder_id }}');
        
        // Example using fetch with progress tracking
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            
            // Track upload progress
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    progressCallback(percentComplete);
                }
            });
            
            xhr.addEventListener('load', () => {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        resolve(response);
                    } catch (e) {
                        reject(new Error('Invalid response'));
                    }
                } else {
                    console.log(xhr);
                    var error = JSON.parse(xhr.response);
                    reject(new Error(`${error.message}`));
                }
            });
            
            xhr.addEventListener('error', () => {
                reject(new Error('Network error'));
            });
            
            xhr.open('POST', "{{ baseUrl('case-with-professionals/save-document') }}");
            xhr.send(formData);
        });
    },
    onFileAdded: function(fileData) {
        console.log('File added:', fileData.name);
    },
    onFileRemoved: function(fileData) {
        console.log('File removed:', fileData.name);
    },
    onUploadProgress: function(fileData, progress) {
        console.log(`Uploading ${fileData.name}: ${progress}%`);
    },
    
    onUploadComplete: function(fileData, result) {
        console.log('Upload completed:', fileData.name, result);
        // You can store the server response (like file ID) here
    },
    
    onUploadError: function(fileData, error) {
        errorMessage(`${error.message}`);
    },
    onError: function(message) {
        // Custom error handling
        errorMessage(message);
    }
});
// Initialize the uploader
feedUploader_{{ $folder_id }}.init();
// Toggle dropdown menu
function toggleDropdown(button) {
    event.stopPropagation();
    
    // Close all other dropdowns
    document.querySelectorAll('.CdsDashboardCaseDocuments-list-view-more-btn').forEach(btn => {
        if (btn !== button) btn.classList.remove('active');
    });
    
    button.classList.toggle('active');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function() {
    document.querySelectorAll('.CdsDashboardCaseDocuments-list-view-more-btn').forEach(btn => {
        btn.classList.remove('active');
    });
});

// View document
// function viewDocument(url) {
//     window.open(url, '_blank');
// }



// Initialize folder-specific check all
 document.querySelectorAll('.datatableCheckAll').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const folderId = this.dataset.folderId;
            const checkboxes = document.querySelectorAll(`#folder-documents-${folderId} .row-checkbox`);
            checkboxes.forEach(cb => cb.checked = this.checked);

            // Call folder-specific update function if it exists
            const updateFn = window[`updateSelectionCount${folderId}`];
            if (typeof updateFn === 'function') updateFn();
        });
    });

// Update selection count for this folder
function updateSelectionCount{{ $folder_id }}() {
    const checkboxes = document.querySelectorAll('#folder-documents-{{ $folder_id }} .row-checkbox:checked');
    const count = checkboxes.length;
    document.querySelector('#folder-documents-{{ $folder_id }} .CdsDashboardCaseDocuments-list-view-selection-count').textContent = count + ' Selected';
}

// Initialize checkbox listeners
document.querySelectorAll('#folder-documents-{{ $folder_id }} .row-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        updateSelectionCount{{ $folder_id }}();
    });
});
</script>
@endpush