@extends('components.custom-popup',['modalTitle'=>$pageTitle ?? 'Generate Via AI'])
@section('custom-popup-content')
<div class="container">
    <div class="row">
        <div class="col-xl-12">
            <div class="cds-ty-dashboard-box">
            <form id="popupForm" class="js-validate" action="{{baseUrl('forms/generated-ai-save')}}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        {!! FormHelper::formSelect([
                            'label' => 'Form Type',
                            'name' => 'form_type',
                            'class' => 'select2-input ga-country',
                            'required' => true,
                            'options' => FormHelper::formType(),
                            'value_column' => 'value',
                            'selected' => '',
                            'label_column' => 'label',
                        ]) !!}
                    </div>
                    <div class="col-md-12">
                        {!! FormHelper::formSelect([
                            'name' => 'service_id',
                            'id' => 'service_id',
                            'label' => 'Service',
                            'class' => 'select2-input ga-country',
                            'options' => $immigrationServices,
                            'value_column' => 'id',
                            'label_column' => 'name',
                            'is_multiple' => false,
                            'required' => true,
                        ]) !!}
                    </div>
                     <div class="col-md-12">
                        {!! FormHelper::formSelect([
                            'name' => 'sub_service_id',
                            'id' => 'sub_service_id',
                            'options' => [],
                            'label' => 'Sub Service',
                            'class' => 'select2-input ga-country',
                            'is_multiple' => false,
                            'required' => true,
                        ]) !!}
                    </div>
                    <div class="col-md-12">
                        {!! FormHelper::formTextarea([
                            'name'=>"message",
                            "label"=>"Message",                           
                            'textarea_class'=>"cds-texteditor",
                            'value' => old('message'),
                            'required'=>true,
                        ]) !!}
                    </div>
                </div>
                @section('custom-popup-footer')
                    <button form="popupForm" type="submit" class="CdsTYButton-btn-primary next signup">Save</button>
                @endsection
                
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    initSelect();
    

    
    $("#popupForm").submit(function(e){
 
        e.preventDefault();
        var is_valid = formValidation('popupForm');
        if (!is_valid) {
            return false;
        }
        $(this).find(".signup").attr("disabled","disabled");
        var formData = new FormData($(this)[0])
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType:"json",
            success: function (response) {
                $(this).find(".signup").removeAttr("disabled");
                if (response.status == true) {
                    successMessage(response.message);
                    $("#popupModal").modal("hide");
                   window.location.href = response.redirect_back;
                } else {
                    if(response.error_type == 'validation'){
                        validation(response.message);
                    }else{
                        errorMessage(response.message);
                    }
                    $(".signup").removeAttr("disabled");
                }
            },
            error: function (response) {
                $(this).find(".signup").removeAttr("disabled");
            }
        });
    });
    
    // 

    $("#service_id").change(function(){
        service_id = $(this).val();
        $.ajax({
            url: "{{ baseUrl('forms/fetch-sub-service') }}",
            data: {
                service_id: service_id
            },
            dataType: "json",
            beforeSend: function() {
                $("#sub_service_id").html('');
            },
            success: function(response) {
                if (response.status == true) {
                    $("#sub_service_id").html(response.options);
                }
            },
            error: function() {

            }
        });
    });


    // 
});
</script> 
@endsection