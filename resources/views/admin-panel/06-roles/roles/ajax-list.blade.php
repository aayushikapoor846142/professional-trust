@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell" data-label="Name">{{$record->name ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Added by">{{$record->user->first_name}} {{$record->user->last_name}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Date">{{ dateFormat($record->created_at ?? '', 'd M Y, h:i A') }}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Action">
   
        <div class="btn-group">
            <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown"
                data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                More
            </a>
            <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
              @if(checkPrivilege([
                'route_prefix' => 'panel.roles',
                'module' => 'professional-roles',
                'action' => 'edit'
            ]))
                <li>
                    <a class="dropdown-item" onclick="openCustomPopup(this)" data-href="{{ baseUrl('roles/edit/'.$record->unique_id) }}" href="javascript:;">
                        <i class="tio-edit"></i> Edit
                    </a>
                </li>
                @endif
                @if(checkPrivilege([
                'route_prefix' => 'panel.roles',
                'module' => 'professional-roles',
                'action' => 'delete'
            ]))

                 <li>
                    <a class="dropdown-item" href="{{ baseUrl('roles/delete/'.$record->unique_id) }}">
                        <i class="tio-edit"></i> Delete
                    </a>
                </li>
                @endif
            </ul>
        </div>
    
     
    </div>
    
</div>
@endforeach
