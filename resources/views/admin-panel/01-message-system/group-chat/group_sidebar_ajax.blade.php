@foreach($groupdata as $group)
<a href="{{baseUrl('group/initialize-group-chat/'.$group->id)}}">
<div class="conversation" style="" data-user="Alice" data-unread="true">
    <img src="{{ url('assets/message/images/profile-demo.jpg')}}" alt="Alice">
    < class="info">
        <h3>{{$group->name}}</h3><br>
        <p>Last Message: {{ $group->lastMessage->message ?? 'No messages yet' }}</p><br>
        <p>Time: {{ $group->lastMessage->created_at ?? '-' }}</p>
        <div>
            <span class="unread-message">{{$group->unreadMessage($group->id,auth()->user()->id)}}</span>
        </div>
    </div>
    
</div>
</a>
@endforeach