@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell cdsCheckbox">
        @if($record->status!=1)
        <div class="custom-checkbox">
            <input type="checkbox" class="custom-control-input row-checkbox" value="{{ $record->unique_id }}" id="row-{{$key}}">
            <label class="custom-control-label" for="row-{{$key}}"></label>
        </div>
        @endif
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Email">{{$record->email ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Status">
        @if($record->status==0)
        <span class="cds-status-badge status-pending }}">
            {{ 'Pending' }}
        </span>
        @else
        <span class="cds-status-badge status-accepted }}">
        {{'Accepted'}}
        </span>
        @endif
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Added By">{{$record->addedBy->first_name ?? ''}} {{$record->addedBy->last_name ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Date">{{ dateFormat($record->created_at ?? '', 'd M Y, h:i A') }}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Action">
        @if(checkPrivilege([
            'route_prefix' => 'panel.chat-invitations',
            'module' => 'professional-chat-invitations',
            'action' => 'delete'
        ]))
        <div class="btn-group">
            <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown"
                data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                More
            </a>
            <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                @if($record->status!=1)
                <li>
                    <a class="dropdown-item text-danger" href="javascript:;" onclick="confirmAction(this)"
                        data-href="{{ baseUrl('connections/invitations/delete/'.$record->unique_id) }}">
                        Delete
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @else
         - 
        @endif
    </div>  
</div>

@endforeach
