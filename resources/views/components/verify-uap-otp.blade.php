<div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="otpModalLabel">Verify OTP</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="popup-form" name="popup-form" class="js-validate mt-3" action="{{ route('tracking.otp.verify') }}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="" class="col-form-label input-label">OTP *</label>
                        <p>Otp has been sent to your email.</p>
                        <div class="js-form-message">
                            <input type="hidden" class="otp_token" name="otp_token" value="{{$otp_token}}" id="otp_token">
                            <input type="number" oninput="onlyNumberKey(this)" class="form-control required" name="otp" id="otp" placeholder="Enter otp" aria-label="Enter OTP">
                            <div class="otp-error text-danger"></div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn add-btn resend-otp btn-warning">Resend OTP</button>
        <button type="submit" form="popup-form" class="btn add-CdsTYButton-btn-primary">Verify OTP</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function(){
      $(".resend-otp").click(function(){
        var formData = new FormData($("#form")[0]);
        formData.append("otp_type","resend");
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
                $(".resend-otp").attr("disabled","disabled");
            },
            success: function(response) {
                hideLoader();
                $(".resend-otp").removeAttr("disabled");
                $('.alert-danger').addClass('d-none');
                $('.alert-danger-lbl').html('');
                if(response.status != 0){
                    // $('#otpModal').modal('show');
                    successMessage("Otp resend successfully");
                    // showPopup('<?php echo url('tracking-otp') ?>/'+response.otp_token);
                }else{
                    $('.alert-danger-lbl').html(response.message);
                    // errorMessage(response.message);
                }
            },
            error: function() {
                $(".btnSearch").removeAttr("disabled");
                internalError();
            }
        });
      })
      $("#popup-form").submit(function(e) {
          e.preventDefault();
          var is_valid = formValidation("popup-form");
          if(!is_valid){
              return false;
          }
          var formData = new FormData($(this)[0]);
          formData.append("track_id",$("#track_id").val());
          formData.append("ref_unique_id",$("#ref_unique_id").val());
          
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
                  $("#otpModal").find(".add-btn").attr("disabled","disabled");
              },
              success: function(response) {
                  hideLoader();
                  $("#otpModal").find(".add-btn").removeAttr("disabled");
                  $('.otp-error').html('');
                  if(response.status != 0){
                      $(".html_div").html(response.html);
                      $("#uap_id").val(response.result.id);
                      $(".comment_div").removeClass('d-none');
                      closeModal();
                  }else{
                      $('.otp-error').html(response.message);
                  }
              },
              error: function() {
                  $("#otpModal").find(".add-btn").removeAttr("disabled");
                  internalError();
              }
          });

      });
    })
  </script>