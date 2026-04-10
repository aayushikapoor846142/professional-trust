@extends('admin-panel.layouts.app')

@section('content')
<div class="ch-action">
                    <a href="{{ baseUrl('forms') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-left fa-solid me-1"></i>
                        Back
                    </a>
                </div><div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
  <div class="cds-form-container cds-height cds-ty-dashboard-box-body">
                    <div id="form-render"></div>
                </div>
			</div>
	
	</div>
  </div>
</div>

@endsection
<!-- End Content -->
@section('javascript')

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
            saveUrl: "{{ baseUrl('forms/save') }}",
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
        // $(".finish-btn").remove();
    });
</script>
@endsection