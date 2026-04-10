@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell cdsCheckbox">
        <div class="custom-checkbox">
            <input type="checkbox" class="custom-control-input row-checkbox" value="{{ $record->unique_id }}" id="row-{{$key}}">
            <label class="custom-control-label" for="row-{{$key}}"></label>
        </div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Feature Name">
        <div>
            <strong>{{ ucfirst(str_replace('_', ' ', $record->feature_key)) }}</strong>
            <div class="text-muted small">{{ ucfirst($record->module_name) }}</div>
        </div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Limit">
        <span class="badge {{ $record->plan_limit == -1 ? 'bg-success' : 'bg-info' }}">
            {{ $record->plan_limit == -1 ? 'Unlimited' : $record->plan_limit }}
        </span>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Usage">
        <span class="badge {{ $record->total_usage >= $record->plan_limit && $record->plan_limit != -1 ? 'bg-danger' : 'bg-success' }}">
            {{ $record->total_usage }}
        </span>
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

function viewDetails(uniqueId) {
    // You can implement a modal or redirect to show detailed information
    alert('View details for record: ' + uniqueId);
    // Example: window.open('/admin/user-plan-feature/details/' + uniqueId, '_blank');
}

function viewUserDetails(userId) {
    // You can implement a modal or redirect to show user information
    alert('View user details for user ID: ' + userId);
    // Example: window.open('/admin/users/details/' + userId, '_blank');
}
</script>