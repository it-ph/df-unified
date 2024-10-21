<table>
    <tr>
        <td><strong>Task Name</strong></td>
        <td><a href="{{url('viewjob')}}/{{ $job->id }}">{{ $job->name }}</a></td>
    </tr>
    <tr>
        <td><strong>Designer</strong></td>
        <td>{{ ucfirst($job->thedeveloper->full_name) }}</td>
    </tr>
    <tr>
        <td><strong>QC Proofreader</strong></td>
        <td>{{ $job->theauditor->full_name }}</td>
    </tr>
    <tr>
        <td><strong>Preview Link</strong></td>
        <td><a href="{{ $audit_log->preview_link }}">{{ $audit_log->preview_link }}</a></td>
    </tr>
    <tr>
        <td><strong>Self QC</strong></td>
        <td>@if($audit_log->self_qc) Yes @else No @endif</td>
    </tr>
    <tr>
        <td><strong>Designer Comments</strong></td>
        <td>{!! $audit_log->dev_comments !!}</td>
    </tr>
    <tr>
        <td><strong>QC Round</strong></td>
        <td></td>
    </tr>
    <tr>
        <td><strong>QC Result</strong></td>
        <td>
            @include('pages.mails.qcs.status')
        </td>
    </tr>
</table>
