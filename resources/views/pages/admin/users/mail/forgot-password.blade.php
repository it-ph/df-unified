<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SLA Tracker : Forgot Password</title>
</head>
<body>

<p> We received your request to reset your password.</p>

<p>Please click this link to proceed :</p>

<p>
    <a href="{{url('forgot-password-verify')}}/{{$request_key}}">
        {{url('forgot-password-verify')}}/{{$request_key}}
    </a>
</p>

<p>Otherwise please ignore this message if you didn't request to reset.</p>


Regards,
<br>
SLA Tracker
<br><br>
<span style="margin-top:50px"> --System Generated Email--</span>
</body>
</html>

