
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" >{{$pageTitle}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="add-stage-form" class="js-validate" action="{{ baseUrl('case-with-professionals/stages/save/'.$case_id) }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                        {!! FormHelper::formInputText([
                            'name'=>"name",
                            'id'=>"name",
                            "label"=> "Enter Name",
                            "required"=>true
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
                        ]) !!}
                    </div>
                    <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                        {!! FormHelper::formInputText([
                            'name'=>"fees",
                            'id'=>"fees",
                            "label"=> "Enter Fees",
                            "required"=>true,
                            'events'=>['oninput=validateNumber(this)']
                            ])
                        !!}
                    </div>  
                </div>
                <div class="text-end mt-4">
                    <button type="submit" class="btn add-CdsTYButton-btn-primary">Save & publish</button>
                </div>
            </form>
        </div>
    </div>
</div>
  
<script>
$(document).ready(function(){
       
    $("#add-stage-form").submit(function(e) {
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        var url = $("#add-stage-form").attr('action');
        
        var is_valid = formValidation("add-stage-form");
        
        if (!is_valid) {
            return false;
        }
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
                    location.reload();
                } else {
                    validation(response.message);
                }
            },
            error: function() {
                internalError();
            }
        });

    });
    

})

</script>

