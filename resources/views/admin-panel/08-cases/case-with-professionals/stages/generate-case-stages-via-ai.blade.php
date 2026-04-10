<div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{$pageTitle}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div id="cds-retainer-form">
                Please wait while we processing...<i class="fa fa-spinner fa-spin"></i>
            </div>
            <!-- <div id="myFormContainers"></div> -->
        </div>
    </div>
</div>
<style>
    #cds-retainer-form{
        text-align: center;
    }
</style>

<script>
$(document).ready(function() {
    // generateRetainerAgreement();
    $("#popup-form").submit(function(e) {
        e.preventDefault();
        var is_valid = formValidation("popup-form");
        if (!is_valid) {
            return false;
        }
        var formData = new FormData($(this)[0]);
        var url = $("#popup-form").attr('action');
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
                    redirect(response.redirect_back);
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
function generateRetainerAgreement(){
    $.ajax({
        url: "{{ baseUrl('case-with-professionals/stages/generate-stages-via-ai/'.$case_id) }}",
        type: "post",
        data:{
            _token:csrf_token
        },
        dataType: "json",
        beforeSend: function() {
            showLoader();
        },
        success: function(response) {
            if(response.status == true){
                hideLoader();
                successMessage(response.message);
                $("#cds-retainer-form").html(response.contents);
            }else{
                hideLoader();
                errorMessage(response.message);
            }
        },
        error: function() {
            internalError();
        }
    });
}
</script>