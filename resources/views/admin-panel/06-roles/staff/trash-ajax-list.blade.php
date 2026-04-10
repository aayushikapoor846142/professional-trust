@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell cdsCheckbox">
        <div class="custom-checkbox">
            <input type="checkbox" class="custom-control-input row-checkbox" value="{{ $record->unique_id }}" id="row-{{$key}}">
            <label class="custom-control-label" for="row-{{$key}}"></label>
        </div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Name">
        <span class="d-block">
            @if($record->profile_image != '' && file_exists(professionalDir().'/profile/'.$record->profile_image))
            <img class="circular-img avatar avatar-soft-primary avatar-circle me-3" src="{{ professionalProfile($record->unique_id,'t')}}" alt="Profile Image">
            @else
            <div class="avatar avatar-soft-primary avatar-circle me-3">
                <span class="avatar-initials">{{ userInitial($record) }}</span>
            </div>
            @endif
            <div class="ml-3 mt-2 mt-sm-0">
                <span class="d-block h6 text-hover-primary mb-0">{{$record->first_name." ".$record->last_name}}</span>
                <span class="d-block font14 text-body">Created on {{ dateFormat($record->created_at) }}</span>
            </div>
        </span>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Email">{{$record->email}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Phone no">{{$record->country_code}} {{$record->phone_no}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Role">{{$record->role}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Status">{{$record->status}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Date">{{ dateFormat($record->created_at ?? '', 'd M Y, h:i A') }}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Action">
           @if(checkPrivilege([
                    'route_prefix' => 'panel.staff',
                    'module' => 'professional-staff',
                    'action' => 'restore'
                ]))
        <div class="btn-group">
            <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown"
                data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                More
            </a>
            <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                <li>
                    <a class="dropdown-item text-danger" href="javascript:;" title="Restore Staff User" data-action="Restore Staff User" onclick="confirmAnyAction(this)"
                        data-href="{{ baseUrl('staff/restore/'.$record->unique_id) }}">
                         Restore
                    </a>
                </li>
            </ul>
        </div>
        @else
         - 
        @endif
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