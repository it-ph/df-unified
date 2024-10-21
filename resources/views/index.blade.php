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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label for="datefilter"><strong>Date Range</strong></label>
                                <div id="datefilter" class="form-control datefilter">
                                    <span></span>
                                </div>
                                <input type="hidden" class="form-control" name="daterange" id="daterange">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="platform"><strong>Platform</strong></label>
                                <select class="form-control select2" name="platform" id="platform"  style="width:100%;">
                                    <option value="all" selected>All</option>
                                    <option value="Duda">Duda</option>
                                    <option value="Wordpress">Wordpress</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="request_type_id"><strong>Request Type</strong></label>
                                <select class="form-control select2" name="request_type_id" id="request_type_id"  style="width:100%;">
                                    <option value="all" selected>All</option>
                                        @foreach ($request_types as $request_type)
                                            @if($request_type)
                                                <option value="{{ $request_type->id }}">{{ ucwords($request_type->name) }}</option>
                                            @endif
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="developer_id"><strong>Developer</strong></label>
                                <select class="form-control select2" name="developer_id" id="developer_id"  style="width:100%;">
                                    <option value="all" selected>All</option>
                                        @foreach ($developers as $developer)
                                            <option value="{{ $developer->id }}">{{ ucwords($developer->username) }}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="row">
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body">
                                {{-- <a href="{{ route("my-tasks.index", ['status' => "In Progress"]) }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="View In Progress Tasks">
                                    <p class="text-muted fw-medium">In Progress</p>
                                    <h4 class="mb-0">{{ number_format($in_progress) }}</h4>
                                </a> --}}
                            </div>

                            <div class="avatar-sm rounded-circle bg-success align-self-center mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-success">
                                    <i class="bx bx-task font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body">
                                {{-- <a href="{{ route("my-tasks.index", ['status' => "On Hold"]) }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="View On Hold Tasks">
                                    <p class="text-muted fw-medium">On Hold</p>
                                    <h4 class="mb-0">{{ number_format($on_hold) }}</h4>
                                </a> --}}
                            </div>

                            <div class="avatar-sm rounded-circle bg-warning align-self-center mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-warning">
                                    <i class="bx bx-task font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body">
                                {{-- <a href="{{ route("my-tasks.index", ['status' => "Completed"]) }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="View Completed Tasks">
                                    <p class="text-muted fw-medium">Completed</p>
                                    <h4 class="mb-0">{{ number_format($completed) }}</h4>
                                </a> --}}
                            </div>

                            <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-primary">
                                    <i class="bx bx-task font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body">
                                {{-- <a href="{{ route("my-tasks.index", ['status' => "all"]) }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="View All Tasks">
                                    <p class="text-muted fw-medium">Total Tasks</p>
                                    <h4 class="mb-0">{{ number_format($all) }}</h4>
                                </a> --}}
                            </div>

                            <div class="avatar-sm rounded-circle bg-secondary align-self-center mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-secondary">
                                    <i class="bx bx-task font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <span class="card-title text-center mb-2">Jobs Closed By Request Type</span>
                    <div id="donut_chart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-center mb-2">SLA Summary</h6>
                    <div id="donut_chart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-center mb-2">QC Rounds</h6>
                    <div id="donut_chart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-4">Line Chart</h4>

                    <div class="row text-center">
                        <div class="col-4">
                            <h5 class="mb-0">86541</h5>
                            <p class="text-muted text-truncate">Activated</p>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0">2541</h5>
                            <p class="text-muted text-truncate">Pending</p>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0">102030</h5>
                            <p class="text-muted text-truncate">Deactivated</p>
                        </div>
                    </div>

                    <canvas id="lineChart" height="300"></canvas>

                </div>
            </div>
        </div> <!-- end col -->

        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-4">Bar Chart</h4>

                    <div class="row text-center">
                        <div class="col-4">
                            <h5 class="mb-0">2541</h5>
                            <p class="text-muted text-truncate">Activated</p>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0">84845</h5>
                            <p class="text-muted text-truncate">Pending</p>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0">12001</h5>
                            <p class="text-muted text-truncate">Deactivated</p>
                        </div>
                    </div>

                    <canvas id="bar" height="300"></canvas>

                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->


    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-4">Pie Chart</h4>

                    <div class="row text-center">
                        <div class="col-4">
                            <h5 class="mb-0">2536</h5>
                            <p class="text-muted text-truncate">Activated</p>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0">69421</h5>
                            <p class="text-muted text-truncate">Pending</p>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0">89854</h5>
                            <p class="text-muted text-truncate">Deactivated</p>
                        </div>
                    </div>

                    <canvas id="pie" height="260"></canvas>

                </div>
            </div>
        </div> <!-- end col -->

        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-4">Donut Chart</h4>

                    <div class="row text-center">
                        <div class="col-4">
                            <h5 class="mb-0">9595</h5>
                            <p class="text-muted text-truncate">Activated</p>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0">36524</h5>
                            <p class="text-muted text-truncate">Pending</p>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0">62541</h5>
                            <p class="text-muted text-truncate">Deactivated</p>
                        </div>
                    </div>

                    <canvas id="doughnut" height="260"></canvas>

                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection
@section('script')
    <!-- apexcharts -->
    <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <!-- dashboard init -->
    <script src="{{ URL::asset('/assets/js/pages/apexcharts.init.js') }}"></script>

    <!-- Chart JS -->
    <script src="{{ URL::asset('/assets/libs/chart-js/chart-js.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/chartjs.init.js') }}"></script>

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
    <script type="text/javascript">
        $(function() {
            var start = moment().subtract(6, 'days');
            var end = moment();

            function cb(start, end) {
                $('#datefilter span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                $('#daterange').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#datefilter').daterangepicker({
                buttonClasses: ['btn', 'btn-sm'],
                applyClass: 'btn-primary',
                cancelClass: 'btn-danger',
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            cb(start, end);

        });
    </script>
@endsection
