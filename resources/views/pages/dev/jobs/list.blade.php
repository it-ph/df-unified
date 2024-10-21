@extends('layouts.master')

@section('title') My Jobs @endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Job @endslot
        @slot('title') My Jobs @endslot
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
                    <table id="tbl_jobs" class="table table-bordered table-striped table-sm nowrap w-100">
                        <thead>
                            <tr>
                                <th>Job Name</th>
                                <th>Type of Request</th>
                                <th>Num Pages</th>
                                <th>Special Request</th>
                                <th>Created On</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Agreed SLA</th>
                                <th>Time Taken</th>
                                <th>SLA Missed</th>
                                <th>Potential SLA Miss</th>
                                <th>Action</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                    </table>
                    {{-- <div id="div-spinner" class="text-center mt-4 mb-4">
                        <span id="loader" style="font-size: 16px"><i class="fa fa-spinner fa-spin"></i> Please wait...</span>
                    </div> --}}
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
@endsection

@section('custom-js')
    <script src="{{asset('scripts/myjobs.js')}}"></script>
@endsection
