@extends('admin-panel.layouts.app')

@section('content')
  <div class="ch-action">
                    <a href="{{ baseUrl('my-services') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-solid fa-left me-1"></i>
                        Back
                    </a>
                </div><div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
  <form id="form" class="js-validate mt-3" action="{{ baseUrl('my-services/update-sub-services/'.$record->unique_id) }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-xl-6">
                                {!! FormHelper::formSelect([
                                    'name' => 'sub_services_type_id',
                                    'id' => 'sub_services_type_id',
                                    'label' => 'Type',
                                    'class' => 'select2-input ga-country',
                                    'options' => $sub_services_types,
                                    'value_column' => 'id',
                                    'label_column' => 'name',
                                    'is_multiple' => false,
                                    'required' => true,
                                    'selected' => $record->sub_services_type_id ?? ''
                                ]) !!}
                            </div>
                            <div class="col-xl-6">
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    <div class="cds-fees">
                                        {!! FormHelper::formInputText([
                                            'name'=>"professional_fees",
                                            'id'=>"professional_fees",
                                            "label"=> "Professional Fees",
                                            "required"=>true,
                                            "disabled"=>$record->tbd == 1?'disabled':'',
                                            "value" => $record->professional_fees??'',
                                            'events'=>['oninput=validatePhoneNumber(this)']
                                        ])!!}
                                    </div>
                                    <div class="cds-tbd">
                                        <label>To be decided later</label><br>
                                        <label class="CDSMainsite-switch">
                                        <input type="checkbox" name="tbd" {{ $record->tbd == 1?'checked':'' }} value="1" class="cds-tbd-checkbox">
                                            <span class="CDSMainsite-switch-button-slider CDSMainsite-round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div id="cds-price-range" style="display:{{ $record->tbd == 1?'block':'none' }}">
                                <div class="d-flex justify-content-start align-items-center gap-2">
                                    <div class="cds-fees">
                                        {!! FormHelper::formInputText([
                                        'name'=>"minimum_fees",
                                        'id'=>"min_fees",
                                        "label"=> "Min Fees",
                                        "value" => $record->minimum_fees??'',
                                        "min" => $record->subService->minimum_fees,
                                        'events'=>['oninput=validateNumber(this)']
                                        ])!!}
                                    </div>
                                    <div class="cds-fees">
                                        {!! FormHelper::formInputText([
                                        'name'=>"maximum_fees",
                                        'id'=>"max_fees",
                                        "label"=> "Max Fees",
                                        "value" => $record->maximum_fees??'',
                                        'events'=>['oninput=validateNumber(this)']
                                        ])!!}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xl-6">
                                {!! FormHelper::formInputText([
                                    'name'=>"consultancy_fees",
                                    'id'=>"consultancy_fees",
                                    "label"=> "Consultancy Fees",
                                    "required"=>true,
                                    "value" => $record->consultancy_fees??'',
                                    'events'=>['oninput=validatePhoneNumber(this)']
                                ])!!}
                            </div>
                            <div class="col-xl-6">
                                {!! FormHelper::formSelect([
                                    'name' => 'form_id',
                                    'id' => 'form_id',
                                    'label' => 'Assesment Form',
                                    'class' => 'select2-input ga-country',
                                    'options' => $forms,
                                    'value_column' => 'id',
                                    'label_column' => 'name',
                                    'is_multiple' => false,
                                    'required' => false,
                                    'selected' => $record->form_id ?? ''
                                ]) !!}
                            </div>
                            <div class="col-xl-12">
                                <a href="javascript:;" onclick="showPopup('<?php echo baseUrl('my-services/add-document-folder/') ?>')">Add Document Folder</a>
                            </div>
                            <div class="col-xl-12 mt-3">
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
                                        'selected' => isset($record->document_folders) ? explode(',',$record->document_folders) : null,
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-xl-12">
                                {!! FormHelper::formTextarea([
                                    'name'=>"description",
                                    'id'=>"description",
                                    'required' => true,
                                    "label"=>"Enter Description",
                                    'class'=>"editor noval",
                                    'value' => $record->description,
                               
                                    ]) !!}
                            </div>
                            
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
                        </div>
                    </form>
          
			</div>
	
	</div>
  </div>
</div>

@endsection
<!-- End Content -->
@section('javascript')

<script>
    $(document).ready(function() {
        $("#min_fees").blur(function(){
            var min = $(this).attr("min");
            if($(this).val() != '' && $(this).val() < min){
                errorMessage("Fees should be minimum "+min);
                $(this).val(min);
            }
        })
        $("#max_fees").blur(function(){
            var min_fees = $("#min_fees").val();
            if(parseInt($(this).val()) < min_fees){
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
        // end google address
        $("#form").submit(function(e) {
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
    });
</script>
@endsection