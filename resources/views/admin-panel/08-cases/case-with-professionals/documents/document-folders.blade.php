@extends('admin-panel.08-cases.case-with-professionals.my-cases-master')

@section('case-container')

<div class="CdsDashboardCaseDocuments-list-view-main-container">
    <!-- Document Header -->
    <div class="CdsDashboardCaseDocuments-list-view-document-header">
        <div class="CdsDashboardCaseDocuments-list-view-header-content">
            <div>
                <h1 class="CdsDashboardCaseDocuments-list-view-page-title">Document Management</h1>
                <p class="CdsDashboardCaseDocuments-list-view-page-subtitle">Organize and manage all your documents securely</p>
            </div>
        </div>
        <div class="CdsDashboardCaseDocuments-list-view-header-actions-row">
            <div class="CdsDashboardCaseDocuments-list-view-search-container">
                <span class="CdsDashboardCaseDocuments-list-view-search-icon">
                    <i class="fa fa-search"></i>
                </span>
                <input type="text" 
                       class="CdsDashboardCaseDocuments-list-view-search-input" 
                       id="search_documents" 
                       name="search_documents"
                       onkeyup="searchDocuments()" 
                       placeholder="Search folders and files...">
            </div>
            <button class="CdsDashboardCaseDocuments-list-view-action-btn secondary" onclick="downloadFiles()">
                <i class="fa fa-download"></i>
                <span>Download All</span>
            </button>
            <button class="CdsDashboardCaseDocuments-list-view-action-btn" 
                    onclick="showPopup('<?php echo baseUrl('case-with-professionals/documents/add-folder/'.$case_id) ?>')">
                <i class="fa-solid fa-folder-plus"></i>
                <span>Add Folder</span>
            </button>
            <button class="CdsDashboardCaseDocuments-list-view-action-btn secondary" 
                    onclick="encryptDocuments(this)">
                <i class="fa fa-lock"></i>
                <span>Encrypt Documents</span>
            </button>
        </div>
    </div>

    <!-- Stats Bar -->
    <div class="CdsDashboardCaseDocuments-list-view-stats-bar">
        <div class="CdsDashboardCaseDocuments-list-view-stat-card">
            <div class="CdsDashboardCaseDocuments-list-view-stat-icon">📁</div>
            <div class="CdsDashboardCaseDocuments-list-view-stat-value">
                {{ $case_record->caseFiles->count() }}
            </div>
            <div class="CdsDashboardCaseDocuments-list-view-stat-label">Total Files</div>
        </div>
        <div class="CdsDashboardCaseDocuments-list-view-stat-card">
            <div class="CdsDashboardCaseDocuments-list-view-stat-icon">📂</div>
            <div class="CdsDashboardCaseDocuments-list-view-stat-value">
                {{ $other_document_folders->count() }}
            </div>
            <div class="CdsDashboardCaseDocuments-list-view-stat-label">Folders</div>
        </div>
        <div class="CdsDashboardCaseDocuments-list-view-stat-card">
            <div class="CdsDashboardCaseDocuments-list-view-stat-icon">🔐</div>
            <div class="CdsDashboardCaseDocuments-list-view-stat-value">
                {{ $other_document_folders->where('is_encrypted', 1)->count() }}
            </div>
            <div class="CdsDashboardCaseDocuments-list-view-stat-label">Encrypted</div>
        </div>
        <!-- <div class="CdsDashboardCaseDocuments-list-view-stat-card">
            <div class="CdsDashboardCaseDocuments-list-view-stat-icon">💾</div>
            <div class="CdsDashboardCaseDocuments-list-view-stat-value">--</div>
            <div class="CdsDashboardCaseDocuments-list-view-stat-label">Storage Used</div>
        </div> -->
    </div>

   

    <!-- Document List View -->
    @if($other_document_folders->isNotEmpty())
    <div id="cdsDocumentCaseListView" class="CdsDashboardCaseDocuments-list-view-document-list">
        <div class="CdsDashboardCaseDocuments-list-view-list-header">
            <div>
           
            </div>
            <div>Name</div>
            <div>Owner</div>
            <div>Modified</div>
            <div>Files</div>
            <div>Actions</div>
        </div>

        <ul class="list-unstyled mb-0 droppable sortable-container" id="accordionProfessionalDoc">
            @foreach($other_document_folders as $key => $value)
            @if($value->is_hidden == 0 || ($value->is_hidden == 1 && $value->added_by == auth()->user()->id))
            <li class="CdsDashboardCaseDocuments-list-view-folder-section folder-name" 
                data-id="{{ $value->unique_id }}" 
                data-group-id="{{ $value->case_id }}">
                
                <div class="CdsDashboardCaseDocuments-list-view-document-item CdsDashboardCaseDocuments-list-view-folder-header" 
                     onclick="cdsDocumentCaseToggleFolder(this)">
                    <div>
        
                    </div>
                    <div class="CdsDashboardCaseDocuments-list-view-file-info">
                        <span class="CdsDashboardCaseDocuments-list-view-expand-icon drag-handle">
                            <i class="fa fa-arrows"></i>
                        </span>
                        <div class="CdsDashboardCaseDocuments-list-view-file-icon folder">📁</div>
                        <div class="CdsDashboardCaseDocuments-list-view-file-details">
                            <div class="CdsDashboardCaseDocuments-list-view-file-name fileTitle">
                                {{ $value->name }}
                            </div>
                            <div class="CdsDashboardCaseDocuments-list-view-file-path">
                                Created By: {{ ($value->user->first_name ?? '') . ' ' . ($value->user->last_name ?? '') }}
                            </div>
                        </div>
                    </div>
                    <div class="CdsDashboardCaseDocuments-list-view-owner-info">
                        <div class="CdsDashboardCaseDocuments-list-view-owner-avatar">
                            {{ substr($value->user->first_name ?? 'U', 0, 1) }}{{ substr($value->user->last_name ?? '', 0, 1) }}
                        </div>
                        <div class="CdsDashboardCaseDocuments-list-view-owner-name">
                            {{ ($value->user->first_name ?? '') . ' ' . ($value->user->last_name ?? '') }}
                        </div>
                    </div>
                    <!-- <div class="CdsDashboardCaseDocuments-list-view-file-size">--</div> -->
                    <div class="CdsDashboardCaseDocuments-list-view-file-date">
                        {{ \Carbon\Carbon::parse($value->created_at)->format('M d, Y') }}
                    </div>
                    <div class="CdsDashboardCaseDocuments-list-view-file-tags">
                        <span class="CdsDashboardCaseDocuments-list-view-tag">{{ $value->documentFiles->count() }} Files</span>
                        @if($value->is_encrypted)
                        <span class="CdsDashboardCaseDocuments-list-view-tag encrypted">🔐 Encrypted</span>
                        @endif
                    </div>
                    <div class="CdsDashboardCaseDocuments-list-view-file-actions">
                        @if($value->added_by == auth()->user()->id)
                        <button class="CdsDashboardCaseDocuments-list-view-action-btn-small CdsDashboardCaseDocuments-list-view-primary"
                                onclick="showPopup('<?php echo baseUrl('case-with-professionals/documents/edit-folder/'.$value->unique_id) ?>')">
                            Rename folder
                        </button>
                        <button class="CdsDashboardCaseDocuments-list-view-action-icon"
                                data-href="{{ baseUrl('case-with-professionals/delete-document-folder/'.$value->unique_id) }}"
                                onclick="confirmAnyAction(this)"
                                title="By Deleting this folder all files inside folder will be deleted"
                                data-action="Delete.By Deleting this folder all files inside folder will be deleted">
                            🗑️
                        </button>
                        @endif
                    </div>
                </div>
                <!-- Folder Contents -->
                <div class="CdsDashboardCaseDocuments-list-view-folder-contents collapse" 
                     id="folder-documents-{{ $value->unique_id }}"
                     data-folder-id="{{ $value->unique_id }}"
                     data-type="extra">
                    <div class="load-folder-documents" 
                         data-folder-id="{{ $value->unique_id }}" 
                         data-type="extra">
                        @include('admin-panel.08-cases.case-with-professionals.documents.documents', [
                            'type' => 'extra',
                            'case_id' => $case_id,
                            'folder_id' => $value->unique_id,
                            'case_documents' => $value->documentFiles
                        ])
                    </div>
                </div>
            </li>
            @endif
            @endforeach
        </ul>
    </div>
    @else
    <div class="CdsDashboardCaseDocuments-list-view-empty-folder">
        No folders found. Click "Add Folder" to create your first folder.
    </div>
    @endif
