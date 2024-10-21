<div class="modal fade" id="uploadSLAModal" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog"
    aria-labelledby="uploadSLAModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadSLAModalLabel">Upload Request SLA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadSLAForm" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="name" class="col-form-label custom-label"><strong>SELECT FILE TO UPLOAD:<span class="important">*</span></strong></label>
                        <input type="file" class="form-control" name="import_file" id="import_file" accept=".xlsx, .xls">
                        <label id="import_fileError" class="error" style="display:none">Please select a file to upload.</label>
                        <ul id="errorList" class="mt-1 text-danger" style="display:none"></ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_import" class="btn btn-primary waves-effect waves-light"><i class="fa fa-save"></i> Upload</button>
                <button type="button" class="btn btn-danger waves-effect waves-light" data-bs-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>
