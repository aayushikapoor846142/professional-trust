@extends('admin-panel.08-cases.my-cases.my-cases-master')
@section('case-container')

<div class="cds-fs-case-details-overview-panel px-0 px-md-3">
    <div class="cds-fs-case-details-overview-panel-main">
        <div class="cds-fs-case-details-overview-panel-header"></div>
        <div class="cds-fs-case-details-overview-panel-body">
            <div class="row align-items-center mb-2">
                <div class="col-sm mb-2 mb-sm-0">
                    <h2 class="h4">Files</h2>
                </div>

                <div class="col-sm-auto mb-2 mb-sm-0">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" onkeydown="searchDocuments()" name="search_documents" id="search_documents" placeholder="Search Folders and Files" />
                        <button class="btn btn-secondary"><i class="fa fa-search"></i></button>
                    </div>
                    <div class="d-flex gap-2 flex-wrap align-items-center justify-content-center justify-content-md-end mb-3">
                        <!-- Add Folder Button -->
                        <a onclick="showPopup('<?php echo baseUrl('my-cases/documents/add-folder/'.$case_id) ?>')" class="fs14 btn btn-success" href="javascript:;"> <i class="fa-solid fa-folder-plus me-1"></i> Add Folder </a>

                        <!-- Encrypt Documents Button -->
                        <a href="javascript:;" onclick="encryptDocuments(this)" class="fs14 btn btn-warning text-dark"> <i class="fa fa-lock me-1"></i> Encrypt Documents </a>

                        <!-- Download All Button -->
                        <a href="javascript:;" onclick="downloadFiles()" class="fs14 CdsTYButton-btn-primary"> <i class="fa fa-download me-1"></i> Download All </a>
                    </div>
                </div>
            </div>
            <!--  -->
            @if($other_document_folders->isNotEmpty())
                <div class="tab-content" id="professionalTabContent1">
                    <div class="tab-pane fade show active professional-request-folders border-bottom-0 position-relative" id="list" role="tabpanel" aria-labelledby="list-tab">
                        <span class="folder-label-professional">Other Requested Folders</span>

                        <ul class="list-group professional-request-folders-list droppable sortable-container" id="accordionProfessionalDoc">
                            @foreach($other_document_folders as $value) @if($value->is_hidden == 0 || ($value->is_hidden == 1 && $value->added_by == auth()->user()->id))
                            <li class="list-group-item folder-name" data-id="{{ $value->unique_id }}" data-group-id="{{ $value->case_id }}">
                                <div class="row flex-column flex-md-row">
                                    <div class="col-auto">
                                        <span class="drag-handle">
                                            <i class="fa fa-arrows"></i>
                                        </span>
                                    </div>

                                    <div class="col-auto">
                                        <img class="size28 img-fluid rounded-0" src="{{ url('assets/svg/folder-files.svg') }}" alt="Files" />
                                    </div>
                                    <div class="col load-folder-documents" data-bs-toggle="collapse" data-bs-target="#folder-documents-{{ $value->unique_id }}" data-folder-id="{{ $value->unique_id }}" data-type="extra" style="cursor: pointer;">
                                        <h5 class="fileTitle mb-0">
                                            {{$value->name}}
                                        </h5>
                                        <ul class="list-inline list-separator small">
                                            Created By: {{ ($value->user->first_name ?? '') . ' ' . ($value->user->last_name ?? '') }}
                                        </ul>
                                        <ul class="list-inline list-separator small">
                                            <li class="list-inline-item">{{ $value->document_file_count }} Files</li>
                                        </ul>
                                    </div>
                                    @if($value->added_by == auth()->user()->id)
                                    <div class="col-auto">
                                        <a onclick="showPopup('<?php echo baseUrl('my-cases/documents/edit-folder/'.$value->unique_id) ?>')" class="CdsTYButton-btn-primary w-100 mb-3" href="javascript:;">
                                            <i class="tio-folder-add mr-1"></i> Rename folder
                                        </a>
                                    </div>
                                    <div class="col-auto">
                                        <!-- Delete Button -->
                                        <a
                                            data-href="{{ baseUrl('my-cases/delete-document-folder/'.$value->unique_id) }}"
                                            onclick="confirmAnyAction(this)"
                                            title="By Deleting this folder all files inside folder will be deleted"
                                            data-action="Delete.By Deleting this folder all files inside folder will be deleted"
                                        >
                                            <i class="fa-regular fa-trash text-danger"></i> Delete
                                        </a>
                                    </div>
                                    @endif
                                    <div id="folder-documents-{{ $value->unique_id }}" class="collapse mt-2">
                                        @include('admin-panel.08-cases.my-cases.documents.documents', [ 'type' => 'extra', 'case_id' => $case_id, 'folder_id' => $value->unique_id, 'case_documents'=> $value->documentFiles ])
                                    </div>
                                </div>
                            </li>
                            @endif @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<form id="group-form" method="post" action="{{ baseUrl('my-cases/documents/link-to-groups') }}">@csrf</form>
