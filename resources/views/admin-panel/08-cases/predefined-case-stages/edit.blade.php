@extends('admin-panel.layouts.app')
@if(checkPrivilege([
                        'route_prefix' => 'panel.predefined-case-stages',
                        'module' => 'professional-predefined-case-stages',
                        'action' => 'edit'
                    ]))
                    @php
                    $canEditPredefinedCaseStages=true;
                    @endphp
@else
                    @php
                    $canEditPredefinedCaseStages=false;
                    @endphp
@endif
@php 
$page_arr = [
    'page_title' => 'Edit Predefined Case Stage ',
    'page_description' => 'Edit predefined case stage.',
    'page_type' => 'edit-predefined-case-stage',
    'canEditPredefinedCaseStages' => $canEditPredefinedCaseStages,
    'predefinedCaseStagesFeatureStatus' => $predefinedCaseStagesFeatureStatus ?? null,
];
@endphp
@section('page-submenu')
{!! pageSubMenu('cases',$page_arr) !!}
@endsection
@section('content')
           <div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
                @if(isset($predefinedCaseStagesFeatureStatus))
                    @if(!$canEditPredefinedCaseStages)
                        <div class="alert alert-danger mb-3">
                            <strong>⚠ Predefined Case Stages Management</strong><br>
                            {{ $predefinedCaseStagesFeatureStatus['message']  }}
                           
                        </div>
                    @else
                        <div class="alert alert-warning mb-3">
                                <strong>⚠ Predefined Case Stages Management</strong><br>
                           
                            {{ $predefinedCaseStagesFeatureStatus['message'] }}
                        </div>
                    @endif
                @endif
			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
                @if($canEditPredefinedCaseStages)
                <form id="edit-predefined-stage-form" class="js-validate" action="{{ baseUrl('predefined-case-stages/update/'.$predefined_case_stages->unique_id) }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                                {!! FormHelper::formInputText([
                                    'name'=>"name",
                                    'id'=>"name",
                                    "label"=> "Enter Name",
                                    "required"=>true,
                                    "value" => $predefined_case_stages->name ?? '',
                         
                                    ])
                                !!}
                            </div>  
                            <div class="col-xl-12">
                                {!! FormHelper::formTextarea([
                                    'name'=>"short_description",
                                    'id'=>"short_description",
                                    'required' => true,
                                    "label"=>"Short Description",
                                    'class'=>"editor noval",
                                    "value" => $predefined_case_stages->short_description ?? '',
                              
                                ]) !!}
                            </div>
                            <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                                {!! FormHelper::formInputText([
                                    'name'=>"fees",
                                    'id'=>"fees",
                                    "label"=> "Enter Fees",
                                    "required"=>true,
                                    "value" => $predefined_case_stages->fees ?? '',
                                    'events'=>['oninput=validateNumber(this)']
                                    ])
                                !!}
                            </div>  
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
                        </div>
                    </form>
                @else
                    <div class="text-center text-muted">
                        <p>You don't have permission to edit predefined case stages.</p>
                    </div>
                @endif
           
			</div>
	
	</div>
  </div>
</div>


@endsection
<!-- End Content -->
@section('javascript')
<script>
    $(document).ready(function() {
        $("#edit-predefined-stage-form").submit(function(e) {
            e.preventDefault();
            var is_valid = formValidation("edit-predefined-stage-form");
            if (!is_valid) {
                return false;
            }
            var formData = new FormData($(this)[0]);
            var url = $("#edit-predefined-stage-form").attr('action');
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
                        redirect(response.redirect_back);
                    } else {
                        if(response.error_type == 'validation'){
                            validation(response.message);
                        }else{
                            errorMessage(response.message);
                        }
                    }
                },
                error: function(xhr) {
                    internalError();
                                 if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.error_type === 'validation') {
            validation(xhr.responseJSON.message);
        } else {
            errorMessage('An unexpected error occurred. Please try again.');
        }
                }
            });

        });
    });
</script>
@endsection