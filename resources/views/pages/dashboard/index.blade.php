@extends('layouts.master')
{{-- @inject('TaskHelper','App\Http\Helpers\TaskHelper') --}}

@section('title') Dashboard @endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables/fixedColumns.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') SLA Tracker @endslot
        @slot('title') Dashboard @endslot
    @endcomponent

    @include('pages.dashboard.filters')
    @include('pages.dashboard.counts')
    @include('pages.dashboard.charts')
    @include('pages.dashboard.tables')
@endsection
@section('script')
    <!-- Chart JS -->
    <script src="{{ URL::asset('/assets/libs/chart-js/chart-js.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/chart-js/chart-js-plugin-datalables.min.js') }}"></script>

    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.fixedColumns.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/pdfmake.min.js') }}"></script>

    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.js') }}"></script>

    <script src="{{ asset('assets/libs/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('assets/libs/daterangepicker/daterangepicker.min.js') }}"></script>
@endsection

@section('custom-js')
    <script src="{{asset('scripts/daterangepicker.init.js')}}"></script>
    <script src="{{asset('scripts/dashboard.js')}}"></script>
@endsection
