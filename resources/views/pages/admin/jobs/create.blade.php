@extends('layouts.master')

@section('title') Add Task @endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables/fixedColumns.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .dataTables_scrollBody thead tr[role="row"]{
            visibility: collapse !important;
        }
    </style>
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Task @endslot
        @slot('title') Add Task @endslot
    @endcomponent

    <div class="row">
        <div class="col-md-12">
            @include('notifications.success')
            @include('notifications.error')
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="addJobForm" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row mb-2 @if(!in_array('admin',$user['roles'])) client_hide @endif">
                                    <label for="client_id" class="col-sm-2 col-form-label fw-bold">Client <strong><span class="important">*</span></strong></label>
                                    <div class="col-sm-10">
                                        <select class="form-control select2" name="client_id" id="client_id" style="width:100%;">
                                            <option value=""disabled selected>-- Select Client -- </option>
                                                @foreach ($clients as $client)
                                                    <option value="{{ $client->id }}" @if($client->id == auth()->user()->client_id) selected @endif>{{ ucwords($client->name) }}</option>
                                                @endforeach
                                        </select>
                                        <label id="client_idError" class="error" style="display:none"></label>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <input type="hidden" name="edit_id" id="edit_id">
                                    <label for="account_name" class="col-sm-2 col-form-label fw-bold">Account Name <strong><span class="important">*</span></strong></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="account_name" id="account_name" placeholder="Enter Account Name">
                                        <label id="account_nameError" class="error" style="display:none"></label>
                                    </div>
                                </div>

                                <div class="form-group row mb-2">
                                    <label for="account_no" class="col-sm-2 col-form-label fw-bold">Account No. <strong><span class="important">*</span></strong></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="account_no" id="account_no" placeholder="Enter Account No">
                                        <label id="account_noError" class="error" style="display:none"></label>
                                    </div>
                                </div>

                                <div class="form-group row mb-2">
                                    <label for="site_id" class="col-sm-2 col-form-label fw-bold">Site ID <strong><span class="important">*</span></strong></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="site_id" id="site_id" placeholder="Enter Site ID">
                                        <label id="site_idError" class="error" style="display:none"></label>
                                    </div>
                                </div>

                                {{-- <div class="form-group row mb-2">
                                    <label for="platform" class="col-sm-2 col-form-label fw-bold">Platform <strong><span class="important">*</span></strong></label>
                                    <div class="col-sm-10">
                                        <select class="form-control select2" name="platform" id="platform">
                                            <option value="Duda" selected>Duda</option>
                                            <option value="Wordpress" >Wordpress</option>
                                        </select>
                                        <label id="platformError" class="error" style="display:none"></label>
                                    </div>
                                </div> --}}

                                <div class="form-group row mb-2">
                                    <label for="platform" class="col-sm-2 col-form-label fw-bold">Designer <strong><span class="important">*</span></strong></label>
                                    <div class="col-sm-10">
                                        <select class="form-control select2" name="platform" id="platform"  style="width:100%;">
                                            <option value="Duda" selected>Duda</option>
                                            <option value="Wordpress" >Wordpress</option>
                                        </select>
                                        <label id="platformError" class="error" style="display:none"></label>
                                    </div>
                                </div>

                                <div class="form-group row mb-2">
                                    <label for="developer_id" class="col-sm-2 col-form-label fw-bold">Designer <strong><span class="important">*</span></strong></label>
                                    <div class="col-sm-10">
                                        <select class="form-control select2" name="developer_id" id="developer_id"  style="width:100%;">
                                            <option value=""disabled selected>-- Select Designer -- </option>
                                        </select>
                                        <label id="developer_idError" class="error" style="display:none"></label>
                                    </div>
                                </div>

                                <div class="form-group row mb-2">
                                    <label for="request_type_id" class="col-sm-2 col-form-label fw-bold">Type of Request <strong><span class="important">*</span></strong></label>
                                    <div class="col-sm-10">
                                        <select class="form-control select2 sla" name="request_type_id" id="request_type_id"  style="width:100%;">
                                            <option value=""disabled selected>-- Select Type of Request -- </option>
                                                @foreach ($request_types as $request_type)
                                                    @if($request_type)
                                                        <option value="{{ $request_type->id }}">{{ ucwords($request_type->name) }}</option>
                                                    @endif
                                                @endforeach
                                        </select>
                                        <label id="request_type_idError" class="error" style="display:none"></label>
                                    </div>
                                </div>

                                <div class="form-group row mb-2">
                                    <label for="request_volume_id" class="col-sm-2 col-form-label fw-bold">Num Pages <strong><span class="important">*</span></strong></label>
                                    <div class="col-sm-10">
                                        <select class="form-control select2 sla" name="request_volume_id" id="request_volume_id"  style="width:100%;">
                                            <option value=""disabled selected>-- Select Num Pages -- </option>
                                                @foreach ($request_volumes as $request_volume)
                                                    @if($request_volume)
                                                        <option value="{{ $request_volume->id }}">{{ ucwords($request_volume->name) }}</option>
                                                    @endif
                                                @endforeach
                                        </select>
                                        <label id="request_volume_idError" class="error" style="display:none"></label>
                                    </div>
                                </div>

                                <div class="form-group row mb-2">
                                    <label for="agreed_sla" class="col-sm-2 col-form-label fw-bold">Agreed SLA <strong><span class="important">*</span></strong></label>
                                    <div class="col-sm-10">
                                        <input type="hidden" class="form-control" name="request_sla_id" id="request_sla_id">
                                        <input type="text" readonly class="form-control" name="agreed_sla" id="agreed_sla" placeholder="Based on selected Type of Request and Num Pages.">
                                        <label id="agreed_slaError" class="error" style="display:none"></label>
                                    </div>
                                </div>

                                <div class="form-group row mb-2">
                                    <label for="addon_comments" class="col-sm-2 col-form-label fw-bold">Additional Comments <strong><span class="important">*</span></strong></label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" name="addon_comments" id="addon_comments" cols="30" rows="4" placeholder="Enter additional comments."></textarea>
                                        <label id="addon_commentsError" class="error" style="display:none"></label>
                                    </div>
                                </div>
                                <hr>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" id="btn_save" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                                    <button type="button" id="btn_cancel" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.fixedColumns.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.js') }}"></script>
@endsection

@section('custom-js')
    <script src="{{asset('scripts/create-job.js')}}"></script>
@endsection
