@extends('emails.mail-master')
@section('content')

<table class="container_400" align="center" width="400" border="0" cellspacing="0" cellpadding="10" style="margin: 0 auto;">
	<tr>
		<td mc:edit="text003" class="text_color_282828" style="color: #282828; font-size: 15px; line-height: 2; font-weight: 500; font-family: lato, Helvetica, sans-serif; mso-line-height-rule: exactly;">
			<div class="editable-text" style="line-height: 2;">
				<div class="text_container">
					@php
						$url = url('accept-invitation/' . $token);
					@endphp
					<p>
						Hello,
					</p>
					<p>
						You have been invited to chat by {{ $user }}. Click the button below to accept the invitation and start connecting!
					</p>
					<p>
						<a href="{{ mainTrustvisoryUrl() }}/chat-invitation/{{ $token }}" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold;">
							Accept Invitation
						</a>
						
					</p>
					<p>
						If you did not expect this invitation, you can safely ignore this email.
					</p>
				</div>
			</div>
		</td>
	</tr>
	<tr><td height="50"></td></tr>
</table>
@endsection