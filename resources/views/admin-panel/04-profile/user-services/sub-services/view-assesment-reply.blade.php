@extends('admin-panel.layouts.app')

@section('content') <div class="ch-action">
                    <a href="{{ baseUrl('my-services/send-assesment-form-list/'.$form_unique_id) }}" class="CdsTYButton-btn-primary">
                        <i class="fa-left fa-solid me-1"></i>
                        Back
                    </a>
                    <a href="{{ baseUrl('my-services/analyze-assesment-reply/'.$record->unique_id) }}" class="CdsTYButton-btn-primary">
                        Analyze with Ai 
                    </a>
                </div><div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
<a class="breadcrumb-link" href="{{ baseUrl('my-services/send-assesment-form-list/'.$form_unique_id) }}">Send Assesment Forms</a>

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
   <div class="cds-form-container cds-height cds-ty-dashboard-box-body">
                    @if (session('summary'))
                        <div class="shadow-lg p-3 mb-3 bg-grey rounded">
                            <b>Summary: </b>{{ session('summary') }}
                        </div>
                    @endif
                    <form id="form" class="js-validate" action="{{ baseUrl('forms/save') }}" method="post">
                        @csrf
                        <div id="form-render"></div>
                    </form>
                </div>
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
    var formJson = '{!!$record->form_fields_json!!}';
    var defaultValues = '{!!$last_saved!!}';

    $(document).ready(function() {
        var fr = $('#form-render').formRender({
            formType: "{{$record->form_type}}",
            formJson: formJson,
            formID:"{{$record->id}}",
            ajax_call: false,
            defaultValues: defaultValues,
        });
        $(".finish-btn").remove();
    });
</script>
@endsection