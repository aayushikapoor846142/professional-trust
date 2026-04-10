@extends('emails.mail-master')

@section('content')
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 30px;">
    <div style="max-width: 600px; margin: auto; background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="color: #333;">Hello {{ $user->first_name ?? 'User' }},</h2>

        <p>Thank you for using {{ siteSetting('company_name') }}.</p>

        <p><strong>Your document password is for case {{ $caseTitle ?? '' }}:</strong></p>
        <div style="background-color: #f1f1f1; padding: 10px 15px; font-size: 16px; border-radius: 4px; margin-bottom: 20px;">
            <code>{{ $password }}</code>
        </div>

        <p>Please use this password to open your encrypted document(s).</p>

        <p>If you did not request this, please ignore this message or contact support.</p>

        <p>Best regards, <br>{{ siteSetting('company_name') }} Team</p>
    </div>
</body>
@endsection