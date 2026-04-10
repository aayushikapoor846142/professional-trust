@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell" data-label="Name on Card">{{$record->billing_details->name}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Last 4 Digits">{{$record->card->last4}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Default">{{ ($defaultPaymentMethod->id == $record->id) ? 'Yes' : 'No' }}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Action">
        <div class="btn-group">
            <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown"
                data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                More
            </a>
            <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                <li>
					<a class="dropdown-item text-danger" href="javascript:;" onclick="confirmPaymentMethod(this)"
					data-href="{{ baseUrl('my-membership-plans/default/'.$record->id) }}">
					     Make Default
				    </a>
                </li>
                <li>
                    <a class="dropdown-item text-danger" href="javascript:;" onclick="removePaymentMethod(this)"
                        data-href="{{ baseUrl('my-membership-plans/delete/'.$record->id) }}">
                         Remove
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