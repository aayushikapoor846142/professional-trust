@foreach($records as $key => $record)
    @php
        $joiningLinkAdded = true;
        if ($record->payment_status == 'paid' &&
            $record->appointment_mode == 'online' &&
            $record->status == 'approved' && 
            Carbon\Carbon::parse($record->appointment_date . ' ' . $record->start_time_converted)->isFuture()) {
            $joiningLinkAdded = false;
        }
        
        // Generate initials for avatar
        $clientName = optional($record->client)->first_name . ' ' . optional($record->client)->last_name;
        $initials = collect(explode(' ', $clientName))
            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
            ->take(2)
            ->implode('');
        
        // Avatar colors
        $avatarColors = ['#5865F2', '#F26B38', '#14B8A6', '#7C3AED', '#EC4899', '#F59E0B'];
        $avatarColor = $avatarColors[$key % count($avatarColors)];
        
        // Status mapping
        $statusClasses = [
            'approved' => 'CdsDashboardAppointment-system-status-confirmed',
            'awaiting' => 'CdsDashboardAppointment-system-status-pending',
            'completed' => 'CdsDashboardAppointment-system-status-completed',
            'cancelled' => 'CdsDashboardAppointment-system-status-cancelled',
            'draft' => 'CdsDashboardAppointment-system-status-pending',
        ];
        $statusClass = $statusClasses[$record->status] ?? 'CdsDashboardAppointment-system-status-pending';
    @endphp

    <div class="cdsTYDashboard-table-row">
        <div class="cdsTYDashboard-table-cell" data-label="Client">
            <div class=" d-block d-md-flex">
                <div class="CdsSendInvitation-client-avatar">
                    @if(optional($record->client)->profile_image)
                        <img src="{{ userDirUrl(optional($record->client)->profile_image, 't') }}" 
                                class="CdsDashboardAppointment-system-doctor-avatar" 
                                alt="{{ $clientName }}">
                        @else
                        <div class="CdsDashboardAppointment-system-doctor-avatar" style="background: {{ $avatarColor }};">
                            {{ $initials ?: 'NA' }}
                        </div>
                    @endif
                </div>
                <div class="CdsSendInvitation-client-info ms-2">
                    <div class="CdsSendInvitation-client-email">{{ $clientName ?: 'NA' }}</div>
                    <div class="CdsSendInvitation-client-meta">ID: {{ $record->unique_id }}</div>
                </div>
            </div>
        </div>
        <div class="cdsTYDashboard-table-cell" data-label="Service">{{ optional($record->service)->name ?: 'NA' }}</div>
        <div class="cdsTYDashboard-table-cell" data-label="Date & time">
            <div class="CdsDashboardAppointment-system-date">
                {{ $record->appointment_date ? dateFormat($record->appointment_date) : 'NA' }}
            </div>
            <div class="CdsDashboardAppointment-system-duration">
                {{ $record->start_time_converted && $record->end_time_converted 
                    ? $record->start_time_converted . ' - ' . $record->end_time_converted 
                    : 'NA' }}
            </div>
        </div>
        <div class="cdsTYDashboard-table-cell" data-label="Duration">{{ $record->meeting_duration ? $record->meeting_duration . ' min' : 'NA' }}</div>
        <div class="cdsTYDashboard-table-cell" data-label="Status">
            <div class="CdsDashboardAppointment-system-status {{ $statusClass }}">
                <span class="CdsDashboardAppointment-system-status-dot"></span>
                {{ ucfirst($record->status) }}
            </div>
        </div>
        <div class="cdsTYDashboard-table-cell" data-label="Payment">
            <div class="CdsDashboardAppointment-system-payment">
                <div class="CdsDashboardAppointment-system-payment-amount">
                    {{ $record->price ? currencySymbol($record->currency) . ' ' . $record->price : 'Free' }}
                </div>
                <div class="CdsDashboardAppointment-system-payment-status 
                    {{ $record->payment_status == 'paid' ? 'CdsDashboardAppointment-system-payment-paid' : 'CdsDashboardAppointment-system-payment-pending' }}">
                    {{ $record->payment_status == 'paid' ? '✓ Paid' : '⏳ Pending' }}
                </div>
            </div>
        </div>
        <div class="cdsTYDashboard-table-cell" data-label="Actions">
            <div class="btn-group">
                <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </a>
                <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                    @if(checkPrivilege([
                        'route_prefix' => 'panel.appointment-booking',
                        'module' => 'professional-appointment-booking',
                        'action' => 'view'
                    ]))
                        <li>
                            <a href="{{ baseUrl('appointments/appointment-booking/view/'.$record->unique_id) }}" 
                                class="dropdown-item" 
                                title="View">👁️ View</a>
                        </li>
                    @endif
                    @if((($record->status == "draft" || $record->status == "awaiting") || 
                        ($record->payment_status != "paid") || 
                        ($record->appointment_date > now())) && 
                        ($record->status != "cancelled") && 
                        ($record->status != "approved"))
                        @if(checkPrivilege([
                            'route_prefix' => 'panel.appointment-booking',
                            'module' => 'professional-appointment-booking',
                            'action' => 'edit'
                        ]))
                            <li>
                                <a href="{{ baseUrl('appointments/appointment-booking/save-booking/'.$record->unique_id) }}" 
                                class="dropdown-item" 
                                title="Edit">✏️ Edit</a>
                            </li>
                        @endif
                    @endif
                    @if(!$joiningLinkAdded)
                        @if(checkPrivilege([
                            'route_prefix' => 'panel.appointment-booking',
                            'module' => 'professional-appointment-booking',
                            'action' => 'add-meeting-link'
                        ]))
                            <li>
                                @if($record->payment_status != 'paid')
                                <a href="javascript:;" onclick="showPopup('{{ baseUrl('appointments/appointment-booking/add-joining-link/' . $record->unique_id) }}')" 
                                    class="CdsDashboardAppointment-system-action-btn CdsDashboardAppointment-system-action-reminder" 
                                    title="Add Meeting Link">
                                    🔗
                                </a>
                                @endif
                            </li>
                        @endif
                    @endif
                    @if($record->status == "approved" || $record->status == "archieved")
                        @if(checkPrivilege([
                            'route_prefix' => 'panel.appointment-booking',
                            'module' => 'professional-appointment-booking',
                            'action' => 'appointment-booking.markStatus'
                        ]))
                            <li>
                                <a href="{{ route('panel.appointment-booking.markStatus', ['uid' => $record->unique_id, 'status' => 'completed']) }}" 
                                class="dropdown-item" 
                                title="Mark Complete">✅ Complete</a>
                            </li>
                        @endif
                    @endif
                    @if(checkPrivilege([
                        'route_prefix' => 'panel.appointment-booking',
                        'module' => 'professional-appointment-booking',
                        'action' => 'delete'
                    ]))
                        <li>
                            <a href="javascript:;" onclick="confirmAction(this)" 
                                data-href="{{ baseUrl('appointments/appointment-booking/delete/'.$record->unique_id) }}"
                                class="dropdown-item" 
                                title="Delete">
                                ❌ Delete
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
@endforeach
