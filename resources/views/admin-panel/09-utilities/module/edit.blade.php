@extends('admin-panel.layouts.app')

@section('content')

   <div class="ch-action">
                    <a href="{{ baseUrl('module') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-left fa-solid me-1" aria-hidden="true"></i>
                        Back
                    </a>
                </div>
    <div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
<form id="form" class="js-validate mt-3" action="{{ baseUrl('/module/update/'.$record->unique_id) }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-xl-6 col-md-6 col-lg-6 mb-2">
                        {!! FormHelper::formInputText(['name'=>"name","id" => "name","value" => $record->name ?? '',"label"=>"Enter name","required"=>true]) !!}
                    </div>
                    <div class="col-xl-6 col-md-6 col-lg-6 mb-2">
                        {!! FormHelper::formSelect([
                        'name' => 'action[]',
                        'label' => 'Action',
                        'class' => 'cds-multiselect add-multi',
                        'required' => true,
                        'options' => $action,
                        'value_column' => 'slug',
                        'label_column' => 'name',
                        'is_multiple' => true,
                        'selected' => $module_action,
                        ]) !!}
                    </div>
                </div>
                <div class="text-start">
                    <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
                </div>
            </form>
   
			</div>
	
	</div>
  </div>
</div>
    


@endsection
<!-- End Content -->
@section('javascript')
    <script>
        $(document).ready(function() {
            $("#form").submit(function(e) {
                e.preventDefault();
                var is_valid = formValidation("form");
                if(!is_valid){
                    return false;
                }
                var formData = new FormData($(this)[0]);
                var url = $("#form").attr('action');
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