<table>
    <tr>
        <td><strong>Job Name</strong></td>
        <td><a href="{{url('viewjob')}}/{{ $job->id }}">{{ $job->name }}</a></td>
    </tr>
    <tr>
        <td><strong>Developer</strong></td>
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
    {{-- <tr>
        <td><strong>Salesforce Link</strong></td>
        <td><a href="{{ $job->salesforce_link }}">{{ $job->salesforce_link }}</a></td>
    </tr> --}}
    <tr>
        <td><strong>Special Request</strong></td>
        <td>@if($job->special_request) Yes @else No @endif</td>
    </tr>
    <tr>
        <td><strong>Status</strong></td>
        <td>
            @include('pages.mails.jobs.status')
        </td>
    </tr>
</table>
