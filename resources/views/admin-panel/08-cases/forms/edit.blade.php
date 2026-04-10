@extends('admin-panel.layouts.app')

@section('content')
 <div class="ch-action">
                    <a href="{{ baseUrl('forms') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-solid fa-left me-1"></i>
                        Back
                    </a>
                </div><div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
  <div class="cds-form-container cds-height cds-ty-dashboard-box-body">
                     <div class="form-generator"></div>
                </div>
			</div>
	
	</div>
  </div>
</div>

@endsection
<!-- End Content -->
@section('javascript')

<!-- <link href="{{ url('assets/plugins/form-generator/css/cds-form-generator.css?v='.mt_rand()) }}" rel="stylesheet" /> -->
<script src="{{ url('assets/plugins/form-generator/js/jquery-ui.js?v='.mt_rand()) }}"></script>
<!-- <script src="{{ url('assets/plugins/form-generator/js/form-generator.js?v='.mt_rand()) }}"></script> -->
<link href="{{ url('assets/plugins/form-generator/css/cds-form-generator.css?v='.mt_rand()) }}" rel="stylesheet" />
<script src="{{ url('assets/plugins/form-generator/js/form-generator-new.js?v='.mt_rand()) }}"></script>

<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  });
  
var formJson = '{!!$record->fg_field_json!!}';
$(document).ready(function() {
    // $('#form-generator').formGenerator({
    //   saveUrl:"{{ baseUrl('/forms/update/'.$record->unique_id) }}",
    //   formName:"{{$record->name}}",
    //   formType:"{{$record->form_type}}",
    //   defaultJson:formJson
    // });
    CdsFormBuilder.formGenerator('.form-generator', {
        saveUrl:"{{ baseUrl('/forms/update/'.$record->unique_id) }}",
        formName:"{{$record->name}}",
        formType:"{{$record->form_type}}",
        defaultJson: formJson,
        dataFormat: 'form',
        debugMode: true,
        services: @json($immigrationServices),
        selectedServiceId: {{ $selectedServiceId ?? 'null' }},
        selectedSubServiceId: {{ $selectedSubServiceId ?? 'null' }},
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.content // For Laravel
    });
  });
     
</script>
@endsection