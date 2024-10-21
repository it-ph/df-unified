@extends('layouts.master')

@section('title') Task Lists @endsection

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
        @slot('li_1') Tasks @endslot
        @slot('title') Tasks List - @if(\Request::get('status')) <span>{{ ucwords(\Request::get('status')) }} @else ALL @endif</span>@endslot
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
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary waves-effect waves-light dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-expanded="false"><i class="fa fa-filter"></i> Filter <i class="mdi mdi-chevron-down"></i></button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route("tasks.index", ['status' => "all"]) }}">All Tasks</a>
                                    <a class="dropdown-item" href="{{ route("tasks.index", ['status' => "In Progress"]) }}">In Progress</a>
                                    <a class="dropdown-item" href="{{ route("tasks.index", ['status' => "On Hold"]) }}">On Hold</a>
                                    <a class="dropdown-item" href="{{ route("tasks.index", ['status' => "Completed"]) }}">Completed</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p id="status" style="display:none">@if(\Request::get('status')) {{ (\Request::get('status')) }} @else all @endif</p>
                    <table id="tbl_task" class="table table-bordered table-striped nowrap w-100">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Employee Name</th>
                                <th>Shift Date</th>
                                <th>Date Received</th>
                                <th>Cluster</th>
                                <th>Client</th>
                                <th>Client Activity</th>
                                <th>Description</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Date Completed</th>
                                <th>Actual Handling Time</th>
                                <th>Volume</th>
                                <th>Remarks</th>
                            </tr>
                            <tr>
                                <th><input type="text" class="form-control"/></th>
                                <th><input type="text" class="form-control"/></th>
                                <th><input type="text" class="form-control"/></th>
                                <th><input type="text" class="form-control"/></th>
                                <th><input type="text" class="form-control"/></th>
                                <th><input type="text" class="form-control"/></th>
                                <th><input type="text" class="form-control"/></th>
                                <th><input type="text" class="form-control"/></th>
                                <th><input type="text" class="form-control"/></th>
                                <th><input type="text" class="form-control"/></th>
                                <th><input type="text" class="form-control"/></th>
                                <th><input type="text" class="form-control"/></th>
                                <th><input type="text" class="form-control"/></th>
                                <th><input type="text" class="form-control"/></>
                            </tr>
                        </thead>
                    </table>
                    <div id="div-spinner" class="text-center mt-4 mb-4">
                        <span id="loader" style="font-size: 16px"><i class="fa fa-spinner fa-spin"></i> Please wait...</span>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
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
    <script src="{{asset('scripts/tasklists.js')}}"></script>
@endsection
