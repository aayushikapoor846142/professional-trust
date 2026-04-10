@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell cdsCheckbox">
        <div class="custom-checkbox">
            <input type="checkbox" class="custom-control-input row-checkbox" value="{{ $record->unique_id }}" id="row-{{$key}}">
            <label class="custom-control-label" for="row-{{$key}}"></label>
        </div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Name">{{$record->leave_date ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Break time">{{optional($record->location)->full_address ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Type">{{$record->reason ?? ''}}</div>
     <div class="cdsTYDashboard-table-cell" data-label="addedBy">{{$record->professional->first_name ?? ''}} {{$record->professional->last_name ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Action">
       
        <div class="btn-group">
            <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown"
                data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                More
            </a>
            <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                
                  @if(checkPrivilege([
                        'route_prefix' => 'panel.appointments.block-dates',
                        'module' => 'professional-appointments-block-dates',
                        'action' => 'edit'
                    ]))   
              
                <li>
                    <a class="dropdown-item" href="{{ baseUrl('appointments/block-dates/edit/'.$record->unique_id) }}">
                        Edit
                    </a>
                </li>
                @endif   
                  @if(checkPrivilege([
                        'route_prefix' => 'panel.appointments.block-dates',
                        'module' => 'professional-appointments-block-dates',
                        'action' => 'delete'
                    ]))             
                <li>
                    <a class="dropdown-item text-danger" href="javascript:;" onclick="confirmAction(this)"
                        data-href="{{ baseUrl('appointments/block-dates/delete/'.$record->unique_id) }}">
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