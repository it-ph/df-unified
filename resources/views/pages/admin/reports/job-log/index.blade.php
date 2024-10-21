@extends('layouts.master')

@section('title') Task Logs Report @endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Reports @endslot
        @slot('title') Task Logs @endslot
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
                        @include('pages.admin.reports.job-log.filters')
                        <button type="submit" id="btn_search" data-toggle="tooltip" title="Click to Search" class="btn btn-primary btn-sm"> <strong> <i class="fa fa-search"></i> Search</strong></button>
                        <button type="submit" id="btn_reset" data-toggle="tooltip" title="Click to Reset" class="btn btn-secondary btn-sm"> <strong> <i class="fa fa-refresh"></i> Reset</strong></button>
                        <button type="submit" id="btn_export" data-toggle="tooltip" title="Click to Export Report" class="btn btn-success btn-sm"> <strong> <i class="fa fa-download"></i> Export</strong></button>
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
                                <th>Task Name</th>
                                @if(in_array('admin',$user['roles']))<th>Client Name</th>@endif
                                <th>Status</th>
                                <th> <i class="fa fa-history"></i></th>
                                <th>Site ID</th>
                                <th>Platform</th>
                                <th>Designer</th>
                                <th>Designer</th>
                                <th>Type of Request</th>
                                <th>Num Pages</th>
                                <th>SLA Agreed</th>
                                <th>SLA Missed</th>
                                <th>SLA Miss Reason</th>
                                <th>Time Taken</th>
                                <th>QC Round</th>
                                <th>Salesforce Link</th>
                                <th>Special Request</th>
                                <th>Comments for Special Request</th>
                                <th>Additional Comments</th>
                                <th>Template Followed</th>
                                <th>Any Issue with Template</th>
                                <th>Comments for Issue in Template</th>
                                <th>Automation Recommended</th>
                                <th>Comments for Automation Recommendation</th>
                                <th>Image(s) used from Localstock</th>
                                <th>Image(s) provided by customer</th>
                                <th>Num of new images used</th>
                                <th>Shared Folder Location</th>
                                <th>Designer Comments</th>
                                <th>Internal Quality</th>
                                <th>External Quality</th>
                                <th>Comments for External Quality</th>
                                <th>Created On</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Closed On</th>
                                <th>Created By</th>
                                <th>Created By</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('pages.admin.jobs.history-modal')
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
    <script src="{{asset('scripts/reports-jobs.js')}}"></script>
@endsection
