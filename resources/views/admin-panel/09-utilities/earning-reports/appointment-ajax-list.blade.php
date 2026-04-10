@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell cdsCheckbox">
        <div class="custom-checkbox">
            <input type="checkbox" class="custom-control-input row-checkbox" value="{{ $record->unique_id }}" id="row-{{$key}}" />
            <label class="custom-control-label" for="row-{{$key}}"></label>
        </div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Invoice No">
        {{$record->invoice_number}}
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Client Name">
        <div>
            <strong>Name:</strong> {{$record->appointmentInvoice->client->first_name}}{{$record->appointmentInvoice->client->last_name}}
            <div><strong>Email:</strong>{{$record->appointmentInvoice->client->email}}</div>
        </div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Appointment Id">{{$record->appointmentInvoice->unique_id}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Earning Amount">
        <div>
            <strong>Amount Paid :</strong> {{ $record->total_amount ?? 0 }}
            <div><strong>Platform Fees :</strong> {{ $record->platform_fees_amount ?? 0 }}</div>
            <div><strong>My Earning :</strong> {{ $record->user_earn_amount ?? 0 }}</div>
        </div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Paid Date">{{date('d M Y',strtotime($record->paid_date))}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Crated Date">{{date('d M Y',strtotime($record->created_at))}}</div>
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