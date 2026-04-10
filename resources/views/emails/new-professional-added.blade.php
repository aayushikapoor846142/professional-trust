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
                        New Professional Signup
                    </h1>
                </center>
                
                <p>
                    {{$name}} has signed up to system as professional. Please verify the details and approve the account.
                </p>
            </div>

            <div style="display:block;text-align:center;padding:20px;margin-top:50px">
                <a style="background-color:#dc3545;padding: 0.375rem 0.75rem;color:#FFF" href="{{ url("panel/professionals/view/".$professional->unique_id) }}">View Profile</a>
            </div>
        </td>
    </tr>
    <tr>
        <td height="50"></td>
    </tr>
</table>
@endsection