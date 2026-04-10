<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">{{ $pageTitle ?? '' }}</h5>
            <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal"
                aria-label="Close">
                <i class="tio-clear tio-lg"></i>
            </button>
        </div>
        <div class="modal-body">
                <!-- Form Group -->
                <div class="js-form-message form-group row">
                    <label class="col-sm-3 col-form-label">Image</label>
                    <div class="col-sm-9">
                        <input type="file" accept="image/*" name="image" id="upload-image" class="form-control">
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="cds-image-container mt-3">
                        <img id="crop-image" style="max-width: 100%;">
                    </div>
                </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-white btn-modal-close" data-dismiss="modal">Close</button>
            <button  type="button" id="image-crop" class="CdsTYButton-btn-primary">Save</button>
        </div>
    </div>
</div>
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script>

    var cropper = null;
    $('#upload-image').on('change', function (e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $('#crop-image').attr('src', e.target.result);
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                cropper = new Cropper(document.getElementById('crop-image'), {
                    viewMode: 1,
                });
            };
            reader.readAsDataURL(files[0]);
        }
    });
    $('#image-crop').on('click', function () {
        if (cropper) {
            const originalCanvas = cropper.getCroppedCanvas();
            const compressionQuality = 0.7; // Adjust the quality (0.1 - 1)
            const fileType = $('#crop-image').data('fileType') || 'image/jpeg'; // Default to JPEG if no type is found
            showLoader();
            originalCanvas.toBlob(
                (blob) => {
                    const formData = new FormData();
                    formData.append('file', blob);
                    // Perform AJAX request
                    $.ajax({
                        url: '{{ baseUrl("/companies/upload-company-logo/".$id) }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        processData: false,
                        contentType: false,
                        data: formData,
                        dataType: 'json',
                        success: function (response) {
                            if (response.status) {
                                successMessage('Image uploaded successfully');
                                $('#showCompanyLogo').attr('src', response.filepath);
                                window.location.href = response.redirect_back;
                                closeModal();
                            } else {
                                errorMessage(response.message);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Upload failed:', error);
                        },
                    });
                },
                fileType, // Dynamically set output format
                compressionQuality
            );
        }
    });

    $('.btn-modal-close').on('click', function () {
        $('#popupModal').modal('hide');
    });
</script>