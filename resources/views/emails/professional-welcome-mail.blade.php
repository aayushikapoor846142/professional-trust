@extends('emails.mail-master')

@section('content')
<table class="container_400" align="center" width="400" border="0" cellspacing="0" cellpadding="10"
    style="margin: 0 auto;">
    <tr>
        <td mc:edit="text003" class="text_color_282828"
            style="color: #282828; font-size: 15px; line-height: 2; font-weight: 500; font-family: lato, Helvetica, sans-serif; mso-line-height-rule: exactly;">
            <div>
                @php
                $company_name=siteSetting('company_name');
                @endphp
                <center>
                    <h1>
                        Welcome to {{siteSetting('company_name')}}
                    </h1>
                </center>
                <p>
                    Hi {{$name}},<br>
                    Thank you for choosing {{siteSetting('company_name')}}. We are glad to have you with us. Our team will be verifying profile and give you update about it soon.
                </p>
            </div>
        </td>
    </tr>
    <tr>
        <td height="50"></td>
    </tr>
</table>
@endsection