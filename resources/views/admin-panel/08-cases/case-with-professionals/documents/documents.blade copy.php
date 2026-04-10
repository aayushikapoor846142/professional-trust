
<div class="cds-fs-case-details-overview-panel px-0 px-md-3">
    <div class="cds-fs-case-details-overview-panel-main">
        <div class="cds-fs-case-details-overview-panel-header"></div>
        <div class="cds-fs-case-details-overview-panel-body">
            <div class="row align-items-center mb-2">
                <div class="col-sm mb-2 mb-sm-0">
                    <h2 class="h4">Files</h2>
                </div>
                <div class="col-xl-12">
                    <div class="cds-action-elements">
                        <span class="font-size-sm mr-3"> <span id="datatableCounter-{{ $folder_id }}">0</span> Selected </span>
                        <a class="CdsTYButton-btn-primary CdsTYButton-border-thick btn-multi-delete ml-2" data-href="{{ baseUrl('my-cases/delete-multiple') }}" onclick="deleteMultiple(this)" href="javascript:;">
                            <i class="fa-solid fa-trash-arrow-up"></i>
                            Delete
                        </a>
                    </div>
                </div>
                <div class="col-sm-auto"></div>
            </div>

            <div class="row">
                <div class="col-xl-10 col-md-10 col-lg-10">
                    <form method="post" id="upload-document-form" action="{{ baseUrl('my-cases/save-document') }}">
                        @csrf

                        <input type="hidden" name="type" value="{{$type}}" />

                        <input type="hidden" name="case_id" value="{{$case_id}}" />
                        <input type="hidden" name="folder_id" value="{{$folder_id}}" />
                        <div class="cds-fileUpload mb-4 upload-dropzone" style="display: none;" id="upload-dropzone-{{$folder_id}}">
                            {!! FormHelper::formDropzone([ 'name' => 'upload-document-drp-'.$folder_id, 'id' => 'upload-document-drp-'.$folder_id, 'dropzone_class' => 'dz-images upload-document-drp', 'required' => false, 'max_files' => 10,
                            ]) !!}
                            <input type="hidden" id="documents" name="documents" value="" />
                            <div class="text-md-end text-center"><button type="submit" class="CdsTYButton-btn-primary">Upload</button></div>
                        </div>
                    </form>
                    <div class="cds-listDocs">
                        <div class="cds-checkheader">
                            <div class="custom-checkbox custom-control">
                                <input id="datatableCheckAll-{{ $folder_id }}" type="checkbox" class="custom-control-input datatableCheckAll" data-folder-id="{{ $folder_id }}" />
                                <label class="custom-control-label" for="datatableCheckAll-{{ $folder_id }}"></label>
                            </div>
                            <h5 class="mb-0">Check All</h5>
                        </div>
                        <ul class="list-group file-sortable-container" data-group="{{ $folder_id }}" data-type="{{ $type }}">
                            @if(count($case_documents) > 0) @foreach($case_documents as $key => $doc) @php $fileNames = explode(',', $doc->file_name); @endphp @foreach($fileNames as $fileName)
                            <li class="list-group-item sortable-item" data-id="{{ $doc->unique_id }}">
                                <div class="row align-items-center gx-2 gap-2 gap-lg-0 flex-column flex-md-row">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <div class="custom-checkbox custom-control">
                                            @if($doc->added_by == auth()->user()->id && $doc->is_encrypted != 1)
                                            <input type="checkbox" class="row-checkbox custom-control-input row-checkbox-{{ $folder_id }}" data-folder-id="{{ $folder_id }}" value="{{ $doc->unique_id }}" id="row-{{$key}}" />
                                            <label class="custom-control-label" for="row-{{$key}}"></label>
                                            @else
                                                <span class="cds-space"></span>
                                            @endif
                                        </div>
                                        <div class="col-auto">
                                            <span class="drag-handle">
                                                <i class="fa fa-arrows"></i>
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap col-sm">
                                            <div class="col-auto w-100 w-md-auto">
                                                <img class="size28 img-fluid rounded-0" src="{{ url('assets/svg/google-slides.svg') }}" alt="Files" />
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
                                                <div class="btn-group ">
                                                    <a class="btn btn-outline-primary dropdown-toggle" href="javascript:void(0)" id="defaultDropdown" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                                                        More
                                                    </a>
                                                    <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ baseUrl('my-cases/download-documents') }}?case_id={{ $case_id }}&file={{  $fileName }}"> <i class="tio-edit"></i> Download </a>
                                                        </li>
                                                        @if( $doc->added_by == auth()->user()->id)
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="javascript:;" onclick="confirmAction(this)" data-href="{{ baseUrl('my-cases/delete-document/'.$doc->unique_id) }}">
                                                                Delete
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="javascript:;" onclick="showPopupRename('{{ $doc->unique_id }}', '{{ trim($fileName) }}')">
                                                                Rename
                                                            </a>
                                                        </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                                @else
                                                <span class="badge bg-warning text-dark">Encrypted</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach @endforeach @else
                            <li class="list-group-item text-center text-muted">
                                No documents found in this folder.
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-md-2 col-lg-2 mt-3 mt-md-0 upload-case-document-div">
                    <div class="text-center">
                        <a class="CdsTYButton-btn-primary upload-case-document" href="javascript:;" id="upload-case-document" data-folder-id="{{$folder_id}}"><i class="fa fa-cloud-upload mr-1"></i> Upload</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


                                                                

