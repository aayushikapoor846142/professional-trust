@extends('components.custom-popup',['modalTitle'=>$pageTitle ?? 'Mark As Client'])
@section('custom-popup-content')
<div class="cds-form-container cds-ty-dashboard-box-body mb-0">

    <form id="accept-modal-form" name="accept-modal-form" class="js-validate" action="{{ baseUrl('/case-join-requests/accept/'.$id) }}" method="post">
        @csrf
        <div class="row">
            <div class="col-xl-12">
                {!! FormHelper::formSelect([
                    'name' => 'sub_service_type_id',
                    'label' => 'Select Service',
                    'class' => 'select2-input cds-multiselect add-multi',
                    'select_class' => 'ga-country',
                    'id' => 'sub_service_type_id',
                    'options' => $subServiceType,
                    'value_column' => 'id',
                    'label_column' => 'name',
                    'is_multiple' => false,
                    'required' => true,
                ]) !!}
            </div>
        </div>        
        @section('custom-popup-footer')
        <div class="text-start">
            <button type="submit" form="accept-modal-form" class="btn btn-primary add-btn">Save</button>
            </div>
        @endsection
    </form>
</div>

<!-- End Content -->
<script>
$(document).ready(function () {
    initSelect();
    
    $("#accept-modal-form").submit(function (e) {
        e.preventDefault();
        var is_valid = formValidation("accept-modal-form");
        if (!is_valid) {
            return false;
        }
        var formData = new FormData($(this)[0]);
        var url = $("#accept-modal-form").attr('action');
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
                    if (response.error_type == 'validation') {
                        validation(response.message);
                    } else {
                        errorMessage(response.message);
                    }
                }
            },
            error: function (xhr) {
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