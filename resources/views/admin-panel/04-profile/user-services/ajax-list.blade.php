@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell" data-label="Service Name">{{$record->ImmigrationServices->name ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Payment Added">{{ $record->price ? 'Yes' : 'No' }}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Created At">{{$record->created_at ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Action">
        <div class="btn-group">
            <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown"
                data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                More
            </a>
            <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                @if(checkPrivilege('my-services','manage-price'))
                <li>
                    <a class="dropdown-item" href="{{ baseUrl('my-services/manage-price/'.$record->unique_id) }}">
                        Manage Price
                    </a>
                </li>
                @endif
                @if(checkPrivilege('my-services','delete'))
                <li>
                    <a class="dropdown-item text-danger" href="javascript:;" onclick="confirmAction(this)"
                        data-href="{{ baseUrl('my-services/delete/'.$record->unique_id) }}">
                        Delete
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