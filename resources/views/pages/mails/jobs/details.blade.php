<table>
    <tr>
        <td><strong>Account No</strong></td>
        <td><a href="{{url('viewjob')}}/{{ $job->id }}">{{ $job->name }}</a></td>
    </tr>
    <tr>
        <td><strong>Account Name</strong></td>
        <td>{{ $job->name }}</td>
    </tr>
    <tr>
        <td><strong>Designer</strong></td>
        <td>{{ ucfirst($job->thedeveloper->full_name) }}</td>
    </tr>
    <tr>
        <td><strong>Type of Request</strong></td>
        <td>{{ $job->therequesttype->name }}</td>
    </tr>
    <tr>
        <td><strong>Num Pages</strong></td>
        <td>{{ $job->therequestvolume->name }}</td>
    </tr>
    <tr>
        <td><strong>Agreed SLA</strong></td>
        <td>{{ $job->therequestsla->agreed_sla }} hrs</td>
    </tr>
    <tr>
        <td><strong>Status</strong></td>
        <td>
            @include('pages.mails.jobs.status')
        </td>
    </tr>
</table>
