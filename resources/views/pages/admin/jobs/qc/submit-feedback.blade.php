<div class="row to-hide">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <p class="fw-bold mb-1 text-primary">Submit Feedback</p>
                    <form id="submitFeedbackForm" method="POST">
                        @csrf
                        <div class="col-md-12">
                            <div class="form-group row mb-2">
                                <input type="hidden" name="edit_id" id="edit_id" value="{{ $job['id'] }}">
                                <label for="qc_status" class="col-sm-3 col-form-label fw-bold">QC Status <strong><span class="important">*</span></strong></label>
                                <div class="col-sm-9">
                                    <select class="form-control" name="qc_status" id="qc_status">
                                        <option value=""disabled selected>-- Select QC Status -- </option>
                                        <option value="Pass" >Pass</option>
                                        <option value="Fail" >Fail</option>
                                    </select>
                                    <label id="qc_statusError" class="error" style="display:none"></label>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="for_rework" class="col-sm-3 col-form-label fw-bold">Call for Rework <strong><span class="important">*</span></strong></label>
                                <div class="col-sm-9 mt-1">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="for_rework" id="for_rework_yes" value="1" onclick="javascript:return false;">
                                        <label class="form-check-label" for="for_rework_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="for_rework" id="for_rework_no" value="0" onclick="javascript:return false;">
                                        <label class="form-check-label" for="for_rework_no">No</label>
                                    </div><br>
                                    <label id="for_reworkError" class="error" style="display:none"></label>
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label for="num_times" class="col-sm-3 col-form-label fw-bold">Num of Times <strong><span class="important">*</span></strong></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="num_times" id="num_times" placeholder="Enter num of times" value="0">
                                    <label id="num_timesError" class="error" style="display:none"></label>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="alignment_aesthetics" class="col-sm-3 col-form-label fw-bold">Alignment & Aesthetics <strong><span class="important">*</span></strong></label>
                                <div class="col-sm-9 mt-1">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="alignment_aesthetics" id="alignment_aesthetics_yes" value="1">
                                        <label class="form-check-label" for="alignment_aesthetics_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input no" type="radio" name="alignment_aesthetics" id="alignment_aesthetics_no" value="0">
                                        <label class="form-check-label" for="alignment_aesthetics_no">No</label>
                                    </div><br>
                                    <label id="alignment_aestheticsError" class="error" style="display:none"></label>
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label for="c_alignment_aesthetics" class="col-sm-3 col-form-label fw-bold">Comments for Alignment & Aesthetics <strong><span class="important">*</span></strong></label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" name="c_alignment_aesthetics" id="c_alignment_aesthetics" cols="30" rows="4" placeholder="Enter comments for alignment & aesthetics ."></textarea>
                                    <label id="c_alignment_aestheticsError" class="error" style="display:none"></label>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="availability_formats" class="col-sm-3 col-form-label fw-bold">Availability and Formats <strong><span class="important">*</span></strong></label>
                                <div class="col-sm-9 mt-1">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="availability_formats" id="availability_formats_yes" value="1">
                                        <label class="form-check-label" for="availability_formats_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input no" type="radio" name="availability_formats" id="availability_formats_no" value="0">
                                        <label class="form-check-label" for="availability_formats_no">No</label>
                                    </div><br>
                                    <label id="availability_formatsError" class="error" style="display:none"></label>
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label for="c_availability_formats" class="col-sm-3 col-form-label fw-bold">Comments for Availability and Formats <strong><span class="important">*</span></strong></label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" name="c_availability_formats" id="c_availability_formats" cols="30" rows="4" placeholder="Enter comments for availability and formats ."></textarea>
                                    <label id="c_availability_formatsError" class="error" style="display:none"></label>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="accuracy" class="col-sm-3 col-form-label fw-bold">Accuracy <strong><span class="important">*</span></strong></label>
                                <div class="col-sm-9 mt-1">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="accuracy" id="accuracy_yes" value="1">
                                        <label class="form-check-label" for="accuracy_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input no" type="radio" name="accuracy" id="accuracy_no" value="0">
                                        <label class="form-check-label" for="accuracy_no">No</label>
                                    </div><br>
                                    <label id="accuracyError" class="error" style="display:none"></label>
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label for="c_accuracy" class="col-sm-3 col-form-label fw-bold">Comments for Accuracy <strong><span class="important">*</span></strong></label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" name="c_accuracy" id="c_accuracy" cols="30" rows="4" placeholder="Enter comments for accuracy ."></textarea>
                                    <label id="c_accuracyError" class="error" style="display:none"></label>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="functionality" class="col-sm-3 col-form-label fw-bold">Functionality <strong><span class="important">*</span></strong></label>
                                <div class="col-sm-9 mt-1">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="functionality" id="functionality_yes" value="1">
                                        <label class="form-check-label" for="functionality_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input no" type="radio" name="functionality" id="functionality_no" value="0">
                                        <label class="form-check-label" for="functionality_no">No</label>
                                    </div><br>
                                    <label id="functionalityError" class="error" style="display:none"></label>
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label for="c_functionality" class="col-sm-3 col-form-label fw-bold">Comments for Functionality <strong><span class="important">*</span></strong></label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" name="c_functionality" id="c_functionality" cols="30" rows="4" placeholder="Enter comments for functionality."></textarea>
                                    <label id="c_functionalityError" class="error" style="display:none"></label>
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label for="qc_comments" class="col-sm-3 col-form-label fw-bold">QC Comments <strong><span class="important">*</span></strong></label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" name="qc_comments" id="qc_comments" cols="30" rows="4" placeholder="Enter qc comments."></textarea>
                                    <label id="qc_commentsError" class="error" style="display:none"></label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" id="btn_save" class="btn btn-primary"><i class="fa fa-save"></i> Submit</button>
                                <button type="button" id="btn_cancel" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
