@extends('layouts.master')

@section('title') Request Volumes @endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb_w_button')
        @slot('li_1') Miscellaneous / Request Volumes @endslot
        @slot('title') Request Volumes
            <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="REQUEST_VOLUME.showModal()"><i class="fas fa-plus"></i> Create</button>
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
                    <table id="tbl_request_volumes" class="table table-bordered table-striped table-sm nowrap w-100">
                        <thead>
                            <tr>
                                {{-- <th hidden>ID</th> --}}
                                <th>Num Pages</th>
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

    @include('pages.admin.request-volumes.volume-modal')
@endsection

@section('script')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/pdfmake.min.js') }}"></script>
@endsection

@section('custom-js')
    <script src="{{asset('scripts/request-volumes.js')}}"></script>
@endsection
