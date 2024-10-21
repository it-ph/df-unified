<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="" method="POST">
                    @csrf
                    <div class="row">
                        @php
                            $isAdmin = in_array('admin',$user['roles']);
                        @endphp
                        <div class="@if(!$isAdmin) col-md-3 @else col-md-4 @endif mb-2">
                            <label for="datefilter"><strong>Date Range</strong></label>
                            <div id="datefilter" class="form-control datefilter filters">
                                <i class="fa fa-calendar"></i>&nbsp;<span></span>
                            </div>
                            <input type="hidden" class="form-control" name="daterange" id="date_range">
                        </div>
                        <div class="col-md-2 mb-2 @if(!$isAdmin) client_hide @endif">
                            <label for="Client"><strong>Client</strong></label>
                            <select class="form-control select2 filters" name="client_id" id="client_id" style="width:100%;">
                                <option value="all" selected>All</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" @if($client->id == auth()->user()->client_id) selected @endif>{{ ucwords($client->name) }}</option>
                                    @endforeach
                            </select>
                            <label id="client_idError" class="error" style="display:none"></label>
                        </div>
                        <div class="@if(!$isAdmin) col-md-3 @else col-md-2 @endif mb-2">
                            <label for="platform"><strong>Platform</strong></label>
                            <select class="form-control select2 filters" name="platform" id="platform" style="width:100%;">
                                <option value="all" selected>All</option>
                                <option value="Duda">Duda</option>
                                <option value="Wordpress">Wordpress</option>
                            </select>
                        </div>
                        <div class="@if(!$isAdmin) col-md-3 @else col-md-2 @endif mb-2">
                            <label for="request_type_id"><strong>Request Type</strong></label>
                            <select class="form-control select2 filters" name="request_type_id" id="request_type_id" style="width:100%;">
                                <option value="all" selected>All</option>
                                    @foreach ($request_types as $request_type)
                                        @if($request_type)
                                            <option value="{{ $request_type->id }}">{{ ucwords($request_type->name) }}</option>
                                        @endif
                                    @endforeach
                            </select>
                        </div>
                        <div class="@if(!in_array('admin',$user['roles'])) col-md-3 @else col-md-2 @endif mb-2">
                            @if(in_array('admin',$user['roles']) || in_array('client',$user['roles']) || in_array('manager',$user['roles']) || in_array('team lead',$user['roles']) || in_array('auditor',$user['roles']))
                                <label for="developer_id"><strong>Developer</strong></label>
                                <select class="form-control select2 filters" name="developer_id" id="developer_id" style="width:100%;">
                                    <option value="all" selected>All</option>
                                        @foreach ($devs as $dev)
                                            <option value="{{ $dev->id }}">{{ ucwords($dev->full_name) }}</option>
                                        @endforeach
                                </select>
                            @else
                                <input type="hidden" class="form-control" name="developer_id" id="developer_id" value="{{ auth()->user()->id }}">
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
