@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell cdsCheckbox">
        <div class="custom-checkbox">
            <input type="checkbox" class="custom-control-input row-checkbox" value="{{ $record->unique_id }}" id="row-{{$key}}">
            <label class="custom-control-label" for="row-{{$key}}"></label>
        </div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Quotation Title">{{ $record->quotation_title }}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Service">{{$record->service->name??''}}</div>    
    <div class="cdsTYDashboard-table-cell" data-label="Action">
        <div class="btn-group">
            <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown"
                data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                More
            </a>
            <ul class="dropdown-menu" aria-labelledby="defaultDropdown">                
                <li>
                    <a class="dropdown-item" href="{{ baseUrl('quotations/edit/'.$record->unique_id) }}">
                        <i class="tio-edit"></i> Edit
                    </a>
                </li>               
                <li>
                    <a class="dropdown-item text-danger" href="javascript:;" onclick="confirmAction(this)"
                        data-href="{{ baseUrl('quotations/delete/'.$record->unique_id) }}">
                         Delete
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