// Edit Discussion Thread Popup Functionality
console.log('Edit discussion modal script loading...');

// Global function for opening edit modal
window.openEditDiscussionModal = function(discussionId) {
    console.log('openEditDiscussionModal called with ID:', discussionId);
    
    // Close dropdown menu
    $('.CdsDiscussionThread-dropdown-menu').removeClass('active');
    
    // Check if modal exists
    if ($('#editDiscussionModal').length === 0) {
        console.error('Edit modal not found!');
        alert('Edit modal not found!');
        return;
    }
    
    // Show loader in modal
    $('#editDiscussionContent').html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading...</p></div>');
    
    // Show modal
    try {
        if (typeof $('#editDiscussionModal').modal === 'function') {
            $('#editDiscussionModal').modal('show');
            console.log('Modal show called');
        } else {
            console.error('Bootstrap modal function not available');
            // Fallback: show modal manually
            $('#editDiscussionModal').show();
            $('body').addClass('modal-open');
        }
    } catch (error) {
        console.error('Error showing modal:', error);
        alert('Error showing modal: ' + error.message);
    }
    
    // Load edit form content
    $.ajax({
        url: BASEURL + '/manage-discussion-threads/edit/' + discussionId + '/modal',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status) {
                $('#editDiscussionContent').html(response.contents);
                
                // Initialize form components after content is loaded
                setTimeout(() => {
                    initializeEditFormComponents();
                }, 100);
            } else {
                $('#editDiscussionContent').html('<div class="alert alert-danger">' + response.message + '</div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            $('#editDiscussionContent').html('<div class="alert alert-danger">Failed to load edit form. Error: ' + error + '</div>');
        }
    });
};

// Initialize edit form components
window.initializeEditFormComponents = function() {
    // Initialize select2
    if (typeof initSelect === 'function') {
        initSelect();
    }
    
    // Initialize text editor
    if (typeof initEditor === 'function') {
        initEditor("edit_description");
    }
    
    // Initialize dropzone for edit form
    initializeEditDropzone();
    
    // Handle discussion type change in edit form
    $(document).on('change', '#edit_type', function() {
        if ($(this).val() == 'private') {
            $(".members-area").show();
            $(".allow-join-member").show();
        } else {
            $(".members-area").hide();
            $(".allow-join-member").hide();
        }
    });
    
    // Handle edit form submission
    $(document).on('submit', '#editDiscussionForm', function(e) {
        e.preventDefault();
        submitEditForm();
    });
    
    // Handle edit dropzone toggle
    $(document).on('click', '.open-edit-dropzone-post', function(event) {
        $(".edit-discussion-media-dropzone").toggle();
    });
};

// Initialize edit dropzone
window.initializeEditDropzone = function() {
    if (typeof Dropzone !== 'undefined') {
        Dropzone.autoDiscover = false;
        
        var editMediaDropzone = new Dropzone("#edit-discussion-media-dropzone-modal", {
            url: BASEURL + '/manage-discussion-threads/upload-file?_token=' + csrf_token + '&timestamp=' + Date.now() + '&document_type=file',
            autoProcessQueue: false,
            addRemoveLinks: true,
            maxFilesize: 2,
            acceptedFiles: '.jpg,.jpeg,.gif,.tiff,.png',
            parallelUploads: 40,
            maxFiles: 6,
            init: function () {
                this.on("processing", function () {
                    showLoader();
                });

                this.on("queuecomplete", function () {
                    hideLoader();
                });
            },
            success: function (file, response) {
                // Add to hidden input for form submission
                var currentFiles = $('#edit-file-modal').val();
                var newFiles = currentFiles ? currentFiles + ',' + response.filename : response.filename;
                $('#edit-file-modal').val(newFiles);
            },
            error: function (file, errors) {
                errorMessage(errors);
                this.removeFile(file);
            }
        });
    }
};

// Submit edit form
window.submitEditForm = function() {
    var is_valid = formValidation("editDiscussionForm");
    if (!is_valid) {
        return false;
    }
    
    var formData = new FormData($('#editDiscussionForm')[0]);
    var url = $("#editDiscussionForm").attr('action');
    
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
            $('.edit-btn').prop('disabled', true);
        },
        success: function(response) {
            hideLoader();
            $('.edit-btn').prop('disabled', false);
            
            if (response.status == true) {
                successMessage(response.message);
                $('#editDiscussionModal').modal('hide');
                
                // Reload the page to show updated content
                location.reload();
            } else {
                validation(response.message);
            }
        },
        error: function(xhr) {
            hideLoader();
            $('.edit-btn').prop('disabled', false);
            internalError();
            
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.error_type === 'validation') {
                validation(xhr.responseJSON.message);
            } else {
                errorMessage('An unexpected error occurred. Please try again.');
            }
        }
    });
};

// Remove current file from edit form
window.removeCurrentFile = function(fileName, index) {
    if (confirm('Are you sure you want to remove this file?')) {
        var currentFiles = $('#edit-updated-files').val();
        var filesArray = currentFiles.split(',');
        
        // Remove the file at the specified index
        filesArray.splice(index, 1);
        
        // Update the hidden input
        $('#edit-updated-files').val(filesArray.join(','));
        
        // Remove the file element from DOM
        $('.current-file-item').eq(index).fadeOut(300, function() {
            $(this).remove();
        });
        
        successMessage('File removed successfully');
    }
};

console.log('Edit discussion modal script loaded successfully'); 