</div>

<!-- Hidden Forms -->
<form id="group-form" method="post" action="{{ baseUrl('case-with-professionals/documents/link-to-groups') }}">@csrf</form>
<form method="post" action="{{ baseUrl('case-with-professionals/download-multiple-documents') }}" id="downloadFileForm">
    @csrf
    <input type="hidden" name="files" id="files-to-download" />
</form>
<form id="document-reorder-form" method="POST" action="{{ baseUrl('case-with-professionals/documents/reorder') }}">
    @csrf
</form>

@endsection

@push("scripts")
<div class="CdsCaseDocumentPreview-overlay" id="cdsCaseDocumentPreviewOverlay"></div>
<script src="{{url('assets/js/custom-file-upload.js')}}"></script>
<link href="{{ url('assets/css/custom-file-upload.css') }}" rel="stylesheet" />
<link href="{{ url('assets/css/16-CDS-case-document-preview.css') }}" rel="stylesheet" />
<script>
// Search functionality
function searchDocuments() {
    const input = document.getElementById("search_documents").value.toLowerCase();
    const folderItems = document.querySelectorAll(".CdsDashboardCaseDocuments-list-view-folder-section");
    
    if (input === '') {
        folderItems.forEach(folder => {
            folder.style.display = "block";
        });
        return;
    }
    
    folderItems.forEach(folder => {
        const folderTitle = folder.querySelector(".CdsDashboardCaseDocuments-list-view-file-name")?.textContent.toLowerCase() || '';
        const fileNames = folder.querySelectorAll(".fileTitle");
        
        let matchFound = folderTitle.includes(input);
        
        fileNames.forEach(file => {
            if (file.textContent.toLowerCase().includes(input)) {
                matchFound = true;
            }
        });
        
        folder.style.display = matchFound ? "block" : "none";
        
        const docSection = folder.querySelector(".CdsDashboardCaseDocuments-list-view-folder-contents");
        if (docSection && matchFound) {
            docSection.classList.add("show");
        }
    });
}


