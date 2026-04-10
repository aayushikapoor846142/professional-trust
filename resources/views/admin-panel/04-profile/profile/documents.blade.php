<div class="cds-t25n-content-professional-profile-container-main-body-information-expertise">
	<div class="cdsTYDashboard-profile-documents-container">
    	<form id="document-form" class="js-validate" action="{{ baseUrl('/professional-submit-profile') }}" method="post"  enctype="multipart/form-data">
        	@csrf
        	<input type="hidden" name="type" value="verify">
        	<input type="hidden" name="company_id" value="{{$user->cdsCompanyDetail->id ?? null}}">
       		<div class="cdsTYDashboard-profile-documents-container-body">
         		<div class="cdsTYDashboard-profile-documents-container-box">
        			<h6>Proof of identify</h6>
					<div class="cds-formbox">
						<div id="pfFileUploader">
							<div class="CDSFeed-upload-container" id="pfMediaUpload">
								<div class="CDSFeed-upload-area">
									<div class="CDSFeed-upload-icon">
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
											<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke-width="2"/>
											<polyline points="7,10 12,15 17,10" stroke-width="2"/>
											<line x1="12" y1="15" x2="12" y2="3" stroke-width="2"/>
										</svg>
									</div>
									<div class="CDSFeed-upload-text">
										<h4>Drop files here or click to upload</h4>
										<p>Support for JPG, PNG, PDF, DOC, DOCX, TXT, CSV, XLS, XLSX files</p>
									</div>
									<input type="file" class="CDSFeed-file-input" multiple accept=".jpg,.jpeg,.gif,.tiff,.png,.pdf,.docx,.doc,.txt,.csv,.xls,.xlsx">
								</div>
								<div class="CDSFeed-upload-preview"></div>
							</div>
						</div>
            		<input type="hidden" id="pf-files" name="pf_files" value="" />
						<input type="hidden" id="existing-pf-files" name="existing_pf_files" value="{{ $document->where('document_type', 'proof_of_identity')->pluck('file_name')->implode(',') }}" />
			  		<div class="cdsTYDashboard-profile-documents-small-title">Uploaded Documents</div>
					<div class="cdsTYDashboard-profile-documents-container-box-segment-wrap">
						@foreach($document as $key => $document_value) 
							@if($document_value->document_type == 'proof_of_identity') 
								@foreach(explode(',',$document_value->file_name) as $value)
									<div class="cdsTYDashboard-profile-documents-container-box-segment">
										<div class="cdsTYDashboard-profile-documents-details-wrap">
											<div class="cdsTYDashboard-profile-documents-image">
										     {!! getProfileImage(auth()->user()->unique_id ?? '') !!}
											</div>
											<div class="cdsTYDashboard-profile-documents-details">
												<h3>{{$value}}</h3>
												<span>{{date('d/m/Y',strtotime($document_value->created_at))}}</span>
											</div> 
										</div>
										<div class="cdsTYDashboard-profile-documents-buttons">
											<a href="{{baseUrl('professional/download-file?file='.$value)}}" class="cdsTYDashboard-button-light cdsTYDashboard-button-small">
												<i class="fa-regular fa-cloud-arrow-down"></i>   Download
											</a>
											<a href="{{baseUrl('professional/remove-file/'.$document_value->id)}}" class="cdsTYDashboard-button-light-outline cdsTYDashboard-button-small">
												Remove
											</a>
										</div>
									</div>
								@endforeach
							@endif
						@endforeach	 
					</div>
        		</div> 
				<div class="cdsTYDashboard-profile-documents-container-box">
        			<h6>Incorporation certificate</h6>
					<div class="cds-formbox">
						<div id="icFileUploader">
							<div class="CDSFeed-upload-container" id="icMediaUpload">
								<div class="CDSFeed-upload-area">
									<div class="CDSFeed-upload-icon">
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
											<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke-width="2"/>
											<polyline points="7,10 12,15 17,10" stroke-width="2"/>
											<line x1="12" y1="15" x2="12" y2="3" stroke-width="2"/>
										</svg>
									</div>
									<div class="CDSFeed-upload-text">
										<h4>Drop files here or click to upload</h4>
										<p>Support for JPG, PNG, PDF, DOC, DOCX, TXT, CSV, XLS, XLSX files</p>
									</div>
									<input type="file" class="CDSFeed-file-input" multiple accept=".jpg,.jpeg,.gif,.tiff,.png,.pdf,.docx,.doc,.txt,.csv,.xls,.xlsx">
								</div>
								<div class="CDSFeed-upload-preview"></div>
							</div>
						</div>
            		<input type="hidden" id="ic-files" name="ic_files" value="" />
						<input type="hidden" id="existing-ic-files" name="existing_ic_files" value="{{ $document->where('document_type', 'incorporation_certificate')->pluck('file_name')->implode(',') }}" />
					@foreach($document as $key => $document_value) 
						@if($document_value->document_type == 'incorporation_certificate') 
							@foreach(explode(',',$document_value->file_name) as $value)
								<div class="cdsTYDashboard-profile-documents-container-box-segment">
									<div class="cdsTYDashboard-profile-documents-details-wrap">
										<div class="cdsTYDashboard-profile-documents-image">
										     {!! getProfileImage(auth()->user()->unique_id ?? '') !!}
										</div>
										<div class="cdsTYDashboard-profile-documents-details">
											<h3>{{$value}}</h3>
											<span>{{date('d/m/Y',strtotime($document_value->created_at))}}</span>
										</div> 
									</div>
									<div class="cdsTYDashboard-profile-documents-buttons">
										<a href="{{baseUrl('professional/download-file?file='.$value)}}" class="cdsTYDashboard-button-light cdsTYDashboard-button-small">
											<i class="fa-regular fa-cloud-arrow-down"></i>   Download
										</a>
										<a href="{{baseUrl('professional/remove-file/'.$document_value->id)}}" class="cdsTYDashboard-button-light-outline cdsTYDashboard-button-small">
											Remove
										</a>
									</div>
								</div>
							@endforeach
						@endif
					@endforeach	 
				</div>
				<div class="cdsTYDashboard-profile-documents-container-box">
        			<h6>License</h6>
					<div class="cds-formbox">
						<div id="lcFileUploader">
							<div class="CDSFeed-upload-container" id="lcMediaUpload">
								<div class="CDSFeed-upload-area">
									<div class="CDSFeed-upload-icon">
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
											<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke-width="2"/>
											<polyline points="7,10 12,15 17,10" stroke-width="2"/>
											<line x1="12" y1="15" x2="12" y2="3" stroke-width="2"/>
										</svg>
									</div>
									<div class="CDSFeed-upload-text">
										<h4>Drop files here or click to upload</h4>
										<p>Support for JPG, PNG, PDF, DOC, DOCX, TXT, CSV, XLS, XLSX files</p>
									</div>
									<input type="file" class="CDSFeed-file-input" multiple accept=".jpg,.jpeg,.gif,.tiff,.png,.pdf,.docx,.doc,.txt,.csv,.xls,.xlsx">
								</div>
								<div class="CDSFeed-upload-preview"></div>
							</div>
						</div>
					<input type="hidden" id="lc-files" name="lc_files" value="" />
						<input type="hidden" id="existing-lc-files" name="existing_lc_files" value="{{ $document->where('document_type', 'license')->pluck('file_name')->implode(',') }}" />
					@foreach($document as $key => $document_value) 
						@if($document_value->document_type == 'license') 
							@foreach(explode(',',$document_value->file_name) as $value)
								<div class="cdsTYDashboard-profile-documents-container-box-segment">
									<div class="cdsTYDashboard-profile-documents-details-wrap">
										<div class="cdsTYDashboard-profile-documents-image">
										     {!! getProfileImage(auth()->user()->unique_id ?? '') !!}
										</div>
										<div class="cdsTYDashboard-profile-documents-details">
											<h3>{{$value}}</h3>
											<span>{{date('d/m/Y',strtotime($document_value->created_at))}}</span>
										</div> 
									</div>
									<div class="cdsTYDashboard-profile-documents-buttons">
										<a href="{{baseUrl('professional/download-file?file='.$value)}}" class="cdsTYDashboard-button-light cdsTYDashboard-button-small">
											<i class="fa-regular fa-cloud-arrow-down"></i>   Download
										</a>
										<a href="{{baseUrl('professional/remove-file/'.$document_value->id)}}" class="cdsTYDashboard-button-light-outline cdsTYDashboard-button-small">
											Remove
										</a>
									</div>
								</div>
							@endforeach
						@endif
					@endforeach
        		</div>
			</div>
        	<div class="cdsTYDashboard-profile-documents-container-footer">
            	<button type="submit" class="btn add-CdsTYButton-btn-primary">Save & publish</button>
        	</div>
    	</form>
	</div>
