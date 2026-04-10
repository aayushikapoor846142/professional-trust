@extends('admin-panel.08-cases.case-with-professionals.my-cases-master')
@section('case-container')
                
<div class="submit-bid p-3 border rounded bg-light mt-3">
    <div class="d-flex justify-content-between align-items-center">
    <h5>{{$pageTitle}}</h5>
    <button type="button" onclick="showPopup('<?php echo baseUrl('case-with-professionals/retain-agreements/generate-retain-agreements/'.$case_id) ?>')" class="CdsTYButton-btn-primary btn-sm">Generate via AI</button>
    </div>
    
    @if(!empty($CaseRetainAgreements))
     <!-- <a href="javascript:;" class="CdsTYButton-btn-primary">Verify Agreement via AI</a> -->
    {{-- <a href="javascript:;" data-href="{{baseUrl('/case-with-professionals/retain-agreements/check-retain-agreements/'.$CaseRetainAgreements->unique_id)}}" type="button" class="CdsTYButton-btn-primary btn-sm verify-retain-agreement">Verify Agreement via AI</a> --}}
    @endif
    
     <!-- <a href="{{baseUrl('/case-with-professionals/retain-agreements/ai-bot/'.$case_id)}}" class="CdsTYButton-btn-primary btn-sm">Generate via AI</a> -->
    <form id="save-agreement-form" class="js-validate mt-3" action="{{ baseUrl('/case-with-professionals/retain-agreements/save-retain-agreements') }}" method="POST">
        @csrf
        <input type="hidden" name="case_id" value="{{$case_id}}">
        <div class="row">
            <div class="mb-2 col-md-12">
                {!! FormHelper::formInputText([
                    'label' => "Title",
                    'id' => 'title',
                    'name' => 'title',
                    'required' => true,
                    'value' => $CaseRetainAgreements->title ?? '',
                ]) !!}
            </div>
            <div class="mb-2 col-md-12">
                {!! FormHelper::formSelect([
                    'name' => 'signature_type',
                    'label' => 'Signature Type',
                    'class' => 'select2-input ga-country',
                    'id' => 'signature_type',
                    'options' => FormHelper::signatureType(),
                    'value_column' => 'value',
                    'label_column' => 'label',
                    'selected' => $CaseRetainAgreements->signature_type ?? '',
                    'is_multiple' => false,
                    'required' => true,
                ]) !!}
            </div>
            <div class="mb-2 col-md-12">
                {!! FormHelper::formTextarea(['name'=>"agreement",
                    'id'=>"agreement",
                    'required'=>true,
                    "label"=>"Enter Agreement",
                    'class'=>"cds-texteditor",
                    'textarea_class'=>"noval",
                    'value' =>  $CaseRetainAgreements->agreement ?? '',
                ]) !!}

            </div>
        </div>
        <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
    </form>
</div>


@endsection

@section('javascript')
<script src="{{ url('assets/plugins/custom-form-builder/custom-form-builder.js') }}"></script>
<link rel="stylesheet" href="{{ url('assets/plugins/custom-form-builder/custom-form-builder.css') }}">
<script type="text/javascript">
    // let editorInstance;

    $(document).ready(function() {
        initEditor("agreement");
    

        $("#save-agreement-form").submit(function(e) {
            e.preventDefault();
            var is_valid = formValidation("save-agreement-form");
            if (!is_valid) {
                return false;
            }
            var formData = new FormData($("#save-agreement-form")[0]);
      
            var url = $("#save-agreement-form").attr('action');
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
                        // redirect(response.redirect_back);
                    } else {
                        validation(response.message);
                    }
                },
                error: function() {
                    internalError();
                }
            });
           
        });
      
    });
    

</script>
@endsection
