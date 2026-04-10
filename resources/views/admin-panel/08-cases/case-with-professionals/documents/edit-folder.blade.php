
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" >{{$pageTitle}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="add-folder-form" class="js-validate" action="{{ baseUrl('case-with-professionals/documents/update-folder/'.$record->unique_id) }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                        {!! FormHelper::formInputText([
                            'name'=>"name",
                            'id'=>"name",
                            "label"=> "Enter Name",
                            "required"=>true,
                            'value' => $record->name ?? '',
                            'events'=>['oninput=replaceSpacewithDash(this)', 'onblur=replaceSpacewithDash(this)']
                            ])
                            !!}
                    </div>  
                    <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                        {!! FormHelper::formTextarea([
                            'name' => 'description',
                            'id' => 'description',
                            "label"=> "Enter Description",
                            'class' => 'select2-input ga-country',
                            'value'=>html_entity_decode($record->description ?? ''),
                            'textarea_class' => 'noval',
                        ]) !!}
                    </div>

                    <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                            <div class="form-group">
                                <div class="form-check cds-ty-dashboard-articles-segments-pref">
                                {!! FormHelper::formCheckbox(['name' => "is_hidden", 'value' => 1,'checked'=>$record->is_hidden, 'id' => "is_hidden"]) !!}
                                <label class="form-check-label" for="is_hidden">Mark as Hidden</label>
                                </div>
                            </div>
                  
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
       
    $("#add-folder-form").submit(function(e) {
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        var url = $("#add-folder-form").attr('action');
        
        var is_valid = formValidation("add-folder-form");
        
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
                    validation(response.message);
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

    });
    

})

</script>

