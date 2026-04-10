
<form id="edit-sub-stage-form" class="js-validate" action="{{ baseUrl('predefined-case-sub-stages/update/'. $record->unique_id) }}" method="post">
                @csrf
    <div class="row">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
            @php 
                $case_documents = array();
                if($record->case_documents != ''){
                    $case_documents = json_decode($record->case_documents,true);
                }
                
            @endphp
            {!! FormHelper::formInputText([
                'name'=>"name",
                'id'=>"name",
                "label"=> "Enter Name",
                "required"=>true,
                  "value" => $record->name
                ])
            !!}
        </div> 
         <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
            {!! FormHelper::formInputText([
                'name'=>"sort_order",
                'id'=>"sort_order",
                "label"=> "Enter Sort Order",
                "required"=>true,
                "value" => $record->sort_order
                ])
            !!}
        </div>   
        <div class="col-xl-12">
            {!! FormHelper::formSelect([
                'name' => 'stage_type',
                'id' => 'stage_type',
                "required"=>true,
                'label' => 'Select Stage Type',
                'class' => 'select2-input ga-country',
                'options' => FormHelper::subStageType(),
                'value_column' => 'value',
                'label_column' => 'label',
                'is_multiple' => false,
                 'selected' => $record->stage_type ?? null
            ]) !!}
        </div>
        <div class="col-xl-12 form-div" style="{{ $record->stage_type != 'fill-form' ? 'display: none;' : '' }}">
            {!! FormHelper::formSelect([
                'name' => 'form_id',
                'id' => 'form_id',
                "required"=>false,
                'label' => 'Select Form',
                'class' => 'select2-input ga-country',
                'options' => $forms,
                'value_column' => 'id',
                'label_column' => 'name',
                'is_multiple' => false,
                  'selected' => $record->type_id ?? null,
            ]) !!}
        </div>
        <div class="col-xl-12 default-document-div" style="{{ $record->stage_type != 'case-document' ? 'display: none;' : '' }}">
            {!! FormHelper::formSelect([
                'name' => 'default_documents[]',
                'id' => 'default_documents',
                "required"=>false,
                'label' => 'Select Default Document',
                'class' => 'select2-input ga-country',
                'options' => $default_documents,
                'value_column' => 'id',
                'label_column' => 'name',
                'is_multiple' => true,
                'selected' => !empty($case_documents['default_documents']) ? $case_documents['default_documents'] : [],
            ]) !!}
            <div class="text-danger default_documents_error"></div>
        </div>
        
    </div>
    <div class="mt-4">
        <button type="submit" class="btn add-CdsTYButton-btn-primary">Save & publish</button>
    </div>
</form>

<script>

$(document).ready(function(){
 initSelect();
    $("#edit-sub-stage-form").submit(function(e) {
       
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        var url = $("#edit-sub-stage-form").attr('action');
        
        var is_valid = formValidation("edit-sub-stage-form");
        
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
                    location.reload();
                } else {
                    $.each(response.message, function (index, value) {
                        if(index == 'default_documents'){
                            $('.default_documents_error').html(value);
                        }
                    });
                    validation(response.message);
                }
            },
            error: function() {
                internalError();
            }
        });

    });
    
    $('#stage_type').change(function() {
        var selectedValue = $(this).val();
        if(selectedValue == 'fill-form'){
            $('.form-div').show();
            $('.default-document-div').hide();
            $('.custom-document-div').hide();
        }else{
            $('.default-document-div').show();
            $('.custom-document-div').show();
            $('.form-div').hide();
        }
    });

});
</script>
