@extends('layouts.master')

@section('title') Pending QC @endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Job @endslot
        @slot('title') Pending QC @endslot
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
                                @if(in_array('admin',$user['roles']))<th>Client Name</th>@endif
                                <th>Type of Request</th>
                                <th>Num Pages</th>
                                <th>Special Request</th>
                                <th>Created On</th>
                                <th>Agreed SLA</th>
                                <th>Time Elapsed</th>
                                <th>SLA Missed</th>
                                <th>Developer</th>
                                <th>Developer</th>
                                <th>QC Round</th>
                                <th>Auditor</th>
                                <th>Auditor</th>
                                <th>Action</th>
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
@endsection

@section('script')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <script>
        window.userRoles = @json($user['roles']);
    </script>
@endsection

@section('custom-js')
    <script src="{{asset('scripts/pendingqc.js')}}"></script>
@endsection