// Toggle folder expansion
function cdsDocumentCaseToggleFolder(folderHeader) {
    if (event.target.closest('.CdsDashboardCaseDocuments-list-view-checkbox, .CdsDashboardCaseDocuments-list-view-action-btn-small, .CdsDashboardCaseDocuments-list-view-action-icon, .drag-handle')) {
        return;
    }
    
    const folderSection = folderHeader.closest('.CdsDashboardCaseDocuments-list-view-folder-section');
    const folderContents = folderSection.querySelector('.CdsDashboardCaseDocuments-list-view-folder-contents');
    const folderId = folderContents.getAttribute('data-folder-id');
    const folderType = folderContents.getAttribute('data-type');
    
    if (folderContents.classList.contains('show')) {
        folderContents.classList.remove('show');
    } else {
        folderContents.classList.add('show');
    }
}

// Download files functionality
function downloadFiles() {
    var files = [];
    if ($(".row-checkbox:checked").length > 0) {
        $(".row-checkbox:checked").each(function() {
            files.push($(this).val());
        });
        $("#files-to-download").val(files.join(","));
        $(".row-checkbox:checked").prop("checked", false);
        $("#downloadFileForm").submit();
        $("#files-to-download").val('');
        $(".row-checkbox:checked").prop("checked", false);
    } else {
        errorMessage("Files not selected");
    }
}

// Encrypt documents functionality
function encryptDocuments(el) {
    let selectedDocuments = [];
    $('.row-checkbox:checked').each(function () {
        selectedDocuments.push($(this).val());
    });

    if (selectedDocuments.length === 0) {
        errorMessage('Please select at least one document to encrypt.');
        return;
    }
    
    Swal.fire({
        title: "Are you sure to encrypt?",
        text: "Do you want to encrypt the selected documents?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "CdsTYButton-btn-primary",
        cancelButtonClass: "CdsTYButton-btn-primary CdsTYButton-border-thick ml-1",
        buttonsStyling: false,
    }).then((result) => {
        if (result.value) {
            let documentIds = selectedDocuments.join(",");
            let url = "<?php echo baseUrl('case-with-professionals/show-encryption-folder-model') ?>?ids=" + documentIds;
            showPopup(url);
        }
    });
}

// Initialize checkbox functionality
function initializeFileCheckboxes() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectionCount();
        });
    });
}

// Update selection count
function updateSelectionCount() {
    const checkboxes = document.querySelectorAll('.row-checkbox:checked');
    const count = checkboxes.length;
    
    // Update UI based on selection count if needed
    console.log(count + ' files selected');
}

