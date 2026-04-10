@extends('emails.mail-master')
@section('content')
<table class="container_400" align="center" width="400" border="0" cellspacing="0" cellpadding="10" style="margin: 0 auto;">
	<tr>
		<td mc:edit="text003" class="text_color_282828" style="color: #282828; font-size: 15px; line-height: 2; font-weight: 500; font-family: lato, Helvetica, sans-serif; mso-line-height-rule: exactly;">
			<div class="editable-text" style="line-height: 2;">
				<div class="text_container">
					Hi, {{$professional_name}} you have received a new chat request from {{$sender_name}}. Please <a href="{{url('/login')}}">Login</a> into your panel to Accept Chat Request.				
				</div>
			</div>
		</td>
	</tr>
	<tr><td height="50"></td></tr>
</table>
@endsection