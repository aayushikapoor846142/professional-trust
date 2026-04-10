<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{$pageTitle}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="spam-form" action="{{ baseUrl('reviews/review-received/update-spam-review/' . $record->unique_id) }}" method="post">
                @csrf
          
                      <label class="col-form-label input-label">Enter Description <span class="danger">*</span></label>
                                {!! FormHelper::formTextarea([
                                    'name' => 'spam_reason',
                                    'id' => 'spam_reason',
                                    'class' => 'cds-texteditor',
                                    'textarea_class' => 'noval',
                                    'required' =>  true,
                                        'value'=> html_entity_decode($record->spam_reason) ?? '',
                                ]) !!}
                <div class="form-group text-center mt-4">
                    <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
  
<script>
    $(document).ready(function(){
            const ed = initEditor("spam_reason");
        initGoogleAddress();
          $("#spam-form").submit(function(e) {
                e.preventDefault();
                var is_valid = formValidation("address-form");
                if(!is_valid){
                    return false;
                }
                var formData = new FormData($(this)[0]);
                var url = $("#spam-form").attr('action');
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
                            closeModal();
                        } else {
                            validation(response.message);
                        }
                    },
                    error: function() {
                        internalError();
                    }
                });

            });
        });
       
    
</script>