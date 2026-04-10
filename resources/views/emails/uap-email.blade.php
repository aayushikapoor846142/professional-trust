@extends('emails.mail-master')

@section('content')
<table class="container_400" align="center" width="400" border="0" cellspacing="0" cellpadding="10" style="margin: 0 auto;">
	<tr>
		<td mc:edit="text003" class="text_color_282828" style="color: #282828; font-size: 15px; line-height: 2; font-weight: 500; font-family: lato, Helvetica, sans-serif; mso-line-height-rule: exactly;">
			<div class="editable-text" style="line-height: 2;">
				<div class="text_container">
					@php
					$url=url('report/unathorized-professional/individual/'.$token);
					@endphp
					<p>Hey there,</p>
					<p>This email is sent you to regarding posting for details of any of the individual or professional who is not authorized and practing immigration servieces.</p>
					<p>Click below link to open the form and fill the  details about  it.</p>
					<br><br>
					<p>
						<p><a style="display:inline-block;padding:15px 20px;background-color: #ff0000;color:white" href="{{ url('report/unathorized-professional/individual/'.$token) }}">Click to Report</a></p>
                        <Br>
                        Or Copy Url: {{ url('report/unathorized-professional/individual/'.$token) }}
					</center>
				</div>
			</div>
		</td>
	</tr>
	<tr><td height="50"></td></tr>
</table>
@endsection