@extends('layouts.master')

@section('title') Add Job @endsection

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
        @slot('li_1') Job @endslot
        @slot('title') Add Job @endslot
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
                                <div class="form-group row mb-2">
                                    <input type="hidden" name="edit_id" id="edit_id">
                                    <label for="name" class="col-sm-3 col-form-label"><strong>Job Name <span class="important">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Job Name">
                                        <label id="nameError" class="error" style="display:none"></label>
                                    </div>
                                </div>

                                <div class="form-group row mb-2">
                                    <label for="site_id" class="col-sm-3 col-form-label"><strong>Site ID <span class="important">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="site_id" id="site_id" placeholder="Enter Site ID">
                                        <label id="site_idError" class="error" style="display:none"></label>
                                    </div>
                                </div>

                                <div class="form-group row mb-2">
                                    <label for="platform" class="col-sm-3 col-form-label"><strong>Platform <span class="important">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="platform" id="platform">
                                            <option value=""disabled selected>-- Select Platform -- </option>
                                            <option value="Duda" >Duda</option>
                                            <option value="Wordpress" >Wordpress</option>
                                        </select>
                                        <label id="platformError" class="error" style="display:none"></label>
                                    </div>
                                </div>

                                <div class="form-group row mb-2">
                                    <label for="developer_id" class="col-sm-3 col-form-label"><strong>Developer <span class="important">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <select class="form-control select2" name="developer_id" id="developer_id"  style="width:100%;">
                                            <option value=""disabled selected>-- Select Developer -- </option>
                                                @foreach ($developers as $developer)
                                                    @if(!$developer->hasActiveJob($developer->id))
                                                        <option value="{{ $developer->id }}">{{ ucwords($developer->username) }}</option>
                                                    @endif
                                                @endforeach
                                        </select>
                                        <label id="developer_idError" class="error" style="display:none"></label>
                                    </div>
                                </div>

                                <div class="form-group row mb-2">
                                    <label for="request_type_id" class="col-sm-3 col-form-label"><strong>Type of Request <span class="important">*</span></strong></label>
                                    <div class="col-sm-9">
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
                                    <label for="request_volume_id" class="col-sm-3 col-form-label"><strong>Num Pages <span class="important">*</span></strong></label>
                                    <div class="col-sm-9">
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
                                    <label for="agreed_sla" class="col-sm-3 col-form-label"><strong>Agreed SLA <span class="important">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <input type="hidden" class="form-control" name="request_sla_id" id="request_sla_id">
                                        <input type="text" readonly class="form-control" name="agreed_sla" id="agreed_sla" placeholder="Based on selected Type of Request and Num Pages.">
                                        <label id="agreed_slaError" class="error" style="display:none"></label>
                                    </div>
                                </div>

                                <div class="form-group row mb-2">
                                    <label for="salesforce_link" class="col-sm-3 col-form-label"><strong>Salesforce Link <span class="important">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="salesforce_link" id="salesforce_link" placeholder="Enter Salesforce Link">
                                        <label id="salesforce_linkError" class="error" style="display:none"></label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="is_special_request" class="col-sm-3 col-form-label"><strong>Special Request <span class="important">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="is_special_request" id="is_special_request_yes" value="1">
                                            <label class="form-check-label" for="is_special_request_yes">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="is_special_request" id="is_special_request_no" value="0">
                                            <label class="form-check-label" for="is_special_request_no">No</label>
                                        </div><br>
                                        <label id="is_special_requestError" class="error" style="display:none"></label>
                                    </div>
                                </div>

                                <div class="form-group row mb-2">
                                    <label for="comments" class="col-sm-3 col-form-label"><strong>Comments for Special Request <span class="important">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" name="comments" id="comments" cols="30" rows="4" placeholder="Enter comments for special request."></textarea>
                                        <label id="commentsError" class="error" style="display:none"></label>
                                    </div>
                                </div>

                                <div class="form-group row mb-2">
                                    <label for="addon_comments" class="col-sm-3 col-form-label"><strong>Additional Comments <span class="important">*</span></strong></label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" name="addon_comments" id="addon_comments" cols="30" rows="4" placeholder="Enter additional comments."></textarea>
                                        <label id="addon_commentsError" class="error" style="display:none"></label>
                                    </div>
                                </div>
                            </div>
                            <hr>
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
