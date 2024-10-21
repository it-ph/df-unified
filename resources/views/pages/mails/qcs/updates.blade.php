<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DF Unified: QC Task Status Change Alert</title>
</head>
<body>
    <h4>Hi, </h4>

    <p>Status changed has been made to QC Task <a href="{{url('viewqualitycheck')}}/{{ $audit_log->id }}">{{ $audit_log->thejob->name }}</a>.</p>

    @if($audit_log->auditor_id <> null && $audit_log->qc_status == "Pending")
        <p>QC Task has been picked and started.</p>
    @elseif($audit_log->auditor_id == null)
        <p>QC Task has been released.</strong></p>
    @endif

    QC Task Status: @include('pages.mails.qcs.status')
    @include('pages.mails.system-generated.email')
</body>
</html>
