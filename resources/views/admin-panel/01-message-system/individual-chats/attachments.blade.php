@foreach($chatAttachments as $getAttachment)
@if($getAttachment!=NULL)
<div class="attachment-files" for="member-list">

@php
$get_attachments=$getAttachment->attachment;
@endphp
@include('admin-panel.01-message-system.individual-chats.attachment-common',['get_attachments'=>$get_attachments,'chat_msg_id'=>$getAttachment->id,'chat_msg_Unique_id' =>$getAttachment->unique_id ])

</div>
@endif
@endforeach