@section('javascript')

<script type="text/javascript">
Dropzone.autoDiscover = false;

let dropzones = {};
let uploadQueue = {};

$(document).ready(function () {

    $(".upload-case-document").click(function () {
        let folderId = $(this).data("folder-id");
        let dropzoneDiv = $("#upload-dropzone-" + folderId);
        dropzoneDiv.toggle();

        let dzId = "upload-document-drp-" + folderId;

        if (!dropzones[dzId]) {
            let images = [];

            dropzones[dzId] = new Dropzone("#" + dzId, {
                url: BASEURL + "/my-cases/upload-document?_token=" + csrf_token,
                autoProcessQueue: false,
                addRemoveLinks: true,
                maxFilesize: 6,
                acceptedFiles: '.jpg,.jpeg,.png,.pdf,.txt,.docx,.xlsx,.xls',
                parallelUploads: 10,
                params: {
                    case_id: $('input[name="case_id"]').val(),
                    folder_id: folderId,
                },
                success: function (file, response) {
                    images.push(response.filename);
                },
                queuecomplete: function () {
                    let form = $("#" + dzId).closest("form");
                    form.find("input[name='documents']").val(images.join(","));
                    uploadQueue[dzId] = true;
                    submitForm(form, dzId);
                },
                error: function (file) {
                    this.removeFile(file);
                }
            });
        }
    });

    // Handle form submit per folder
    $(document).on("submit", "form[id='upload-document-form']", function (e) {
        e.preventDefault();
        let form = $(this);
        let dz = form.find(".upload-document-drp");
        let dzId = dz.attr("id");

        uploadQueue[dzId] = false;

        if (dropzones[dzId].getQueuedFiles().length > 0) {
            dropzones[dzId].processQueue();
        } else {
            uploadQueue[dzId] = true;
            submitForm(form, dzId);
        }
    });

    function submitForm(form, dzId) {
        if (!uploadQueue[dzId]) return;

        let formData = new FormData(form[0]);
        let url = form.attr('action');

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                showLoader();
            },
            success: function (response) {
                hideLoader();
                if (response.status === true) {
                    successMessage(response.message);
                    location.reload();
                } else {
                    errorMessage(response.message);
                }
            },
            error: function () {
                internalError();
            }
        });
    }

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
   <script>
function showPopupRename(uniqueId, fileName) {
    const url = BASEURL+`/my-cases/rename-document/${uniqueId}?old_file_name=${encodeURIComponent(fileName)}`;
    showPopup(url);
}
    </script>

@endsection