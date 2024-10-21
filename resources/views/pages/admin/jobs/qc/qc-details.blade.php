<div class="row to-hide">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <p class="fw-bold mb-1 text-primary">QC Details</p>
                    <table class="table table-bordered table-sm nowrap w-100">
                        <tr>
                            <td class="col-sm-3 fw-bold">Preview Link</td>
                            <td class="col-sm-9"><a href="{{ $job['preview_link'] }}" target="_blank">{{ $job['preview_link'] }}</a></td>
                        </tr>
                        <tr>
                            <td class="col-sm-3 fw-bold">Self QC Performed</td>
                            <td class="col-sm-9">{{ $job['self_qc'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3 fw-bold">Developer Comments</td>
                            <td class="col-sm-9">{{ $job['audit_dev_comments'] }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3 fw-bold">QC Round</td>
                            <td class="col-sm-9">{{ $job['qc_round'] }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
