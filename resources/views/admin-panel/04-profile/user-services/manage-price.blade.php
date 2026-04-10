<form id="manage-price-form" class="js-validate" action="{{ baseUrl('myservices/save') }}" method="post">
    @csrf
    <input type="hidden" name="actual_service_id" value="{{ $record->service_id ?? '' }}">
    <input type="hidden" name="professional_service_id" value="{{ $record->id ?? '' }}">
    <!-- Input Group -->
    <div class="row mt-4">
        <div class="col-xl-12 col-md- col-lg-12 col-sm-12">
            {!! FormHelper::formSelect([
            'name' => 'type',
            'label' => 'Select Type',
            'class' => 'select2-input ga-country',
            'required' => true,
            'options' => $types,
            'value_column' => 'id',
            'label_column' => 'name',
            'selected' => isset($price) ? $price->type : null,
            ]) !!}
            {{--'options' => $types->pluck('name', 'id')->toArray(),--}}
        </div>
        <div class="col-xl-12 col-md- col-lg-12 col-sm-12">
            {!! FormHelper::formSelect([
            'name' => 'documents',
            'label' => 'Select Document Type',
            'class' => 'select2-input ga-country',
            'required' => true,
            'options' => $docuemnts,
            'value_column' => 'id',
            'label_column' => 'name',
            'selected' => isset($price) ? $price->documents : null,
            ]) !!}
        </div>
        <div class="col-xl-12 col-md- col-lg-12 col-sm-12">
            {!! FormHelper::formInputText([
            'name' => 'professional_fees',
            'label' => 'Professional Fees',
            'id' => 'professional_fees',
            'required' => true,
            'value' => $price->professional_fees ?? '',
            ]) !!}
        </div>
        <div class="col-xl-12 col-md- col-lg-12 col-sm-12">
            {!! FormHelper::formInputText([
            'name' => 'consultancy_fees',
            'label' => 'Counsultancy Fees',
            'id' => 'consultancy_fees',
            'required' => true,
            'value' => $price->consultancy_fees ?? '',
            ]) !!}
        </div>
    </div>
    <div class="text-end">
        <button type="submit" class="btn add-CdsTYButton-btn-primary">Submit</button>
    </div>
</form>

<script>
initFloatingLabel();
initSelect();
$(document).ready(function() {
    $("#manage-price-form").on('submit', function(e) {
        e.preventDefault();
        var is_valid = formValidation("manage-price-form");
        if (!is_valid) {
            return false;
        }
        var formData = $("#manage-price-form").serialize();
        $.ajax({
            url: "{{ baseUrl('my-services/save') }}",
            type: "post",
            data: formData,
            dataType: "json",
            beforeSend: function() {
                showLoader()
            },
            success: function(response) {
                hideLoader();
                if (response.status == true) {
                    successMessage(response.message);
                    backToServices();
                } else {
                    validation(response.message);
                }
            },
            error: function() {
                hideLoader();
                internalError();
            }
        });
    });
});
</script>