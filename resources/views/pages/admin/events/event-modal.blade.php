<div class="modal fade" id="eventModal" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog"
    aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle">Create New Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="eventForm" method="POST">
                    @csrf
                    <div class="form-group @if(!in_array('admin',$user['roles'])) ihide @endif">
                        <label for="client_id" class="col-form-label fw-bold">Client <strong><span class="important">*</span></strong></label>
                        <select class="form-control select2" name="client_id" id="client_id"  style="width:100%;">
                            <option value=""disabled selected>-- Select Client -- </option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" @if($client->id == auth()->user()->client_id) selected @endif>{{ ucwords($client->name) }}</option>
                                @endforeach
                        </select>
                        <label id="client_idError" class="error" style="display:none"></label>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="edit_id" id="edit_id">
                        <label for="title" class="col-form-label"><strong>Title:<span class="important">*</span></strong></label>
                        <input type="text" class="form-control" name="title" id="title" placeholder="Enter event title">
                        <label id="titleError" class="error" style="display:none"></label>
                    </div>
                    <div class="form-group">
                        <label for="description" class="col-form-label"><strong>Description:<span class="important">*</span></strong></label>
                        <textarea name="description" id="description" class="form-control" cols="30" rows="3" placeholder="Enter event description"></textarea>
                        <label id="descriptionError" class="error" style="display:none"></label>
                    </div>
                    <div class="form-group mb-2">
                        <label for="event_type" class="col-form-label"><strong>Event Type:<span class="important">*</span></strong></label>
                        <select class="form-select" name="event_type" id="event_type">
                            <option value="" disabled selected>-- Select Event Type --</option>
                            <option value="Client Holiday">Client Holiday</option>
                            <option value="eClerx Holiday" >eClerx Holiday</option>
                            <option value="Meeting" >Meeting</option>
                            <option value="Reminder" >Reminder</option>
                            <option value="Others" >Others</option>
                        </select>
                        <label id="event_typeError" class="error" style="display:none"></label>
                    </div>
                    <div class="form-group">
                        <label for="datefilter"><strong>Event Date</strong></label>
                        <div id="datefilter" class="form-control datefilter">
                            <i class="fa fa-calendar"></i>&nbsp;<span></span>
                        </div>
                        <input type="hidden" class="form-control" name="daterange" id="date_range">
                        <label id="datefilterError" class="error" style="display:none"></label>
                    </div>
                    <div class="form-group">
                        <label for="color" class="col-form-label"><strong>Color:<span class="important">*</span></strong></label>
                        <input type="color" class="form-control" name="color" id="color">
                        <label id="colorError" class="error" style="display:none"></label>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="btn_save" class="btn btn-primary waves-effect waves-light"><i class="fa fa-save"></i> Save</button>
                <button type="button" id="btn_delete" class="btn btn-danger waves-effect waves-light" style="display: none"><i class="fa fa-trash"></i> Delete</button>
                <button type="button" id="btn_cancel" class="btn btn-secondary waves-effect waves-light" data-bs-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>
