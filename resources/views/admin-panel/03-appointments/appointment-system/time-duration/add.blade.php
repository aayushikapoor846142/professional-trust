
@extends('components.custom-popup',['modalTitle'=>$pageTitle ?? 'Create New Feed'])
@section('custom-popup-content')
<div class="cds-form-container mb-0">
    <form id="form" name="form" class="js-validate" action="{{ baseUrl('time-duration/save') }}" method="post">
        @csrf
        
        <div class="row">
            <div class="col-xl-12">
                {!! FormHelper::formInputText(['name'=>"name","id" => "name","label"=>"Enter Name","required"=>true,]) !!}
            </div>
            <div class="col-xl-12">
                    {!! FormHelper::formInputNumber([
                    'name' => 'break_time',
                    'label' => 'Appointments Gap (Minutes)(Gap between 2 Appointments)',
                    "required"=>true,
                    'input_class' => 'ga-pincode',
                    'events'=>['oninput=validateDigit(this)', 'onblur=validateDigit(this)']
                    ]) !!}
            </div>
            <div class="col-xl-12">
                {!! FormHelper::formInputNumber(['name'=>"duration","id" => "duration","label"=>"Enter Duration","required"=>true,
                    'events'=>['oninput=validateDigit(this)', 'onblur=validateDigit(this)'
                    ]]) !!}
            </div>
            <div class="col-xl-12">
                {!! FormHelper::formSelect([
                    'name' => 'type',
                    'label' => 'Select Type',
                    'class' => 'select2-input ga-country',                                    
                    'options' => FormHelper::selectTimeDurationType(),
                    'value_column' => 'value',
                    'label_column' => 'label',
                    'selected' => old('type') ?? null,
                    'is_multiple' => false
                ]) !!}
            </div>
        </div>
        <div class="text-start">
        @section('custom-popup-footer')
         <button type="submit" form="form" class="CdsTYButton-btn-primary add-btn">Save</button>
        @endsection
        </div>
    </form>
</div>
<!-- End Content -->
<script>
    $(document).ready(function () {
        $("#form").submit(function (e) {
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
                beforeSend: function () {
                    showLoader();
                },
                success: function (response) {
                    hideLoader();
                    if (response.status == true) {
                        successMessage(response.message);
                        renderPage('{{ baseUrl('time-duration') }}', 'time-duration-content');  
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