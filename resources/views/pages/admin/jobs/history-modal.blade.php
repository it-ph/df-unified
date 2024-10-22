<div class="modal fade" id="jobHistoryModal" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog"
    aria-labelledby="jobHistoryModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jobHistoryModal">Task History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="jobHistoryForm" method="POST">
                    @csrf
                    <div class="form-group">
                        <div class="table-responsive col-md-12">
                            <table class="table table-bordered table-striped table-sm text-nowrap key-buttons w-100" id="tbl_histories">
                                <thead>
                                    <tr>
                                        <th class="text-center">Created at</th>
                                        <th class="text-center">Created by</th>
                                        <th class="text-center">Account No.</th>
                                        <th class="text-center">Activity</th>
                                    </tr>
                                </thead>
                            </table>

                            <div id="div-spinner" class="text-center mt-4 mb-4 font-size-16">
                                <span id="loader-history"><i class="fa fa-spinner fa-spin"></i> Please wait...</span>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_cancel_history" class="btn btn-danger waves-effect waves-light" data-bs-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>
