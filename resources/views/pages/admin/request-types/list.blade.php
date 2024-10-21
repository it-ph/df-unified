@extends('layouts.master')

@section('title') Request Types @endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb_w_button')
        @slot('li_1') Miscellaneous / Request Types @endslot
        @slot('title') Request Types
            <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="REQUEST_TYPE.showModal()"><i class="fas fa-plus"></i> Create</button>
        @endslot
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
                    <table id="tbl_request_types" class="table table-bordered table-striped table-sm nowrap w-100">
                        <thead>
                            <tr>
                                {{-- <th hidden>ID</th> --}}
                                <th>Name</th>
                                <th>Created By</th>
                                <th>Created By</th>
                                <th>Created On</th>
                                <th>Last Modified By</th>
                                <th>Last Modified By</th>
                                <th>Last Modified On</th>
                                <th>Status</th>
                                <th width="5%">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('pages.admin.request-types.type-modal')
@endsection

@section('script')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/pdfmake.min.js') }}"></script>
@endsection

@section('custom-js')
    <script src="{{asset('scripts/request-types.js')}}"></script>
@endsection
