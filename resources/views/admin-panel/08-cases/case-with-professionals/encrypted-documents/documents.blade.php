
<div class="cds-fs-case-details-overview-panel">
    <div class="cds-fs-case-details-overview-panel-main">
        <div class="cds-fs-case-details-overview-panel-header"></div>
        <div class="cds-fs-case-details-overview-panel-body">
            <div class="row align-items-center mb-2">
                <div class="col-sm mb-2 mb-sm-0">
                <h2 class="h4">Files</h2>
                </div>
                <div class="cds-action-elements">
                    <span class="font-size-sm mr-3">
                        <span id="datatableCounter-{{ $folder_id }}">0</span> Selected
                    </span>
                   <a class="btn btn-success btn-multi-delete ml-2" onclick="decryptDocuments(this)" href="javascript:;">
                    <i
                    class="fa fa-unlock"></i>
                    Decrypt Documents
                    </a>
                    
                </div>
                <div class="col-sm-auto">
                
                </div>
            </div>
            
           <div class="row">
                <div class="col-xl-10 col-md-10 col-lg-10">
                    
                    <div class="text-center custom-checkbox custom-control">
                        <input id="datatableCheckAll-{{ $folder_id }}" 
                               type="checkbox" 
                               class="custom-control-input datatableCheckAll" 
                               data-folder-id="{{ $folder_id }}" />
                        <label class="custom-control-label" for="datatableCheckAll-{{ $folder_id }}"></label>
                    </div>
                    <ul class="list-group" data-group="{{ $folder_id }}" data-type="{{ $type }}">
                     
                        @if(count($case_documents) > 0)
                        @foreach($case_documents as $key => $doc)
                        @php
            $fileNames = explode(',', $doc->file_name);
        @endphp
           @foreach($fileNames as $fileName)
                    <li class="list-group-item file-name w-100" data-id="{{ $doc->unique_id }}">
                       
                            <div class="row align-items-center gx-2">
                                <div class="text-md-center text-start custom-checkbox custom-control">
                          
                                    <input type="checkbox" 
                                    class="row-checkbox custom-control-input row-checkbox-{{ $folder_id }}" 
                                    data-folder-id="{{ $folder_id }}"
                                    value="{{ $doc->unique_id }}" id="row-{{$key}}">
                                    <label class="custom-control-label" for="row-{{$key}}"></label>
                                  
                                </div>
                                <div class="col-auto">
                                    <img class="size28 img-fluid rounded-0" src="{{ url('assets/svg/google-slides.svg') }}" alt="Files">
                                </div>
                                <div class="col">
                                    <h6 class="fileTitle mb-0">
                                        {{ $fileName }}
                                        
                                    </h6>
                                    <ul class="list-inline list-separator small">
                                        <li class="list-inline-item">Added on {{dateFormat($doc->created_at)}}</li>
                                    </ul>
                                    <ul class="list-inline list-separator small">
                                        <li class="list-inline-item">Added By: {{ ($doc->user->first_name ?? '') . ' ' . ($doc->user->last_name ?? '') }}</li>
                                    </ul>
                                </div>
                                <div class="col-12 mt-2 mt-sm-0 col-sm-auto text-center">
                                    @if($doc->is_encrypted != 1)
                                    <div class="btn-group">
                                        <a class="btn btn-outline-primary dropdown-toggle" href="javascript:void(0)" id="defaultDropdown" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                                            More
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                                         
                                            <li>                                    
                                                <a class="dropdown-item" 
                                                href="{{ baseUrl('case-with-professionals/download-documents') }}?case_id={{ $case_id }}&file={{  $fileName  }}">
                                                <i class="tio-edit"></i>    Download
                                             </a>

                                              
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="javascript:;" onclick="confirmAction(this)" data-href="{{ baseUrl('case-with-professionals/delete-document/'.$doc->unique_id) }}">
                                                 Delete
                                                </a>
                                            </li>

                                            <li>
                                                <a class="dropdown-item text-danger" href="javascript:;" onclick="showPopup('{{ baseUrl('case-with-professionals/rename-document/'.$doc->unique_id) }}')">

                                                    <a class="dropdown-item text-danger" href="javascript:;" onclick="showPopup('{{ baseUrl('case-with-professionals/rename-document/'.$doc->unique_id) }}')">
                                                Rename
                                                </a>
                                            </li>
                                     
                                        </ul>
                                    </div>
                                    @else
                                    <span class="badge bg-warning text-dark">Encrypted</span>
                                    @endif
                                    

                                </div>
                            </div>
                        </li>
                              @endforeach
                        @endforeach
                        @else
                        <li class="list-group-item text-center text-muted">
                            No documents found in this folder.
                        </li>
                    @endif
                    </ul>
            
                </div>
                
            </div>
        </div>
    </div>
</div>

                                                                

@section('javascript')

<script type="text/javascript">

$(document).ready(function () {
    $(document).on("change", ".datatableCheckAll", function () {
    let folderId = $(this).data("folder-id");
    let isChecked = $(this).is(":checked");

    $(".row-checkbox-" + folderId).prop("checked", isChecked);

    $("#datatableCounter-" + folderId).text($(".row-checkbox-" + folderId + ":checked").length);
});

$(document).on("change", ".row-checkbox", function () {
    let folderId = $(this).data("folder-id");
    $("#datatableCounter-" + folderId).text($(".row-checkbox-" + folderId + ":checked").length);
});
});
</script>


@endsection