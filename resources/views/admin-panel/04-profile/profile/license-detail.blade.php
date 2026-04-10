<div class="cds-t25n-content-professional-profile-container-main-body-information-expertise">
    <form id="licence-form" class="js-validate" action="{{ baseUrl('/professional-submit-profile') }}" method="post">
        @csrf
        <input type="hidden" name="type" value="licence_details">
        <input type="hidden" name="licence_unique_id" value="{{$license_detail->unique_id ?? null}}">
        <div class="row">
            <div class="col-xl-6 col-md-12 col-lg-6 col-sm-12">
                {!! FormHelper::formInputText([
                'name' => 'title',
                'label' => 'Title',
                'value' => $license_detail->title ?? '',
                'required' => true,
                ]) !!}
            </div>
            <div class="col-xl-6 col-md-12 col-lg-6 col-sm-12">
                {!! FormHelper::formSelect([
                'name' => 'regulatory_country_id',
                'label' => 'Regulatory Country',
                'class' => 'select-flotlabel',
                'id' => 'regulatory-country',
                'options' => $regulatory_countries,
                'value_column' => 'id',
                'label_column' => 'name',
                'selected' => $license_detail->regulatory_country_id ?? null,
                'is_multiple' => false,
                'required' => true
                ]) !!}
            </div>
            <div class="col-xl-6 col-md-12 col-lg-6 col-sm-12">
                {!! FormHelper::formSelect([
                'name' => 'regulatory_body_id',
                'label' => 'Regulatory Body',
                'class' => 'select-flotlabel',
                'id' => 'regulatory-body',
                'options' => $regulatory_bodies,
                'value_column' => 'id',
                'label_column' => 'name',
                'selected' => $license_detail->regulatory_body_id ?? null,
                'is_multiple' => false,
                'required' => true,
                'option_attributes' => function ($option) {
                    return ['data-prefix' => $option->license_prefix];
                }
                ]) !!}
            </div>
            <div class="col-xl-6 col-md-12 col-lg-6 col-sm-12">
                {!! FormHelper::formInputText([
                'name' => 'class_level',
                'label' => 'Class',
                'value' => $license_detail->class_level ?? '',
                'required' => true,
                ]) !!}
            </div>
            <div class="col-xl-6 col-md-12 col-lg-6 col-sm-12">
                {!! FormHelper::formInputText([
                'name' => 'license_number',
                'label' => 'License No',
                'value' => $license_detail->license_number ?? '',
                'required' => true,
                ]) !!}
            </div>
            <div class="col-xl-6 col-md-12 col-lg-6 col-sm-12">
                <div class="dob-block mb-4 mb-lg-0">
                    {!! FormHelper::formDatepicker([
                    'label' => 'License Start Date',
                    'name' => 'license_start_date',
                    'class' => 'select2-input ga-country',
                    'id' => 'license_start_date',
                    'value' => $license_detail->license_start_date ?? '',
                    'required' => true
                    ]) !!}
                </div>
            </div>
            <div class="col-xl-6 col-md-12 col-lg-6 col-sm-12">
                {!! FormHelper::formSelect([
                    'name' => 'country_of_practice[]',
                    'label' => 'Country of Practice',
                    'options' => $countries,
                    'class' => 'select2-input cds-multiselect add-multi',
                    'value_column' => 'id',
                    'label_column' => 'name',
                    'selected' => $license_detail->country_ids ?? [],
                    'is_multiple' => true,
                    'required' => true
                ]) !!}
            </div>
            <div class="col-xl-6 col-md-12 col-lg-6 col-sm-12">
                <div class="cds-selectbox">
                    {!! FormHelper::formSelect([
                    'name' => 'license_status',
                    'label' => 'License Status',
                    'options' => [
                    ['id' => 'None', 'name' => 'None'],
                    ['id' => 'Active', 'name' => 'Active'],
                    ['id' => 'In Active', 'name' => 'In Active'],
                    ['id' => 'Suspended', 'name' => 'Suspended'],
                    ['id' => 'Revoked', 'name' => 'Revoked']
                    ],
                    'value_column' => 'id',
                    'label_column' => 'name',
                    'selected' => $license_detail->license_status ?? null,
                    'is_multiple' => false,
                    'required' => true
                    ]) !!}
                </div>
            </div>
            <div class="col-xl-6 col-md-12 col-lg-6 col-sm-12 mb-3 mb-lg-0">
                <label class="form-label">Do you have more licenses?</label>
                <div class="d-flex align-items-center gap-2">
                    {!! FormHelper::formCheckbox([
                    'name' => 'do_you_more_license',
                    'value' => 1,
                    'id' => 'do-you-more-license',
                    'required' => false,
                    'checked' => ($license_detail->do_you_more_license ?? '') === 1
                    ]) !!}
                    <label class="cds-t66-radio-labels" for="do-you-more-license">Tick if Yes</label>
                </div>
            </div>
            <div class="col-xl-6 col-md-12 col-lg-6 col-sm-12 cds-gender-list">
                <div class="form-check form-check-inline ps-0">
                    <label class="form-label">Entitled to Practice <span style="color: red;">*</span></label>
                    {!! FormHelper::formRadio([
                    'name' => 'entitled_to_practice',
                    'options' => [
                    ['value' => 'Yes', 'label' => 'Yes'],
                    ['value' => 'No', 'label' => 'No']
                    ],
                    'value_column' => 'value',
                    'label_column' => 'label',
                    'id' => 'entitled_to_practice',
                    'selected' => $license_detail->entitled_to_practice ?? null
                    ]) !!}
                </div>
            </div>
            <div class="text-end mt-3">
                <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
            </div>
        </div>
    </form>
</div>
@push("scripts")
<script>
    $(document).ready(function(){
        initGoogleAddress();
        initPastDatePicker("license_start_date");
        $("#licence-form").submit(function(e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            var url = $("#licence-form").attr('action');
            var is_valid = formValidation("licence-form");
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
                        location.reload();
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
    function addMoreCompanyAdd(){
        $.ajax({
            url: "{{ baseUrl('more-company-address') }}",
            type: "get",
            dataType: "json",
            beforeSend: function() {
            },
            success: function(response) {
                $(".company-address").append(response.contents);
                initGoogleAddress();
                initFloatingLabel();
                initSelect();
            },
            error: function() {
                internalError();
            }
        });
    }
</script>
@endpush