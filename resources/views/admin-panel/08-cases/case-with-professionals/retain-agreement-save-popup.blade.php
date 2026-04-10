
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" >{{$pageTitle}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="retain-ai-agreement-form" class="js-validate" action="{{ baseUrl('case-with-professionals/retain-agreements/save-ai-retain-agreements') }}" method="post">
                @csrf
                <input type="hidden" name="agreement_id" value="{{$agreement_id}}">
                <div class="row">
                    <div class="col-xl-12">
                         {!! FormHelper::formInputText([
                            "label"=>"Title",
                            'name'=>"title",
                            'id'=>"title",
                            'required'=>true,
                        ]) !!}
                    </div>  
        
                </div>
                <div class="text-end mt-4">
                    <button type="submit" class="btn add-CdsTYButton-btn-primary">Save & publish</button>
                </div>
            </form>
        </div>
    </div>
</div>
  
<script>
$(document).ready(function(){
    initSelect();
    $("#retain-ai-agreement-form").submit(function(e) {
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        var url = $("#retain-ai-agreement-form").attr('action');
        
        var is_valid = formValidation("retain-ai-agreement-form");
        
        if (!is_valid) {
            return false;
        }
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
                if (response.status == true) {
                    hideLoader();
                    successMessage(response.message);
                    location.reload();
                } else {
                    hideLoader();
                    validation(response.message);
                }
            },
            error: function() {
                internalError();
            }
        });

    });
    

})

</script>

