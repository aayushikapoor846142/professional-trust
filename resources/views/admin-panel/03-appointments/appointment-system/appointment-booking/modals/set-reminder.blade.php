 <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
        <form id="popup-form" enctype="multipart/form-data" action="{{ baseUrl('appointments/appointment-booking/save-reminder/') }}" method="post">
            <div class="modal-header">
                <h5 class="modal-title">{{$pageTitle}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @csrf
                    <div class="col-xl-12">
                        {!! FormHelper::formDatepicker([ 'label' => 'Reminder Date', 'name' => 'reminder_date', 'id' => 'reminder_date', 'class' => 'select2-input ga-country', 'required' => true, ]) !!}
                    </div>
                    <div class="col-xl-12">
                        <input class="form-control" type="hidden" value="{{$appointmentId}}" name="appointment_id" />
                        {!! FormHelper::formTimepicker([ 'label' => 'Reminder Time', 'name' => 'reminder_time', 'id' => 'reminder_time', 'class' => 'select2-input ga-country', 'required' => true, ]) !!}
                    </div>
                    <div class="col-xl-12">
                        {!! FormHelper::formTextarea(['name'=>"reminder_message",'id'=>"editor","label"=>"Reminder Message", 'textarea_class'=>"noval"]) !!}                        
                    </div>
                </div>                
            </div>
            <div class="modal-footer">
                <div class="form-group text-start">
                    <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
       initTimePicker('reminder_time');
        reminderDatePicker('reminder_date');
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
                        window.location.reload();
                    } else {
                        validation(response.message);
                    }
                },
                error: function(xhr) {
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