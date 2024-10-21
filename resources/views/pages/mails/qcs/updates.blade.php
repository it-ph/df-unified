<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SLA Tracker: QC Job Status Change Alert</title>
</head>
<body>
    <h4>Hi, </h4>

    <p>Status changed has been made to QC Job <a href="{{url('viewqualitycheck')}}/{{ $audit_log->id }}">{{ $audit_log->thejob->name }}</a>.</p>

    @if($audit_log->auditor_id <> null && $audit_log->qc_status == "Pending")
        <p>QC Job has been picked and started.</p>
    @elseif($audit_log->auditor_id == null)
        <p>QC Job has been released.</strong></p>
    @endif

    QC Job Status: @include('pages.mails.qcs.status')
    @include('pages.mails.system-generated.email')
</body>
</html>