<form method="post" action="{{ baseUrl('my-cases/download-multiple-documents') }}" id="downloadFileForm">
    @csrf
    <input type="hidden" name="files" id="files-to-download" />
</form>
<form id="document-reorder-form" method="POST" action="{{ baseUrl('my-cases/documents/reorder') }}">
    @csrf
</form>

@endsection

@push("scripts")


<script>

function searchDocuments() {
    const input = document.getElementById("search_documents").value.toLowerCase();
    const folderItems = document.querySelectorAll(".list-group-item.folder-name");
    if (input === '') {
        return;
    }
    folderItems.forEach(folder => {
        const folderTitle = folder.querySelector(".fileTitle")?.textContent.toLowerCase() || '';
        const fileNames = folder.querySelectorAll(".fileTitle");

        let matchFound = folderTitle.includes(input);

        fileNames.forEach(file => {
            if (file.textContent.toLowerCase().includes(input)) {
                matchFound = true;
            }
        });

   
        folder.style.display = (input === '' || matchFound) ? "block" : "none";

   
        const docSection = folder.querySelector("[id^='folder-documents-']");
        if (docSection) {
            if (matchFound) {
                docSection.classList.add("show"); // Bootstrap expands
            } else {
                docSection.classList.remove("show");
            }
        }
    });
}
// function searchDocuments() {
//     let input = document.getElementById("search_documents").value.toLowerCase();
    
//     let folderItems = document.querySelectorAll(".list-group-item.folder-name");

//     folderItems.forEach(item => {
//         let fileTitle = item.querySelector(".fileTitle");
//         if (fileTitle) {
//             let text = fileTitle.textContent.toLowerCase();
//             item.style.display = (input === '' || text.includes(input)) ? "block" : "none";
//         }
//     });
// }

$(document).ready(function () {
    $(".file-sortable-container").on("click", ".sortable-item", function(e) {
        // Skip selection if checkbox is checked
        if ($(e.target).is("input[type='checkbox']") && $(e.target).prop("checked")) {
            return;
        }

        // Continue selection logic
        if (e.ctrlKey || e.metaKey) {
            $(this).toggleClass("selected");
        } else {
            $(".sortable-item").removeClass("selected");
            $(this).addClass("selected");
        }

        // Update icons
        $(".sortable-item").each(function() {
            let icon = $(this).find(".drag-handle i");
            if ($(this).hasClass("selected")) {
                icon.removeClass("fa-arrows").addClass("fa-check text-success");
            } else {
                icon.removeClass("fa-check text-success").addClass("fa-arrows");
            }
        });

        selectedItems = $(".sortable-item.selected");
    });
});

$(".file-sortable-container").sortable({
    connectWith: ".file-sortable-container",
    handle: ".drag-handle",
    placeholder: "sortable-placeholder",
    opacity: 0.7,
    cursor: "move",
    tolerance: "pointer",
     update: function(event, ui) {
        if (this === ui.item.parent()[0]) { // Only act if updated in the same list
            var orderedIds = $(this).children().map(function () {
                return $(this).data('id');
            }).get();

            var folderId = $(this).data("group");
            var type = $(this).data("type");

            var $form = $("#document-reorder-form");
            $form.find("input[name^='moved_ids[]']").remove(); // Clear old moved_ids inputs
            $form.find("input[name='target_group']").remove();
            $form.find("input[name='folder_type']").remove();

            // Add all reordered IDs to the form
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
        var documentId = ui.item.find('input[type="checkbox"]').val(); // Get file ID
        var targetFolderId = $(this).data("group"); // Target folder
        var type =$(this).data("type");
  
        // Add to form
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
                url: BASEURL + "/my-cases/documents-folders/reorder",
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
</script>

<script>
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
            let url = "<?php echo baseUrl('my-cases/show-encryption-folder-model') ?>?ids=" + documentIds;

        showPopup(url); 
          
        }
    });
}

</script>

@endpush

