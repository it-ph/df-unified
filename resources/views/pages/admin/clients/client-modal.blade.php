<div class="modal fade" id="clientModal" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog"
    aria-labelledby="clientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clientModalTitle">Create New Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="clientForm" method="POST">
                    @csrf
                    <div class="form-group">
                        <input type="hidden" name="edit_id" id="edit_id">
                        <label for="name" class="col-form-label"><strong>Client Name:<span class="important">*</span></strong></label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Client Name">
                        <label id="nameError" class="error"></label>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label for="start" class="col-form-label"><strong>Start of Shift:<span class="important">*</span></strong></label>
                            <input type="time" class="form-control" name="start" id="start" step="1">
                            <label id="startError" class="error" style="display:none"></label>
                        </div>
                        <div class="col-sm-6">
                            <label for="end" class="col-form-label"><strong>End of Shift:<span class="important">*</span></strong></label>
                            <input type="time" class="form-control" name="end" id="end" step="1">
                            <label id="endError" class="error" style="display:none"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sla_threshold" class="col-form-label"><strong>SLA Threshold for Email Notifs (%):<span class="important">*</span></strong></label>
                        <input type="text" class="form-control" name="sla_threshold" id="sla_threshold" min="0" max="100"  placeholder="Enter SLA Threshold E.g 70">
                        <label id="sla_thresholdError" class="error"></label>
                    </div>
                    <div class="form-group">
                        <label for="sla_threshold_to" class="col-form-label"><strong>SLA Threshold Cross Email Recipients (TO):<span class="important">*</span></strong></label>
                        <input type="text" class="form-control" name="sla_threshold_to" id="sla_threshold_to" placeholder="Enter SLA Threshold Cross Email Recipients (TO)">
                        <label id="sla_threshold_toError" class="error"></label>
                    </div>
                    <div class="form-group">
                        <label for="sla_threshold_cc" class="col-form-label"><strong>SLA Threshold Cross Email Recipients (CC):<span class="important">*</span></strong></label>
                        <input type="text" class="form-control" name="sla_threshold_cc" id="sla_threshold_cc" placeholder="Enter SLA Threshold Cross Email Recipients (CC)">
                        <label id="sla_threshold_ccError" class="error"></label>
                    </div>
                    <div class="form-group">
                        <label for="sla_missed_to" class="col-form-label"><strong>SLA Miss Email Recipients (TO):<span class="important">*</span></strong></label>
                        <input type="text" class="form-control" name="sla_missed_to" id="sla_missed_to" placeholder="Enter SLA Missed Email Recipients (TO)">
                        <label id="sla_missed_toError" class="error"></label>
                    </div>
                    <div class="form-group">
                        <label for="sla_missed_cc" class="col-form-label"><strong>SLA Miss Email Recipients (CC):<span class="important">*</span></strong></label>
                        <input type="text" class="form-control" name="sla_missed_cc" id="sla_missed_cc" placeholder="Enter SLA Missed Email Recipients (CC)">
                        <label id="sla_missed_ccError" class="error"></label>
                    </div>
                    <div class="form-group">
                        <label for="new_job_cc" class="col-form-label"><strong>New Job Email Recipients (CC):<span class="important">*</span></strong></label>
                        <input type="text" class="form-control" name="new_job_cc" id="new_job_cc" placeholder="Enter New Job Email Recipients (CC)">
                        <label id="new_job_ccError" class="error"></label>
                    </div>
                    <div class="form-group">
                        <label for="qc_send_cc" class="col-form-label"><strong>QC Send Email Recipients (CC):<span class="important">*</span></strong></label>
                        <input type="text" class="form-control" name="qc_send_cc" id="qc_send_cc" placeholder="Enter SLA Missed Email Recipients (CC)">
                        <label id="qc_send_ccError" class="error"></label>
                    </div>
                    <div class="form-group">
                        <label for="daily_report_to" class="col-form-label"><strong>Daily Report Recipients (TO):<span class="important">*</span></strong></label>
                        <input type="text" class="form-control" name="daily_report_to" id="daily_report_to" placeholder="Enter Daily Report Recipients (TO)">
                        <label id="daily_report_toError" class="error"></label>
                    </div>
                    <div class="form-group">
                        <label for="daily_report_cc" class="col-form-label"><strong>Daily Report Recipients (CC):<span class="important">*</span></strong></label>
                        <input type="text" class="form-control" name="daily_report_cc" id="daily_report_cc" placeholder="Enter Daily Report Recipients (CC)">
                        <label id="daily_report_ccError" class="error"></label>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="btn_save" class="btn btn-primary waves-effect waves-light"><i class="fa fa-save"></i> Save</button>
                <button type="button" class="btn btn-danger waves-effect waves-light" data-bs-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>
