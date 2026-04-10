@foreach($records as $key => $record)
<div class="CdsTYDashboardAppointment-settings-glass-list-item">
    <input type="checkbox" class="CdsTYDashboardAppointment-settings-glass-checkbox row-checkbox" value="{{ $record->unique_id }}" id="row-{{$key}}">
    <div class="CdsTYDashboardAppointment-settings-item-content">
        <h4 class="CdsTYDashboardAppointment-settings-item-title">{{$record->name ?? ''}}</h4>
        <div class="CdsTYDashboardAppointment-settings-item-meta">
            <span class="CdsTYDashboardAppointment-settings-meta-item">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M8 1.5C11.866 1.5 15 4.634 15 8.5C15 12.366 11.866 15.5 8 15.5C4.134 15.5 1 12.366 1 8.5C1 4.634 4.134 1.5 8 1.5Z" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M8 5.5V8.5L10.5 10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                Duration: {{optional($record->timeDuration)->name ?? 'Not Set'}}
            </span>
        </div>
    </div>
    
    @if(checkPrivilege([
        'route_prefix' => 'panel.appointment-types',
        'module' => 'professional-appointment-types',
        'action' => 'edit'
    ]) || checkPrivilege([
        'route_prefix' => 'panel.appointment-types',
        'module' => 'professional-appointment-types',
        'action' => 'delete'
    ]))
        <div class="dropdown">
            <button class="CdsTYDashboardAppointment-settings-actions-btn" type="button" id="dropdownMenuButton{{$key}}" data-bs-toggle="dropdown" aria-expanded="false">
                ⋯
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{$key}}">
                @if(checkPrivilege([
                    'route_prefix' => 'panel.appointment-types',
                    'module' => 'professional-appointment-types',
                    'action' => 'edit'
                ]))
                    <li>
                        <a class="dropdown-item" onclick="openCustomPopup(this)" href="javascript:;" data-href="{{ baseUrl('appointment-types/edit/'.$record->unique_id) }}">
                            <i class="fa fa-edit me-2"></i>Edit
                        </a>
                    </li> 
                @endif
                @if(checkPrivilege([
                    'route_prefix' => 'panel.appointment-types',
                    'module' => 'professional-appointment-types',
                    'action' => 'delete'
                ]))              
                    <li>
                        <a class="dropdown-item text-danger" href="javascript:;" onclick="confirmAction(this)" data-href="{{ baseUrl('appointment-types/delete/'.$record->unique_id) }}">
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