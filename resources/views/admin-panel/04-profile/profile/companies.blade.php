

<div class="cds-t25n-content-professional-profile-container-main-body-information-expertise pt-4">
    <div class="text-end py-3">
        <a class="CdsTYButton-btn-primary" href="{{ baseUrl('companies/add') }}">
            Add Company
        </a>
    </div>
    <div id="companies">
    </div>
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
    loadCompanies();
    function loadCompanies(){
        $.ajax({
            type: "POST",
            url: "{{baseUrl('/companies-ajax')}}",
            data:{
                _token:csrf_token,
            },
            dataType:'json',
            success: function (data) {
                $("#companies").html(data.contents);
            },
        });
    }
    function showCompanyAddress()
    {
        $('.companies').removeClass('d-none');
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