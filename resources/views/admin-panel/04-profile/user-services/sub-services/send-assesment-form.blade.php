@extends('admin-panel.layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-xl-12">
            <div class="cds-ty-dashboard-box">
                <div class="cds-ty-dashboard-box-body">
                    <div class="cds-form-container">
                        <form method="post" id="popup-form" class="js-validate" action="{{ baseUrl('/my-services/send-mail/'.$form_id) }}">  
                            @csrf
                            <!-- Form Group -->
                            <div class="row">
                                <div class="col-md-12 js-form-message">
                                    <label for="" class="col-form-label input-label my-2">Send to existing user?</label>
                                    <div class="form-check">
                                        <input type="radio"
                                            name="existing_user"
                                            id="existing_user2"
                                            value="yes"
                                            class="radio-input" checked>
                                        <label for="existing_user2">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio"
                                            name="existing_user"
                                            id="existing_user1"
                                            value="no"
                                            class="radio-input">
                                        <label for="existing_user1">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row email-div" style="display:none;">
                                <div class="col-md-12 js-form-message">
                                    <label for="" class="col-form-label input-label my-2">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" placeholder="Enter Email" aria-label="Enter Email" >
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row user-div">
                                <div class="col-md-12 js-form-message">
                                    <label for="" class="col-form-label input-label my-2">User</label>
                                    <select class="form-control" name="user_id">
                                        <option value="">Select User</option>
                                        @foreach($users as $user)
                                            <option value="{{$user->id}}">{{$user->first_name}} {{$user->last_name}} [{{$user->email}}]</option>
                                        @endforeach
                                    </select>
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="" class="col-form-label input-label my-2">Message</label>
                                    <textarea type="text" class="form-control @error('message') is-invalid @enderror" name="message" id="message" placeholder="" ></textarea>
                                    @error('message')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="text-start mt-3">
                                <button type="submit" class="btn add-CdsTYButton-btn-primary">Next</button>
                            </div>
                        </form>
                        <div class="form-render-div" style="display:none;">
                            @csrf
                            <div id="form-render"></div>
                            <input type="hidden" name="send_form_id" id="send_form_id">
                            <a id="send-assesment-email" class="CdsTYButton-btn-primary">Send Email</a>
                            <a id="edit-form" class="CdsTYButton-btn-primary">Edit Form</a>
                        </div>
                        <div id="edit-form-div"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('javascript')

<link href="{{ url('assets/plugins/form-generator/css/cds-form-generator.css') }}" rel="stylesheet" />
<script src="{{ url('assets/plugins/form-generator/js/jquery-ui.js') }}"></script>
<script src="{{ url('assets/plugins/form-generator/js/form-generator.js') }}"></script>
<script type="text/javascript">
  initEditor("message");
 
  $(document).ready(function(){
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
                //   successMessage(response.message);
                    $("#popup-form").hide();
                    $(".form-render-div").show();
                    var defaultValues = '';
                    $("#send_form_id").val(response.id);
                    $("#edit-form").attr("href", response.edit_url);
                    $("#send-assesment-email").attr("href",response.send_url);
                    
                    var fr = $('#form-render').formRender({
                        formType: response.formType,
                        formJson: response.formJson,
                        formID: response.formId,
                        ajax_call: false,
                        defaultValues: defaultValues,
                        saveUrl: "",
                    });
                    $(".finish-btn").remove();
                    // setTimeout(function(){
                    //   location.reload();
                    // },2000);
                    
                }else{
                    if(response.error_type == 'validation'){
                        validation(response.message);
                    }else{
                        errorMessage(response.message);
                    };
                }
                },
                error:function(){
                internalError();
                }
            });
        });

        $(document).on("change", "input[name='existing_user']", function () {
            var selected_val = $(this).val();

            if(selected_val == "yes"){
                $(".user-div").show();
                $(".email-div").hide();
            }else{
                $(".user-div").hide();
                $(".email-div").show();
            }
        });
  });

  
</script>
@endsection