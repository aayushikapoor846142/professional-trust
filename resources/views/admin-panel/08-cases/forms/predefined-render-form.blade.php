


@extends('components.custom-popup',['modalTitle'=>$pageTitle])
@section('custom-popup-content')
<div class="container">
    <div class="row">
        <div class="col-xl-12">
            <div class="cds-ty-dashboard-box">
                <a class="CdsTYButton-btn-primary"  onclick="confirmUseTemplateAction(this)"  data-href="{{baseUrl('forms/save-predefined-template/'.$record->unique_id)}}">Use template</a>
                <div class="cds-form-container cds-height cds-ty-dashboard-box-body">
                    <div id="form-render"></div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<!-- End Content -->


<link href="{{ url('assets/plugins/form-generator/css/form-generator-new.css') }}" rel="stylesheet" />
<script src="{{ url('assets/plugins/form-generator/js/jquery-ui.js') }}"></script>
<script src="{{ url('assets/plugins/form-generator/js/form-generator-new.js') }}"></script>
<script>
    var formJson = '{!!$record->fg_field_json!!}';
    var defaultValues = '{!!$last_saved!!}';

    $(document).ready(function() {
        CdsFormBuilder.formRender('#form-render', {
            formJson: formJson,
            mode:'preview',
            formType: "{{$record->form_type}}",
            defaultValues: defaultValues,
            csrfToken:csrf_token
        });
        // var fr = $('#form-render').formRender({
        //     formType: "{{$record->form_type}}",
        //     formJson: formJson,
        //     formID:"{{$record->id}}",
        //     ajax_call: false,
        //     defaultValues: defaultValues,
        //     saveUrl: "{{ baseUrl('forms/save') }}",
        // });
        $(".finish-btn").remove();
    });

    function confirmUseTemplateAction(e) {
        var url = $(e).attr("data-href");
        Swal.fire({
            title: "Are you sure to use this template?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
            confirmButtonClass: "CdsTYButton-btn-primary",
            cancelButtonClass: "CdsTYButton-btn-primary CdsTYButton-border-thick ml-1",
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                redirect(url);
            }
        });
    }
</script>
@endsection
