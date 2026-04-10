<div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
        <div class="modal-header border-bottom-0 pb-0">
            <h5 class="modal-title ">Generate Assessment Using AI</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="popupForm" action="{{ baseUrl('/my-services/generate-assessment/'.$record->unique_id) }}" method="post" enctype="multipart/form-data" class="question-form">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <p>Form will be generate for  {{ $record->subServices->parentService->name??'' }} > {{ $record->subServices->name??'' }}</p>
                        <div class="form-group">
                            <label for="" class="col-form-label input-label my-2">Give your summary for generating assessment form (optional)</label>
                            <textarea class="form-control post-textarea" name="message"
                                placeholder="Give your summary for generating assessment form (optional)"
                                id="feeds-description-edit"></textarea>
                        </div>
                        <div class="form-group text-end mt-4">
                            <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
                        </div>
                    </div>
                </div>
            </form>

            <div style="display:none" class="generated-form">
                <div class="card">
                    <div class="card-header">
                        <p>Here is the generated form from AI relevant to service ask form.</p>
                    </div>
                    <div class="card-body">
                        <div class="generated-form-preview"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

$(document).ready(function() {

    var formJson = "";
    var service_id = "";
    $("#popupForm").submit(function(e) {
        e.preventDefault();
        var is_valid = formValidation("popupForm");
        if (!is_valid) {
            return false;
        }
        var formData = new FormData($(this)[0]);
        var url = $("#popupForm").attr('action');
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
                $(".generated-form-preview").html('');
            },
            success: function(response) {
                hideLoader();
                if (response.status == true) {
                    formJson = response.fg_field_json;
                    $(".generated-form").show();
                    $(".generated-form-preview").html(response.preview_form);
                    $(".question-form").hide();
                } else {
                    errorMessage(response.message);
                }
            },
            error: function() {
                internalError();
            }
        });

    });

    $(document).on('click', '#modify-question', function(event) {
        $(".post-textarea").val('');
        $(".generated-form").hide();
        $(".question-form").show();
    });

    $(document).on('click', '#save-form', function(event) {
      
        var formData = new FormData();
        formData.append('_token', $('input[name=_token]').val());
        formData.append('fg_field_json', formJson);
        formData.append('formName',$("#form_name").val());

        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url:  "{{ baseUrl('/my-services/generate-assessment-form-save/'.$id) }}",
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
                    $("#popupModal").modal('hide');
                    successMessage(response.message);
                } else {
                    errorMessage(response.message);
                }
            },
            error: function() {
                internalError();
            }
        });
    });
   
});
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