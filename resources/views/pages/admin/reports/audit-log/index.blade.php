@extends('layouts.master')

@section('title') Audit Logs Report @endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Reports @endslot
        @slot('title') Audit Logs @endslot
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
                    <form id="report_form" method="POST">
                        @csrf
                        @include('pages.admin.reports.audit-log.filters')
                        <button type="submit" id="btn_search" data-toggle="tooltip" title="Click to Download Report" class="btn btn-primary btn-sm"> <strong> <i class="fa fa-search"></i> Search</strong></button>
                        <button type="submit" id="btn_reset" data-toggle="tooltip" title="Click to Download Report" class="btn btn-secondary btn-sm"> <strong> <i class="fa fa-refresh"></i> Reset</strong></button>
                        <button type="submit" id="btn_export" data-toggle="tooltip" title="Click to Download Report" class="btn btn-success btn-sm"> <strong> <i class="fa fa-download"></i> Export</strong></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row ihide">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="tbl_jobs" class="table table-bordered table-striped table-sm nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Job Name</th>
                                @if(in_array('admin',$user['roles']))<th>Client Name</th>@endif
                                <th>Site ID</th>
                                <th>Platform</th>
                                <th>Developer</th>
                                <th>Developer</th>
                                <th>Type of Request</th>
                                <th>Num Pages</th>
                                <th>Preview Link</th>
                                <th>Self QC Performed</th>
                                <th>Developer Comment</th>
                                <th>Time Taken</th>
                                <th>QC Round</th>
                                <th>QC Auditor</th>
                                <th>QC Auditor</th>
                                <th>QC Status</th>
                                <th>Call For Rework</th>
                                <th>Num of Times</th>
                                <th>Alignment & Aesthetics</th>
                                <th>Comments for Alignment & Asthetics</th>
                                <th>Availability and Formats</th>
                                <th>Comments for Availability and Formats</th>
                                <th>Accuracy</th>
                                <th>Comments for Accuracy</th>
                                <th>Functionality</th>
                                <th>Comments for Functionality</th>
                                <th>QC Comments</th>
                                <th>QC Start Time</th>
                                <th>QC End Time</th>
                                <th>Created On</th>
                                <th>Created By</th>
                                <th>Created By</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/libs/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('assets/libs/daterangepicker/daterangepicker.min.js') }}"></script>
    <script>
        window.userRoles = @json($user['roles']);
    </script>
@endsection

@section('custom-js')
    <script src="{{asset('scripts/daterangepicker.init.js')}}"></script>
    <script src="{{asset('scripts/reports-qcs.js')}}"></script>
@endsection
