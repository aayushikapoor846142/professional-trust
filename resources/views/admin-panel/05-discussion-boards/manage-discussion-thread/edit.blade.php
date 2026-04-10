@extends('admin-panel.layouts.app')
@section('content')
<div class="edit-discussion-form">
    <div class="cdsTYDashboard-feed-create">
        <div class="cdsTYDashboard-feed-title">
            <h2>Edit Discussion</h2>
        </div>
        <div class="cdsTYDashboard-feed-create-form">
            <form id="editform" action="{{ baseUrl('/manage-discussion-threads/update/'.$record->unique_id) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-md-12 col-sm-6">
                                {!! FormHelper::formInputText([
                                'name'=>"topic_title",
                                'required'=>true,
                                'id'=>"topic_title",
                                'value'=>$record->topic_title,
                                "label"=>"Enter Topic Title",
                                ]) !!}
                            </div>
                            <div class="col-md-12 col-sm-6">
                                {!! FormHelper::formTextarea(['name'=>"short_description",
                                'id'=>"short_description",
                                'required'=>true,
                                "label"=>"Enter Short Description",
                                'class'=>"noval cds-texteditor",
                                'value'=>html_entity_decode($record->short_description),
                                ]) !!}
                            </div>
                            <div class="col-md-12 col-sm-6">
                                {!! FormHelper::formTextarea(['name'=>"description",
                                'id'=>"edit_description",
                                'required'=>true,
                                "label"=>"Enter Description",
                                'class'=>"noval cds-texteditor",
                                'value'=>html_entity_decode($record->description),
                                ]) !!}
                            </div>

                            <div class="col-md-12 col-sm-6">
                                <div class="cds-selectbox">
                                    {!! FormHelper::formSelect([
                                    'name' => 'discussion_category',
                                    'id' => 'discussion_category',
                                    'label' => 'Select Discussion Category ',
                                    'class' => 'select2-input',
                                    'required' => true,
                                    'options' => $categories,
                                    'value_column' => 'id',
                                    'label_column' => 'name',
                                    'selected' => $record->category_id,
                                    'is_multiple' => false
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-6">
                                <div class="cds-selectbox">
                                    {!! FormHelper::formSelect([
                                    'name' => 'type',
                                    'id' => 'type',
                                    'label' => 'Select Discussion Type',
                                    'class' => 'select2-input',
                                    'required' => true,
                                    'options' => FormHelper::groupType(),
                                    'value_column' => 'value',
                                    'label_column' => 'label',
                                    'selected' => $record->type ?? '',
                                    'is_multiple' => false
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-6 members-area" style="display:{{ $record->type == 'private'?'block':'none' }}">
                                <div class="cds-selectbox">
       
                                    {!! FormHelper::formSelect([
                                        'name' => 'selected_members[]',
                                        'id' => 'selected_members',
                                        'label' => 'Select Members',
                                        'class' => 'select2-input',
                                        'options' => $members ?? [],
                                        'selected' => $record->member ? $record->member->pluck('member_id')->toArray() : [],
                                        'value_column' => 'id',
                                        'label_column' => 'name', 
                                        'is_multiple' => true
                                    ]) !!}

                                </div>
                            </div>

                            

                            <div id="thumbnail-preview" style="margin-top: 10px;">
                                <!-- Thumbnail will be displayed here -->
                            </div>
                            <a href="javascript:;" class="edit-dropzone-discussion">
                                <div class="cdsTYDashboard-feed-post-media-btn-wrap">
                                    <div class="cds-feeds-post-actionbtn-content">
                                        <div class="form-group js-form-message">
                                            <label for="media" class="col-form-label input-label position-relative">
                                                <img src="{{url('assets/images/icons/gallery-icon.svg') }}"
                                                    alt="Gallery Icon">
                                                Media
                                            </label>  
                                        </div>
                                    </div>
            
            
                                </div>
                            </a>
                            {!! FormHelper::formDropzone([
                                'name' => 'file',
                                'id' => 'edit-file-dropzone',
                                'class' => 'edit-discussion-media-dropzone',
                                'required' => false,
                                'max_files' => 6,
                            ]) !!}
                       
                                @if($record->files != NULL) 
                                    @foreach(explode(',',$record->files) as $value)
                                        <div class="cdsTYDashboard-profile-documents-container-box-segment">
                                            <div class="cdsTYDashboard-profile-documents-details-wrap">
                                                <div class="cdsTYDashboard-profile-documents-image">
                                                    <img id="showProfileImage" src="{{ $value ? discussionDirUrl($value, 't') : 'assets/images/default.jpg' }} " alt="Profile Image" class="img-fluid">
                                                </div>
                                                <div class="cdsTYDashboard-profile-documents-details">
                                                    <h3>{{$value}}</h3>
                                                    <span>{{date('d/m/Y',strtotime($record->created_at))}}</span>
                                                </div> 
                                            </div>
                                            <div class="cdsTYDashboard-profile-documents-buttons">
                                            
                                                <a href="{{url('download-media-file?dir='.discussionDir().'&file_name='.$value)}}" class="cdsTYDashboard-button-light cdsTYDashboard-button-small" download>
                                                    <i class="fa-regular fa-cloud-arrow-down"></i>   Download
                                                </a>
                                                <a href="{{baseUrl('discussion-threads/remove-file/'.$record->id.'/'.$value)}}" class="cdsTYDashboard-button-light-outline cdsTYDashboard-button-small">
                                                    Remove
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
            
                            <!--  -->
                            <input type="hidden" name="updated_files" id="updated_files">

                        </div>
                    </div>
                    <div class="col-md-12">
                    
                        <div class="form-group text-end mt-4">
                            <button type="submit" class="btn add-CdsTYButton-btn-primary">Update</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">
 Dropzone.autoDiscover = false;
    var fileDropzone;
    var file_files_uploaded = [];
    var timestamp = "{{time()}}";
    var upload_count = 0;
    var isError = 0;
    $(document).ready(function(){
        
        initSelect();
        $("#type").change(function(){
            if($(this).val() == 'private'){
                $(".members-area").show();
            }else{
                $(".members-area").hide();
            }
        })
        initEditor("edit_description");
        $(document).on("click", ".edit-dropzone-discussion", function(){
       
        $(".edit-discussion-media-dropzone").toggle();
        
    });

    fileDropzone = new Dropzone("#edit-file-dropzone", {
            url: BASEURL + "/discussion-threads/upload-file?_token=" + csrf_token + "&timestamp=" +
            timestamp+ "&document_type=" +"updated_files",
            autoProcessQueue: false, // Prevent automatic upload
            addRemoveLinks: true,
            maxFilesize: 2,
            acceptedFiles: '.jpg,.jpeg,.gif,.tiff,.png',
            parallelUploads: 40,
            maxFiles: 6,
            init: function () {
                this.on("processing", function () {
                    showLoader(); // Show loader when file starts processing
                });

                this.on("queuecomplete", function () {
                    hideLoader(); // Hide loader when all uploads complete
                });
            },
            success: function (file, response) {
                file_files_uploaded.push(response.filename);
            },
            error: function (file, errors) {
                errorMessage(errors);
                isError = 1;
                this.removeFile(file);
            },
            queuecomplete: function () {
                var file_value = file_files_uploaded.join(",");
                $('#updated_files').val(file_value); // Store filenames in a hidden input
            }
        });
        

    });

  
    $("#editform").submit(function(e) {
        e.preventDefault();
        var pendingUploads = 0;
        if (fileDropzone.getQueuedFiles().length > 0) {
            pendingUploads++;
            fileDropzone.on("queuecomplete", function () {
                pendingUploads--;
                if (pendingUploads === 0 && isError === 0) {
                    // poi_upload = 1;
                    upload_count = 1;
                    if (upload_count === 1) {
                        $('.document-error').html('');
                        submitForm();
                    }
                }
            });
            fileDropzone.processQueue();
        }else{
            submitForm();
        }
        
    });

    function submitForm()
    {
        var is_valid = formValidation("editform");
        if (!is_valid) {
            return false;
        }
        var formData = new FormData($("#editform")[0]);
        var url = $("#editform").attr('action');
       
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
                    successMessage(response.message);
                    // loadFeedContentAjax("{{$record->unique_id}}", "{{$record->id}}");
                    // $(".edit-feed-content").html('');
                    redirect(response.redirect_back)
                } else {
                    validation(response.message);
                }
            },
            error: function() {
                internalError();
            }
        });
    }

</script>

<script type="text/javascript">
$(document).ready(function() {
    console.log("Document is ready");

    // Check if jQuery is available
    if (typeof $ === 'undefined') {
        console.log("jQuery is not loaded");
    } else {
        console.log("jQuery is loaded");
    }

    // File input change event handler
    $('#media1555').on('change', function(event) {


        const fileInput = event.target;
        const previewContainer = document.getElementById('thumbnail-preview');
        previewContainer.innerHTML = ''; // Clear previous thumbnails

        const files = fileInput.files;
        console.log(files); // Check if files are selected

        if (files.length > 0) {
            Array.from(files).forEach((file) => {
                // Check if the file is an image
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        console.log('File read successfully');

                        const imgContainer = document.createElement('div');
                        imgContainer.style.display = 'inline-block';
                        imgContainer.style.margin = '10px';
                        imgContainer.style.textAlign = 'center';

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = 'Selected Image';
                        img.style.maxWidth = '150px';
                        img.style.maxHeight = '150px';
                        img.style.border = '1px solid #ddd';
                        img.style.padding = '5px';
                        img.style.borderRadius = '5px';
                        imgContainer.appendChild(img);

                        // Create the Discard Button
                        const discardButton = document.createElement('button');
                        discardButton.textContent = 'Discard';
                        discardButton.style.display = 'block';
                        discardButton.style.marginTop = '5px';
                        discardButton.style.padding = '5px 10px';
                        discardButton.style.border = 'none';
                        discardButton.style.backgroundColor = '#f44336';
                        discardButton.style.color = 'white';
                        discardButton.style.cursor = 'pointer';
                        discardButton.style.borderRadius = '3px';

                        discardButton.addEventListener('click', function() {
                            previewContainer.removeChild(imgContainer);
                            fileInput.value = ''; // Clear the file input
                        });

                        imgContainer.appendChild(discardButton);
                        previewContainer.appendChild(imgContainer);
                    };
                    reader.readAsDataURL(file);
                } else {
                    console.log(`Invalid file type: ${file.type}`);
                    const errorText = document.createElement('p');
                    errorText.textContent = `File "${file.name}" is not a valid image file.`;
                    errorText.style.color = 'red';
                    errorText.style.fontSize = '14px';
                    previewContainer.appendChild(errorText);
                }
            });
        }
    });
});
</script>
@endpush