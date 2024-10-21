<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SLA Tracker: Reallocate Job Alert</title>
</head>
<body>
    <h4>Hi, </h4>

    <p>Job <a href="{{url('viewjob')}}/{{ $job->id }}">{{ $job->name }}</a> has been reallocated and assigned to <strong>{{ $job->thedeveloper->full_name }}</strong>.</p>
    Job Status: @include('pages.mails.jobs.status')

    @include('pages.mails.system-generated.email')
</body>
</html>