</div>
@push("styles")
<link rel="stylesheet" href="{{ url('assets/css/custom-file-upload.css') }}">
@endpush

@push("scripts")
<script src="{{ url('assets/js/custom-file-upload.js') }}"></script>
<script>
    
var pfUploader, icUploader, lcUploader;
    var pf_files_uploaded = [];
    var ic_files_uploaded = [];
    var lc_files_uploaded = [];
    var timestamp = "{{time()}}";
var upload_count = 0;
var isError = 0;

    $(document).ready(function(){
    
    // Check if FileUploadManager is available
    if (typeof FileUploadManager === 'undefined') {
        console.error('FileUploadManager is not loaded');
        return;
    }

    // Initialize FileUploadManager for Proof of Identity
    pfUploader = new FileUploadManager('#pfMediaUpload', {
        uploadUrl: BASEURL + "/upload-professional-document?_token=" + csrf_token + "&timestamp=" + timestamp + "&document_type=proof_of_identity",
        maxFiles: 60,
        maxFileSize: 6 * 1024 * 1024, // 6MB
            acceptedFiles: '.jpg,.jpeg,.gif,.tiff,.png,.pdf,.docx,.doc,.txt,.csv,.xls,.xlsx',
        onUploadStart: function() {
            showLoader(); // Show loader when file starts processing
        },
        onUploadComplete: function() {
            hideLoader(); // Hide loader when all uploads complete
        },
        onUploadSuccess: function(file, response) {
                pf_files_uploaded.push(response.filename);
            updateHiddenField('pf-files', pf_files_uploaded);
            },
        onUploadError: function(file, error) {
            errorMessage(error);
                isError = 1;
            console.error('Upload error:', error);
        },
        onFileRemoved: function(file) {
            if (file && file.serverName) {
                const index = pf_files_uploaded.indexOf(file.serverName);
                if (index > -1) {
                    pf_files_uploaded.splice(index, 1);
                    updateHiddenField('pf-files', pf_files_uploaded);
                }
                }
            }
        });

    // Initialize FileUploadManager for Incorporation Certificate
    icUploader = new FileUploadManager('#icMediaUpload', {
        uploadUrl: BASEURL + "/upload-professional-document?_token=" + csrf_token + "&timestamp=" + timestamp + "&document_type=incorporation_certificate",
        maxFiles: 60,
        maxFileSize: 6 * 1024 * 1024, // 6MB
            acceptedFiles: '.jpg,.jpeg,.gif,.tiff,.png,.pdf,.docx,.doc,.txt,.csv,.xls,.xlsx',
        onUploadStart: function() {
            showLoader(); // Show loader when file starts processing
        },
        onUploadComplete: function() {
            hideLoader(); // Hide loader when all uploads complete
        },
        onUploadSuccess: function(file, response) {
                ic_files_uploaded.push(response.filename);
            updateHiddenField('ic-files', ic_files_uploaded);
            },
        onUploadError: function(file, error) {
            errorMessage(error);
                isError = 1;
            console.error('Upload error:', error);
        },
        onFileRemoved: function(file) {
            if (file && file.serverName) {
                const index = ic_files_uploaded.indexOf(file.serverName);
                if (index > -1) {
                    ic_files_uploaded.splice(index, 1);
                    updateHiddenField('ic-files', ic_files_uploaded);
                }
                }
            }
        });

    // Initialize FileUploadManager for License
    lcUploader = new FileUploadManager('#lcMediaUpload', {
        uploadUrl: BASEURL + "/upload-professional-document?_token=" + csrf_token + "&timestamp=" + timestamp + "&document_type=license",
        maxFiles: 60,
        maxFileSize: 6 * 1024 * 1024, // 6MB
            acceptedFiles: '.jpg,.jpeg,.gif,.tiff,.png,.pdf,.docx,.doc,.txt,.csv,.xls,.xlsx',
        onUploadStart: function() {
            showLoader(); // Show loader when file starts processing
        },
        onUploadComplete: function() {
            hideLoader(); // Hide loader when all uploads complete
        },
        onUploadSuccess: function(file, response) {
                lc_files_uploaded.push(response.filename);
            updateHiddenField('lc-files', lc_files_uploaded);
            },
        onUploadError: function(file, error) {
            errorMessage(error);
                isError = 1;
            console.error('Upload error:', error);
        },
        onFileRemoved: function(file) {
            if (file && file.serverName) {
                const index = lc_files_uploaded.indexOf(file.serverName);
                if (index > -1) {
                    lc_files_uploaded.splice(index, 1);
                    updateHiddenField('lc-files', lc_files_uploaded);
                }
            }
        }
    });

    // Initialize all uploaders
    pfUploader.init();
    icUploader.init();
    lcUploader.init();
    
    // Helper function to update hidden fields
    function updateHiddenField(fieldId, filesArray) {
        const fileValue = filesArray.join(",");
        $('#' + fieldId).val(fileValue);
    }

        $("#document-form").submit(function(e) {
            e.preventDefault();

        // Check if there are any files still uploading
        const pfUploading = pfUploader.getUploadingFiles().length;
        const icUploading = icUploader.getUploadingFiles().length;
        const lcUploading = lcUploader.getUploadingFiles().length;
        
        if (pfUploading > 0 || icUploading > 0 || lcUploading > 0) {
            errorMessage("Please wait for all files to finish uploading");
            return;
        }
        
        // Check if any files are selected or if there are existing documents
        const existingDocuments = $('.cdsTYDashboard-profile-documents-container-box-segment').length;
        
        if (pf_files_uploaded.length === 0 && ic_files_uploaded.length === 0 && lc_files_uploaded.length === 0 && existingDocuments === 0) {
            errorMessage('Please select at least one document');
            return;
        }
        
        // All files are uploaded, submit the form
                submitForm();
    });
});

    function submitForm() {
    
    // If no new files were uploaded, set the existing document values
    if ($('#pf-files').val() === '' && $('#ic-files').val() === '' && $('#lc-files').val() === '') {
        const existingPfFiles = $('#existing-pf-files').val();
        const existingIcFiles = $('#existing-ic-files').val();
        const existingLcFiles = $('#existing-lc-files').val();
        
        if (existingPfFiles) {
            $('#pf-files').val(existingPfFiles);
        }
        if (existingIcFiles) {
            $('#ic-files').val(existingIcFiles);
        }
        if (existingLcFiles) {
            $('#lc-files').val(existingLcFiles);
        }
    }
    
        var formData = new FormData($("#document-form")[0]);
        var url = $("#document-form").attr('action');
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
                window.location.reload();
                } else {
                    errorMessage(response.message);
                }
            },
            error: function() {
                internalError();
            }
        });
    }
</script>
@endpush