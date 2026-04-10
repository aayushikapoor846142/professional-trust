@extends('admin-panel.layouts.app')

@section('content')


<div class="container">
    <h2>Login History</h2>

    <div class="row">
        @foreach ($loginActivities as $ip => $logins)
    <div class="card mb-3">
        <div class="card-body">
            <h4 class="mt-4">{{ $ip ?? 'Unknown IP Address' }}</h4>

            @foreach ($logins as $index => $login)
             @php
    $isMatching = false;
    foreach ($userLocationAccess as $access) {
        if (checkIfLocationMatches($login, $access)) {
            $isMatching = true;
            break;
        }
    }
@endphp
                <div class="card mb-2">
                    <div class="card-body">
                        <h5 class="card-title">Login #{{ $index + 1 }}</h5>
                        <p><strong>Device:</strong> {{ $login->device_name ?? 'N/A' }}</p>
                        <p><strong>Browser:</strong> {{ $login->browser_name ?? 'N/A' }}</p>
                        <p><strong>Location:</strong> {{ $login->city }}, {{ $login->state }}, {{ $login->country }}</p>
                        <p><strong>Time:</strong> {{ $login->created_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                     @if ($isMatching)
                                    <p class="alert alert-success">This device is recognized</p>
                                     @else
                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="button" onclick="confirmAnyAction(this)" data-action="Confirm this Device" data-href="{{ baseUrl('confirm-save-login/'.$login->unique_id) }}" class="CdsTYButton-btn-primary">Yes,This was me</button>
                                                        <a class="CdsTYButton-btn-primary CdsTYButton-border-thick btn-sm"  href="{{ baseUrl('change-password/'.$user->unique_id) }}">
                        No, This was not me
                    </a>
                                          
                                        </div> 
                                        @endif
                                       
                </div>
            @endforeach

        </div>
    </div>
@endforeach
    </div>
</div>


@endsection