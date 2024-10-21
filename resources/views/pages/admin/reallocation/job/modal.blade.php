<div class="modal fade" id="reallocateModal" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog"
    aria-labelledby="reallocateModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reallocateModalTitle">Reallocate Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reallocateForm" method="POST">
                    @csrf
                    <div class="form-group client_hide">
                        <label for="client_id" class="col-sm-3 col-form-label fw-bold">Client Name <strong><span class="important">*</span></strong></label>
                        <input type="text" class="form-control" name="client_id" id="client_id">
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="edit_id" id="edit_id">
                        <label for="developer_id" class="col-sm-3 col-form-label fw-bold">Designer <strong><span class="important">*</span></strong></label>
                        <select class="form-control select2" name="developer_id" id="developer_id"  style="width:100%;">
                            <option value=""disabled selected>-- Select Designer -- </option>
                        </select>
                        <label id="developer_idError" class="error" style="display:none"></label>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="btn_save"  class="btn btn-primary waves-effect waves-light"><i class="fa fa-handshake-o"></i> Reallocate</button>
                <button type="button" class="btn btn-danger waves-effect waves-light" data-bs-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>
