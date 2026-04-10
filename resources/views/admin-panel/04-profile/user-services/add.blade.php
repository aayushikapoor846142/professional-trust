@extends('admin-panel.layouts.app')
@section('content')
 <div class="ch-action">
                    <a href="{{ baseUrl('my-services') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-solid fa-left me-1"></i>
                        Back
                    </a>
                </div><div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
------------------

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <form id="professional-form-site" class="js-validate" action="{{ baseUrl('myservices/save') }}" method="post">
                        @csrf
                        <input type="hidden" name="actual_service_id" value="{{ $record->service_id ?? '' }}">
                        <input type="hidden" name="professional_service_id" value="{{ $record->id ?? '' }}">
                        <!-- Input Group -->
                        <div class="row">
                            <div class="col-xl-6 col-md- col-lg-6 col-sm-6">
                                {!! FormHelper::formSelect([
                                'name' => 'type',
                                'label' => 'Select Type',
                                'class' => 'select2-input ga-country',
                                'required' => true,
                                'options' => $types,
                                'value_column' => 'id',
                                'label_column' => 'name',
                                'selected' => isset($price) ? $price->type : null,
                                ]) !!}
                                {{--'options' => $types->pluck('name', 'id')->toArray(),--}}
                            </div>
                            <div class="col-xl-6 col-md- col-lg-6 col-sm-6">
                                {!! FormHelper::formSelect([
                                'name' => 'documents',
                                'label' => 'Select Document Type',
                                'class' => 'select2-input ga-country',
                                'required' => true,
                                'options' => $docuemnts,
                                'value_column' => 'id',
                                'label_column' => 'name',
                                'selected' => isset($price) ? $price->documents : null,
                                ]) !!}
                            </div>
                            <div class="col-xl-6 col-md- col-lg-6 col-sm-6">
                                {!! FormHelper::formInputText([
                                'name' => 'professional_fees',
                                'label' => 'Professional Fees',
                                'id' => 'professional_fees',
                                'required' => true,
                                'value' => $price->professional_fees ?? '',
                                ]) !!}
                            </div>
                            <div class="col-xl-6 col-md- col-lg-6 col-sm-6">
                                {!! FormHelper::formInputText([
                                'name' => 'consultancy_fees',
                                'label' => 'Counsultancy Fees',
                                'id' => 'consultancy_fees',
                                'required' => true,
                                'value' => $price->consultancy_fees ?? '',
                                ]) !!}
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn add-CdsTYButton-btn-primary">Submit</button>
                        </div>
                    </form>
            
			</div>
	
	</div>
  </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-xl-12">
            <div class="cds-ty-dashboard-box">
                <div class="cds-ty-dashboard-box-header">
                </div>
                <div class="cds-ty-dashboard-box-body">
                           <!-- End Input Group -->
                </div>
                <!-- End Card body-->
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        $("#professional-form-site").on('submit', function(e) {
            e.preventDefault();
            var is_valid = formValidation("professional-form-site");
            if (!is_valid) {
                return false;
            }
            var formData = $("#professional-form-site").serialize();
            $.ajax({
                url: "{{ baseUrl('my-services/save') }}",
                type: "post",
                data: formData,
                dataType: "json",
                beforeSend: function() {
                    showLoader()
                },
                success: function(response) {
                    hideLoader();
                    if (response.status == true) {
                        successMessage(response.message);
                        window.location.href = response.redirect_back;
                    } else {
                        validation(response.message);
                    }
                },
                error: function() {
                    hideLoader();
                    internalError();
                }
            });
        });
    });
</script>
@endsection