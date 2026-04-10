
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" >{{$pageTitle}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="add-workflow-form" class="js-validate" action="{{ baseUrl('case-with-professionals/stages/save-workflow') }}" method="post">
                @csrf
                <input type="hidden" name="case_id" value="{{$case_id}}">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                           {!! FormHelper::formSelect([
                                'name' => 'workflow_id[]',
                                'label' => 'Select Stage',
                                'class' => 'select2-input ga-country',
                                'id' => 'workflow_id',
                                'options' => $predefinedCaseStages,
                                'value_column' => 'id',
                                'label_column' => 'name',
                                'is_multiple' => true,
                                'required' => true,
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
       
    $("#add-workflow-form").submit(function(e) {
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        var url = $("#add-workflow-form").attr('action');
        
        var is_valid = formValidation("add-workflow-form");
        
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
                hideLoader();
                if (response.status == true) {
                    successMessage(response.message);
                    location.reload();
                } else {
                    errorMessage(response.message);
                }
            },
            error: function() {
                internalError();
            }
        });

    });
    

})

</script>

