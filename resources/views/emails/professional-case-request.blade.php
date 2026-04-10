@extends('emails.mail-master')

@section('content')
<table class="container_400" align="center" width="400" border="0" cellspacing="0" cellpadding="10"
    style="margin: 0 auto;">
    <tr>
    <td valign="middle" class="hero bg_white">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">

            <tr>
                <td style="text-align: center;">
                    <div class="text-author">
                        <div class="email-message-body">
                            <h3>Dear {{$name}},</h3>
                            <span>You have received a new {{$request_type}} request titled <b>{{$titles}}</b>. It has been added to your
                                case - <b>@if($casetitle){{$casetitle}}@endif</b>. You are requested to respond as soon as possible.</span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </td>
</tr>
</table>
@endsection