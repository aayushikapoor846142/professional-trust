

<div class="cds-t25n-content-professional-profile-container-main-body-information-expertise pt-4">
    <h3>{{ $pageTitle }}</h3>
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
                        <a href="javascript:;" class="CdsTYButton-btn-primary show-address-div" onclick="openCustomPopup(this)" data-href="<?php echo baseUrl('companies/add-company-address/0/'.$company->unique_id) ?>">
                            <!-- <i class="fa fa-edit"></i>  -->
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
    })
    loadCompanyAddress();
    function loadCompanyAddress(){
        $.ajax({
            type: "POST",
            url: "{{baseUrl('/companies/get-company-address')}}",
            data:{
                _token:csrf_token,
                company_id:"{{ $company->id }}",
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
   
    // .cds-register-address-list
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