// jQuery Document Ready
$(document).ready(function () {
    initializeDragAndDrop();
    // Initialize sortable
    $(".sortable-container").sortable({
        handle: ".drag-handle",
        placeholder: "sortable-placeholder",
        opacity: 0.7,
        cursor: "move",
        tolerance: "pointer",
        update: function(event, ui) {
            let groupId = $(".sortable-container > li").map(function(){
                return $(this).data("id");
            }).get();

            let caseId = $(".sortable-container > li").map(function(){
                return $(this).data("group-id");
            }).get();
            
            $.ajax({
                url: BASEURL + "/case-with-professionals/documents-folders/reorder",
                method: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    caseId: caseId,
                    groupId: groupId
                },
                success: function(response) {
                    if (response.status === 'success') {
                        successMessage(response.message);
                        location.reload();
                    } else {
                        validation(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error:", error);
                }
            });
        }
    }).disableSelection();

    // File sortable
    $(".file-sortable-container").sortable({
        connectWith: ".file-sortable-container",
        handle: ".drag-handle",
        placeholder: "sortable-placeholder",
        opacity: 0.7,
        cursor: "move",
        tolerance: "pointer",
        update: function(event, ui) {
            if (this === ui.item.parent()[0]) {
                var orderedIds = $(this).children().map(function () {
                    return $(this).data('id');
                }).get();

                var folderId = $(this).data("group");
                var type = $(this).data("type");

                var $form = $("#document-reorder-form");
                $form.find("input[name^='moved_ids[]']").remove();
                $form.find("input[name='target_group']").remove();
                $form.find("input[name='folder_type']").remove();

                $.each(orderedIds, function(index, id) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'moved_ids[]',
                        value: id
                    }).appendTo($form);
                });

                $('<input>').attr({
                    type: 'hidden',
                    name: 'target_group',
                    value: folderId
                }).appendTo($form);

                $('<input>').attr({
                    type: 'hidden',
                    name: 'folder_type',
                    value: type
                }).appendTo($form);

                $form.submit();
            }
        },
        receive: function(event, ui) {
            var documentId = ui.item.find('input[type="checkbox"]').val();
            var targetFolderId = $(this).data("group");
            var type = $(this).data("type");

            var $form = $("#group-form");
            $('<input>').attr({
                type: 'hidden',
                name: 'moved_ids[]',
                value: documentId
            }).appendTo($form);

            $('<input>').attr({
                type: 'hidden',
                name: 'target_group',
                value: targetFolderId
            }).appendTo($form);

            $('<input>').attr({
                type: 'hidden',
                name: 'folder_type',
                value: type
            }).appendTo($form);

            $form.submit();
        }
    }).disableSelection();

    // Form submissions
    $("#document-reorder-form").submit(function(e) {
        e.preventDefault();
        var url = $("#document-reorder-form").attr('action');
        $.ajax({
            url: url,
            type: "post",
            data: $("#document-reorder-form").serialize(),
            dataType: "json",
            beforeSend: function() {
                showLoader();
            },
            success: function(response) {
                hideLoader();
                if (response.status == true) {
                    successMessage(response.message);
                    location.reload();
                } else {
                    errorMessage(response.message);
                }
            },
            error: function() {
                internalError();
            }
        });
    });

    $("#group-form").submit(function(e) {
        e.preventDefault();
        var url = $("#group-form").attr('action');
        $.ajax({
            url: url,
            type: "post",
            data: $("#group-form").serialize(),
            dataType: "json",
            beforeSend: function() {
                showLoader();
            },
            success: function(response) {
                hideLoader();
                if (response.status == true) {
                    successMessage(response.message);
                    location.reload();
                } else {
                    errorMessage(response.message);
                }
            },
            error: function() {
                internalError();
            }
        });
    });

    // Initialize checkboxes
    initializeFileCheckboxes();
});

function cdsDocumentToggleDropdown(id) {
    const dropdown = $('#dropdown-' + id);
    $('.CDSDocument-dropdown-menu').not(dropdown).removeClass('show');
    dropdown.toggleClass('show');
}
function confirmCommentDelete(e) {
    var url = $(e).attr("data-href");
    var comment_id  = $(e).data("id");
    Swal.fire({
        title: "Are you sure to delete?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "CdsTYButton-btn-primary",
        cancelButtonClass: "CdsTYButton-btn-primary CdsTYButton-border-thick ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            $.ajax({
                url:$(e).data("href"),
                method: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                beforeSend: function() {
                    showLoader();
                },
                success: function(response) {
                    hideLoader();
                    if (response.status) {
                        $("#case-document-comment-"+comment_id).fadeOut(200);
                        $("#case-document-comment-"+comment_id).remove();
                    } else {
                        errorMessage(response.message);
                    }
                },
                error: function() {
                    hideLoader();
                    internalError();
                }
            });
        }
    });
}

