@extends('layouts.master')

@section('title') Upload TaskLists @endsection

@section('css')

@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Upload Tasks @endslot
        @slot('title') Upload Tasks @endslot
    @endcomponent

    <div class="row">
        <div class="col-md-12">
            @include('notifications.success')
            @include('notifications.error')
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card" >
                <div class="card-body p-b-20">
                    <h4><strong><i class="fa fa-file-excel" style="color: #04B381;"></i>  UPLOAD TASKS</strong></h4>
                    <hr>
                    <div class="mb-2" style="float: right;">
                        <a href="{{ url('tasks-upload-task-template') }}">
                            <button class="btn btn-primary btn-sm mt-2"  data-toggle="tooltip" title="Click to Download Upload Template"><i class="fa fa-download"></i> Template</button>
                        </a>
                    </div>

                    <form action="{{ route('tasks-import') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="file" class="form-control" name="import_file" required><br>
                        <div style="float: right;">
                            <button class="btn btn-primary" onclick="return confirm('Confirm Tasks Upload?')"><strong> <i class="fa fa-upload"></i> UPLOAD </strong></button>
                        </div>
                    </form>
            </div>
        </div>
    </div>

@endsection

@section('script')

@endsection
