@extends('admin-panel.08-cases.case-with-professionals.my-cases-master')
@section('case-container')


<div class="cds-fs-case-details-overview-panel">
    <div class="cds-fs-case-details-overview-panel-main">
        <div class="cds-fs-case-details-overview-panel-header"></div>
        <div class="cds-fs-case-details-overview-panel-body">
            <div class="row align-items-center mb-2">
                <div class="col-sm mb-2 mb-sm-0">
                <h2 class="h4">Encrypted Files</h2>
                </div>

                <div class="input-group mb-3">
                    <input type="text" class="form-control" onkeydown="searchDocuments()" name="search_documents" id="search_documents" placeholder="Search Folders and Files" />
                    <button class="btn btn-secondary"><i class="fa fa-search"></i></button>
                </div>
            </div>

            @if($encrypted_document_folders->isNotEmpty())
            <div class="tab-content" id="professionalTabContent">
                <div class="tab-pane fade show active professional-request-folders border-bottom-0 position-relative" id="list" role="tabpanel" aria-labelledby="list-tab">
                  
                   
                    <ul class="list-group professional-request-folders-list"  id="accordionProfessionalDoc">
                      
                    @foreach($encrypted_document_folders as $value)
                    @if(!empty($value->document_file_count) && $value->document_file_count > 0)
                    
                                <li  class="list-group-item folder-name" data-id="{{ $value->unique_id }}" data-group-id="{{ $value->case_id }}">
                                <div class="row">
                                    
                                    <div class="col-auto">
                                        <img class="size28 img-fluid rounded-0" src="{{ url('assets/svg/folder-files.svg') }}" alt="Files">
                                    </div>
                                    <div class="col load-folder-documents" data-bs-toggle="collapse"
                                                      data-bs-target="#folder-documents-{{ $value->unique_id }}"
                                    data-folder-id="{{ $value->unique_id }}"  data-type="extra"
                                    style="cursor: pointer;">
                                      
                                    <h5 class="fileTitle mb-1">
                                        {{$value->folder_name ?? ''}} ({{$value->folder_id ?? ''}})
                                    </h5>
                                  
                                    <ul class="list-inline list-separator small mb-1">
                                                <li class="list-inline-item">{{ $value->document_file_count }} Files</li>
                                     </ul>

                                     <h5 class="created_at">
                                        {{ $value->created_at ? $value->created_at->format('d M Y') : '' }}
                                    </h5>
                                    
                                    </div>
                                  
                                    <div class="col-auto">
                                       
                                     <a class="dropdown-item" 
                                     href="{{ baseUrl('case-with-professionals/download-zip') }}?zip_id={{ $value->folder_id }}">
                                     Download Zip 
                                    <br>(Zip is Password Protected)</br>
                                  </a>
                              
                                    <a href="javascript:;"  onclick="showPopup('{{ baseUrl('case-with-professionals/encryption/forgot-key/' . $value->unique_id) }}')">
                                     Send Password
                                    </a>
                            
                                    </div>
                                  
                                    
                                    <div id="folder-documents-{{ $value->unique_id }}" class="collapse mt-2">
                                        @include('admin-panel.08-cases.case-with-professionals.encrypted-documents.documents', [
                                            'type' => 'extra',
                                            'case_id' => $case_id,
                                            'folder_id' => $value->unique_id,
                                            'case_documents'=> $value->documentFiles
                                        ])
                                           </div>
                                
                                </div>
                            </li>
                            @endif
                            @endforeach
                    </ul>
                 
                </div>
            </div>
        @endif

        </div>
    </div>
</div>

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

        // Show or hide the folder list item
        folder.style.display = (input === '' || matchFound) ? "block" : "none";

        // Expand or collapse the inner documents div
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
</script>

<script>
    function decryptDocuments(el) {
    let selectedDocuments = [];
    $('.row-checkbox:checked').each(function () {
        selectedDocuments.push($(this).val());
    });

    if (selectedDocuments.length === 0) {
        errorMessage('Please select at least one document to decrypt.');
        return;
    }
    Swal.fire({
       
        title: "Are you sure to decrypt?",
        text: "Do you want to decrypt the selected documents?",
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
            const encoded = encodeURIComponent(JSON.stringify(selectedDocuments));
            const url = BASEURL + "/case-with-professionals/show-decryption-document-form?document_ids=" + encoded;
            showPopup(url);
        }
    });
}

</script>

@endpush

