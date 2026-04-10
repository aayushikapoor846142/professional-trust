@extends('emails.mail-master')
@section('content')

<tr>
    <td valign="middle" class="hero bg_white">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">

            <tr>
                <td style="text-align: center;">
                    <div class="text-author">
                        <img src="{{url('/')}}/assets/fs-assets/img/email-icons/task.png" alt=""
                            style="width: 100px; max-width: 600px; height: auto; margin: auto; display: block;">
                        <div class="email-message-body">
                            @if($name != '')
                            <h3>Dear {{$name}},</h3>
                            @else
                            <h3>Greetings!</h3>
                            @endif
                            <span>You have received a new form. {!! $mail_message !!}. You are requested to
                                respond as soon as possible.</span>

                            <a href="{{$url}}">Click to Open</a>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </td>
</tr><!-- end tr -->

@endsection