@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell" data-label="Name">{{$record['connected_user']['name'] ?? ''}}</div>
     <div class="cdsTYDashboard-table-cell" data-label="Email">{{$record['connected_user']['email'] ?? ''}}</div>
      <div class="cdsTYDashboard-table-cell" data-label="Role">{{$record['connected_user']['role'] ?? ''}}</div>
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