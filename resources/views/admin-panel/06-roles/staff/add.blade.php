@extends('admin-panel.layouts.app')

@php 
$page_arr = [
    'page_title' => 'Add Staff ',
    'page_description' => 'Add New Staff Member.',
    'page_type' => 'add-staff',
];
@endphp
@section('page-submenu')
{!! pageSubMenu('accounts',$page_arr) !!}
@endsection
@section('content')
 <div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-body CDSDashboardContainer-main-form">
                <form id="form" class="js-validate mt-3" action="{{ baseUrl('staff/save') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xl-4">
                            {!! FormHelper::formInputText(['name'=>"first_name","label"=>"First Name",'id'=>"validationFormFirstnameLabel","required"=>true,'events'=>
                            ['oninput=validateString(this)', 'onblur=validateString(this)']]) !!}
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xl-4">
                            {!! FormHelper::formInputText(['name'=>"last_name","label"=>"Last Name",'id'=>"validationFormLastnameLabel","required"=>true,'events'=>
                            ['oninput=validateString(this)', 'onblur=validateString(this)']]) !!}
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xl-4">
                            {!! FormHelper::formInputEmail(['name'=>"email","label"=>"Email",'id'=>"validationFormEmailLabel","required"=>true,'events'=>
                            ['oninput=validateEmail(this)', 'onblur=validateEmail(this)']]) !!}
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xl-4 phoneno-with-code">
                            {!! FormHelper::formPhoneNo([
                                'name' => "phone_no",
                                'country_code_name' => "country_code",
                                "label" => "Phone Number",
                                "default_country_code"=>'+1',
                                "required" => true,
                                'events'=>['oninput=validatePhoneInput(this)']]
                                ) !!}
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xl-4">
                            {!! FormHelper::formSelect([
                                'name' => 'status',
                                'label' => 'Status',
                                'class' => 'select2-input ga-country',
                                'required' => true,
                                'options' => FormHelper::accountStatus(),
                                'value_column' => 'value',
                                'label_column' => 'label',
                            ]) !!}
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xl-4">
                            {!! FormHelper::formSelect([
                                'name' => 'role',
                                'label' => 'Select Role',
                                'class' => 'select2-input ga-country',
                                'required' => true,
                                'options' => getRoles(),
                                'value_column' => 'slug',
                                'label_column' => 'name',
                            ]) !!}
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xl-6">
                            {!! FormHelper::formPassordText(['name'=>"password","label"=>"Password","required"=>true,]) !!}                                
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xl-6">
                            {!! FormHelper::formPassordText(['name'=>"password_confirmation","label"=>"Confirm Password","required"=>true,]) !!}
                        </div>                        
                    </div>
                    <div class="text-start mt-4 mt-md-0">
                        <button type="submit" class="CdsTYButton-btn-primary add-btn">Save</button>
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
            var formData = new FormData($(this)[0]);
            var url = $("#form").attr('action');
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
    });
</script>

@endsection