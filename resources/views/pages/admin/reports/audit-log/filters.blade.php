<div class="row">
    <div class="@if(!in_array('admin',$user['roles'])) col-md-4 col-sm-12 @else col-md-2 col-sm-6 @endif mb-2">
        <label for="datefilter"><strong>Date Range</strong></label>
        <div id="datefilter" class="form-control datefilter">
            <i class="fa fa-calendar"></i>&nbsp;<span></span>
        </div>
        <input type="hidden" class="form-control" name="daterange" id="date_range">
    </div>
    <div class="col-md-2 col-sm-6 mb-2 @if(!in_array('admin',$user['roles'])) client_hide @endif">
        <label for="Client"><strong>Client</strong></label>
        <select class="form-control select2" name="client_id" id="client_id" style="width:100%;">
            <option value="all" selected>All</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}" @if($client->id == auth()->user()->client_id) selected @endif>{{ ucwords($client->name) }}</option>
                @endforeach
        </select>
        <label id="client_idError" class="error" style="display:none"></label>
    </div>
    <div class="col-md-2 col-sm-6 mb-2">
        <label for="platform"><strong>Platform</strong></label>
        <select class="form-control select2" name="platform" id="platform" style="width:100%;">
            <option value="all" selected>All</option>
            <option value="Duda">Duda</option>
            <option value="Wordpress">Wordpress</option>
        </select>
    </div>
    <div class="col-md-2 col-sm-6 mb-2">
        <label for="request_type_id"><strong>Request Type</strong></label>
        <select class="form-control select2" name="request_type_id" id="request_type_id" style="width:100%;">
            <option value="all" selected>All</option>
                @foreach ($request_types as $request_type)
                    @if($request_type)
                        <option value="{{ $request_type->id }}">{{ ucwords($request_type->name) }}</option>
                    @endif
                @endforeach
        </select>
    </div>
    <div class="col-md-2 col-sm-6 mb-2">
        <label for="auditor_id"><strong>Auditor</strong></label>
        <select class="form-control select2" name="auditor_id" id="auditor_id" style="width:100%;">
            <option value="all" selected>All</option>
                @foreach ($auditors as $auditor)
                    <option value="{{ $auditor->id }}">{{ ucwords($auditor->full_name) }}</option>
                @endforeach
        </select>
    </div>
    <div class="col-md-2 col-sm-6 mb-2">
        <label for="email"><strong>Status</strong></label>
        <select class="form-control select2" name="status" id="status_" style="width:100%;" required>
            <option value="all" selected>All</option>
            <option value="Pending">Pending</option>
            <option value="Pass">Pass</option>
            <option value="Fail">Fail</option>
        </select>
    </div>
</div>
