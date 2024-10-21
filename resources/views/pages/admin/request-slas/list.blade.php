@extends('layouts.master')

@section('title') Request SLA @endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb_w_button')
        @slot('li_1') Miscellaneous / Request SLA @endslot
        @slot('title') Request SLA
            <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="REQUEST_SLA.showModal()"><i class="fas fa-plus"></i> Create</button>
            @if(in_array('admin',$user['roles']) || in_array('manager',$user['roles']))
                    <button type="button" id="btn_export" class="btn btn-primary btn-sm waves-effect waves-light"><i class="fas fa-download"></i> Template</button>
                    <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="REQUEST_SLA.showUploadModal()"><i class="fas fa-upload"></i> Upload</button>
            @endif
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
                    <table id="tbl_request_slas" class="table table-bordered table-striped table-sm nowrap w-100">
                        <thead>
                            <tr>
                                {{-- <th hidden>ID</th> --}}
                                <th>Request Type</th>
                                <th>Num Pages</th>
                                <th>Agreed SLA (hours)</th>
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
                    {{-- <div id="div-spinner" class="text-center mt-4 mb-4">
                        <span id="loader" style="font-size: 16px"><i class="fa fa-spinner fa-spin"></i> Please wait...</span>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    @include('pages.admin.request-slas.sla-modal')
    @include('pages.admin.request-slas.upload-modal')
@endsection

@section('script')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.js') }}"></script>
@endsection

@section('custom-js')
    <script src="{{asset('scripts/request-slas.js')}}"></script>
@endsection
