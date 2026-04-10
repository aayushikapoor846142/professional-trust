
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" >{{$pageTitle}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="add-document-form" class="js-validate" action="{{ baseUrl('my-services/save-document-folder') }}" method="post">
                @csrf
              
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                        {!! FormHelper::formInputText([
                                'name'=>"name",
                                "label"=>"Document Folder",
                                "required"=>true,
                            ]) 
                        !!}
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
       
    $("#add-document-form").submit(function(e) {
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        var url = $("#add-document-form").attr('action');
        
        var is_valid = formValidation("add-document-form");
        
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
                    $("#popupModal").modal('hide');
                    var options = '<option value="">Select Document Folder</option>';
                    $.each(response.records, function (key, value) {
                        options += '<option value="' + value.id + '">' + value.name + '</option>';
                    });
                   
                    $('#documents-folders').html(options);

                    // location.reload();
                } else {
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

