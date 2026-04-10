@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell cdsCheckbox">
        <div class="custom-checkbox">
            <input type="checkbox" class="custom-control-input row-checkbox" value="{{ $record->unique_id }}" id="row-{{$key}}">
            <label class="custom-control-label" for="row-{{$key}}"></label>
        </div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Receiver Name">
        @if($record->sender)
            {{$record->receiver->first_name." ".$record->receiver->last_name}}
        @endif
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Status">
        @if($record->is_accepted==1)
        {{'Accepted'}}
        @elseif($record->is_accepted==2)
        {{'Declined'}}
        @else
        {{'Pending'}}
        @endif
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Action">
        <div class="btn-group">
            <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown"
                data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                More
            </a>
            <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                @if($record->is_accepted==0)
                <li>
                    <a class="dropdown-item" href="{{ baseUrl('accept-chat-request/'.$record->unique_id) }}">
                        Accept
                    </a>
                </li>
                <li>
                    <a class="dropdown-item text-danger" href="javascript:;" onclick="confirmAction(this)"
                        data-href="{{ baseUrl('decline-chat-request/'.$record->unique_id) }}">
                        Decline
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </div>  
</div>

@endforeach
<script type="text/javascript">
$(document).ready(function() {
    $(".row-checkbox").change(function() {
        if ($(".row-checkbox:checked").length > 0) {
            $("#datatableCounterInfo").show();
        } else {
            $("#datatableCounterInfo").show();
        }
        $("#datatableCounter").html($(".row-checkbox:checked").length);
    });
})
</script>