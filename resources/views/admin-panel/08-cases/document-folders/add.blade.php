@extends('components.custom-popup',['modalTitle'=>$pageTitle ?? 'Mark As Client'])
@section('custom-popup-content')
<div class="cds-form-container cds-ty-dashboard-box-body mb-0">

    <form id="form" class="js-validate mt-3" action="{{ baseUrl('document-folders/save') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-xl-12">
               {!! FormHelper::formInputText(['name'=>"name","id" => "name","label"=>"Enter name","required"=>true,'events'=>['oninput=validateString(this)']]) !!}
            </div>
        </div>        
        @section('custom-popup-footer')
        <div class="text-start">
            <button type="submit" form="form" class="btn btn-primary add-btn">Save</button>
            </div>
        @endsection
    </form>
</div>

<!-- End Content -->
<script>
$(document).ready(function () {
    
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