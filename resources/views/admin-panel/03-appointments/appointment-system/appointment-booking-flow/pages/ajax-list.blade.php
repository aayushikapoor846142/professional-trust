@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell cdsCheckbox">
        <div class="custom-checkbox">
            {!! FormHelper::formCheckbox([
                'value' => $record->unique_id,
                'data-id' => $record->unique_id,
                'checkbox_class' => 'custom-control-input row-checkbox case-checkbox',
                'id' => 'row-' . $key
            ]) !!}
        </div>
    </div>

    
    <div class="cdsTYDashboard-table-cell" data-label="Title">{{$record->title ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Time Duration">{{optional($record->timeDuration)->name ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Appointment Type">{{optional($record->appointmentType)->name ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Working Hours">{{optional($record->workingHours)->day ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Timezone">{{optional($record->location)->timezone ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Appointment Mode">{{$record->appointment_mode ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Status">{{$record->status ?? ''}}</div>
       <div class="cdsTYDashboard-table-cell" data-label="Date">{{ dateFormat($record->created_at ?? '', 'd M Y, h:i A') }}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Action">
        @if(checkPrivilege([
            'route_prefix' => 'panel.appointment-booking-flow',
            'module' => 'professional-appointment-booking-flow',
            'action' => 'edit'
        ]) || checkPrivilege([
            'route_prefix' => 'panel.appointment-booking-flow',
            'module' => 'professional-appointment-booking-flow',
            'action' => 'delete'
        ]))
            <div class="btn-group">
                <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown"
                    data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                    More
                </a>    
                <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                    @if(checkPrivilege([
                        'route_prefix' => 'panel.appointment-booking-flow',
                        'module' => 'professional-appointment-booking-flow',
                        'action' => 'edit'
                    ]))
                    @if((($record->status == "draft" || $record->status == "awaiting" ) || ($record->payment_status != "paid") || ($record->appointment_date >= date('Y-m-d'))) && ($record->status!="cancelled") )
                    <li>
                        <a class="dropdown-item" href="{{ baseUrl('appointments/appointment-booking-flow/add/'.$record->unique_id) }}">
                            Edit
                        </a>
                    </li>  
                    @endif  
                    @endif   
                    @if(checkPrivilege([
                        'route_prefix' => 'panel.appointment-booking-flow',
                        'module' => 'professional-appointment-booking-flow',
                        'action' => 'delete'
                    ]))        
                    <li>
                        <a class="dropdown-item text-danger" href="javascript:;" onclick="confirmAction(this)"
                            data-href="{{ baseUrl('appointments/appointment-booking-flow/delete/'.$record->unique_id) }}">
                            Delete
                        </a>
                    </li>   
                    @endif             
                </ul>
            </div>
        @endif
    </div>  
</div>

@endforeach
