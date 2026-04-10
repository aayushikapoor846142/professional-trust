<div class="cds-ty-dashboard-box">
    <div class="cds-ty-dashboard-box-header">
        <h3>Change Password</h3>
    </div>
    
    <div class="cds-ty-dashboard-box-body">
        <form id="change-password-form" class="js-validate"
            action="{{ baseUrl('/update-password/' . $record->unique_id) }}" method="post">
            @csrf
            <div class="row">
                
                <div class="col-md-4 col-xl-4 col-lg-4 col-sm-12 mb-4">
                    {!! FormHelper::formPassordText(['name'=>"old_password","label"=>"Old Password","required"=>true]) !!}
                </div>

                <div class="col-md-4 col-xl-4 col-lg-4 col-sm-12 mb-4">
                    {!! FormHelper::formPassordText(['name'=>"password","label"=>"New Password","required"=>true]) !!}
                </div>

                <div class="col-md-4 col-xl-4 col-lg-4 col-sm-12 mb-4">
                    {!! FormHelper::formPassordText(['name'=>"password_confirmation","label"=>"Confirm Password","id"=>"password_confirmation","required"=>true]) !!}
                </div>
            </div>
            <div class="d-flex justify-content-start">
                <button type="submit" class="CdsTYButton-btn-primary">Save</button>
            </div>
            <!-- End Input Group -->
        </form>
    </div>
</div>

@push("scripts")
<script>
    $(document).ready(function() {
        $("#change-password-form").submit(function(e) {
            e.preventDefault();
            var is_valid = formValidation("change-password-form");
            if (!is_valid) {
                return false;
            }
            var formData = $("#change-password-form").serialize();
            var url = $("#change-password-form").attr('action');

            $.ajax({
                url: url,
                type: "post",
                data: formData,
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
@endpush