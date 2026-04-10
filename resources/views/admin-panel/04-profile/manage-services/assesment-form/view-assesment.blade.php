@extends('admin-panel.layouts.app')

@section('content')

<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
   <form id="form" class="js-validate" action="{{ baseUrl('forms/save') }}" method="post">
                        @csrf
                        <div id="form-render"></div>
                    </form>
			</div>
	
	</div>
  </div>
</div>				

@endsection
<!-- End Content -->
@section('javascript')

<link href="{{ url('assets/plugins/form-generator/css/cds-form-generator.css') }}" rel="stylesheet" />
<script src="{{ url('assets/plugins/form-generator/js/jquery-ui.js') }}"></script>
<script src="{{ url('assets/plugins/form-generator/js/form-generator.js') }}"></script>
<script>
    var formJson = '{!!$record->fg_field_json!!}';
    var defaultValues = '{!!$last_saved!!}';

    $(document).ready(function() {
        var fr = $('#form-render').formRender({
            formType: "{{$record->form_type}}",
            formJson: formJson,
            formID:"{{$record->id}}",
            ajax_call: false,
            defaultValues: defaultValues,
            saveUrl: "{{ baseUrl('forms/save') }}",
        });
        $(".finish-btn").remove();
    });
</script>
@endsection