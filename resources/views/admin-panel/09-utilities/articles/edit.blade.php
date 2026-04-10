@extends('admin-panel.layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/custom-file-upload.css') }}">
@endsection

@section('content')
  <div class="ch-action">
                    <a href="{{ baseUrl('articles') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-left fa-solid me-1"></i>
                        Back
                    </a>
                </div><div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <form id="form" class="js-validate mt-3" action="{{ baseUrl('/articles/update/'.$record->unique_id) }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-sm-6">
                                {!! FormHelper::formInputText([
                                'name'=>"name",
                                'required'=>true,
                                'id'=>"name",
                                "label"=>"Enter Name",
                                'value'=>$record->name,
                                        
                                ]) !!}
                            </div>     
                            <div class="col-md-6 col-sm-6">
                                {!! FormHelper::formInputText([
                                'name'=>"slug",
                                "id" => "slug",
                                "label"=>"Enter Page Slug",
                                "required"=>true,
                                'events'=>['oninput=replaceSpacewithDash(this)', 'onblur=replaceSpacewithDash(this)'],
                                'value'=>$record->slug
                                ]) 
                                !!}
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="cds-selectbox">
                                    {!! FormHelper::formSelect([
                                    'name' => 'category',
                                    'id' => 'category',
                                    'label' => 'Select Category ',
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
                            <div class="col-md-6 col-sm-12">
                                <div class="cds-selectbox">
                                    {!! FormHelper::formSelect([
                                    'name' => 'article_type',
                                    'id' => 'article_type',
                                    'label' => 'Article Type',
                                    'class' => 'select2-input',
                                    'required' => true,
                                    'options' => $article_types,
                                    'value_column' => 'id',
                                    'label_column' => 'name',
                                    'selected' => $record->article_type_id,
                                    'is_multiple' => false
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-12">
                                {!! FormHelper::formTextarea(['name'=>"summary",
                                'id'=>"summary",
                                "label"=>"Enter Summary",
                                'textarea_class'=>" noval",
                                'required' => true,
                                'value'=>$record->summary ?? '',
                              
                                ]) !!}
                            </div>
                            <div class="col-md-12">
                                <button class="CdsTYButton-btn-primary my-2" href="javascript:;" data-modal="popupModal" type="button" href="javascript:void(0)" onclick="showPopup('<?= url('editor-shortcode') ?>')">
                                    <i class="fa-code fa-solid"></i> Shortcode
                                </button>
                            </div>
                            <div class="col-md-12">
                                <label class="col-form-label input-label">Enter Description <span class="danger">*</span></label>
                                {!! FormHelper::formTextarea([
                                    'name' => 'description',
                                    'id' => 'description',
                                    'class' => 'cds-texteditor',
                                    'textarea_class' => 'noval',
                                    'required' =>  true,
                                    'value'=> html_entity_decode($record->description) ?? '',
                                ]) !!}
                            </div>
                                  
                            <div class="col-lg-8 col-sm-12">
                                <div class="cds-selectbox"> 
                                    <label class="col-form-label non-floting">Check If you want to show revise date and time to display on details page</label>                          
                                    {!! FormHelper::formSelect([ 
                                    'name' => 'revise_date',
                                    'id' => 'revise_date',
                                    'label' => 'Check If you want to show revise date and time to display on details page',
                                    'class' => 'select2-input cds-left',
                                    'required' => false,
                                    'options' => [
                                    ['value' => 0, 'label' => 'No'],
                                    ['value' => 1, 'label' => 'Yes']
                                    ],
                                    'value_column' => 'value',
                                    'label_column' => 'label',
                                    'selected' => $record->revise_date,
                                    'is_multiple' => false
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                {!! FormHelper::formInputText([
                                'name'=>"seo_title",
                                'required'=>true,
                                'id'=>"seo_title",
                                "label"=>"Enter Seo Title",
                                'value'=> $record->seoDetails->meta_title ?? '',
                                'events'=>['oninput=validateString(this)']
                                ]) !!}
                            </div>
                            <div class="col-md-12"> 
                                <label class="col-form-label non-floting">Enter Seo Keywords (Please add comma separated)</label>                        
                                {!! FormHelper::formTextarea([
                                'name'=>"seo_keywords",
                                'id'=>"seo_keywords",
                                'required'=>true,
                                'class' => 'cds-left',
                                "label"=>"Enter Seo Keywords (Please add comma separated)",
                                'textarea_class'=>"editor noval",
                                'value'=>html_entity_decode($record->seoDetails->meta_keywords ?? '') ,
                             
                                ]) !!}
                            </div>
                            <div class="col-md-12">
                                {!! FormHelper::formTextarea([
                                'name'=>"seo_description",
                                'id'=>"seo_description",
                                'required'=>true,
                                "label"=>"Enter Seo Description",
                                'textarea_class'=>"editor noval",
                                'value'=>html_entity_decode($record->seoDetails->meta_description ?? ''),
                             
                                ]) !!}
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xl-4 py-1 py-md-3">
                                <div class="form-group">
                                    <div class="d-flex form-check gap-2 ps-0">
                                    {!! FormHelper::formCheckbox(['name' => "show_on_home", 'value' => 1, 'checked'=>$record->show_on_home, 'id' => "show_on_home", 'required' => true]) !!}
                                    <label class="form-check-label" for="show_on_home">Show On Home</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xl-4 py-1 py-md-3">
                                <div class="form-group">
                                    <div class="d-flex form-check gap-2 ps-0">
                                    {!! FormHelper::formCheckbox(['name' => "is_featured", 'value' => 1, 'checked'=>$record->is_featured, 'id' => "is_featured", 'required' => true]) !!}
                                    <label class="form-check-label" for="is_featured">Featured</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xs-12 mb-4 mt-3 mt-md-0">
                                <div class="form-group js-form-message">
                                    <label class="col-form-label">Image</label>
                                    <button type="button" onclick="showPopup('<?php echo baseUrl('articles/image-cropper') ?>')" class="CdsTYButton-btn-primary btn-sm">Choose Image</button>
                                    <input type="hidden" name="images" id="image" class="form-control">
                                    <div class="mt-3">
                                        @if ($record->images != '')
                                        <div class="my-4">
                                            <img id="showImage" src="{{ articleDirUrl($record->images, 't') }}" class="img-fluid mb-2" />
                                        </div>
                                        @else
                                        <img id="showImage" width="100" height="100" src="assets/svg/browse.svg" alt="Profile Image">
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="custom-file-upload-container">
                                    <label for="custom-file-input" class="custom-file-label">Attachments</label>
                                    <div class="custom-file-upload-area" id="file-upload-area">
                                        <div class="upload-icon">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 16L12 8M12 8L15 11M12 8L9 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M3 15V16C3 18.8284 3 20.2426 3.87868 21.1213C4.75736 22 6.17157 22 9 22H15C17.8284 22 19.2426 22 20.1213 21.1213C21 20.2426 21 18.8284 21 16V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <p class="upload-text">Drag & drop files here or <span class="browse-link">browse</span></p>
                                        <p class="upload-hint">Supported formats: JPG, JPEG, PNG, PDF, DOC, DOCX, XLS, XLSX (Max 6MB per file)</p>
                                        <input type="file" id="custom-file-input" name="custom_attachment[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx" style="display: none;">
                                    </div>
                                    <div class="file-preview-container" id="file-preview-container" style="display: none;">
                                        <h6>Selected Files:</h6>
                                        <div class="file-list" id="file-list"></div>
                                    </div>
                                </div>
                                <input type="hidden" id="files" name="attachments" value="" />
                                
                                {{-- Existing Attachments --}}
                                @if(!empty($record->files))
                                <div class="existing-attachments-container mt-3">
                                    <h6>Existing Attachments:</h6>
                                    <div class="existing-attachments-list">
                                        @foreach(explode(',',$record->files) as $value)
                                        <div class="existing-attachment-item">
                                            <div class="file-info">
                                                <svg class="file-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <div>
                                                    <a href="{{ url('download-media-file?dir='.articleDir().'&file_name='.$value) }}" download class="file-name">{{$value}}</a>
                                                </div>
                                            </div>
                                            <input type="hidden" name="prev_files[]" value="{{$value}}" />
                                            <button type="button" class="remove-existing-file" onclick="removeExistingFile(this, '{{$value}}')">Remove</button>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="CdsTYButton-btn-primary add-btn">Save</button>
                        </div>
                    </form>
              
			</div>
	
	</div>
  </div>
</div>

@endsection
<!-- End Content -->
@section('javascript')
<script>
    // Global variables
    let selectedFiles = [];
    let existingFiles = [];
    const ed = initEditor("description");
    
    // Initialize existing files from hidden inputs
    $(document).ready(function() {
        $('input[name="prev_files[]"]').each(function() {
            existingFiles.push($(this).val());
        });
        
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
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
                if (!allowedTypes.includes(file.type)) {
                    showError(`File "${file.name}" is not a supported format. Please use JPG, JPEG, PNG, PDF, DOC, DOCX, XLS, or XLSX files.`);
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
        $("#form").submit(function(e) {
            e.preventDefault();
            
            var is_valid = formValidation("form");
            if (!is_valid) {
                return false;
            }

            // Upload files first, then submit form
            if (selectedFiles.length > 0) {
                uploadFiles();
            } else {
                submitForm();
            }
        });
    });

    // Function to remove existing files
    function removeExistingFile(button, filename) {
        $(button).closest('.existing-attachment-item').remove();
        // Remove from existingFiles array
        const index = existingFiles.indexOf(filename);
        if (index > -1) {
            existingFiles.splice(index, 1);
        }
    }

    function uploadFiles() {
        const formData = new FormData();
        
        // Add files to FormData
        selectedFiles.forEach((file, index) => {
            formData.append(`file[${index}]`, file);
        });

        // Add CSRF token
        formData.append('_token', csrf_token);

        $.ajax({
            url: BASEURL + "/articles/upload-files",
            type: "POST",
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
                if (response.status === true) {
                    // Store uploaded filenames
                    const uploadedFiles = response.files || [response.filename];
                    $('#files').val(uploadedFiles.join(','));
                    submitForm();
                } else {
                    validation(response.message || 'File upload failed');
                }
            },
            error: function() {
                hideLoader();
                internalError();
            }
        });
    }

    function submitForm() {
        var formData = new FormData($("#form")[0]);
        var content = ed.getValue();
        var reading_time = contentReadingTime(content);
        formData.append("reading_time", reading_time);
        
        // Add existing files to form data
        existingFiles.forEach((file, index) => {
            formData.append(`prev_files[${index}]`, file);
        });
        
        var url = $("#form").attr('action');
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
                    redirect(response.redirect_back);
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
</script>
<script>
    function showPopup(url, method = "get", paramters = {}) {
        $.ajax({
            url: url + "?_token=" + csrf_token,
            dataType: "json",
            type: method,
            data: paramters,
            beforeSend: function () {
                showLoader();
                // var html = "<div class='popup-loader'><i class='fa fa-2x fa-spin fa-spinner'></i></div>"
                // $("#popupModal").html(html);
            },
            success: function (result) {
                if (result.status == true) {
                    $("#popupModal").html(result.contents);
                    initFloatingLabel();
                    $("#popupModal").modal("show");
                } else {
                    if (result.message != undefined) {
                        errorMessage(result.message);
                    } else {
                        errorMessage("No Modal Data found");
                    }
                }
            },
            complete: function () {
                hideLoader();
            },
            error: function () {
                hideLoader();
                internalError();
            },
        });
    }
    $(document).ready(function() {
        initFloatingLabel();
        $('.form-group input[type="text"], .form-group input[type="email"], .form-group input[type="password"], .form-group input[type="url"], .form-group select, .form-group textarea').each(function() {
                // Apply 'focused' class when the input is focused
                $(this).on('focus', function() {
                    $(this).addClass('focused');
                });
        
                // Remove 'focused' class if the field is empty and loses focus
                $(this).on('blur', function() {
                    if ($(this).val() === '') {
                        $(this).removeClass('focused');
                    }
                });
    
                $(this).on('change', function() {
                    if ($(this).val() === '') {
                        $(this).removeClass('focused');
                    }else{
                        $(this).addClass('focused');
                    }
                });
    
                // On page load, check if the field has a value
                if ($(this).val() !== '') {
                    $(this).addClass('focused');  // Apply 'focused' class if field is not empty
                    $(this).focus();               // Focus on the input field if it has a value
                }
            });
        setTimeout(() => {
            $("body").trigger('click');
        }, 1500);
    });
    function initFloatingLabel(){
        $('.form-group input[type="text"], .form-group input[type="email"], .form-group input[type="password"], .form-group input[type="url"], .form-group select, .form-group textarea').each(function() {
            // Apply 'focused' class when the input is focused
            $(this).on('focus', function() {
                $(this).addClass('focused');
            });
    
            // Remove 'focused' class if the field is empty and loses focus
            $(this).on('blur', function() {
                if ($(this).val() === '') {
                    $(this).removeClass('focused');
                }
            });

            $(this).on('change', function() {
                if ($(this).val() === '') {
                    $(this).removeClass('focused');
                }else{
                    $(this).addClass('focused');
                }
            });

            // On page load, check if the field has a value
            if ($(this).val() !== '') {
                $(this).addClass('focused');  // Apply 'focused' class if field is not empty
                $(this).focus();               // Focus on the input field if it has a value
            }
        });
    }
</script>
@endsection