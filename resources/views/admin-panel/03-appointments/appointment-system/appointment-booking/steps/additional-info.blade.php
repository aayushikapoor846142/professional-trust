<div class="CdsAppointmentSystem-header">
    <h1 class="CdsAppointmentSystem-title">Additional Information</h1>
</div>

<form id="additional-info-form" class="js-validate" action="{{ baseUrl('appointments/appointment-booking/save') }}" method="post">
    @csrf
    <input type="hidden" name="professional_id" value="{{$professional_id}}">
    <input type="hidden" name="type" value="additional_information">
    <input type="hidden" name="booking_id" value="{{$booking_id}}">
    
    <div class="CdsAppointmentSystem-form-group">
        {!! FormHelper::formTextarea([
            'name'=>"additional_info",
            'id'=>"additional_info_textarea",
            "label"=>"Enter Additional Information",
            'textarea_class'=>"CdsAppointmentSystem-textarea cds-texteditor",
            'value'=>html_entity_decode($appointment_data->additional_info)?? ''
        ]) !!}
    </div>
    
    <div class="CdsAppointmentSystem-btn-group">
        <button type="button" class="CdsAppointmentSystem-btn CdsAppointmentSystem-btn-secondary previous">Previous</button>   
        <button type="submit" class="CdsAppointmentSystem-btn CdsAppointmentSystem-btn-primary">Next</button>
    </div>
</form>
@push('scripts')
<script>
$(document).ready(function() {
    $("#additional-info-form").submit(function(e) {
        e.preventDefault();
        var is_valid = formValidation("form");
        if (!is_valid) {
            return false;
        }
        var formData = new FormData($(this)[0]);
        var url = $("#additional-info-form").attr('action');

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
                    $.each(response.message, function(key, val) {
                       errorMessage(val);
                    });
                }
            },
            error: function() {
                internalError();
            }
        });
    });
});
</script>
@endpush