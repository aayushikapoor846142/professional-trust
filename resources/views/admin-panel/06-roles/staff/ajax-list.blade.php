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
        <div class="btn-group">
            <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown"
                data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                More
            </a>
            <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                @if(checkPrivilege([
            'route_prefix' => 'panel.staff',
             'module' => 'professional-staff',
            'action' => 'edit'
         ]))
                <li>
                    <a class="dropdown-item" href="{{ baseUrl('staff/edit/'.$record->unique_id) }}">
                        Edit
                    </a>
                </li>
                @endif
                @if(checkPrivilege([
                    'route_prefix' => 'panel.staff',
                    'module' => 'professional-staff',
                    'action' => 'chat-invitations'
                ]))
                <li>
                    <a class="dropdown-item" href="javascript:;" onclick="sendInvitation('{{ $record->email }}')">
                        Send Chat Invitation
                    </a>
                </li>
                @endif
                @if(checkPrivilege([
                    'route_prefix' => 'panel.staff',
                    'module' => 'professional-staff',
                    'action' => 'changepassword'
                ]))
                <li>
                    <a class="dropdown-item" href="{{ baseUrl('staff/change-password/'.$record->unique_id) }}">
                        Change Password
                    </a>
                </li>
                @endif
                @if(checkPrivilege([
                    'route_prefix' => 'panel.staff',
                    'module' => 'professional-staff',
                    'action' => 'delete'
                ]))
                <li>
                    <a class="dropdown-item text-danger" href="javascript:;" onclick="confirmAction(this)"
                        data-href="{{ baseUrl('staff/delete/'.$record->unique_id) }}">
                        Delete
                    </a>
                </li>   
                @endif     
            </ul>
        </div>
    </div>  
</div>

{{--<tr>
    <td class="table-column-pr-0">
        <div class="custom-control custom-checkbox text-md-center">
            <input type="checkbox" class="custom-control-input row-checkbox" value="{{ $record->unique_id }}"
                id="row-{{$key}}">
            <label class="custom-control-label" for="row-{{$key}}"></label>
        </div>
    </td>
    <td class="table-column-pl-0" data-title="Name">
        <span class="d-block d-sm-flex align-items-center">
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
    </td>
    <td data-title="Email">
        <div class="d-flex">
            {{$record->email}}
        </div>
    </td>

    <td data-title="Phone no">
        <div class="d-flex">
            {{$record->country_code}} {{$record->phone_no}}
        </div>
    </td>


    <td data-title="Role">
        <div class="d-flex">
            {{$record->role}}
        </div>
    </td>

    <td data-title="Status">
        {{$record->status}}
    </td>

    <td data-title="Action">
        <div class="btn-group">
            <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown"
                data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                More
            </a>
            <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                <li>
                    <a class="dropdown-item" href="{{ baseUrl('staff/edit/'.$record->unique_id) }}">
                        <i class="tio-edit"></i> Edit
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="javascript:;" onclick="sendInvitation('{{ $record->email }}')">
                         Send Chat Invitation
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ baseUrl('staff/change-password/'.$record->unique_id) }}">
                        <i class="tio-password"></i> Change Password
                    </a>
                </li>
                <li>
                    <a class="dropdown-item text-danger" href="javascript:;" onclick="confirmAction(this)"
                        data-href="{{ baseUrl('staff/delete/'.$record->unique_id) }}">
                         Delete
                    </a>
                </li>
        
            </ul>
        </div>
    </td>

</tr>--}}
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