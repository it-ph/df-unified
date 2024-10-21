<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SLA Tracker: New Job Alert</title>
</head>
<body>
    <h4>Hi, </h4>

    <p>A job has been created and assigned to <strong>{{ $job->thedeveloper->full_name }}</strong>.</p>

    @include('pages.mails.jobs.details')
    @include('pages.mails.system-generated.email')
</body>
</html>
