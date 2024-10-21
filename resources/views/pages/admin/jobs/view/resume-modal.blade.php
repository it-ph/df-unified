<div class="modal fade" id="resumeJobModal" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog"
    aria-labelledby="resumeJobModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resumeJobModalTitle">Resume Job</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="resumeJobForm" method="POST">
                    @csrf
                    <div class="form-group text-center">
                        <h4>Are you sure?</h4>
                        <h6>You won't be able to revert this!</h6>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_resume" class="btn btn-primary waves-effect waves-light" onclick="JOB.resume({{ $job['id'] }})"><i class="fa fa-play"></i> Resume</button>
                <button type="button" class="btn btn-danger waves-effect waves-light" data-bs-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>
