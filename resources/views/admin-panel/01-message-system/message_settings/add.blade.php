@extends('admin-panel.layouts.app')

@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
 <div class="ch-head">
                            <i class="fa-table fas me-1"></i>
                            Fill form data
                        </div>
                        <div class="ch-action">
                            <a href="{{ baseUrl('company-settings') }}" class="CdsTYButton-btn-primary">
                            Back
                            </a>
                        </div>

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
<form id="form" class="js-validate" action="{{ baseUrl('/company-settings/save/') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="" class="col-form-label input-label">Company Name</label>
                                    <div class="js-form-message">
                                        <input type="text" class="form-control form-control-lg required" name="company_name"
                                            id="name" placeholder="Please Enter Name" aria-label="Enter Name"
                                            data-msg="" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="" class="col-form-label input-label">Email</label>
                                    <div class="js-form-message">
                                        <input type="text" class="form-control form-control-lg required" name="email"
                                            id="email" placeholder="Please Enter Email" aria-label="Enter Email"
                                            data-msg="" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="image" class="col-form-label input-label">Logo <span class="text-danger">(Size should be less than 2 MB)</span></label>
                                    <div class="js-form-message">
                                        <input type="file" class="form-control form-control-lg" name="logo"
                                            id="logo" aria-label="Choose image" accept=".jpg,.jpeg,.png,.gif,.bmp,.svg,.webp"
                                            data-msg="Please select an logo file (jpeg, png, etc.)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- New Checkboxes -->
                        <div class="form-group text-start mt-3">
                            <button type="submit" class="CdsTYButton-btn-primary add-btn">Save</button>
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