

<div class="cds-t25n-content-professional-profile-container-main-body-information-expertise pt-4">
    <form id="company-detail-form" class="js-validate" action="{{ baseUrl('/professional-submit-profile') }}" method="post">
        @csrf
        <input type="hidden" name="type" value="business">    
        <input type="hidden" name="company_unique_id" value="{{$user->cdsCompanyDetail->unique_id ?? null }}">
        <div class="row">            
            <div class="col-xl-6 col-md-12 col-lg-12 col-sm-12">
                {!! FormHelper::formInputText([
                    'name'=>"company_name",
                    'id'=>"name",
                    "label"=> "Company name",
                    "value"=> $user->cdsCompanyDetail->company_name ?? '',
                    "required"=>true,
                ])!!}
            </div>
            <div class="col-xl-6 col-md-12 col-lg-12 col-sm-12">
                <div class="cds-selectbox">
                    {!! FormHelper::formSelect([
                        'name' => 'company_type',
                        'label' => 'Company Type',
                        'class' => 'select2-input',
                        'select_class' => 'ga-country',
                        'id' => 'company_type',
                        'options' => FormHelper::ownerCompanyType(),
                        'value_column' => 'value',
                        'label_column' => 'label',
                        'selected' => isset($user->cdsCompanyDetail->company_type) ? $user->cdsCompanyDetail->company_type : null,
                        'is_multiple' => false,
                        'required' => true,
                    ]) !!}
                </div>  
            </div>  
            <div class="col-md-12">
                <div class="form-check form-check-inline cds-gender-list me-0 p-0"> 
                    <label class="mb-1">Ownership Type <span class="text-danger">*</span></label>
                    {!! FormHelper::formRadio([
                        'name' => 'owner_type',
                        'options' => [
                            ['value' => 'Self Employed', 'label' => 'Self Employed'],
                            ['value' => 'Employed', 'label' => 'Employed']
                        ],
                        'value_column' => 'value',
                        'label_column' => 'label',
                        'id' => 'label',
                        'value' => 'label',                        
                        'selected' => $user->cdsCompanyDetail->owner_type ?? null
                    ]) !!}                                
                </div>
            </div>
            <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                {!! FormHelper::formTextarea([
                    'name'=>"about",
                    'id'=>"about",
                    'class' => 'bgcolor',
                    "label"=>"About",
                    "value" => $user_details->about ?? '',               
                ]) !!}
            </div>
        </div>
        <div class="text-end mt-4">
            <button type="submit" class="btn add-CdsTYButton-btn-primary">Save & publish</button>
        </div>
    </form>
</div>
<div class="cds-t25n-content-professional-profile-container-main-body-information-expertise pt-4">
    <div class="cds-register-address-list render-address" id="add-pr-address">
        <div class="address-item">
            <div class="address-header">
                <div class="map-thumbnail">
                    <i class="fa-sharp fa-solid fa-location-dot" style="color:#000000;"></i>
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
                        <a href="javascript:;" class="CdsTYButton-btn-primary show-address-div" onclick="openCustomPopup(this)" data-href="<?php echo baseUrl('professional/add-company-address/0') ?>">
                            <i class="fa fa-add"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12 mb-3" id="company-address"></div>
</div>
@push("scripts")
<script>
    var company_address_id = '';
    $(document).ready(function(){
        initGoogleAddress();
        $("#company-detail-form").submit(function(e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            var url = $("#company-detail-form").attr('action');
            var is_valid = formValidation("company-detail-form");
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
    loadCompanyAddress();
    function loadCompanyAddress(){
        $.ajax({
            type: "POST",
            url: "{{url('/get-address-for-signup')}}",
            data:{
                _token:csrf_token,
                type:'company',
            },
            dataType:'json',
            success: function (data) {
                $("#company-address").html(data.contents);
            },
        });
    }
    function showCompanyAddress()
    {
        $('.company-address').removeClass('d-none');
        $('#comp-address input').val('');
        $('#comp-address select').val('').trigger("change");
    }
    function markAsPrimary(company_id,location_id){
        $.ajax({
            type: "POST",
            url: "{{url('/mark-company-as-primary')}}",
            data:{
                _token:csrf_token,
                company_id:company_id,
                location_id:location_id
            },
            dataType:'json',
            success: function (response) {
                if (response.status == true) {
                    successMessage(response.message);
                    location.reload();
                } else {
                    errorMessage(response.message);
                }
            },
        });
    }
</script>
<script>
function deleteCompanyAddress(e) {
    var parentDiv = $(e).attr('data-id');
    var url = $(e).attr("data-href");
    Swal.fire({
        title: "Are you sure to delete?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "CdsTYButton-btn-primary",
        cancelButtonClass: "CdsTYButton-btn-primary CdsTYButton-border-thick ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            $.ajax({
                type: "GET",
                url: url,
                data:{
                    _token:csrf_token,
                },
                dataType:'json',
                success: function (response) {
                    if (response.status == true) {
                        successMessage(response.message);
                        $('#personal-address-div-'+parentDiv).remove();
                    } else {
                        errorMessage(response.message);
                    }
                },
            });
        }
    });
}
</script>
@endpush