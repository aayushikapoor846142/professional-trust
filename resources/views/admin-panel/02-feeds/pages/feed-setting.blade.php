 <div class="chat-profile-card rounded">
    <h6>Feed Settings</h6>
    <form id="feed-setting-form" method="post" action="{{baseUrl('my-feeds/save-feed-setting/'.$feeds->unique_id)}}">
        @csrf
        <div class="form-group mb-3">
            {!! FormHelper::formRadio([
                'name' => "settings",
                'label' => 'Who can see my posts in the feed',
                'radio_class' => '',                        
                'options' => FormHelper::getFeedSettings(),
                'value_column' => 'value',
                'label_column' => 'label',
                'value' => 'label',
                'selected' => $feeds->allow_to_view ?? ''
                ]) 
            !!}
        </div>
        <div class="form-group mb-3">
            <div class="d-flex justify-content-between align-items-center cds-toogle-button">
                <span>Allow to Mute</span>
                {!! FormHelper::formToogleCheckbox([
                    'name' => "allow_to_mute",
                    'checkbox_class' => 'allow_to_mute',   
                    'data_attr' => "data-feed=".$feeds->unique_id."",                     
                    'id' => 'radio-{{$feeds->unique_id}}',
                    'checked' => $feeds->allow_to_mute,
                    'value' => 1,
                    ]) 
                !!}
            </div>
        </div>
        <div class="form-group mb-3">
            <div class="d-flex justify-content-between align-items-center cds-toogle-button">
                <span>Allow to Repost</span>
                {!! FormHelper::formToogleCheckbox([
                    'name' => "allow_to_repost",
                    'checkbox_class' => 'allow_to_repost',   
                    'data_attr' => "data-feed=".$feeds->unique_id."",                     
                    'id' => 'radio-{{$feeds->unique_id}}',
                    'checked' => $feeds->allow_to_repost,
                    'value' => 1,
                    ]) 
                !!}
            </div>
        </div>
        <div class="col-md-12 mt-3">
            <button type="submit" class="CdsTYButton-btn-primary">Save</button>
        </div>
    </form>
</div>


<script>
$(document).ready(function () {
    $("#feed-setting-form").submit(function (e) {
        e.preventDefault();
        var is_valid = formValidation("feed-setting-form");
        if (!is_valid) {
            return false;
        }
        var formData = new FormData($(this)[0]);
        var url = $("#feed-setting-form").attr('action');
        $.ajax({
            url: url,
            type: "post",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            beforeSend: function () {
                showLoader();
            },
            success: function (response) {
                hideLoader();
                if (response.status == true) {
                    successMessage(response.message);
                    // location.reload();
                } else {

                    if (response.error_type == 'validation') {
                        validation(response.message);
                    } else {
                        errorMessage(response.message);
                    }

                }
            },
            error: function (xhr) {
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