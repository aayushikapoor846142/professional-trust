@foreach($results as $result)
@if($result['role'] == 'user')
<div class="chat-bubble sent-block">
    <p>{!! $result['message'] !!}</p>
</div>
@else
<div class="chat-bubble receiver-block">
    <p>{!! $result['message'] !!}</p>
</div>
@endif
@endforeach

