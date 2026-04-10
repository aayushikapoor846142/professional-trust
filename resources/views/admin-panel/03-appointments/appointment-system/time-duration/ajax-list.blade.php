@foreach($records as $key => $record)
<div class="CdsTYDashboardAppointment-settings-glass-list-item">
    <input type="checkbox" class="CdsTYDashboardAppointment-settings-glass-checkbox row-checkbox" value="{{ $record->unique_id }}" id="row-{{$key}}">
    <div class="CdsTYDashboardAppointment-settings-item-content">
        <h4 class="CdsTYDashboardAppointment-settings-item-title">{{$record->name ?? ''}}</h4>
        <div class="CdsTYDashboardAppointment-settings-item-meta">
            <span class="CdsTYDashboardAppointment-settings-meta-item">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M8 4V8L10.5 10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                Break: {{$record->break_time ?? '0'}} min
            </span>
            <span class="CdsTYDashboardAppointment-settings-meta-item">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <rect x="3" y="3" width="10" height="10" rx="1" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M3 7H13M7 3V13" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                Type: {{ucfirst($record->type ?? '')}}
            </span>
            <span class="CdsTYDashboardAppointment-settings-meta-item">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M8 1.5V8H14.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                Duration: {{$record->duration ?? '0'}}
            </span>
        </div>
    </div>
    
    @if(checkPrivilege([
        'route_prefix' => 'panel.time-duration',
        'module' => 'professional-time-duration',
        'action' => 'edit'
    ]) || checkPrivilege([
        'route_prefix' => 'panel.time-duration',
        'module' => 'professional-time-duration',
        'action' => 'delete'
    ]))
        <div class="dropdown">
            <button class="CdsTYDashboardAppointment-settings-actions-btn" type="button" id="dropdownMenuButton{{$key}}" data-bs-toggle="dropdown" aria-expanded="false">
                ⋯
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{$key}}">
                @if(checkPrivilege([
                    'route_prefix' => 'panel.time-duration',
                    'module' => 'professional-time-duration',
                    'action' => 'edit'
                ]))
                    <li>
                        <a class="dropdown-item" onclick="openCustomPopup(this)" href="javascript:;" data-href="{{ baseUrl('time-duration/edit/'.$record->unique_id) }}">
                            <i class="fa fa-edit me-2"></i>Edit
                        </a>
                    </li>  
                @endif
                @if(checkPrivilege([
                    'route_prefix' => 'panel.time-duration',
                    'module' => 'professional-time-duration',
                    'action' => 'delete'
                ]))             
                    <li>
                        <a class="dropdown-item text-danger" href="javascript:;" onclick="confirmAction(this)" data-href="{{ baseUrl('time-duration/delete/'.$record->unique_id) }}">
                            <i class="fa fa-trash me-2"></i>Delete
                        </a>
                    </li>
                @endif                
            </ul>
        </div>
    @else
        <span class="CdsTYDashboardAppointment-settings-actions-btn" style="cursor: default;">-</span>
    @endif
</div>
@endforeach

@if($records->count() == 0)
    <div class="text-center text-danger py-4">No records available</div>
@endif