<div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
        <form id="flag-comment-form" enctype="multipart/form-data" action="{{ baseUrl('my-feeds/save-flag-comment') }}"
            method="post">
            <div class="modal-header">
                <h5 class="modal-title">{{$pageTitle}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                @csrf
                <input type="hidden" name="comment_id" value="{{ $feedcomments->unique_id }}">
                <div class="cdsTYDashboard-chat-group-section flex-column">
                    <div class="cdsTYDashboard-chat-group-section-main-panel">
                        <div class="col-md-12">
                            {!! FormHelper::formSelect([
                            'name' => 'comment_flag_id',
                            'label' => 'Flag Type',
                            'class' => 'select-flotlabel',
                            'id' => 'comment_flag_i',
                            'options' => $commentFlags,
                            'value_column' => 'id',
                            'label_column' => 'name',
                            'is_multiple' => false,
                            'required' => true,
                            'selected' => $existingComment->comment_flag_id ?? ''
                            ]) !!}
                        </div>

                        <div class="col-md-12">
                            {!! FormHelper::formTextarea([
                            'name'=>"description",
                            'id'=>"description",
                            "label"=>"Enter Description",
                            'required'=>true,
                            'textarea_class'=>"noval cds-texteditor",
                            'class' => 'select2-input ga-country',
                            'value' => html_entity_decode($existingComment?->description ?? '')

                            ]) !!}
                        </div>
                    </div>
                    <div class="cdsTYDashboard-chat-group-section-sidebar-panel">

                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <div class="form-group text-start">
                    @if(!empty($existingComment))
                    <a href="javascript:;" data-href="{{ baseUrl('my-feeds/remove-flag/' . $existingComment->unique_id) }}" onclick="confirmAnyAction(this)" title="Remove Flag Comment" data-action="Remove Flag Comment" class="text-danger">Remove Flagged Comment</a>
                    @endif
                    <button type="submit" class="btn add-CdsTYButton-btn-primary">Flag Comment</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {

    $("#flag-comment-form").submit(function(e) {
        e.preventDefault();
        var is_valid = formValidation("flag-comment-form");
        if (!is_valid) {
            return false;
        }
        
        var formData = new FormData($(this)[0]);
        var url = $("#flag-comment-form").attr('action');
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
                    closeModal();
                    location.reload();
                } else {
                    validation(response.message);
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