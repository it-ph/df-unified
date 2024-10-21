<div class="row to-hide">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <p class="fw-bold mb-1 text-primary">QC Feedback</p>
                    <table class="table table-bordered table-sm nowrap w-100">
                        <tr>
                            <td class="col-sm-3 fw-bold">QC Auditor</td>
                            <td class="col-sm-9">{{ $job['auditor'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3 fw-bold">QC Status</td>
                            <td class="col-sm-9"><span class="badge bg-<?php echo $job['qc_status'] == 'Pass' ? 'success' : 'danger'; ?>">{{ $job['qc_status'] }}</span></td>
                        </tr>
                        <tr>
                            <td class="col-sm-3 fw-bold">Call for Rework</td>
                            <td class="col-sm-9">{{ $job['for_rework'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3 fw-bold">Num of Times</td>
                            <td class="col-sm-9">{{ $job['num_times'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3 fw-bold">Alignment & Aesthetics</td>
                            <td class="col-sm-9">{{ $job['alignment_aesthetics'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3 fw-bold">Comments for Alignment & Aesthetics</td>
                            <td class="col-sm-9 tdbreak">{{ $job['c_alignment_aesthetics'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3 fw-bold">Availability and Formats</td>
                            <td class="col-sm-9">{{ $job['availability_formats'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3 fw-bold">Comments for Availability and Formats</td>
                            <td class="col-sm-9 tdbreak">{{ $job['c_availability_formats'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3 fw-bold">Accuracy</td>
                            <td class="col-sm-9">{{ $job['accuracy'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3 fw-bold">Comments for Accuracy</td>
                            <td class="col-sm-9 tdbreak">{{ $job['c_accuracy'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3 fw-bold">Functionality</td>
                            <td class="col-sm-9">{{ $job['functionality'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3 fw-bold">Comments for Functionality</td>
                            <td class="col-sm-9 tdbreak">{{ $job['c_functionality'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3 fw-bold">QC Comments</td>
                            <td class="col-sm-9 tdbreak">{{ $job['qc_comments'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3 fw-bold">QC Start Time</td>
                            <td class="col-sm-9">{{ $job['qc_start_at'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3 fw-bold">QC End Time</td>
                            <td class="col-sm-9">{{ $job['qc_end_at'] }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
