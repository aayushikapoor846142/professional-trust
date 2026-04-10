@extends('admin-panel.layouts.app')
@if(checkPrivilege([
                        'route_prefix' => 'panel.staff',
                        'module' => 'professional-staff',
                        'action' => 'changepassword'
                    ]))
                    @php
                    $canChangePassword=true;
                    @endphp
@else
                    @php
                    $canChangePassword=false;
                    @endphp
@endif
@php 
$page_arr = [
    'page_title' => 'Change Password ',
    'page_description' => 'Change Staff Password.',
    'page_type' => 'change-password',
    'canChangePassword' => $canChangePassword,
    'staffFeatureStatus' => $staffFeatureStatus ?? null,
];
@endphp
@section('page-submenu')
{!! pageSubMenu('accounts',$page_arr) !!}
@endsection
@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
                @if(isset($staffFeatureStatus))
                    @if(!$canChangePassword)
                        <div class="alert alert-danger mb-3">
                            <strong>⚠ Staff Management</strong><br>
                            {{ $staffFeatureStatus['message']  }}
                           
                        </div>
                    @else
                        <div class="alert alert-warning mb-3">
                                <strong>⚠ Staff Management</strong><br>
                           
                            {{ $staffFeatureStatus['message'] }}
                        </div>
                    @endif
                @endif
			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
                @if($canChangePassword)
                <form id="form" class="js-validate mt-3" action="{{ baseUrl('/staff/update-password/' . $record->unique_id) }}" method="post">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                {!! FormHelper::formInputText(["label"=>"Name","value" => $record->first_name . ' ' . $record->last_name,'readonly'=>true]) !!}                        
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                {!! FormHelper::formInputText(["label"=>"Email","value" => $record->email,'readonly'=>true]) !!} 
                            </div>
                            <div class="col-md-4 col-xl-4 col-lg-4 col-sm-12 mb-4">
                                {!! FormHelper::formPassordText(['name'=>"old_password","label"=>"Old Password","required"=>true]) !!}  
                            </div>
                            <div class="col-md-4 col-xl-4 col-lg-4 col-sm-12 mb-4">
                                {!! FormHelper::formPassordText(['name'=>"password","label"=>"Password","required"=>true]) !!}
                            </div>
                            <div class="col-md-4 col-xl-4 col-lg-4 col-sm-12 mb-4">
                                {!! FormHelper::formPassordText(['name'=>"password_confirmation","label"=>"Confirm Password","required"=>true]) !!}
                            </div>
                            <div class="col-xl-12">
                                <div class="text-start">
                                    <button type="submit" class="CdsTYButton-btn-primary">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="text-center text-muted">
                        <p>You don't have permission to change staff passwords.</p>
                    </div>
                @endif
        
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
                        redirect(response.redirect_back);
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