function cdsCaseDocumentOpenPreview(e) {
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
                // const overlay = document.getElementById('cdsCaseDocumentPreviewOverlay');
                $("#cdsCaseDocumentPreviewOverlay").html(response.contents);
                // overlay.innerHTML = response.contents;
                // overlay.classList.add('CdsCaseDocumentPreview-active');
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

@stack("document_scripts")

<script>

// Global variables to track drag state
let isDraggingFile = false;
let currentExpandedFolder = null;
let expandTimeout = null;
let originallyExpandedFolders = new Set();

// Initialize drag and drop functionality
function initializeDragAndDrop() {
    // Track originally expanded folders
    document.querySelectorAll('.CdsDashboardCaseDocuments-list-view-folder-contents.show').forEach(folder => {
        originallyExpandedFolders.add(folder.getAttribute('data-folder-id'));
    });

    // Detect when dragging starts/stops on the document
    document.addEventListener('dragenter', function(e) {
        if (e.dataTransfer && e.dataTransfer.types && e.dataTransfer.types.includes('Files')) {
            isDraggingFile = true;
            document.body.classList.add('dragging-files');
        }
    });

    document.addEventListener('dragleave', function(e) {
        // Only remove the dragging state if we're leaving the document
        if (!e.relatedTarget || !document.contains(e.relatedTarget)) {
            isDraggingFile = false;
            document.body.classList.remove('dragging-files');
            clearTimeout(expandTimeout);
            resetAutoExpandedFolders();
        }
    });

    document.addEventListener('drop', function(e) {
        // Don't prevent default here, let individual drop zones handle it
        isDraggingFile = false;
        document.body.classList.remove('dragging-files');
        clearTimeout(expandTimeout);
    });

    document.addEventListener('dragend', function(e) {
        isDraggingFile = false;
        document.body.classList.remove('dragging-files');
        clearTimeout(expandTimeout);
        resetAutoExpandedFolders();
    });

    // Prevent default dragover to allow drop
    document.addEventListener('dragover', function(e) {
        if (isDraggingFile) {
            e.preventDefault();
        }
    });

    // Add hover detection to folder sections
    const folderSections = document.querySelectorAll('.CdsDashboardCaseDocuments-list-view-folder-section');
    
    folderSections.forEach(folderSection => {
        const folderHeader = folderSection.querySelector('.CdsDashboardCaseDocuments-list-view-folder-header');
        const folderContents = folderSection.querySelector('.CdsDashboardCaseDocuments-list-view-folder-contents');
        const folderId = folderContents.getAttribute('data-folder-id');
        
        folderHeader.addEventListener('dragenter', function(e) {
            if (!isDraggingFile) return;
            e.preventDefault();
            handleFolderDragEnter(folderSection);
        });

        folderHeader.addEventListener('dragover', function(e) {
            if (!isDraggingFile) return;
            e.preventDefault();
            e.dataTransfer.dropEffect = 'copy';
        });

        folderHeader.addEventListener('dragleave', function(e) {
            if (!isDraggingFile) return;
            
            // Check if we're still within the folder header
            if (!folderHeader.contains(e.relatedTarget)) {
                folderSection.classList.remove('drag-hover');
                clearTimeout(expandTimeout);
            }
        });

        // Also add dragover to folder contents to keep it expanded
        folderContents.addEventListener('dragover', function(e) {
            if (!isDraggingFile) return;
            e.preventDefault();
        });

        // Handle drop on folder header - redirect to upload area
        folderHeader.addEventListener('drop', function(e) {
            if (!isDraggingFile) return;
            
            e.preventDefault();
            e.stopPropagation();
            
            // Expand the folder if not already
            if (!folderContents.classList.contains('show')) {
                expandFolderForDrop(folderSection);
            }
            
            // Find the upload area and trigger drop there
            setTimeout(() => {
                const uploadArea = folderContents.querySelector('.CDSFeed-upload-area');
                if (uploadArea && window.fileUploaders && window.fileUploaders['folder_' + folderId]) {
                    // Get the file uploader instance
                    const uploader = window.fileUploaders['folder_' + folderId];
                    
                    // Process the dropped files
                    const files = Array.from(e.dataTransfer.files);
                    files.forEach(file => {
                        // Use the FileUploadManager's file handling
                        if (uploader.validateFile && typeof uploader.validateFile === 'function') {
                            uploader.validateFile(file);
                        }
                    });
                    
                    // Trigger file input change event if the uploader expects it
                    const fileInput = uploadArea.querySelector('.CDSFeed-file-input');
                    if (fileInput && e.dataTransfer.files.length > 0) {
                        fileInput.files = e.dataTransfer.files;
                        fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
            }, 100);
            
            // Reset drag state
            isDraggingFile = false;
            document.body.classList.remove('dragging-files');
            folderSection.classList.remove('drag-hover');
        });
    });
}

// Handle folder drag enter with delay
function handleFolderDragEnter(folderSection) {
    // Clear any existing timeout
    clearTimeout(expandTimeout);
    
    // Add visual feedback immediately
    folderSection.classList.add('drag-hover');
    
    // Set timeout to expand folder after hovering for 600ms
    expandTimeout = setTimeout(() => {
        expandFolderForDrop(folderSection);
    }, 600);
}

// Function to expand folder for drop
function expandFolderForDrop(folderSection) {
    const folderContents = folderSection.querySelector('.CdsDashboardCaseDocuments-list-view-folder-contents');
    const folderId = folderContents.getAttribute('data-folder-id');
    
    // If this folder is already expanded, do nothing
    if (folderContents.classList.contains('show')) {
        return;
    }
    
    // Collapse previously auto-expanded folder (but not originally expanded ones)
    document.querySelectorAll('.folder-expanded-by-drag').forEach(autoExpandedFolder => {
        if (autoExpandedFolder !== folderSection) {
            const contents = autoExpandedFolder.querySelector('.CdsDashboardCaseDocuments-list-view-folder-contents');
            const autoFolderId = contents.getAttribute('data-folder-id');
            
            // Only collapse if it wasn't originally expanded
            if (!originallyExpandedFolders.has(autoFolderId)) {
                contents.classList.remove('show');
            }
            autoExpandedFolder.classList.remove('folder-expanded-by-drag');
        }
    });
    
    // Expand the new folder
    folderContents.classList.add('show');
    folderSection.classList.add('folder-expanded-by-drag');
    
    // Smooth scroll to show upload area
    setTimeout(() => {
        const uploadArea = folderContents.querySelector('.CDSFeed-upload-container');
        if (uploadArea) {
            uploadArea.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }, 300);
}

// Reset auto-expanded folders when drag ends
function resetAutoExpandedFolders() {
    document.querySelectorAll('.folder-expanded-by-drag').forEach(folderSection => {
        const folderContents = folderSection.querySelector('.CdsDashboardCaseDocuments-list-view-folder-contents');
        const folderId = folderContents.getAttribute('data-folder-id');
        
        // Only collapse if it wasn't originally expanded
        if (!originallyExpandedFolders.has(folderId)) {
            folderContents.classList.remove('show');
        }
        folderSection.classList.remove('folder-expanded-by-drag');
    });
}

// Delete selected files
function deleteSelectedFiles(folder_id) {
    var selectedFiles = $('#folder-documents-'+folder_id+' .row-checkbox:checked');
    if (selectedFiles.length === 0) {
        errorMessage('Please select files to delete');
        return;
    }
    const fileIds = Array.from(selectedFiles).map(cb => cb.value);
    
    Swal.fire({
        title: "Are you sure?",
        text: `Delete ${fileIds.length} selected file(s)?`,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete!",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.value) {
            // Submit delete request
            $.ajax({
                url: BASEURL + "/case-with-professionals/delete-multiple-documents",
                method: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    file_ids: fileIds
                },
                beforeSend: function() {
                    showLoader();
                },
                success: function(response) {
                    hideLoader();
                    if (response.status) {
                        successMessage(response.message);
                        location.reload();
                    } else {
                        errorMessage(response.message);
                    }
                },
                error: function() {
                    hideLoader();
                    internalError();
                }
            });
        }
    });
}
function showPopupRename(uniqueId, fileName) {
    const url = BASEURL+`/case-with-professionals/rename-document/${uniqueId}?old_file_name=${encodeURIComponent(fileName)}`;
    showPopup(url);
}
</script>
@endpush