@extends('emails.mail-master')
@section('content')
<table class="container_400" align="center" width="400" border="0" cellspacing="0" cellpadding="10" style="margin: 0 auto;">
	<tr>
		<td mc:edit="text003" class="text_color_282828" style="color: #282828; font-size: 15px; line-height: 2; font-weight: 500; font-family: lato, Helvetica, sans-serif; mso-line-height-rule: exactly;">
			<div class="editable-text" style="line-height: 2;">
				<div class="text_container">
					Hi, Admin submitted violations for uap <b>{{$uap_name}}</b></br>
                    <b>Name: </b>{{$name}}</br>  
                    <b>Summary: </b>{{$summary}}</br>
                    @if($extra_details != '')
                    <b>Extra Details: </b>{{$extra_details}}</br>
                    @endif
                    <b>Date: </b>{{date('d M Y',strtotime($date))}}</br>
                    <b>Are you there existing client?: </b>{{$existing == 0 ? 'No' : 'Yes'}}</br>
                    <b>Added by: </b>{{$added_by}}
				</div>
			</div>
		</td>
	</tr>
	<tr><td height="50"></td></tr>
</table>
@endsection