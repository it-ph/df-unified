<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DF Unified: Reallocate QC Task Alert</title>
</head>
<body>
    <h4>Hi, </h4>

    <p>QC Task <a href="{{url('viewqualitycheck')}}/{{ $audit_log->id }}">{{ $audit_log->thejob->name }}</a> has been reallocated and assigned to <strong>{{ $audit_log->theauditor->full_name }}</strong>.</p>
    QC Task Status: @include('pages.mails.qcs.status')

    @include('pages.mails.system-generated.email')
</body>
</html>
