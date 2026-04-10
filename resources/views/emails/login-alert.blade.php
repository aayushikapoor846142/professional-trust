@extends('emails.mail-master')

@section('content')

<p>Hi {{ $user->first_name ?? 'N/A'}} {{ $user->last_name ?? 'N/A' }},</p>

<p>We detected a login with the following details:</p>
<p>{{ $loginMessage ?? 'N/A' }}</p>


<ul>
    <li><strong>Alert Type:</strong> {{ $locationType ?? 'N/A' }}</li>
  <li><strong>IP Address:</strong> {{ $currentLogin->ip ?? 'N/A'}}</li>
  <li><strong>Platform:</strong> {{ $currentLogin->platform  ?? 'N/A'}} {{ $currentLogin->platform_version ?? 'N/A' }}</li>
  <li><strong>Browser:</strong> {{ $currentLogin->browser ?? 'N/A'}} {{ $currentLogin->browser_version ?? 'N/A' }}</li>
  <li><strong>Device Type:</strong> {{ $currentLogin->device_type ?? 'N/A' }}</li>
  <li><strong>Device Name:</strong> {{ $currentLogin->device_name ?? 'N/A' }}</li>
  <li><strong>Location:</strong> {{ $currentLogin->city ?? 'N/A' }}, {{ $currentLogin->region ?? 'N/A' }}, {{ $currentLogin->country ?? 'N/A' }}</li>
  <li><strong>ISP/Org:</strong> {{ $currentLogin->org ?? 'N/A' }}</li>
  <li><strong>Time Zone:</strong> {{ $currentLogin->timezone ?? 'N/A' }}</li>
</ul>

<p>If this wasn’t you, please secure your account by changing password.</p>
<a class="small" href="{{ route('password.request') }}">Click Here to Change Password</a>

<p>
      <a href="{{ baseUrl('confirm-login/' . $user->unique_id) }}" style="padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">
        This Was Me
    </a>
</p>

<p>Thanks,<br>{{ siteSetting("company_name") }} Team</p>

@endsection