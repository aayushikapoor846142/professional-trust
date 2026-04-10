@if($chat!=NULL)
<div class="chat-messages" id="chatMessages{{$chat_id}}">
    <input type="hidden" value="{{baseUrl('message-centre/send-msg/'.$chat_id)}}" id="geturl">
    <input type="hidden" value="" id="edit_message_id">
    <input type="hidden" value="" id="reply_to_id">
    <input type="hidden" value="{{$chat_id}}" id="get_chat_id">
    
    @include('admin-panel.01-message-system.message-centre.components.message-area.header')
    
    @include('admin-panel.01-message-system.message-centre.components.message-area.messages-list')
    
    @include('admin-panel.01-message-system.message-centre.components.message-area.input-area')
</div>
@else
@include('admin-panel.01-message-system.message-centre.blank_chat')
@endif 