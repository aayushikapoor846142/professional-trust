
<div class="chat-list chat-notification">
    <div class="chat-title">
     
        <div class="group-chat-title">
            <h2>Group Chats</h2>
            <a class="message-upload-file" href="javascript:;"
                onclick="openCustomPopup(this)" data-href="<?php echo baseUrl('group/add-new-group') ?>">
                <i class="fa-solid fa-users-medical"></i>
            </a>
        </div>
    </div>
    
    <div class="tab-content active" id="myGroup" style="display: none;">
        <h2>My groups</h2> <h3>Recent</h3>
        <div class="chat-content">
            <div class="chat-list" id="chatList">
            
                <div class="recent-chats" id="group-conversation-list">
                
                    @include('admin-panel.01-message-system.group-chat.chat.chat_sidebar_ajax')
                </div>
           
            </div>
        </div>  
    </div>
    
    <div class="tab-content" id="otherGroups" style="display: none;">
        <h2>Other Group Content</h2>
        <div class="chat-content">
            <div class="chat-list" id="chatList">
            <h3>Recent</h3>
                <div class="recent-chats" id="other-group-conversation-list">
                   @include('admin-panel.01-message-system.group-chat.chat.other_group_chat_sidebar_ajax') 
                </div>
           
            </div>
        </div>  
    </div>

   
    
</div>

<script>
function showTab(tabId) {
    // Remove 'active' class from all tabs
    document.querySelectorAll('.chat-tab').forEach(tab => {
        tab.classList.remove('active');
    });

    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.style.display = "none"; // Hide all tab contents
    });

    // Show the selected tab content and make the tab active
    document.getElementById(tabId).style.display = "block"; 
    document.querySelector(`.chat-tab[data-tab="${tabId}"]`).classList.add('active');
}
</script>