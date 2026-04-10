<div class="modal-dialog modal-xl">
    <div class="modal-content">
        <form id="update-file-name" enctype="multipart/form-data"
            action="{{ baseUrl('/case-with-professionals/documents/decrypt-documents') }}" method="POST">
            <div class="modal-header">
                <h5 class="modal-title">{{$pageTitle}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @csrf
                <div class="cdsTYDashboard-chat-group-section">
                    <div class="cdsTYDashboard-chat-group-section-main-panel">
                        <div class="chatgroup-name-block">
                         
                            <div>
                                {!! FormHelper::formInputText(['name'=>"decryption_key","id" => "decryption_key","label"=>"Enter Password","required"=>true]) !!}
                           
                            </div>
                            <input type="hidden" name="documentIds" id="documentIds" value="{{ implode(',',$documentIds) }}">
                      
                        </div>
                    </div>
                 
                </div>
            </div>
            <div class="modal-footer">
                <div class="form-group text-start">
                    <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
                </div>
              
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
   
        $("#update-file-name").submit(function(e) {
            e.preventDefault();
            var is_valid = formValidation("update-file-name");
            if (!is_valid) {
                return false;
            }
            
           
            var formData = new FormData($(this)[0]);
            var url = $("#update-file-name").attr('action');
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

        });
    });
 
    
</script>