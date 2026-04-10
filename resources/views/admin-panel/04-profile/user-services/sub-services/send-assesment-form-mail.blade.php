@extends('admin-panel.layouts.app')

@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
 <div style="display:none" class="generated-form">
                            <div class="card">
                                <div class="card-header">
                                    <p>Here is the generated form from AI relevant to service ask form.</p>
                                </div>
                                <div class="card-body">
                                    <div class="generated-form-preview"></div>
                                </div>
                            </div>
                        </div>

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
  <form id="popupForm" action="{{ baseUrl('/my-services/send-form-email-to-user/'.$record->unique_id) }}" method="post" enctype="multipart/form-data" class="question-form">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="" class="col-form-label input-label my-2">Do you want to send Email</label>
                                    <div class="form-check">
                                        <!-- Hidden radio input -->
                                        <input type="radio"
                                            name="send_email"
                                            id="send_email"
                                            value="yes"
                                            class="radio-input" checked>
                                        <!-- Custom radio button label -->
                                        <label for="send_email">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <!-- Hidden radio input -->
                                        <input type="radio"
                                            name="send_email"
                                            id="send_email1"
                                            value="no"
                                            class="radio-input">
                                        <!-- Custom radio button label -->
                                        <label for="send_email1">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="text-start mt-3">
                                <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
                            </div>
                        </form>
            
			</div>
	
	</div>
  </div>
</div>



@endsection
@section('javascript')
<script type="text/javascript">

$(document).ready(function() {

    
    $("#popupForm").submit(function(e) {
        e.preventDefault();
        var is_valid = formValidation("popupForm");
        if (!is_valid) {
            return false;
        }
        var formData = new FormData($(this)[0]);
        var url = $("#popupForm").attr('action');
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
                if(response.status == true){
                    successMessage(response.message);
                    window.location.href = response.redirect_back;
                }else{
                    errorMessage(response.message);
                    window.location.href = response.redirect_back;
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