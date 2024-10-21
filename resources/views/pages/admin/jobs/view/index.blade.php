@extends('layouts.master')

@section('title') View Job @endsection

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

    @component('components.breadcrumb_w_button')
        @slot('li_1') View Job @endslot
        @slot('title') View Job
            @if(auth()->user()->id == $job['developer_id'] && $job['status'] == 'Not Started')
                <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" title="Start Job" onclick="JOB.showStartModal()"></i> Start Job</button>
            @endif
            @if(auth()->user()->id == $job['developer_id'] && ($job['status'] == 'In Progress' || $job['status'] == 'Bounce Back'))
                <button type="button" class="btn btn-warning btn-sm waves-effect waves-light" title="Pause Job" onclick="JOB.showPauseModal()"></i> Pause Job</button>
            @endif
            @if(auth()->user()->id == $job['developer_id'] && $job['status'] == 'On Hold')
                <button type="button" class="btn btn-warning btn-sm waves-effect waves-light" title="Resume Job" onclick="JOB.showResumeModal()"></i> Resume Job</button>
            @endif
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-md-12">
            @include('notifications.success')
            @include('notifications.error')
        </div>
    </div>

    {{-- qc history --}}
    {{-- @if((in_array('admin',$user['roles']) || in_array('team lead',$user['roles']) || in_array('manager',$user['roles'])) && $job['status'] == 'Closed') --}}
    @if($job['audit_logs'])
        @include('pages.admin.jobs.view.qc-history')
    @endif

    {{-- job details --}}
    @include('pages.admin.jobs.view.job-details')

    {{-- start modal if assigned dev and status Not Started --}}
    @if(auth()->user()->id == $job['developer_id'] && $job['status'] == 'Not Started')
        @include('pages.admin.jobs.view.start-modal')
    @endif

    {{-- pause modal if assigned dev and status In Progress/Bounce Back --}}
    @if(auth()->user()->id == $job['developer_id'] && ($job['status'] == 'In Progress' || $job['status'] == 'Bounce Back'))
        @include('pages.admin.jobs.view.pause-modal')
    @endif

    {{-- resume modal if assigned dev and status On Hold --}}
    @if(auth()->user()->id == $job['developer_id'] && $job['status'] == 'On Hold')
        @include('pages.admin.jobs.view.resume-modal')
    @endif

    {{-- submit details and wherein status In Progress --}}
    @if(auth()->user()->id == $job['developer_id'] && $job['status'] == "In Progress" && $job['dev_comments'] == null)
        @include('pages.admin.jobs.view.submit-details')
    @endif

    {{-- additional details --}}
    @if(auth()->user()->id == $job['developer_id'] && in_array($job['status'], ["In Progress","Bounce Back"]) && $job['dev_comments'])
        @include('pages.admin.jobs.view.qc-submission')
    @endif

    {{-- external quality --}}
    @if((in_array('admin',$user['roles']) || in_array('team lead',$user['roles']) || in_array('manager',$user['roles'])) && $job['status'] == 'Closed' && $job['external_quality'] == null)
        @include('pages.admin.jobs.view.external-quality')
    @endif

    {{-- sla miss reason --}}
    @if(auth()->user()->id == $job['developer_id'] && $job['status'] == 'Info Needed')
        @include('pages.admin.jobs.view.sla-miss-reason')
    @endif

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
    <script src="{{asset('scripts/viewjob.js')}}"></script>
@endsection
