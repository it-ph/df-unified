<div class="row to-hide">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <p class="fw-bold mb-1 text-primary">SLA Miss Details</p>
                    <form id="slaMissReasonForm" method="POST">
                        @csrf
                        <div class="col-md-12">
                            <input type="hidden" name="edit_id" id="edit_id" value="{{ $job['id'] }}">
                            <div class="form-group row mb-2">
                                <label for="sla_miss_reason" class="col-sm-2 col-form-label fw-bold">SLA Miss Reason <strong><span class="important">*</span></strong></label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="sla_miss_reason" id="sla_miss_reason" cols="30" rows="4" placeholder="Enter sla miss reason."></textarea>
                                    <label id="sla_miss_reasonError" class="error" style="display:none"></label>
                                </div>
                            </div>

                        </div>
                        <hr>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" id="btn_reason" class="btn btn-primary waves-effect waves-light"><i class="fa fa-save"></i> Submit</button>
                                <button type="button" id="btn_cancel" class="btn btn-danger waves-effect waves-light" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
