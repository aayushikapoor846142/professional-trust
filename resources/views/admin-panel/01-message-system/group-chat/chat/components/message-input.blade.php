@props(['groupId'])

<div class="message-input-container">
    <form id="message-form" class="message-form">
        @csrf
        <input type="hidden" name="group_id" value="{{ $groupId }}">
        <input type="hidden" name="reply_to" id="reply_to_id">
        
        <div class="input-wrapper">
            <div class="emoji-picker-wrapper">
                <button type="button" class="emoji-btn" id="emoji-btn">
                    <i class="fa-regular fa-face-smile"></i>
                </button>
            </div>
            
            <div class="text-input-wrapper">
                <textarea 
                    id="sendmsgg" 
                    name="message" 
                    class="message-textarea" 
                    placeholder="Type your message..."
                    rows="1"></textarea>
            </div>
            
            <div class="action-buttons">
                <button type="button" class="file-upload-btn" id="file-upload-btn">
                    <i class="fa-solid fa-paperclip"></i>
                </button>
                
                <button type="submit" class="send-btn" id="send-message-btn">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
        </div>
        
        @include('admin-panel.01-message-system.group-chat.chat.components.file-upload')
    </form>
    
    @include('admin-panel.01-message-system.group-chat.chat.components.typing-indicator')
</div> 