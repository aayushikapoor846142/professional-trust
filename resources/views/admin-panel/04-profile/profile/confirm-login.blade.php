

@section("styles")
<link rel="stylesheet" href="{{ url('assets/css/22-CDS-login-devices.css') }}">
@endsection
@php
    $devicesByIP = $userLocationAccess->groupBy('ip_address');
    $hasUnrecognizedLogins = false;
@endphp

<div class="CDSDashboardActivity-login-container">
    <!-- Page Header -->
    <header class="CDSDashboardActivity-login-page-header">
        <h1 class="CDSDashboardActivity-login-page-title">Login History</h1>
        <p class="CDSDashboardActivity-login-page-subtitle">Review and manage your recent login sessions</p>
    </header>

    {{-- Known Devices --}}
    <div id="loginList">
        @forelse ($devicesByIP as $ip => $devices)
         
                @foreach ($devices as $device)
                    <div class="CDSDashboardActivity-login-login-item card p-3 mb-2" data-id="{{ $device->id }}">
                        <div class="CDSDashboardActivity-login-status-indicator CDSDashboardActivity-login-active">
                            <span class="CDSDashboardActivity-login-status-dot"></span>
                            <span>Active</span>
                        </div>
                <div class="CDSDashboardActivity-login-session-id">
                          {{ $ip ?? 'Unknown IP Address' }}
                        </div>
                      

                        <div class="CDSDashboardActivity-login-login-details">
                            <div class="CDSDashboardActivity-login-detail-item">
                                <span class="CDSDashboardActivity-login-detail-label">Device</span>
                                <span class="CDSDashboardActivity-login-detail-value">{{ $device->device_name ?? 'N/A' }}</span>
                            </div>
                            <div class="CDSDashboardActivity-login-detail-item">
                                <span class="CDSDashboardActivity-login-detail-label">Browser</span>
                                <span class="CDSDashboardActivity-login-detail-value">{{ $device->browser_name ?? 'N/A' }}</span>
                            </div>
                            <div class="CDSDashboardActivity-login-detail-item">
                                <span class="CDSDashboardActivity-login-detail-label">Location</span>
                                <span class="CDSDashboardActivity-login-detail-value">{{ $device->city }}, {{ $device->state }}, {{ $device->country }}</span>
                            </div>
                            <div class="CDSDashboardActivity-login-detail-item">
                                <span class="CDSDashboardActivity-login-detail-label">Time</span>
                                <span class="CDSDashboardActivity-login-detail-value">{{ $device->created_at->format('Y-m-d H:i:s') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
           
        @empty
            <div class="text-muted">No known login devices found.</div>
        @endforelse
    </div>

    {{-- Unknown Devices --}}
    @foreach ($loginActivities as $ip => $logins)
        @php
            $unrecognizedLogins = collect();

            foreach ($logins as $login) {
                $isMatching = false;

                foreach ($userLocationAccess as $access) {
                    if (checkIfLocationMatches($login, $access)) {
                        $isMatching = true;
                        break;
                    }
                }

                if (!$isMatching) {
                    $unrecognizedLogins->push($login);
                }
            }

            if ($unrecognizedLogins->isNotEmpty()) {
                $hasUnrecognizedLogins = true;
            }
        @endphp

        @if ($unrecognizedLogins->isNotEmpty())
      
                <h3 class="mb-3">Recent Unknown Devices</h3>
          

                @foreach ($unrecognizedLogins as $index => $login)
                    <div class="card mb-3 p-3" data-id="{{ $login->id }}">
                        <div class="CDSDashboardActivity-login-status-indicator CDSDashboardActivity-login-suspicious">
                            <span class="CDSDashboardActivity-login-status-dot"></span>
                            <span>Suspicious</span>
                        </div>
                               <div class="CDSDashboardActivity-login-session-id">
                          {{ $ip ?? 'Unknown IP Address' }}
                        </div>

                        <div class="CDSDashboardActivity-login-login-details">
                            <div class="CDSDashboardActivity-login-detail-item">
                                <span class="CDSDashboardActivity-login-detail-label">Device</span>
                                <span class="CDSDashboardActivity-login-detail-value">{{ $login->device_name ?? 'N/A' }}</span>
                            </div>
                            <div class="CDSDashboardActivity-login-detail-item">
                                <span class="CDSDashboardActivity-login-detail-label">Browser</span>
                                <span class="CDSDashboardActivity-login-detail-value">{{ $login->browser_name ?? 'N/A' }}</span>
                            </div>
                            <div class="CDSDashboardActivity-login-detail-item">
                                <span class="CDSDashboardActivity-login-detail-label">Location</span>
                                <span class="CDSDashboardActivity-login-detail-value">{{ $login->city }}, {{ $login->state }}, {{ $login->country }}</span>
                            </div>
                            <div class="CDSDashboardActivity-login-detail-item">
                                <span class="CDSDashboardActivity-login-detail-label">Time</span>
                                <span class="CDSDashboardActivity-login-detail-value">{{ $login->created_at->format('Y-m-d H:i:s') }}</span>
                            </div>
                        </div>

                        <div class="CDSDashboardActivity-login-action-buttons mt-3">
                            <button type="button"
                                    class="CDSDashboardActivity-login-btn CDSDashboardActivity-login-btn-primary"
                                    onclick="confirmAnyAction(this)"
                                    data-action="Confirm this Device"
                                    data-href="{{ baseUrl('confirm-save-login/'.$login->unique_id) }}">
                                Yes, This was me
                            </button>
                            <a href="{{ baseUrl('change-password/'.$user->unique_id) }}"
                               class="CDSDashboardActivity-login-btn CDSDashboardActivity-login-btn-secondary">
                                No, This was not me
                            </a>
                        </div>
                    </div>
                @endforeach
       
        @endif
    @endforeach

    @if (!$hasUnrecognizedLogins)
        <div class="alert alert-success mt-3">No unknown devices found yet.</div>
    @endif
</div>

