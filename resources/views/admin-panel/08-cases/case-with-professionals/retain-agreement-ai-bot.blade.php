@extends('admin-panel.08-cases.case-with-professionals.my-cases-master')
@section('case-container')
<style>
    .cds-container {
        display: flex;
        justify-content: space-between;
        padding: 20px;
        gap: 20px;
    }

    .chat-history {
        width: 25%;
        background-color: #fff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .chat-history ul {
        list-style-type: none;
        margin-bottom: 20px;
    }

    .chat-history li {
        padding: 8px;
        margin-bottom: 8px;
        background-color: #f0f0f0;
        border-radius: 5px;
    }

    .suggest-colleges {
        width: 100%;
        padding: 10px;
        background-color: #3b8d99;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .suggest-colleges:hover {
        background-color: #2a6a74;
    }

    .chatbox-area {
        width: 50%;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        min-height: 500px;
        justify-content: space-between;
        height:500px;
        overflow:auto;
    }

    .chat-bubble {
        margin-bottom: 20px;
        background-color: #ebf1f7;
        padding: 15px;
        border-radius: 10px;
    }
    .application-draft-area{
        width: 50%;
        background-color: #FFF;
        padding: 15px;
    }
    .application-draft {
        background-color: #fff9e6;
        padding: 15px;
        border-radius: 8px;
        border: 1px dashed #ccc;
        
    }

    .case-summary-title {
        background-color: #fff9e6;
        padding: 15px;
        border-radius: 8px;
        border: 1px dashed #ccc;
        margin-top:15px;
    }
    .file-upload {
        background-color: #fff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    #file-upload {
        margin-bottom: 10px;
    }


    .message-input {
        display: flex;
        align-items: center;
        gap: 10px;
        border-top: 1px solid #ccc;
        padding-top: 10px;
    }

    .message-input input {
        width: 90%;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    .upload-draft-area{
        width: 50%;
        background-color: #FFF;
        padding: 15px;
    }
    .upload-draft {
        background-color: #fff9e6;
        padding: 15px;
        border-radius: 8px;
        border: 1px dashed #ccc;
        
    }
    .upload-box {
      border: 2px solid #ddd;
      border-radius: 12px;
      padding: 20px;
      background-color: #f9f9f9;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      max-width: 700px;
      margin: 30px auto;
    }

    .upload-box .row > div {
      margin-bottom: 15px;
    }

    .upload-box label {
      font-weight: 500;
      font-size: 1.1rem;
    }

    .upload-box input[type="file"] {
      padding: 8px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    .upload-box button {
      border-radius: 6px;
    }

    .summary-file-block {
  margin-bottom: 15px;
  font-family: sans-serif;
}

.summary-file-label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

.summary-file-row {
  display: flex;
  align-items: center;
  gap: 10px;
  background-color: #f1f1f1;
  padding: 10px 15px;
  border: 1px solid #ddd;
  border-radius: 8px;
  flex-wrap: wrap;
}

.summary-file-name {
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.summary-download-btn,
.summary-remove-btn {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.summary-remove-btn {
  background-color: #f44336;
}

.summary-download-btn:hover {
  background-color: #45a049;
}

.summary-remove-btn:hover {
  background-color: #e53935;
}


</style>
<section class="cds-t21n-breadcrumbs-section">
        <div class="container">
            <div class="row">
                <!-- {{ Breadcrumbs::render('report.individual') }} -->
                Generate Summary
            </div>
        </div>
    </section>
    <section class="cds-t25n-content-reporting-form-section-content cds-post-case">
        <div class="container">
            <div class="cds-container">
                <div class="chatbox-area">
                    <div class="chat-area">


                    </div>
                    <form id="cds-post-form-1" action="{{ baseUrl('case-with-professionals/retain-agreements/ai-bot-chat/'.$case_id) }}">
                        @csrf
                        <div class="message-input">
                            <input type="text" name="message" placeholder="Type your message..." class="ai-message" />
                            <button class="CdsTYButton-btn-primary" type="submit">Send</button>
                        </div>
                    </form>
                </div>

                <div class="application-draft-area">
                    <div class="upload-draft">
                        <h4>Upload Document</h4>
                    </div>
                    <div class="upload-draft-summary">
                        <div class="file-inputs"></div>
                    </div>
                    <div class="application-draft">
                        <h4>Application Draft</h4>
                        <a href="javascript:;" class="CdsTYButton-btn-primary" onclick="showPopup('<?php echo baseUrl('case-with-professionals/retain-agreements/save-popup/'.$CaseRetainAgreements->unique_id) ?>')">Save Agreement</a>
                        <!-- <button type="button" class="CdsTYButton-btn-primary save-agreement">Save Agreement</button> -->
                    </div>
                    <div class="application-draft-summary"></div>
                    <div class="case-summary-title" style="display:none;">
                        <h4>Final Summary</h4>
                    </div>
                    <div class="case-summary" style="display:none;"></div>
                </div>
            </div>
        </div>
    </section>
    <!-- <input type="hidden" id="agreement_id" name="agreement_id" value="{{$CaseRetainAgreements->unique_id}}"> -->
@endsection

@section('javascript')

<script>
    $(document).ready(function() {
        // showPopup('<?php echo url('case/open-cv-modal') ?>');

       
        let summary_data = "";
        loadSummaryData();
        loadFileSummaryData();
        $("#cds-post-form-1").submit(function(e){
          
            e.preventDefault();
            var is_valid = formValidation("cds-post-form-1");
            if (!is_valid) {
                return false;
            }
           
            var formData = new FormData($(this)[0]);
            {{-- formData.append('conversation_id', "{{$conversation_id}}"); --}}

            var url = $("#cds-post-form-1").attr('action');
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
                        var ai_msg = $(".ai-message").val();
                        var sendmsgHtml = '<div class="chat-bubble sent-block"><p>'+ai_msg+'</p></div>';
                        $(".chat-area").append(sendmsgHtml);
                        var msg = response.message;
                        var application_draft = response.agreement_draft;
                        var file_label = response.file_label;


                        var msgHtml = '<div class="chat-bubble received-block"><p>'+msg+'</p></div>';
                        $(".chat-area").append(msgHtml);

                        // if(response.file_upload_required == true){
                        //     const uploadBox = $(`
                        //         <div class="upload-box">
                        //             <label>How many files you want to upload?</label>
                        //             <input type="number" class="file-count" placeholder="Enter number of files" />
                        //             <button class="generate-inputs-btn CdsTYButton-btn-primary">Generate Inputs</button>
                        //             <div class="file-inputs"></div>
                        //         </div>
                        //         `);

                        //     // Append to a container on your page
                        //     $('.upload-draft-summary').append(uploadBox); // Or any specific wrapper like $('#your-container')

                        //     // // Handle the generate button click
                        //     // uploadBox.find('.generate-inputs-btn').on('click', function(e) {
                        //     //     e.preventDefault();

                        //     //     const fileInputsContainer = uploadBox.find('.file-inputs');
                        //     //     const numberOfFiles = parseInt(uploadBox.find('.file-count').val());

                        //     //     fileInputsContainer.empty(); // clear previous

                        //     //     if (!isNaN(numberOfFiles) && numberOfFiles > 0) {
                        //     //         for (let i = 1; i <= numberOfFiles; i++) {
                        //     //             const fileGroup = $(`
                        //     //                 <div class="file-group">
                        //     //                     <label for="file${i}">${file_label}:</label>
                        //     //                     <input type="file" name="file${i}" id="file${i}" />
                                               
                        //     //                 </div>
                        //     //             `);
                        //     //             fileInputsContainer.append(fileGroup);
                        //     //         }
                        //     //         const submitAllBtn = $(`
                        //     //             <button type="button" class="submit-files-btn btn btn-success mt-2">Submit All Files</button>
                        //     //         `);
                        //     //         fileInputsContainer.append(submitAllBtn);
                                   

                        //             // fileInputsContainer.append(`
                        //             // <button type="submit" class="submit-files-btn">Submit Files</button>
                        //             // `);
                        //     //     } else {
                        //     //         alert('Please enter a valid number.');
                        //     //     }
                        //     // });
                        // //    var html = "<div class='upload-box'><div class='row'><div class='col-md-3'><label for='uploadDocuments'>'"+file_label+"'</label></div><div class='col-md-7'><input type='file' name='upload_documents' class='form-control file-input'></div><div class='col-md-2'><button type='button' class='CdsTYButton-btn-primary upload-doc-btn'>Upload</button></div></div>";
                        // //     $(".upload-draft-summary").append(html);
                        // }
                        // send message

                        // if(response.type == "text"){
                        //     var sendmsgHtml = '<div class="chat-bubble sent-block"><p>'+response.send_message+'</p></div>';
                        //     $(".chat-area").append(sendmsgHtml);
                        // }else{
                        //     var sendmsgHtml = '<div class="chat-bubble sent-block"><a href="'+response.file+'" >Download</a></div>';
                        //     $(".chat-area").append(sendmsgHtml);
                        // }
                       

                        // receive message
                        // if(response.type == "text"){
                        //     var msgHtml = '<div class="chat-bubble received-block"><p>'+msg+'</p></div>';
                        //     $(".chat-area").append(msgHtml);
                        // }else{
                        //     var msgHtml = '<div class="chat-bubble received-block"><p>'+response.preview+'</p></div>';
                        //     $(".chat-area").append(msgHtml);
                        // }

                        // // application draft
                        var applicationDraft = '<div class="chat-bubble"><p>'+application_draft+'</p></div>';
                        $(".application-draft-summary").html(applicationDraft);
                        $(".ai-message").val("");
                     
                    } else {
                        if(response.error_type == "error"){
                            errorMessage(response.message);
                        }else{
                            validation(response.message);
                        }
                       
                    }
                },
                error: function() {
                    internalError();
                }
            });
        });

        $(document).on('click', '.generate-inputs-btn', function(e) {
            e.preventDefault();

            const uploadBox = $(this).closest('.upload-box');
            const fileInputsContainer = uploadBox.find('.file-inputs');
            const numberOfFiles = parseInt(uploadBox.find('.file-count').val());

            fileInputsContainer.empty(); // clear previous

            if (!isNaN(numberOfFiles) && numberOfFiles > 0) {
                for (let i = 1; i <= numberOfFiles; i++) {
                    const fileGroup = $(`
                        <div class="file-group">
                            <label for="file${i}">File ${i}:</label>
                            <input type="file" name="file${i}" id="file${i}" />
                        </div>
                    `);
                    fileInputsContainer.append(fileGroup);
                }
                const submitAllBtn = $(`
                                        <button type="button" class="submit-files-btn btn btn-success mt-2">Submit All Files</button>
                                    `);
                                    fileInputsContainer.append(submitAllBtn);
                                   

                // Optional: Add a "submit all" button
                // fileInputsContainer.append(`<button type="submit" class="submit-files-btn">Submit Files</button>`);
            } else {
                alert('Please enter a valid number.');
            }
        });
        
       
        // $("#form").submit(function(e) {
        //     e.preventDefault();
        //     var is_valid = formValidation("form");
        //     if (!is_valid) {
        //         return false;
        //     }
        //     var formData = new FormData($(this)[0]);
        //     var url = $("#form").attr('action');
        //     $.ajax({
        //         url: url,
        //         type: "post",
        //         data: formData,
        //         cache: false,
        //         contentType: false,
        //         processData: false,
        //         dataType: "json",
        //         beforeSend: function() {
        //             showLoader();
        //         },
        //         success: function(response) {
        //             hideLoader();
        //             if (response.status == true) {
        //                 successMessage(response.message);
        //                 redirect(response.redirect_back);
        //             } else {
        //                 validation(response.message);
        //             }
        //         },
        //         error: function() {
        //             internalError();
        //         }
        //     });

        // });
        
        // $(document).on('click', '.upload-doc-btn', function () {
        //     const box = $(this).closest('.upload-box');
        //     const fileInput = box.find('.file-input')[0];
        //     const files = fileInput.files[0];
            
        //     const btn = $(this);
        //     if (files == '') {
        //         errorMessage("Please select at least one file.");
        //         return;
        //     }

        //     const formData = new FormData();
        //     formData.append('upload_documents', files);
        //     // for (let i = 0; i < files.length; i++) {
        //     //     formData.append('upload_documents', files[i]);
        //     // }
        //     formData.append('conversation_id', "{{$conversation_id}}");
        //     formData.append('_token', $('input[name=_token]').val());

        //     $.ajax({
        //         url: "{{url('case/upload-document')}}",
        //         method: "POST",
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         data: formData,
        //         processData: false,
        //         contentType: false,
        //         beforeSend: function() {
        //             btn.disabled = true;   
        //         },
        //         success: function (response) {
        //             btn.false = true;   
        //             if(response.status == true){
        //                 if (response.api_response && Array.isArray(response.api_response)) {
        //                         response.api_response.forEach(function(item, index) {
                                  
        //                         var sendHtml = '<div class="chat-bubble received-block"><a href="'+item.download_url+'" download>Download</a></div>';
        //                         $(".chat-area").append(sendHtml);

        //                         var receiveHtml = '<div class="chat-bubble sent-block"><p>'+item.message+'</p></div>';
        //                         $(".chat-area").append(receiveHtml);
                                
        //                         // application draft
        //                         var applicationDraft = '<div class="chat-bubble"><p>'+item.application_draft+'</p></div>';
        //                         $(".application-draft-summary").html(applicationDraft);
        //                     // // Optional: Append download link to a div
        //                     //     $('#download-links').append(
        //                     //         '<p><a href="' + item.download_url + '" target="_blank">Download File ' + (index + 1) + '</a></p>'
        //                     //     );
        //                     });
        //                 } 
        //                 successMessage('Upload successful!');
        //                 fileInput.value = '';
        //             }else{
        //                 errorMessage(response.message);
        //             }
                    
                   
        //         },
        //         error: function (xhr) {
        //             errorMessage('Upload failed!');
        //         }
        //     });
        // });

        $(document).on('click', '.submit-files-btn', function(e) {
            e.preventDefault();  // Prevent the default action (e.g., form submission)
          
            const $button = $(this);  // Get the clicked button
            const fileIndex = $button.data('file-index');  // Get the file index from data attribute
            const fileGroup = $button.closest('.file-inputs');
            const $fileInputs = fileGroup.find('input[type="file"]');
         
            // const personalDocInput = fileGroup.find('.personal_document_id');
           
            // if (!file) {
            //     errorMessage(`Please select a file for File ${fileIndex}.`);
            //     return;  // If no file is selected, stop execution
            // }

            // Prepare the FormData object for file upload
            const formData = new FormData();
            $fileInputs.each(function () {
                const input = this;
                const files = input.files;

                if (files.length > 0) {
                    // Assuming one file per input
                    formData.append('upload_documents[]', files[0]);
                    console.log('Appending file:', files[0].name);
                }
            });
           
            formData.append('conversation_id', "{{$conversation_id}}");
            formData.append('_token', $('input[name=_token]').val());
            
            // if(personalDocInput.length == 0){
            //     formData.append('personal_document_id',0);
            // }else{
            //     formData.append('personal_document_id',personalDocInput.val());
            // }
           
            // AJAX request to upload the file
            $.ajax({
                url: "{{url('case/upload-document')}}",
                method: 'POST',
                data: formData,
                processData: false,  // Don't process the data (important for file uploads)
                contentType: false,  // Don't set the content type (important for file uploads)
                success: function(response) {
                    if(response.status == true){
                        if (response.status && Array.isArray(response.api_response)) {
                            response.api_response.forEach(function (item, index) {
                                var sendHtml = '<div class="chat-bubble received-block"><a href="'+item.download_url+'" download>Download</a></div>';
                                $(".chat-area").append(sendHtml);

                                var receiveHtml = '<div class="chat-bubble sent-block"><p>'+item.message+'</p></div>';
                                $(".chat-area").append(receiveHtml);
                                
                                // application draft
                                var applicationDraft = '<div class="chat-bubble"><p>'+item.application_draft+'</p></div>';
                                $(".application-draft-summary").html(applicationDraft);

                            });
                            if(response.profile_generation_request == true){
                                showPopup('<?php echo url('case/open-cv-modal') ?>');
                            }
                        }

                        successMessage('Upload successful!');
                        
                        // if(fileGroup.find('.personal_document_id').length === 0)
                        // {
                        //     fileGroup.append('<input type="text" class="form-control personal_document_id" name="personal_document_id" value="'+response.personalDocumentsID+'">');
                        // }else{
                        //     fileGroup.find('.personal_document_id').val(response.personalDocumentsID);
                        // }
                      
                    }else{
                        errorMessage(response.message);
                    }
                },
                error: function() {
                    alert(`Error uploading File ${fileIndex}.`);
                }
            });
        });

    });
    function goBack(step){
        $(".cds-post-step").removeClass("cds-post-step-active");
        $("#panel-"+step).addClass("cds-post-step-active");
    }

    function loadSummaryData()
    {
        $.ajax({
            type: "GET",
            url: BASEURL + '/case-with-professionals/retain-agreements/fetch-ai-bot-chat/{{$case_id}}',
            dataType: 'json',
            success: function(data) {
                $(".chat-area").append(data.contents);
                $(".application-draft-summary").html(data.application_draft);
            },
        });
    }


    function loadFileSummaryData()
    {
        $.ajax({
            type: "GET",
            url: SITEURL + '/case/get-file-case-summary/{{$conversation_id}}',
            dataType: 'json',
            success: function(data) {
                $(".upload-draft-summary").append(data.contents);
                $(".application-draft-summary").html(data.application_draft);
            },
        });
    }

    function getApplicationSummary(e)
    {
        var url = $(e).data('href');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: url,
            type: "post",
            data: {application_data: $(".application-draft-summary").html()},
            dataType: "json",
            beforeSend: function() {
                showLoader();
            },
            success: function(response) {
                hideLoader();
                // console.log(response.status);
                if(response.status == true){
                    successMessage('Summary generated Succesfully');
                    $(".case-summary").show();
                    $(".case-summary-title").show();
                    $(".case-summary").html("<div class='post-case-summary'>"+response.message+"</div><button type='button' class='CdsTYButton-btn-primary' onclick='postCase()'>Post Case </button>");
                    summary_data = response.data;
                }else{
                    errorMessage(response.message);
                }
               
            },
            error: function() {
                internalError();
            }
        });
    }

    function postCase()
    {
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{url('case/post-application-case')}}",
            type: "post",
            data: {summary_data: summary_data},
            dataType: "json",
            beforeSend: function() {
                showLoader();
            },
            success: function(response) {
                hideLoader();
                if(response.status == true){
                    successMessage(response.message);
                    window.location.href = response.redirect_url;
                }else{
                    errorMessage(response.message);
                }   
               
            },
            error: function() {
                internalError();
            }
        });
    }
</script>
@endsection
