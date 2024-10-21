@extends('layouts.master')

@section('title') Clients @endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Manage @endslot
        @slot('title') Clients @endslot
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
                            <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="CLIENT.showModal()"><i class="fas fa-plus"></i> Create</button>
                        </div>
                    </div>
                    <table id="tbl_clients" class="table table-bordered table-striped table-sm nowrap w-100 text-center">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Work Shift</th>
                                <th>Work Shift</th>
                                <th>Created By</th>
                                <th>Created By</th>
                                <th>Created On</th>
                                <th>Last Modified By</th>
                                <th>Last Modified By</th>
                                <th>Last Modified On</th>
                                <th width="5%" class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('pages.admin.clients.client-modal')
@endsection

@section('script')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/pdfmake.min.js') }}"></script>
@endsection

@section('custom-js')
    <script src="{{asset('scripts/clients.js')}}"></script>
@endsection
