@extends('components.custom-popup',['modalTitle'=>$pageTitle ?? 'Create New Feed'])
@section('custom-popup-content')
<div class="cds-form-container cds-ty-dashboard-box-body mb-0">

    <form id="edit-at-form" name="edit-at-form" class="js-validate" action="{{ baseUrl('/appointment-types/update/'.$record->unique_id) }}" method="post">
        @csrf
         
        <div class="row">
            <div class="col-xl-12">
                {!! FormHelper::formInputText(['name'=>"name","id" => "name","value" =>$record->name ?? '',"label"=>"Enter Name","required"=>true]) !!}                        
            </div>
            <div class="col-xl-12">
                {!! FormHelper::formSelect([
                    'name' => 'duration',
                    'label' => 'Select Duration',
                    'class' => 'select2-input ga-country',                                    
                    'options' => $durations,
                    'value_column' => 'id',
                    'label_column' => 'name',
                    'selected' => $record->timeDuration->id?? '' ?? null,
                    'is_multiple' => false
                ]) !!}
            </div>
            <div class="col-xl-12">
                {!! FormHelper::formInputNumber([
                    'name' => 'price',
                    'label' => 'Price',
                    "required"=>true,
                    'value' => $record->price?? '' ?? 0,
                    'input_class' => 'ga-pincode',
                    'events'=>['oninput=validateDigit(this)',
                        'onblur=validateDigit(this)']
                ]) !!}
            </div>
            <div class="col-xl-12">
                {!! FormHelper::formSelect([
                    'name' => 'currency',
                    'label' => 'Select Currency',
                    'class' => 'select2-input ga-country',                                    
                    'options' => FormHelper::supportCurrency(),
                    'value_column' => 'value',
                    'label_column' => 'label',
                    'selected' => $record->currency?? '' ?? 0,
                    'is_multiple' => false
                ]) !!}
            </div>
        </div>        
        @section('custom-popup-footer')
        <div class="text-start">
            <button type="submit" form="edit-at-form" class="CdsTYButton-btn-primary add-btn">Save</button>
            </div>
        @endsection
    </form>
</div>

<!-- End Content -->
<script>
$(document).ready(function () {
    initSelect();
    $("#edit-at-form").submit(function (e) {
        e.preventDefault();
        var is_valid = formValidation("edit-at-form");
        if (!is_valid) {
            return false;
        }
        var formData = new FormData($(this)[0]);
        var url = $("#edit-at-form").attr('action');
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
                    renderPage('{{ baseUrl('appointment-types') }}', 'appointment-types-content');
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