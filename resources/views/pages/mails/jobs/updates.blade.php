<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DF Unified: Task Status Change Alert</title>
</head>
<body>
    <h4>Hi, </h4>

    <p>Status changed has been made to task <a href="{{url('viewjob')}}/{{ $job->id }}">{{ $job->name }}</a>.</p>
    Task Status: @include('pages.mails.jobs.status')

    @include('pages.mails.system-generated.email')
</body>
</html>
