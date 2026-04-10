@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell" data-label="Name">{{$record->name ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Type">{{$record->form_type}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Action">
        <div class="btn-group">
            <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown"
                data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                More
            </a>
            <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                <li>
                    <a class="dropdown-item" href="{{ baseUrl('manage-services/view-assesment/'.$record->unique_id) }}">
                         View
                    </a>
                </li>
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