<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{$pageTitle}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="workflow-form" action="{{ baseUrl('case-with-professionals/stages/generate-workflow') }}" method="post">
                @csrf
                <input type="hidden" name="case_id" value="{{$case_id}}">
                    <div class="cds-form-group address2-div">
                        @if(!empty($retainAgreement) && $retainAgreement->agreement != '')
                            The workflow will be generated with the retain agreement.
                        @else
                            First You need to add retain agreement.
                        @endif
                    </div>
                <div class="form-group text-center mt-4">
                    @if(!empty($retainAgreement) && $retainAgreement->agreement != '')
                        <button type="submit" class="btn add-CdsTYButton-btn-primary">Ok</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
  
<script>
    $(document).ready(function(){
        initGoogleAddress();
          $("#workflow-form").submit(function(e) {
                e.preventDefault();
                var is_valid = formValidation("workflow-form");
                if(!is_valid){
                    return false;
                }
                var formData = new FormData($(this)[0]);
                var url = $("#workflow-form").attr('action');
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