@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell cdsCheckbox">
        <div class="custom-checkbox">
            <input type="checkbox" class="custom-control-input row-checkbox" value="{{ $record->unique_id }}" id="row-{{$key}}" />
            <label class="custom-control-label" for="row-{{$key}}"></label>
        </div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Name">
        {{$record->invoice_number}}
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Email">
      <div><strong>Name:</strong> {{ $record->caseInvoice->client->first_name }} {{ $record->caseInvoice->client->last_name }}
    <div><strong>Email:</strong> {{ $record->caseInvoice->client->email }}</div>
    </div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Phone no">{{$record->caseInvoice->case_title}} </br>CaseId: {{$record->caseInvoice->unique_id}} </div>

    <div class="cdsTYDashboard-table-cell" data-label="Associate Case">
        @if($record->caseInvoice)
            @if($record->caseInvoice->is_associate_case == 1)
                Yes
            @else
                No
            @endif
        @endif
    </div>
     <div class="cdsTYDashboard-table-cell" data-label="Is Secure">
         <div><strong>Amount Paid :</strong>  {{ $record->total_amount ?? 0 }}
    <div><strong>Platform Fees :</strong> {{ $record->platform_fees_amount ?? 0 }}</div>
      <div><strong>My Earning :</strong> {{ $record->user_earn_amount ?? 0 }}</div>
    </div>
    
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Role">{{date('d M Y',strtotime($record->paid_date))}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Status">{{date('d M Y',strtotime($record->created_at))}}</div>
   
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