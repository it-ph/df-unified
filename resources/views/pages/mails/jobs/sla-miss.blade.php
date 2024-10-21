<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DF Unified: SLA Miss Task Alert</title>
</head>
<body>
    <h4>Hi, </h4>

    <p>Please be informed that task <a href="{{url('viewjob')}}/{{ $job->id }}">{{ $job->name }}</a> has exceeded the agreed SLA.</p>
    Task Status: @include('pages.mails.jobs.status')

    @include('pages.mails.system-generated.email')
</body>
</html>
