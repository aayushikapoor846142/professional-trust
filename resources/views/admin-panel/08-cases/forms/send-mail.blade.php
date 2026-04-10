<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">{{$pageTitle}}</h5>
            <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-bs-dismiss="modal" aria-label="Close">
                <i class="tio-clear tio-lg"></i>
            </button>
        </div>
        <div class="modal-body">
            <form method="post" id="popup-form" class="js-validate" action="{{ baseUrl('/forms/send-mail/'.$form_id) }}">  
                @csrf
                <!-- Form Group -->
                <div class="row form-group js-form-message send-to" id="non-registered">
                    <label class="col-sm-3 col-form-label input-label">Email</label>
                    <div class="col-sm-9">
                        <div class="input-group input-group-sm-down-break">
                            
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" placeholder="Enter Email" aria-label="Enter Email" >
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row form-group js-form-message mt-3">
                    <label class="col-sm-3 col-form-label input-label">Message</label>
                    <div class="col-sm-9">
                        <div class="input-group input-group-sm-down-break">
                            <textarea type="text" class="form-control @error('message') is-invalid @enderror" name="message" id="message" placeholder="" ></textarea>
                            @error('message')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" id="closebtn" class="btn btn-white" data-bs-dismiss="modal">Close</button>
            <button form="popup-form" class="CdsTYButton-btn-primary">Send</button>
        </div>
    </div>
</div>

<script type="text/javascript">
  initEditor("message");
 
  $(document).ready(function(){

    $("#send_to").select2({
            dropdownParent: $('#popupModal .modal-content')
        });

    function closeModal(){
      //alert('sajsh')
    $("#closebtn").click();

    }

      $("#registered_user").change(function(){
        $(".send-to").hide();
        if($(this).is(":checked")){
            $("#registered").show();
            $("#email").attr("disabled","disabled");
            $("#send_to").removeAttr("disabled");
            setTimeout(() => {
                $('#popupModal').on('shown.bs.modal', function () {
                        // Initialize all elements with the class 'js-select2' within the modal
                        $('select').each(function() {
                            $.HSCore.components.HSSelect2.init($(this));
                        });
                    });    
                }, 1000);
        }else{
          $("#non-registered").show();
          $("#send_to").attr("disabled","disabled");
          $("#email").removeAttr("disabled");
        }
      })
      $("#popup-form").submit(function(e){
          e.preventDefault();
          var formData = $("#popup-form").serialize();
          var url  = $("#popup-form").attr('action');
          $("#popupModal button[form=popup-form]").attr("disabled","disabled");
          $.ajax({
              url:url,
              type:"post",
              data:formData,
              dataType:"json",
              beforeSend:function(){
                showLoader();
              },
              success:function(response){
                hideLoader();
                $("#popupModal button[form=popup-form]").removeAttr("disabled");
                if(response.status == true){
                  successMessage(response.message);
                 closeModal();
                  // setTimeout(function(){
                  //   location.reload();
                  // },2000);
                  
                }else{
                        validation(response.message);
                  errorMessage(response.message);
                }
              },
              error:function(){
                internalError();
              }
          });
      });
  });

  function userCases() {
        $.ajax({
            url: "{{ url('user-cases') }}",
            type: "post",
            data: {
                _token: "{{csrf_token()}}",
                user_id: $("#send_to").val()
            },
            dataType: "json",
            beforeSend: function() {
                showLoader();
            },
            success: function(response) {
                hideLoader();
                $("#cases").html(response.options);
				$('#popupModal').on('shown.bs.modal', function () {
					//initSelect(".user-cases");
					// Initialize all elements with the class 'js-select2' within the modal
					$(".user-cases select").each(function(){
						$.HSCore.components.HSSelect2.init($(this));
					});
					
				});    
            },
            error: function() {
                internalError();
            }
        });
    }
 function closeModal() {
        $('#popupModal').modal('hide');
    }
</script>