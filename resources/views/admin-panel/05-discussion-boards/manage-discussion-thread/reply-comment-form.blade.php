<div class="CDSDiscussion-reply-form" id="reply-form-comment-{{ $parent_comment->unique_id}}">
    <form id="CDSDiscussion-reply-comment-form-{{ $parent_comment->unique_id }}" action="{{ baseUrl('manage-discussion-threads/save-comment/'.$parent_comment->discussion->id) }}">
        @csrf
        <input type="hidden" name="comment_type" value="reply" />
        <input type="hidden" name="reply_to" value="{{ $parent_comment->unique_id }}" />
        <div class="CDSDiscussion-reply-input-wrapper">
            <div class="CDSDiscussion-comment-avatar CDSDiscussion-reply-avatar">
                {!! getProfileImage(auth()->user()->unique_id) !!}
            </div>
            <div class="CDSDiscussion-reply-input-container">
                <textarea class="CDSDiscussion-reply-input" id="comment-field-{{ $parent_comment->unique_id }}" name="comment" placeholder="Write a reply..."></textarea>
                <button type="button" class="CdsDiscussionThread-comment-toolbar CdsDiscussionThread-emoji-btn CDSDiscussion-emoji-btn-small message-emoji-icon-{{ $parent_comment->unique_id }}" title="Add emoji">
                    <i class="fa-regular fa-face-smile" aria-hidden="true"></i>
                </button>
            </div>
        </div>
        <div class="CDSDiscussion-reply-actions">
            <button type="button" class="CDSDiscussion-btn CDSDiscussion-btn-sm CdsTYButton-btn-secondary" onclick="toggleReplyForm('{{ $parent_comment->unique_id }}','close')">Cancel</button>
            <button type="submit" class="CdsTYButton-btn-primary CdsTYButton-border-thick CdsTYButton-size-sm">Reply</button>
        </div>
    </form>
</div>

<script>
setTimeout(() => {
    // new EmojiPicker(".message-emoji-icon-{{ $parent_comment->unique_id }}", {
    //     targetElement: "#comment-field-{{ $parent_comment->unique_id }}"
    // });
    // Initialize EmojiPicker for each comment (dynamic ID)
new EmojiPicker(`.message-emoji-icon-{{ $parent_comment->unique_id }}`, {
    // No targetElement (handle insertion manually)
    onEmojiSelect: function(emoji) {
        // Dynamically pass the correct textarea ID
        insertAtCursor(emoji, `comment-field-{{ $parent_comment->unique_id }}`);
    }
});


}, 500);
// Universal function to insert emoji at cursor (works with dynamic IDs)
function insertAtCursor(emoji, textareaId) {
    const textarea = document.getElementById(textareaId);
    if (!textarea) {
        console.error("Textarea not found:", textareaId);
        return;
    }

    const startPos = textarea.selectionStart;
    const endPos = textarea.selectionEnd;
    
    // Insert emoji at cursor position
    textarea.value = 
        textarea.value.substring(0, startPos) + 
        emoji + 
        textarea.value.substring(endPos);
    
    // Move cursor after the inserted emoji
    const newCursorPos = startPos + emoji.length;
    textarea.selectionStart = newCursorPos;
    textarea.selectionEnd = newCursorPos;
    
    // Restore focus
    textarea.focus();
}
document.getElementById('CDSDiscussion-reply-comment-form-{{ $parent_comment->unique_id }}').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    try {
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
                    toggleReplyForm('{{ $parent_comment->unique_id }}','close')
                    location.reload();
                } else {
                    hideLoader();
                    errorMessage(response.message);
                }
            },
            error: function() {
                $(".CDSFeed-btn").removeAttr("disabled");
                hideLoader();
                internalError();
            }
        });
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
});
</script>