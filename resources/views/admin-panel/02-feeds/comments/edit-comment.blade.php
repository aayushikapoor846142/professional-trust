<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{$pageTitle}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="CDSFeed-edit-comment-form">
                <form id="popup-form" action="{{ baseUrl('my-feeds/update-comment/'.$record->unique_id) }}">
                    @csrf
                    <div class="CDSFeed-reply-input-wrapper">
                        <div class="CDSFeed-comment-avatar CDSFeed-reply-avatar">
                            {!! getProfileImage(auth()->user()->unique_id) !!}
                        </div>
                        <div class="CDSFeed-reply-input-container">
                            <textarea class="CDSFeed-reply-input" id="comment-field-{{ $record->unique_id }}" name="comment" placeholder="Write a comment...">{{ $record->comment }}</textarea>
                            <button type="button" class="CDSFeed-emoji-btn CDSFeed-emoji-btn-small message-emoji-icon-{{ $record->unique_id }}" title="Add emoji">
                                <i class="fa-regular fa-face-smile" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                    <div class="CDSFeed-reply-actions">
                        <button type="submit" class="CDSFeed-btn CDSFeed-btn-sm CDSFeed-btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
    setTimeout(() => {
        new EmojiPicker(".message-emoji-icon-{{ $record->unique_id }}", {
            targetElement: "#comment-field-{{ $record->unique_id }}"
        });
    }, 500);
    $("#popup-form").submit(function(e) {
        e.preventDefault();
        var is_valid = formValidation("popup-form");
        if(!is_valid){
            return false;
        }
        var formData = new FormData($(this)[0]);
        var url = $("#popup-form").attr('action');
        $.ajax({
            url: $(this).attr("action"),
            type: "post",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            beforeSend: function() {
                showLoader();
                $(".CDSFeed-btn").attr("disabled","disabled");
            },
            
            success: function(response) {
                
                $(".CDSFeed-btn").removeAttr("disabled");
                if (response.status == true) {
                    hideLoader();
                    successMessage(response.message);
                    closeModal();
                } else {
                    hideLoader();
                    validation(response.message);
                }
            },
            error: function() {
                hideLoader();
                internalError();
            }
        });
    });
});

</script>