@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell" data-label="Name">{{$record->associate->first_name ?? ''}}{{$record->associate->last_name ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Date">{{ dateFormat($record->created_at ?? '', 'd M Y, h:i A') }}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Action">
   
        <div class="btn-group">
            <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown"
                data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                More
            </a>
            <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
              @if(checkPrivilege([
                'route_prefix' => 'panel.case-join-requests',
                'module' => 'professional-case-join-requests',
                'action' => 'edit'
            ]))
                <li>
                    <a class="dropdown-item" href="{{ baseUrl('case-join-requests/view/'.$record->unique_id) }}">
                        <i class="tio-edit"></i> View
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
});
</script>