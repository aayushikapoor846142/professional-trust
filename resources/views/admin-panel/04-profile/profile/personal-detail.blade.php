<div class="cds-t25n-content-professional-profile-container-main-body-information-expertise">
    <h4 class="title">Personal Details.</h4>
    <form id="personal-detail-form" class="js-validate" action="{{ baseUrl('/professional-submit-profile') }}" method="post">
        @csrf
        <input type="hidden" name="type" value="contact">
        <input type="hidden" name="personal_location_unique_id" value="{{$user->personalLocation->unique_id ?? null }}">
        <div class="row">
            <div class="col-xl-6 col-md-12 col-lg-6 col-sm-12">
                {!! FormHelper::formInputText([
                'name'=>"first_name",
                'id'=>"first_name",
                "label"=> "First name",
                "value"=> $user->first_name,
                "required"=>true,
                'events'=>['oninput=validateName(this)']])
                !!}
            </div>
            <div class="col-xl-6 col-md-12 col-lg-6 col-sm-12">
                {!! FormHelper::formInputText([
                'name'=>"last_name",
                'id'=>"last_name",
                "label"=> "Last name",
                "value"=> $user->last_name,
                "required"=>true,
                'events'=>['oninput=validateName(this)']])
                !!}
            </div>
            <div class="col-xl-6 col-md-12 col-lg-6 col-sm-12">
                {!! FormHelper::formInputText([
                'name'=>"email",
                'id'=>"email",
                "label"=> "Email",
                "value"=> $user->email,
                "required"=>true,
                'readonly'=>true,
                ])!!}
            </div>
            <div class="col-xl-6 col-md-12 col-lg-6 col-sm-12">
                {!! FormHelper::formPhoneNo([
                'name' => "phone_no",
                'country_code_name' => "country_code",
                "label" => "Phone Number",
                "value" => $user->phone_no,
                "default_country_code"=>$user->country_code,
                "required" => true,
                'events'=>['oninput=validatePhoneInput(this)']]
                ) !!}
            </div>
            <div class="col-xl-6 col-md-12 col-lg-12 col-sm-12">
                {!! FormHelper::formDatepicker([
                    'name'=>"date_of_birth",
                    'id'=>"date_of_birth",
                    "label"=> "Date of Birth",
                    'value' => $user->date_of_birth ?? '',
                    'required' => true,
                ]) !!}
            </div>
            <div class="col-xl-6 col-md-12 col-lg-12 col-sm-12 m-1rem mt-3 mt-lg-0">
                <div class="form-check form-check-inline cds-gender-list me-0 mt-3 mt-md-0 p-0">
                    <label class="mb-1">Gender <span class="text-danger">*</span></label>
                    {!! FormHelper::formRadio([
                    'name' => 'gender',
                    'required' => true,
                    'options' => FormHelper::selectThreeGender(),
                    'value_column' => 'value',
                    'label_column' => 'label',
                    'selected' => $user->gender ?? ''
                    ]) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-6 col-md-12 col-lg-12 col-sm-12 m-1rem mt-3 mt-xl-0">
                {!! FormHelper::formSelect([
                'name' => 'timezone',
                'id' => 'timezone',
                'label' => 'Select Timezone',
                'class' => 'select2-input ga-country',       
                'options' => FormHelper::getTimezone(),
                'value_column' => 'label',
                'label_column' => 'value',
                'selected' =>$user->timezone ?? '',
                'is_multiple' => false,
                'required' => true,
                ]) !!}
                <span class="error-text text-danger" id="error-text-timezone"></span>
            </div>
            <div class="col-xl-6">
                {!! FormHelper::formSelect([
                    'name' => 'languages[]',
                    'label' => 'Select Language',
                    'class' => 'select2-input cds-multiselect add-multi',
                    'select_class' => 'ga-country',
                    'id' => 'languages',
                    'options' => $languages,
                    'value_column' => 'name',
                    'label_column' => 'name',
                    'is_multiple' => true,
                    'selected' => explode(',', $user_details->languages_known ?? ''),
                    'required' => false,
                ]) !!}
            </div>
        </div>
        <div class="text-end mt-4">
            <button type="submit" class="btn add-CdsTYButton-btn-primary">Save & publish</button>
        </div>
    </form>
</div>
<div class="cds-t25n-content-professional-profile-container-main-body-information-expertise">
    <h6>Personal Address</h6>
    @if($personal_address == 0)
        <div class="cds-register-address-list render-address" id="add-pr-address">
            <div class="address-item">
                <div class="address-header">
                    <div class="map-thumbnail">
                        <i class="fa-sharp fa-solid fa-location-dot" style="color: #000000;"></i>
                    </div>
                    <div class="address-details">
                        <div class="address-text">
                            <div class="address-1" data-value="XXXXXXXXX"> XXXXXXXXX</div>
                            <div class="address-2" data-value="XXXXXXXXX"> XXXXXXXXX</div>
                        </div>
                        <div class="address-text">
                            <div class="city" data-value="XXXXXXXXX"> XXXXXXXXX</div>
                            <div class="state" data-value="XXXXXXXXX"> XXXXXXXXX</div>
                            <div class="pincode" data-value="XXXXXXXXX"> XXXXXXXXX</div>
                            <div class="country" data-value="XXXXXXXXX"> XXXXXXXXX</div>
                        </div>
                        <div class="" style="float:right;">
                            <a href="javascript:;" class="CdsTYButton-btn-primary show-address-div" onclick="showPopup('<?php echo baseUrl('professional/add-personal-address/0') ?>')">
                                <i class="fa fa-add"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12 mb-3" id="personal-address"></div>
</div>
@push("scripts")
<script>
    $(document).ready(function () {
        dobDatePicker("date_of_birth");
        initFloatingLabel();
        initSelect();
        initGoogleAddress();
        $("#personal-detail-form").submit(function (e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            var url = $("#personal-detail-form").attr('action');
            var is_valid = formValidation("personal-detail-form");
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
                beforeSend: function () {
                    showLoader();
                },
                success: function (response) {
                    hideLoader();
                    if (response.status == true) {
                        successMessage(response.message);
                        location.reload();
                    } else {
                        validation(response.message);
                    }
                },
                error: function () {
                    internalError();
                }
            });
        });
        loadPersonalAddress();
        function loadPersonalAddress(){
            $.ajax({
                type: "POST",
                url: "{{url('/get-address-for-signup')}}",
                data:{
                    _token:csrf_token,
                    type:'personal'
                },
                dataType:'json',
                success: function (data) {
                    $("#personal-address").html(data.contents);
                },
            });
        }
    })
</script>
@endpush