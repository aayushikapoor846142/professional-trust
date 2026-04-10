@extends('admin-panel.layouts.app')
@php 
$page_arr = [
    'page_title' => 'Add Article ',
    'page_description' => 'Add New Article.',
    'page_type' => 'add-articles',
];
@endphp
@section('page-submenu')
{!! pageSubMenu('articles',$page_arr) !!}
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/custom-file-upload.css') }}">
@endsection

@section('content')
 <div class="ch-action">
                    <!-- <a href="{{ baseUrl('articles') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-left fa-solid me-1"></i>
                        Back
                    </a> -->
                </div>
				<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
  <form id="form" class="js-validate" action="{{ baseUrl('articles/save') }}" method="post">
                        @csrf
                    <div class="cds-ty-dashboard-articles-segments"> <div class="cds-ty-dashboard-articles-segments-title"> Basic details </div> <div class="row">
                            <div class="col-md-6 col-sm-6">
                                {!! FormHelper::formInputText([
                                'name'=>"name",
                                'required'=>true,
                                'id'=>"name",
                                "label"=>"Enter Topic Title",
                              
                                ]) !!}
                            </div>
                            <div class="col-md-6 col-sm-6">
                                {!! FormHelper::formInputText(['name'=>"slug","id" => "slug","label"=>"Enter Page Slug","required"=>true,'events'=>['oninput=replaceSpacewithDash(this)', 'onblur=replaceSpacewithDash(this)']]) !!}
                            </div>
                            <div class="col-md-6 col-sm-6">
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
                                    'selected' => '',
                                    'is_multiple' => false
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
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
                                    'selected' => '',
                                    'is_multiple' => false
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cds-ty-dashboard-articles-segments">
                        <div class="cds-ty-dashboard-articles-segments-title"> Article Summary </div><div class="row">
                            <div class="col-md-12">
                                {!! FormHelper::formTextarea(['name'=>"summary",
                                    'id'=>"summary",
                                    "label"=>"Enter Summary",
                                    'required' => true,
                                    'class'=>" noval",
                                    
                                ]) !!}

                            </div></div></div><div class="cds-ty-dashboard-articles-segments"> <div class="cds-ty-dashboard-articles-segments-title"> Main Content </div><div class="row">
                            <div class="col-md-12"><div class="cds-ty-dashboard-articles-shortcode">
                                <a class="cds-ty-dashboard-articles-shortcode-button" href="javascript:;" data-modal="popupModal" type="button" href="javascript:void(0)" onclick="showPopup('<?= url('editor-shortcode') ?>')">
                                    <i class="fa-code fa-solid"></i> Shortcode
                                </a> </div>
                            </div>
                            <div class="col-md-12">
                                <label class="col-form-label input-label">Enter Description <span class="danger">*</span></label>
                                {!! FormHelper::formTextarea([
                                    'name' => 'description',
                                    'id' => 'description',
                                    'class' => 'cds-texteditor',
                                    'textarea_class' => 'noval',
                                    'required' =>  true,
                                ]) !!}
                            </div>   </div>   </div><div class="cds-ty-dashboard-articles-segments"> <div class="cds-ty-dashboard-articles-segments-title"> Banner Image </div>
                            <div class="row">
                                <div class="col-sm-12 col-xs-12 mb-4">
                                    <div class="form-group js-form-message"> 
                                    
                                    <div class="cds-ty-dashboard-articles-segments-banner-image">
                                    
                                    
                                        <input type="hidden" name="images" id="image" class="form-control">
                                        <div class="cds-ty-dashboard-articles-segments-banner-image-upload">
                                            <img id="showImage" width="100" height="100" src="assets/svg/browse.svg" alt="Profile Image">
                                            <button type="button" onclick="showPopup('<?php echo baseUrl('articles/image-cropper') ?>')" class="CdsTYButton-btn-primary btn-sm">Choose Image</button>
                                        </div>
                                    </div></div>
                                </div>
                            </div>
                    </div><div class="cds-ty-dashboard-articles-segments"> <div class="cds-ty-dashboard-articles-segments-title"> Attachments </div>
                            <div class="row">
                            <div class="col-md-12 mt-3 mt-md-0">
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
                            </div>
                        </div> </div> <div class="cds-ty-dashboard-articles-segments"> <div class="cds-ty-dashboard-articles-segments-title"> Preferences </div>
                            <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xl-4">
                                <div class="form-group">
                                    <div class="form-check cds-ty-dashboard-articles-segments-pref">
                                    {!! FormHelper::formCheckbox(['name' => "show_on_home", 'value' => 1, 'id' => "show_on_home", 'required' => true]) !!}
                                    <label class="form-check-label" for="show_on_home">Show On Home</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xl-4">
                                <div class="form-group">
                                    <div class="form-check cds-ty-dashboard-articles-segments-pref">
                                    {!! FormHelper::formCheckbox(['name' => "is_featured", 'value' => 1,  'id' => "is_featured", 'required' => true]) !!}
                                    <label class="form-check-label" for="is_featured">Featured</label>
                                    </div>
                                </div>
                            </div> </div> </div> 
                            
                            
                            <div class="cds-ty-dashboard-articles-segments"> <div class="cds-ty-dashboard-articles-segments-title"> SEO Content </div>
                            <div class="row">
                            <div class="col-md-12">
                                {!! FormHelper::formInputText([
                                'name'=>"seo_title",
                                'required'=>true,
                                'id'=>"seo_title",
                                "label"=>"Enter Seo Title",
                                'events'=>['oninput=validateString(this)']
                                ]) !!}
                            </div>
                            <div class="col-md-6 col-sm-6">
                                {!! FormHelper::formTextarea([
                                'name'=>"seo_keywords",
                                'id'=>"seo_keywords",
                                'required' => true,
                                "label"=>"Enter Seo Keywords",
                                'class'=>"editor noval",
                                
                                ]) !!}
                            </div>
                            <div class="col-md-6 col-sm-6">
                                {!! FormHelper::formTextarea([
                                'name'=>"seo_description",
                                'id'=>"seo_description",
                                'required' => true,
                                "label"=>"Enter Seo Description",
                                'class'=>"editor noval",
                               
                                ]) !!}
                            </div></div></div>
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
    const ed = initEditor("description");
    
    $(document).ready(function() {
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
                    // Reset form fields
                    $('#name').val('');
                    $('#slug').val('');
                    $('#summary').val('');
                    ed.setValue('');
                    $('#seo_title').val('');
                    $('#seo_keywords').val('');
                    $('#seo_description').val('');
                    
                    // Reset file selection
                    selectedFiles = [];
                    updateFilePreview();
                    
                    // Reset file input
                    $('#custom-file-input').val('');
                    
                    // Reset checkboxes
                    $('#show_on_home').prop('checked', false);
                    $('#is_featured').prop('checked', false);
                    
                    // Reset select dropdowns
                    $('#category').val('').trigger('change');
                    $('#article_type').val('').trigger('change');
                    
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
@endsection