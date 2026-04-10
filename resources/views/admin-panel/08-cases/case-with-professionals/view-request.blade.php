@extends('admin-panel.08-cases.case-with-professionals.my-cases-master')
<link rel="stylesheet" href="{{ asset('assets/css/custom-file-upload.css') }}">

@section('case-container')
<div class="CdsCaseRequest-layout">

    {{-- LEFT SIDEBAR --}}
    <aside class="CdsCaseRequest-sidebar-left">
        <div class="CdsCaseRequest-case-manager">
            <div class="CdsCaseRequest-manager-header">
                <div class="CdsCaseRequest-manager-avatar"> {{ strtoupper(substr($record->userAdded->first_name, 0, 1)) }}{{ strtoupper(substr($record->userAdded->last_name, 0, 1)) }}</div>
                <div class="CdsCaseRequest-manager-info">
                    <h3>{{ $record->userAdded->first_name }} {{ $record->userAdded->last_name }}</h3>
                    <p>{{$record->userAdded->role}}</p>
                </div>
            </div>
            <div class="CdsCaseRequest-status-pill">
                <span style="display:inline-block; width:8px; height:8px; background:white; border-radius:50%; animation: blink 1.5s infinite;"></span>
                {{ ucfirst($record->status) }}
            </div>
        </div>

        <div class="CdsCaseRequest-case-details">
            <div class="CdsCaseRequest-detail-item">
                <div class="CdsCaseRequest-detail-label">Case ID</div>
                <div class="CdsCaseRequest-detail-value">{{ $record->cases->unique_id ?? 'N/A' }}</div>
            </div>
            <div class="CdsCaseRequest-detail-item">
                <div class="CdsCaseRequest-detail-label">Requested On</div>
                <div class="CdsCaseRequest-detail-value">{{ dateFormat($record->created_at) }}</div>
            </div>
            <div class="CdsCaseRequest-detail-item">
                <div class="CdsCaseRequest-detail-label">Updated On</div>
                <div class="CdsCaseRequest-detail-value">{{ $record->updated_at ? dateFormat($record->updated_at) : 'N/A' }}</div>
            </div>
            <div class="CdsCaseRequest-detail-item">
                <div class="CdsCaseRequest-detail-label">Submitted By</div>
                <div class="CdsCaseRequest-detail-value">{{ $record->cases->userAdded->first_name ?? 'N/A' }}</div>
            </div>
            <div class="CdsCaseRequest-detail-item">
                <div class="CdsCaseRequest-detail-label">Completed On</div>
                <div class="CdsCaseRequest-detail-value">{{ $record->completed_at ? dateFormat($record->completed_at) : 'Pending' }}</div>
            </div>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="CdsCaseRequest-main-content">
        <div class="CdsCaseRequest-content-card">
            <h1 class="CdsCaseRequest-case-title">{{ $record->title }}</h1>
            <p class="CdsCaseRequest-case-subtitle">{!! $record->message_body ?? 'No message provided.' !!}</p>

            {{-- Conditional Sections --}}
            @if($record->request_type == 'assesment-form-request' || $record->request_type == 'document-request')
                @if($record->reply != '')
                    <a href="{{ baseUrl('case-with-professionals/view-request-form/'.$record->unique_id) }}" class="btn btn-warning mt-3">View Submitted Form</a>
                @else
                    <p class="text-danger mt-3"><strong>Not Submitted</strong></p>
                @endif
            @endif

            @if($record->request_type == "information-request")
                <p class="mt-4">{!! $record->additional_detail ?? '' !!}</p>
            @endif

            {{-- Attachments --}}
            @if($record->attachment != '')
                <div class="mt-4 w-full">
                    <h5>Attachments</h5>
                    @foreach(explode(',', $record->attachment) as $value)
                        <a class="btn btn-outline-primary btn-sm mt-2"
                           href="{{ baseUrl('/case-with-professionals/download-request-attachment?file='.$value) }}" 
                           download>
                            <img src="{{ url('assets/svg/docusign-g.svg') }}" alt="Doc" style="width: 16px;" />
                            {{ $value }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </main>

    {{-- RIGHT SIDEBAR: COMMENTS --}}
    <aside class="CdsCaseRequest-sidebar-right">
        <div class="CdsCaseRequest-comments-section">
            <div class="CdsCaseRequest-comments-header">
                <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" /></svg>
                <h3>Comments</h3>
            </div>

            {{-- Existing Comments --}}
            <div class="CdsCaseRequest-comments-thread" id="case-comments">
                @include('admin-panel.08-cases.case-with-professionals.partials.case_request_comments')
            </div>

            {{-- Add Comment --}}
            <form method="post" id="comment-form" action="{{ baseUrl('case-with-professionals/save-note/'.$record->id) }}" class="CdsCaseRequest-comment-form">
                @csrf
                {!! FormHelper::formTextarea([
                    'name'=>"notes",
                    'id'=>"notes",
                    'required'=>true,
                    "label"=>"Enter Comment",
                    'textarea_class'=>"CdsCaseRequest-comment-input"
                ]) !!}
                
                <div class="custom-file-upload-container">
                    <label for="custom-file-input" class="custom-file-label">Attachment</label>
                    <div class="custom-file-upload-area" id="file-upload-area">
                        <div class="upload-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 16L12 8M12 8L15 11M12 8L9 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3 15V16C3 18.8284 3 20.2426 3.87868 21.1213C4.75736 22 6.17157 22 9 22H15C17.8284 22 19.2426 22 20.1213 21.1213C21 20.2426 21 18.8284 21 16V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <p class="upload-text">Drag & drop files here or <span class="browse-link">browse</span></p>
                        <p class="upload-hint">Supported formats: JPG, JPEG, PNG (Max 6MB per file)</p>
                        <input type="file" id="custom-file-input" name="custom_attachment[]" multiple accept=".jpg,.jpeg,.png" style="display: none;">
                    </div>
                    <div class="file-preview-container" id="file-preview-container" style="display: none;">
                        <h6>Selected Files:</h6>
                        <div class="file-list" id="file-list"></div>
                    </div>
                </div>

                <input type="hidden" id="attachments" name="attachments" value="" />
                <input type="hidden" id="case_id" name="case_id" value="{{ $case_id }}" />

                <button type="submit" class="CdsCaseRequest-submit-btn">Submit</button>
            </form>
        </div>
    </aside>

</div>
@endsection

@section('javascript')
<script>
    // Global variables
    let selectedFiles = [];
    
    $(document).ready(function() {
        const fileUploadArea = document.getElementById('file-upload-area');
        const fileInput = document.getElementById('custom-file-input');
        const filePreviewContainer = document.getElementById('file-preview-container');
        const fileList = document.getElementById('file-list');

        // Click to browse files
        fileUploadArea.addEventListener('click', () => {
            fileInput.click();
        });

        // Browse link click
        document.querySelector('.browse-link').addEventListener('click', (e) => {
            e.stopPropagation();
            fileInput.click();
        });

        // Drag and drop events
        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('dragover');
        });

        fileUploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('dragover');
        });

        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('dragover');
            const files = Array.from(e.dataTransfer.files);
            handleFiles(files);
        });

        // File input change
        fileInput.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            handleFiles(files);
        });

        function handleFiles(files) {
            files.forEach(file => {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    showError(`File "${file.name}" is not a supported format. Please use JPG, JPEG, or PNG files.`);
                    return;
                }

                // Validate file size (6MB)
                const maxSize = 6 * 1024 * 1024; // 6MB in bytes
                if (file.size > maxSize) {
                    showError(`File "${file.name}" is too large. Maximum size is 6MB.`);
                    return;
                }

                // Add file to selected files
                selectedFiles.push(file);
            });

            updateFilePreview();
        }

        function updateFilePreview() {
            if (selectedFiles.length === 0) {
                filePreviewContainer.style.display = 'none';
                return;
            }

            filePreviewContainer.style.display = 'block';
            fileList.innerHTML = '';

            selectedFiles.forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                
                const fileSize = formatFileSize(file.size);
                
                fileItem.innerHTML = `
                    <div class="file-info">
                        <svg class="file-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div>
                            <div class="file-name">${file.name}</div>
                            <div class="file-size">${fileSize}</div>
                        </div>
                    </div>
                    <button type="button" class="remove-file" onclick="removeFile(${index})">Remove</button>
                `;
                
                fileList.appendChild(fileItem);
            });
        }

        function removeFile(index) {
            selectedFiles.splice(index, 1);
            updateFilePreview();
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function showError(message) {
            // You can use your existing error display method
            if (typeof errorMessage === 'function') {
                errorMessage(message);
            } else {
                alert(message);
            }
        }

        // Make functions global
        window.removeFile = removeFile;
        window.updateFilePreview = updateFilePreview;

        // Form submission
        $("#comment-form").submit(function(e) {
            e.preventDefault();
            
            var is_valid = formValidation("form");
            if (!is_valid) {
                return false;
            }

            // Always submit form with files together
            submitFormWithFiles();
        });
    });

    // Global functions
    function submitFormWithFiles() {
        var formData = new FormData($("#comment-form")[0]);
        
        // Add files to FormData if any are selected
        if (selectedFiles.length > 0) {
            selectedFiles.forEach((file, index) => {
                formData.append(`attachment[${index}]`, file);
            });
        }
      
        var url = $("#comment-form").attr('action');
        $.ajax({
            url: url,
            type: "post",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            beforeSend: function() {
                showLoader();
            },
            success: function(response) {
                hideLoader();
                if (response.status == true) {
                    // Reset form fields
                    $('#notes').val('');
                    $('#attachments').val('');
                    
                    // Reset file selection
                    selectedFiles = [];
                    updateFilePreview();
                    
                    // Reset file input
                    $('#custom-file-input').val('');
                    
                    // Show success message
                    successMessage(response.message);
                    
                    // Refresh comments without page reload
                    refreshComments();
                } else {
                    validation(response.message);
                }
            },
            error: function() {
                hideLoader();
                internalError();
            }
        });
    }

    // Function to refresh comments without page reload
    function refreshComments() {
        $.ajax({
            url: BASEURL + "/case-with-professionals/view-request-comments/" + '{{ $record->id }}',
            type: "POST",
            data: {
                _token: csrf_token
            },
            dataType: "json",
            success: function(response) {
                if (response.status == true) {
                    $('#case-comments').html(response.html);
                }
            },
            error: function() {
                // If AJAX fails, fallback to page reload
                location.reload();
            }
        });
    }

</script>
@endsection