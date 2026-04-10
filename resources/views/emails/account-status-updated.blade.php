@extends('emails.mail-master')

@section('content')
<table class="container_400" align="center" width="400" border="0" cellspacing="0" cellpadding="10"
    style="margin: 0 auto;">
    <tr>
        <td mc:edit="text003" class="text_color_282828"
            style="color: #282828; font-size: 15px; line-height: 2; font-weight: 500; font-family: lato, Helvetica, sans-serif; mso-line-height-rule: exactly;">
            <div>
                <center>
                    <h1>
                        Account status updated
                    </h1>
                </center>
                <p>
                    Hi {{$name}},<br>
                    Your account status is changed to <b>{{$status}}</b>.
                </p>
                <p>
                Here is your login URL:  
        <a href="https://professionals.trustvisory.com/login">https://professionals.trustvisory.com/login</a></p>
            </div>
        </td>
    </tr>
    <tr>
        <td height="50"></td>
    </tr>
</table>
@endsection