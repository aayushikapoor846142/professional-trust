<div class="modal-dialog modal-dialog-centered"> <!-- Optional: use fullscreen on small devices -->
    <div class="modal-content">  
        <form id="popup-form1" enctype="multipart/form-data" action="{{ baseUrl('appointments/appointment-booking/save-joining-link/'.$appointmentBookingId) }}" method="post">
        <div class="modal-header">
            <h5 class="modal-title">{{$pageTitle}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          
                @csrf
                      
                    
                    <div>
                    {!! FormHelper::formInputUrl([
                                'name'=>"appointment_mode_details",
                                'id'=>"appointment_mode_details",
                                "label"=>"Enter Joining Link",
                                "value"=>$appointment->appointment_mode_details,
                                "required"=>true,
                            ]) !!}  
                    </div>
            
        </div>
        <div class="modal-footer"><div class="form-group text-start">
                    <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
                </div>
            </div> 
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {
       

        $("#popup-form").submit(function(e) {
            e.preventDefault();
            var is_valid = formValidation("popup-form");
            if (!is_valid) {
                return false;
            }
        
            var formData = new FormData($(this)[0]);
            var url = $("#popup-form").attr('action');
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