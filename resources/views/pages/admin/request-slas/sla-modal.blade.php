<div class="modal fade" id="requestSLAModal" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog"
    aria-labelledby="requestSLAModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestSLAModalTitle">Create New Request SLA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="requestSLAForm" method="POST">
                    @csrf
                    <div class="form-group">
                        <input type="hidden" name="edit_id" id="edit_id">
                        <label for="request_type_id" class="col-form-label"><strong>Type of Request:<span class="important">*</span></strong></label>
                        <select class="form-control select2" name="request_type_id" id="request_type_id" style="width:100%;">
                            <option value="" selected disabled>-- Select Type of Request -- </option>
                                @foreach ($request_types as $request_type )
                                    @if($request_type)
                                        <option value="{{ $request_type->id }}">{{ ucwords($request_type->name) }}</option>
                                    @endif
                                @endforeach
                        </select>
                        <label id="request_type_idError" class="error"></label>
                    </div>
                    <div class="form-group">
                        <label for="request_volume_id" class="col-form-label"><strong>Num Pages:<span class="important">*</span></strong></label>
                        <select class="form-control select2" name="request_volume_id" id="request_volume_id" style="width:100%;">
                            <option value="" selected disabled>-- Select Num Pages -- </option>
                                @foreach ($request_volumes as $request_volume )
                                    @if($request_volume)
                                        <option value="{{ $request_volume->id }}">{{ ucwords($request_volume->name) }}</option>
                                    @endif
                                @endforeach
                        </select>
                        <label id="request_volume_idError" class="error"></label>
                    </div>
                    <div class="form-group">
                        <label for="agreed_sla" class="col-form-label"><strong>Agreed SLA (hours):<span class="important">*</span></strong></label>
                        <input type="string" class="form-control" name="agreed_sla" id="agreed_sla" placeholder="Enter Agreed SLA E.g 8">
                        <label id="agreed_slaError" class="error"></label>
                    </div>
                    <div class="form-group" id="default-status">
                        <label for="status" class="col-form-label"><strong>Status:<span class="important">*</span></strong></label>
                        <select class="form-select" name="status" id="status_">
                            <option value="active">Active</option>
                            <option value="inactive" >Inactive</option>
                        </select>
                        <label id="statusError" class="error" style="display:none"></label>
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
