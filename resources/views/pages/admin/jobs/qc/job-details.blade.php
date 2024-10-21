<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <p class="fw-bold mb-1 text-primary">Task Details</p>
                    <table class="table table-bordered table-sm nowrap w-100">
                        <tr>
                            <td class="col-sm-2 fw-bold">Status</td>
                            <td class="col-sm-10">
                                @include('pages.admin.jobs.view.status')
                            </td>
                        </tr>
                        <tr>
                            <td class="col-sm-2 fw-bold">Task Name</td>
                            <td class="col-sm-10">{{ $job['name'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-2 fw-bold">Site ID</td>
                            <td class="col-sm-10">{{ $job['site_id'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-2 fw-bold">Platform</td>
                            <td class="col-sm-10">{{ $job['platform'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-2 fw-bold">Developer</td>
                            <td class="col-sm-10">{{ $job['developer'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-2 fw-bold">Type of Request</td>
                            <td class="col-sm-10">{{ $job['request_type'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-2 fw-bold">Num of Pages</td>
                            <td class="col-sm-10">{{ $job['request_volume'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-2 fw-bold">Salesforce Link</td>
                            <td class="col-sm-10"><a href="{{ $job['salesforce_link'] }}" rel="noopener noreferrer" target="_blank" class="text-info">{{ $job['salesforce_link'] }}</a></td>
                        </tr>
                        <tr>
                            <td class="col-sm-2 fw-bold">Special Request</td>
                            <td class="col-sm-10">{{ $job['special_request'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-2 fw-bold">Comments for Special Request</td>
                            <td class="col-sm-10 tdbreak">{{ $job['comments_special_request'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-2 fw-bold">Additional Comments</td>
                            <td class="col-sm-10 tdbreak">{{ $job['addon_comments'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-2 fw-bold">SLA Agreed</td>
                            <td class="col-sm-10"><span class="badge bg-primary">{{ $job['agreed_sla'] }} hrs</span></td>
                        </tr>
                        <tr>
                            <td class="col-sm-2 fw-bold">SLA Missed</td>
                            <td class="col-sm-10">@if($job['sla_missed']) <span class="text-danger">Yes</span> @else <span class="text-success">No</span> @endif</td>
                        </tr>
                        @include('pages.admin.jobs.view.additional-details')
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
