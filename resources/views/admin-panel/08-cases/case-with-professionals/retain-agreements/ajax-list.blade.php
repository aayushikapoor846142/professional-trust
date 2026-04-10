@foreach($records as $key => $record)
{{--<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell" data-label="Title">{{$record->title ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Case">{{$record->case->case_title ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Client">{{$record->case->client->first_name??'N/A'}} {{$record->case->client->last_name??''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Service">{{$record->case->subServices->name??'N/A'}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Posted On">{{dateFormat($record->posted_on !=''?$record->posted_on:$record->created_at)}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Is Accepted">{{$record->is_accept == 1?'Accepted':'No Accepted'}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Accepted On">{{$record->accepted_date != '' ? dateFormat($record->accepted_date):''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Additional Note">{{$record->additional_details ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Status">{{$record->status}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Added By">{{$record->userAdded->first_name??'N/A'}} {{$record->userAdded->last_name??''}}</div>

    <div class="cdsTYDashboard-table-cell" data-label="Action">
        <div class="btn-group">
            @if($record->is_accepted != 1)
            <a href="javascript:;" onclick="confirmAnyAction(this)" data-action="Send Reminder" data-href="{{ baseUrl('case-with-professionals/retainers/send-reminder/'.$record->unique_id) }}" class="CdsTYButton-btn-primary btn-sm">Send Reminder</a>
            @endif
        </div>
    </div>  
</div>--}}
<!-- # -->

<div class="cdsTYDashboard-table-row" data-status="{{$record->status}}">
    <div class="cdsTYDashboard-table-cell" data-label="Title">{{$record->title ?? ''}}</div> 
    <div class="cdsTYDashboard-table-cell" data-label="Case">{{$record->case->case_title ?? ''}}</div> 
    <div class="cdsTYDashboard-table-cell" data-label="Client">{{$record->case->client->first_name??'N/A'}} {{$record->case->client->last_name??''}}</div> 
    <div class="cdsTYDashboard-table-cell" data-label="Service">{{$record->case->subServices->name??'N/A'}}</div> 
    <div class="cdsTYDashboard-table-cell" data-label="Date Sent">{{dateFormat($record->posted_on !=''?$record->posted_on:$record->created_at)}}</div> 
    <div class="cdsTYDashboard-table-cell" data-label="Accepted">{{$record->is_accept == 1?'Accepted':'No Accepted'}}</div> 
    <div class="cdsTYDashboard-table-cell" data-label="Status"><span class="CdsCaseRetainer-status-badge CdsCaseRetainer-pending">{{$record->status ?? 'pending'}}</span></div> 
    <div class="cdsTYDashboard-table-cell" data-label="Added by">{{$record->userAdded->first_name??'N/A'}} {{$record->userAdded->last_name??''}}</div> 
        <div class="cdsTYDashboard-table-cell" data-label="Date">{{ dateFormat($record->created_at ?? '', 'd M Y, h:i A') }}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Action">
        @if($record->is_accepted != 1)
            @if(checkPrivilege([
                                'route_prefix' => 'panel.retainers',
                                'module' => 'professional-retainers',
                                'action' => 'reminder'
                            ]))
            <button class="CdsCaseRetainer-action-btn" onclick="confirmAnyAction(this)" data-action="Send Reminder" data-href="{{ baseUrl('case-with-professionals/retainers/send-reminder/'.$record->unique_id) }}">Send Reminder</button></div>
            @endif
            @else
            -
        @endif
    </div> 
</div>  

{{--<div class="CdsCaseRetainer-grid-row" data-status="{{$record->status}}">
    <div class="CdsCaseRetainer-title-cell">{{$record->title ?? ''}}</div>
    <div>{{$record->case->case_title ?? ''}}</div>
    <div>{{$record->case->client->first_name??'N/A'}} {{$record->case->client->last_name??''}}</div>
    <div>{{$record->case->subServices->name??'N/A'}}</div>
    <div>{{dateFormat($record->posted_on !=''?$record->posted_on:$record->created_at)}}</div>
    <div>{{$record->is_accept == 1?'Accepted':'No Accepted'}}</div>
    <div><span class="CdsCaseRetainer-status-badge CdsCaseRetainer-pending">{{$record->status ?? 'pending'}}</span></div>
    <div>{{$record->userAdded->first_name??'N/A'}} {{$record->userAdded->last_name??''}}</div>
    <div>
        @if($record->is_accepted != 1)
            <button class="CdsCaseRetainer-action-btn" onclick="confirmAnyAction(this)" data-action="Send Reminder" data-href="{{ baseUrl('case-with-professionals/retainers/send-reminder/'.$record->unique_id) }}">Send Reminder</button></div>
        @else
            -
        @endif
</div>--}}
                

<!--  -->
@endforeach

 @if(!empty($records) && $current_page > 2 && $current_page < $last_page)
<div class="professional-view-more-link text-center mt-4">
    <a href="javascript:;" onclick="loadData({{ $next_page }})" class="CdsTYButton-btn-primary">
        View More <i class="fa fa-chevron-down"></i>
    </a>
</div>
@endif
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