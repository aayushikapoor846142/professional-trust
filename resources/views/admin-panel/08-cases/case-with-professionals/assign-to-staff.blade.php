
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" >{{$pageTitle}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="assign-staff-form" class="js-validate" action="{{ baseUrl('case-with-professionals/save-assign-staff') }}" method="post">
                @csrf
                <input type="hidden" name="case_id" value="{{$cases->unique_id}}">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="multi-selectbox">
                            {!! FormHelper::formSelect([
                                'name' => 'staffs[]',
                                'label' => 'Select Staff',
                                'select_class' => 'select2-input cds-multiselect add-multi',
                                'id' => 'assign_to_staff',
                                'options' => $users,
                                'value_column' => 'id',
                                'label_column' => 'name',
                                'is_multiple' => true,
                                'selected' => isset($selected_staff_ids) ? $selected_staff_ids : null,
                                'required' => true,
                            ]) !!}
                        </div>
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
    initSelect();
    $("#assign-staff-form").submit(function(e) {
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        var url = $("#assign-staff-form").attr('action');
        
        var is_valid = formValidation("assign-staff-form");
        
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

