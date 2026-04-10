
    <div class="demo-container">
        <div class="demo-section">
            <div id="aiFormContainer"></div>
        </div>
    </div>

      <script>
      
        // Get the dynamic form config from the controller (ensure $records is valid JSON)
        const formConfig = @json($records ?? []);

        // Initialize form builder
        const formBuilder = new FormBuilder();

        // Render the form if config is available
        if (formConfig && Object.keys(formConfig).length > 0) {
            formBuilder.cdsFbBuilderRender('aiFormContainer', formConfig, {
                onSave: function(data) {
                    // Handle save action
                    document.getElementById('result').textContent = JSON.stringify(data, null, 2);
                    // You can add AJAX here to submit data to the server if needed
                },
                saveButtonText: 'Submit Form',
                clearButtonText: 'Reset',
                showClearButton: true
            });
        } else {
            document.getElementById('aiFormContainer').innerHTML = '<div style="color:red;">No form configuration found.</div>';
        }

        formBuilder.cdsFbBuilderRender('aiFormContainer', formConfig, {
            onSave: function(data) {
                // Handle save action
               
                const result = [];
                if (formConfig && Array.isArray(formConfig.questions)) {
                    formConfig.questions.forEach(config => {
                        if (config.id && data.hasOwnProperty(config.id)) {
                            result.push({
                                question: config.question || config.label || config.name || config.id,
                                value: data[config.id]
                            });
                        }
                    });
                }
               
                var case_id = "{{$case_id}}";
                var url  = BASEURL+"/case-with-professionals/retain-agreements/save-ai-retain-agreements/"+case_id;
                $.ajax({
                    url: url,
                    type: "post",
                    data: { answers: result },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token in header
                    },
                    cache: false,
                    // contentType: false,
                    // processData: false,
                    dataType: "json",
                    beforeSend: function() {
                        showLoader();
                    },
                    success: function(response) {
                        hideLoader();
                        if (response.status == true) {
                            successMessage(response.message);
                            closeModal();
                            if (editorInstance && editorInstance.editor && typeof editorInstance.editor.setContent === 'function') {
                                editorInstance.editor.setContent({ html: response.agreement });
                            
                            }
                            // location.reload();
                        } else {
                            errorMessage(response.message);
                        }
                    },
                    error: function(xhr) {
                        internalError();
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.error_type === 'validation') {
                            validation(xhr.responseJSON.message);
                        } else {
                            errorMessage('An unexpected error occurred. Please try again.');
                        }
                    }
                });
            },
            saveButtonText: 'Submit Form',
            clearButtonText: 'Reset',
            showClearButton: true
        });
    </script>