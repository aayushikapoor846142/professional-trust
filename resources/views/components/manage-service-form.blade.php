<form id="form" class="js-validate mt-3" action="{{ baseUrl('my-services/add-sub-service-types/'.$service_id) }}" method="post">
    @csrf
    <div class="cds-ty-dashboard-box">
        <div class="cds-ty-dashboard-box-header">
        </div>
        <div class="cds-ty-dashboard-box-body">

            <div class="row">
                <div class="col-xl-6">
                    <label>Services</label>
                </div>
                <div class="col-xl-6">
                    <div class="cds-service-checkbox gap-4">
                        @foreach($sub_services_types as $service_type)
                            {!! FormHelper::formCheckbox([
                                'name' => 'sub_services_type_id[]',
                                'value' => $service_type->id,
                                'label' => $service_type->name,
                                'checkbox_class' => 'sub_services_type',
                            ]) !!}
                        @endforeach
                    </div>
                    <div class="descriptions">
                    
                    </div>
                </div>
                <div class="col-xl-6">
                    <label>Professional Fees</label>
                </div>
                <div class="col-xl-6">
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <div class="cds-fees">
                            {!! FormHelper::formInputText([
                            'name'=>"professional_fees",
                            'id'=>"professional_fees",
                            "label"=> "Professional Fees",
                            "required"=>true,
                            'events'=>['oninput=validateNumber(this)']
                            ])!!}
                        </div>
                        <div class="cds-tbd">
                            <label>To be decided later</label><br>
                            <label class="CDSMainsite-switch">
                            <input type="checkbox" name="tbd" value="1" class="cds-tbd-checkbox">
                                <span class="CDSMainsite-switch-button-slider CDSMainsite-round"></span>
                            </label>
                        </div>
                    </div>
                    <div id="cds-price-range" style="display:none">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <div class="cds-fees">
                                {!! FormHelper::formInputText([
                                'name'=>"minimum_fees",
                                'id'=>"min_fees",
                                "label"=> "Min Fees",
                                "disabled" => 'disabled',
                                "min" => $record->subServices->minimum_fees,
                                'events'=>['oninput=validateNumber(this)']
                                ])!!}
                            </div>
                            <div class="cds-fees">
                                {!! FormHelper::formInputText([
                                'name'=>"maximum_fees",
                                'id'=>"max_fees",
                                "label"=> "Max Fees",
                                'events'=>['oninput=validateNumber(this)']
                                ])!!}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-6">
                    <label>Consultancy Fees</label>
                    <div>If you add fees as 0 the consultation will be free</div>
                </div>

                <div class="col-xl-6">
                    {!! FormHelper::formInputText([
                    'name'=>"consultancy_fees",
                    'id'=>"consultancy_fees",
                    "label"=> "Consultancy Fees",
                    "required"=>true,
                    'events'=>['oninput=validatePhoneNumber(this)']
                    ])!!}
                </div>
                <div class="col-xl-6">
                    <label>Assesment Form</label>
                    <div>If you add fees as 0 the consultation will be free</div>
                </div>
                <div class="col-xl-6">
                    <div class="cds-assessment-list">
                        @foreach($forms as $form)
                            <div class="cds-assessment-row">
                                <div class="cds-assessment-col">
                                    <div class="cds-form-container mb-2">
                                        <div class="radio-group ">
                                            <div class="form-check">
                                                <input type="radio" name="form_id" id="form-id-{{ $form->id }}" value="{{ $form->id }}" class="radio-input required">
                                                <label for="form-id-{{ $form->id }}"> {{ $form->name }} </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cds-assessment-col">
                                    <a class="btn btn-sm btn-primary" target="_blank" href="{{ baseUrl('my-services/view-assesment/'.$form->unique_id) }}">
                                        View
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                </div>
                <div class="col-xl-6">
                    <label>Select Documents</label>
                </div>
                <div class="col-xl-6">
                    <div class="multi-selectbox">
                        {!! FormHelper::formSelect([
                            'name' => 'document[]',
                            'label' => 'Select Document Folder',
                            'select_class' => 'select2-input cds-multiselect add-multi',
                            'id' => 'documents-folders',
                            'options' => $documents,
                            'value_column' => 'id',
                            'label_column' => 'name',
                            'is_multiple' => true,
                            'required' => false,
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="text-end button-div">
                <button class="btn add-CdsTYButton-btn-primary">Save</button>
            </div>
        </div>
    </div>
</form>
@if($form_type == 'page')
@push("scripts")
@endif
<script>
    $(document).ready(function(){
        $(".sub_services_type").change(function(){
            $(".descriptions").html('');
            $(".sub_services_type:checked").each(function(){
                var id = $(this).val();
                var label = $(this).parents(".cds-check-box").find("label:first-child").text().trim();
                var html = '<div class="cds-form-container mb-4">';
                html += '<div class="js-form-message ">';
                html += '<div class="form-group form-floating  editor noval ">';
                html += '<textarea class="form-control border-line textarea  required" placeholder="Input description..." name="description['+id+']" rows="3"></textarea>';
                html += '<label>Enter Description for '+label;
                html += '<span class="danger">*</span>';
                html += '</label>';
                html += '</div>';
                html += '</div>';
                html += '</div>';

                $(".descriptions").append(html);
            });
            initFloatingLabel();
        })
        $("#min_fees").blur(function(){
            var min = $(this).attr("min");
            if($(this).val() < min){
                errorMessage("Fees should be minimum "+min);
                $(this).val(min);
            }
        })
        $("#max_fees").blur(function(){
            var min_fees = $("#min_fees").val();
            if($(this).val() < min_fees){
                errorMessage("Max Fees should be greater than "+min_fees);
                $(this).val('');
            }
        })
        $(".cds-tbd-checkbox").change(function(){
            if($(this).is(":checked")){
                $("#professional_fees").attr("disabled","disabled");
                $("#professional_fees").removeClass("required");
                
                $("#min_fees").removeAttr("disabled");
                $("#cds-price-range").show();
            }else{
                $("#professional_fees").removeAttr("disabled");
                $("#professional_fees").addClass("required");
                $("#min_fees").attr("disabled","disabled");
                $("#cds-price-range").hide();
            }
        });
        $("#form").on('submit', function(e) {
            e.preventDefault();
            var is_valid = formValidation("form");
            if (!is_valid) {
                return false;
            }
            var formData = $("#form").serialize();
            $.ajax({
                url: $("#form").attr("action"),
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
                        closeModal();
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
        $("#manage-document").on('submit', function(e) {
            e.preventDefault();
            var is_valid = formValidation("manage-document");
            if (!is_valid) {
                return false;
            }
            var formData = $("#manage-document").serialize();
            $.ajax({
                url: "{{ baseUrl('my-services/save-document') }}",
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
                        closeModal();
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
@if($form_type == 'page')
@endpush
@endif