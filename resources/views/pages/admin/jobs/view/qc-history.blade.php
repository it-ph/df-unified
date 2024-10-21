<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <p class="fw-bold mb-1 text-primary">QC History</p>
                    <table class="table table-bordered table-sm nowrap w-100">
                        <tr>
                            <th class="text-center">QC Round</th>
                            <th class="text-center">QC Auditor</th>
                            <th class="text-center">QC Result</th>
                            <th class="text-center">QC Start Time</th>
                            <th class="text-center">QC End Time</th>
                            <th class="text-center">Self QC Performed</th>
                            <th class="text-center">Action</th>
                        <tr>
                            @foreach ($job['audit_logs'] as $log)
                                <tr>
                                    <td class="text-center">{{ $log['qc_round'] }}</td>
                                    <td class="text-center">{{ $log['auditor'] }}</td>
                                    <td class="text-center">
                                        <?php
                                            $badge_status = $log['qc_status'];
                                            switch ($badge_status) {
                                                case "Pending":
                                                    $badge = 'warning';
                                                    break;
                                                case "Pass":
                                                    $badge = 'success';
                                                    break;
                                                case "Fail":
                                                    $badge = 'danger';
                                                    break;
                                            }
                                        ?>
                                        <span class="badge bg-{{ $badge }}">{{ $log['qc_status'] }}</span>
                                    </td>
                                    <td class="text-center">{{ $log['qc_start_at'] }}</td>
                                    <td class="text-center">{{ $log['qc_end_at'] }}</td>
                                    <td class="text-center">{{ $log['self_qc'] }}</td>
                                    <td class="text-center">
                                        <a href="{{ url('viewqualitycheck') }}/{{ $log['audit_log_id'] }}" rel="noopener noreferrer" target="_blank" class="btn btn-primary btn-sm waves-effect waves-light" title="View Quality Check"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
