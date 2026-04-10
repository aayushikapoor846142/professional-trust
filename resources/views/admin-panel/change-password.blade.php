@extends('admin-panel.layouts.app')

@section('content')
 <div class="ch-action">
                    <a href="{{ baseUrl('/') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-left fa-solid me-1"></i>
                        Back
                    </a>
                </div>
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <form id="form" class="js-validate" action="{{ baseUrl('/update-password/' . $record->unique_id) }}" method="post">
            @csrf
            <div class="cdsTYDashboard-password-change-container">
                <div class="cdsTYDashboard-password-change-container-header">
                    <h3>Change Password</h3>
                </div>
                <div class="cdsTYDashboard-password-change-container-body">
                    <div class="cdsTYDashboard-password-change-container-body-list">
                        <div class="cdsTYDashboard-password-change-container-body-list-segment">
                            {!! FormHelper::formInputText([ 'label' => "Name", 'readonly' => true, 'value' =>$record->first_name.' '.$record->last_name, ]) !!}
                        </div>
                        <div class="cdsTYDashboard-password-change-container-body-list-segment">
                            {!! FormHelper::formInputText([ 'label' => "Email", 'readonly' => true, 'value' => $record->email, ]) !!}
                        </div>
                        <div class="cdsTYDashboard-password-change-container-body-list-segment">
                            {!! FormHelper::formPassordText(['name'=>"old_password","label"=>"Old Password","required"=>true]) !!}
                        </div>
                        <div class="cdsTYDashboard-password-change-container-body-list-segment">
                            {!! FormHelper::formPassordText(['name'=>"password","label"=>"New Password","required"=>true]) !!}
                        </div>
                        <div class="cdsTYDashboard-password-change-container-body-list-segment">
                            {!! FormHelper::formPassordText(['name'=>"password_confirmation","label"=>"Confirm Password","id"=>"password_confirmation","required"=>true]) !!} @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="cdsTYDashboard-password-change-container-footer">
                    <button type="submit" class="CdsTYButton-btn-primary">Save</button>
                </div>
                <!-- End Input Group -->
            </div>
        </form>
  
			</div>
	
	</div>
  </div>
</div>


@endsection
<!-- End Content -->
@section('javascript')
<script>
    $(document).ready(function() {
        $("#form").submit(function(e) {
            e.preventDefault();
            var is_valid = formValidation("form");
            if (!is_valid) {
                return false;
            }
            var formData = $("#form").serialize();
            var url = $("#form").attr('action');

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
@endsection