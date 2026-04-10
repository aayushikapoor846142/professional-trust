
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" >{{$pageTitle}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="add-encryption-folder-form" class="js-validate" method="post">
                @csrf
                <div class="row">
                    <input type="hidden" name="documents" id="documents" value="{{ $documentIds }}" />
                    <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                        {!! FormHelper::formInputText([
                            'name'=>"folder_name",
                            'id'=>"folder_name",
                            "label"=> "Enter Encryption Folder Name",
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
    $("#add-encryption-folder-form").submit(function (e) {
    e.preventDefault();

    var el = $(this).find('button[type="submit"]'); 

    var formData = new FormData(this); 
    var url = $(this).attr('action');

    var is_valid = formValidation("add-encryption-folder-form");

    if (!is_valid) {
        return false;
    }

    $.ajax({
        url: BASEURL + "/case-with-professionals/documents/encrypt-documents",
        type: "POST",
        data: formData,
        processData: false, 
        contentType: false, 
        beforeSend: function () {
            el.html('<i class="fa fa-spinner fa-spin"></i> Encrypting...');
        },
        success: function (response) {
            if (response.status === true) {
                successMessage(response.message);
                setTimeout(() => {
                            location.reload();
                        }, 200);
            } else {
                errorMessage(response.message);
            }
        },
        error: function (xhr) {
            internalError();
        },
        complete: function () {
            el.html('<i class="fa fa-lock"></i> Encrypt Documents');
        }
    });
});

})

</script>

