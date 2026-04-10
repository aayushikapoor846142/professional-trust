
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" >{{$pageTitle}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="edit-sub-stage-forms" class="js-validate" action="{{ baseUrl('case-with-professionals/stages/sub-stages/update/'.$record->unique_id) }}" method="post">
                @csrf
                    @php 
                        $case_documents = array();
                        if($record->case_documents != ''){
                            $case_documents = json_decode($record->case_documents,true);
                        }
                      
                    @endphp
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                        {!! FormHelper::formInputText([
                            'name'=>"name",
                            'id'=>"name",
                            "label"=> "Enter Name",
                            "required"=>true,
                            "value" => $record->name
                            ])
                        !!}
                    </div>  
                    <div class="col-xl-12">
                        {!! FormHelper::formTextarea([
                            'name'=>"description",
                            'id'=>"edit_description",
                            "label"=>"Enter Description",
                            'required'=>false,
                            'textarea_class'=>"noval cds-texteditor",
                            'class' => 'select2-input ga-country',
                            'value' => $record->description
                        ]) !!}
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
                            'selected' => $record->stage_type ?? null,
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
                            'selected' => $record->form_id ?? null,
                        ]) !!}
                    </div>
                    <div class="col-xl-12 custom-document-div" style="{{ $record->stage_type != 'case-document' ? 'display: none;' : '' }}">
                         
                        {!! FormHelper::formSelect([
                            'name' => 'folder[]',
                            'id' => 'folder',
                            "required" => false,
                            'label' => 'Select Folder',
                            'class' => 'select2-input ga-country',
                            'options' => $custom_documents,
                            'value_column' => 'id',
                            'label_column' => 'name',
                            'is_multiple' => true,
                            'selected' => !empty($record->folder_id) ? json_decode($record->folder_id, true) : [],
                        ]) !!}

                        <div class="text-danger custom_documents_error"></div>
                    </div>
                    <div class="col-xl-12 payment-div" style="{{ $record->stage_type != 'payment' ? 'display: none;' : '' }}">
                         {!! FormHelper::formInputText([
                            'name'=>"fees",
                            'id'=>"fees",
                            "label"=> "Enter Fees",
                            "required"=>false,
                            "value" => $record->fees
                            ])
                        !!}
                    </div>
                    <div class="col-xl-12 payment-description-div" style="{{ $record->stage_type != 'payment' ? 'display: none;' : '' }}">
                        {!! FormHelper::formTextarea([
                            'name'=>"payment_description",
                            'id'=>"payment_description",
                            "label"=>"Enter Payment Description",
                            'required'=>false,
                            'textarea_class'=>"noval cds-texteditor",
                            'class' => 'select2-input ga-country',
                            'value' => $invocieItem->particular ?? ''
                        ]) !!}
                    </div>
                </div>
                <div class="text-end mt-4">
                    <button type="submit" class="btn add-CdsTYButton-btn-primary">Save & publish</button>
                </div>
            </form>
        </div>
    </div>
</div>
  
<script>
$(document).ready(function(){
    initSelect();
    $("#edit-sub-stage-forms").submit(function(e) {
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        var url = $("#edit-sub-stage-forms").attr('action');
        
        var is_valid = formValidation("edit-sub-stage-forms");
        
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
                        if(index == 'folder'){
                            $('.custom_documents_error').html(value);
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
            $('.payment-div').hide();
            $('.payment-description-div').hide();
        }else if(selectedValue == 'case-document'){
            $('.default-document-div').show();
            $('.custom-document-div').show();
            $('.form-div').hide();
            $('.payment-div').hide();
            $('.payment-description-div').hide();
        }else if(selectedValue == 'payment'){
            $('.default-document-div').hide();
            $('.custom-document-div').hide();
            $('.form-div').hide();
            $('.payment-div').show();
            $('.payment-description-div').show();
        }
    });


})

</script>

