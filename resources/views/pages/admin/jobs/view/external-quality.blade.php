<div class="row to-hide">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <p class="fw-bold mb-1 text-primary">External Quality Details</p>
                    <form id="updateExternalQualityForm" method="POST">
                        @csrf
                        <div class="form-group row mb-2">
                            <input type="hidden" name="edit_id" id="edit_id" value="{{ $job['id'] }}">
                            <label for="external_quality" class="col-sm-2 col-form-label fw-bold">External Quality Status <strong><span class="important">*</span></strong></label>
                            <div class="col-sm-10">
                                <select class="form-control" name="external_quality" id="external_quality">
                                    <option value=""disabled selected>-- Select External Quality Status -- </option>
                                    <option value="Pass"@if($job['external_quality'] == "Pass") selected @endif>Pass</option>
                                    <option value="Fail"@if($job['external_quality'] == "Fail") selected @endif>Fail</option>
                                </select>
                                <label id="external_qualityError" class="error" style="display:none"></label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row mb-2">
                                <label for="c_external_quality" class="col-sm-2 col-form-label fw-bold">Comments <strong><span class="important">*</span></strong></label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="c_external_quality" id="c_external_quality" cols="30" rows="4" placeholder="Enter comments.">{{ $job['c_external_quality'] }}</textarea>
                                    <label id="c_external_qualityError" class="error" style="display:none"></label>
                                </div>
                            </div>

                        </div>
                        <hr>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" id="btn_ext_qc" class="btn btn-primary waves-effect waves-light"><i class="fa fa-save"></i> Submit</button>
                                <button type="button" id="btn_cancel" class="btn btn-danger waves-effect waves-light" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
