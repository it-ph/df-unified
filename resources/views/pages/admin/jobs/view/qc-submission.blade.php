<div class="row to-hide">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <p class="fw-bold mb-1 text-primary">QC Submission</p>
                    <form id="sendForQCForm" method="POST">
                        @csrf
                        <div class="col-md-12">
                            <div class="form-group row mb-2">
                                <input type="hidden" name="edit_id" id="edit_id" value="{{ $job['id'] }}">
                                <label for="preview_link" class="col-sm-2 col-form-label fw-bold">Preview Link <strong><span class="important">*</span></strong></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="preview_link" id="preview_link" placeholder="Enter preview link" value="">
                                    <label id="preview_linkError" class="error" style="display:none"></label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="self_qc" class="col-sm-2 col-form-label fw-bold">Self QC Performed <strong><span class="important">*</span></strong></label>
                                <div class="col-sm-10 mt-1">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="self_qc" id="self_qc_yes" value="1">
                                        <label class="form-check-label" for="self_qc_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="self_qc" id="self_qc_no" value="0">
                                        <label class="form-check-label" for="self_qc_no">No</label>
                                    </div><br>
                                    <label id="self_qcError" class="error" style="display:none"></label>
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label for="dev_comments" class="col-sm-2 col-form-label fw-bold">Developer Comments <strong><span class="important">*</span></strong></label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="dev_comments" id="dev_comments" cols="30" rows="4" placeholder="Enter developer comments."></textarea>
                                    <label id="dev_commentsError" class="error" style="display:none"></label>
                                </div>
                            </div>

                        </div>
                        <hr>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" id="btn_send" class="btn btn-primary waves-effect waves-light"><i class="fa fa-paper-plane"></i> Send For QC</button>
                                <button type="button" id="btn_cancel" class="btn btn-danger waves-effect waves-light" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
