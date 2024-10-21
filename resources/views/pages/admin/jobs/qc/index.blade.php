@extends('layouts.master')

@section('title') Quality Check @endsection

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
        @slot('title') Quality Check @endslot
    @endcomponent

    <div class="row">
        <div class="col-md-12">
            @include('notifications.success')
            @include('notifications.error')
        </div>
    </div>

    @include('pages.admin.jobs.qc.job-details')

    {{-- theauditor w/ Pending status --}}
    @if(auth()->user()->id == $job['auditor_id'] && $job['qc_status'] == 'Pending')
        @include('pages.admin.jobs.qc.qc-details')
        @include('pages.admin.jobs.qc.submit-feedback')
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
    <script src="{{asset('scripts/viewqcjob.js')}}"></script>
@endsection
