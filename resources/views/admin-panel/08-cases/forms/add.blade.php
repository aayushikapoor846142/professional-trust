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
<div class="cds-ty-dashboard-box-body cds-createform">
                    <div class="cds-form-container">
                        <div class="form-generator"></div>
                        <!-- <form id="form" class="js-validate" action="{{ baseUrl('forms/save') }}" method="post">
                            @csrf
                            <div id="form-generator"></div>
                        </form> -->
                    </div>
                </div>
			</div>
	
	</div>
  </div>
</div>


@endsection
<!-- End Content -->
@section('javascript')
<link href="{{ url('assets/plugins/form-generator/css/cds-form-generator.css?v='.mt_rand()) }}" rel="stylesheet" />
<!-- <script src="{{ url('assets/plugins/form-generator/js/jquery-ui.js?v='.mt_rand()) }}"></script> -->
<!-- <script src="{{ url('assets/plugins/form-generator/js/form-generator.js?v='.mt_rand()) }}"></script> -->

<script src="{{ url('assets/plugins/form-generator/js/form-generator-new.js?v='.mt_rand()) }}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });


    $(document).ready(function() {
        // $('.form-generator').formGenerator({
        //     saveUrl: "{{ baseUrl('forms/save') }}"
        // });
        CdsFormBuilder.formGenerator('.form-generator', {
            saveUrl: "{{ baseUrl('forms/save') }}",
            formName: 'My Form',
            formType: 'step_form',
            services: @json($immigrationServices),
            selectedServiceId: null,
            selectedSubServiceId: null,
            dataFormat: 'form', // This is the default
            debugMode: true, // See what's being sent
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.content // For Laravel
        });
        
    });
</script>

@endsection