@extends('layouts.backend.layouts.main')
@section('content')
    <div class="row">
        <div class="col-md-12 row">
            <div class="card-box filter">
                <div class="container-fluid">
                    <div class="card-header" role="tab" id="headingInformation">
                        <div class="panel-heading">
                            <h5 class="mb-0 mt-0 font-16">
                                Tham số báo cáo
                            </h5>
                        </div>
                    </div>
                    <div id="collapseInformation" class="collapse show" role="tabpanel"
                         aria-labelledby="headingOne"
                         style="">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="">Loại báo cáo</label>
                                </div>
                            </div>
                            <div class="row display-type">
                                <div class="col-md-6">
                                    <div class="radio radio-info form-check-inline">
                                        <input type="radio" id="by_performance" value="1"
                                               name="displayType" checked>
                                        <label for="by_performance">Năng suất xe</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="radio radio-info form-check-inline">
                                        <input type="radio" id="by_quality" value="2"
                                               name="displayType">
                                        <label for="by_quality">Chất lượng dịch vụ</label>
                                    </div>
                                </div>
                            </div>
                            @if(Auth::user()->role == 'admin')
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label for="partner_id">Đối tác</label>
                                        <div class="input-group select2-bootstrap-prepend">
                                            <select class="select2 select-partner" id="partner_id"
                                                    name="partner_id" multiple>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="advanced form-group row">
                                <div class="col-md-12">
                                    <label for="vehicle_team_ids">Đội tài xế</label>
                                    <div class="input-group select2-bootstrap-prepend">
                                        <select class="select2 select-vehicle-team" id="vehicle_team_ids"
                                                name="vehicle_team_ids" multiple>
                                            <option>Vui lòng chọn đội tài xế</option>
                                        </select>
                                        <span class="input-group-addon vehicle-team-search"
                                              id="secondary-vehicle-team-search" data-type="multiple">
                                                    <div class="input-group-text bg-transparent">
                                                        <i class="fa fa-users"></i>
                                                    </div>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="advanced form-group row">
                                <div class="col-md-12">
                                    <label for="vehicle_ids">Xe</label>
                                    <div class="input-group select2-bootstrap-prepend">
                                        <select class="select2 select-vehicle" id="vehicle_ids"
                                                name="vehicle_ids" multiple>
                                            <option>Vui lòng chọn xe</option>
                                        </select>
                                        <span class="input-group-addon vehicle-search"
                                              id="secondary-vehicle-search" data-type="multiple" data-all="1">
                                                    <div class="input-group-text bg-transparent">
                                                        <i class="fa fa-truck"></i>
                                                    </div>
                                                </span>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-12" id="wrap-day-condition">
                                    <label for="">Thống kê theo loại thời gian</label>
                                    <div class="input-group">
                                        <select class="select2" id="dayCondition">
                                            <option value="1" {{$dayCondition == 1 ? "selected":""}}>Thời
                                                gian nhận hàng dự kiến
                                            </option>
                                            <option value="2" {{$dayCondition == 2 ? "selected":""}}>Thời
                                                gian nhận hàng thực tế
                                            </option>
                                            <option value="3" {{$dayCondition == 3 ? "selected":""}}>Thời
                                                gian trả hàng dự kiến
                                            </option>
                                            <option value="4" {{$dayCondition == 4 ? "selected":""}}>Thời
                                                gian trả hàng thực tế
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Thời gian</label>
                                    <div id="reportrange" class="pull-right form-control">
                                        <span></span>
                                        <i class="pull-right glyphicon-calendar fa fa-calendar"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-blue" id="btnApply" style="width: 120px">Áp dụng</button>
                                    <button class="btn btn-default" id="btnDefault">Về mặc định</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-box result">
                <div class="container-fluid">
                    <div class="card-header" role="tab" id="headingResult">
                        <div class="panel-heading">
                            <h5 class="mb-0 mt-0 font-16">
                                <div class="row">
                                    <div class="col-md-7 row title">
                                        <span style="margin-right: 8px; margin-left: 8px"><a href="#" id="collapse"><i
                                                        class="fa fa-bars"></i></a></span>
                                        <span class="content" id="report-title">Báo cáo năng suất xe</span>
                                        <span class="parameter"></span>

                                    </div>
                                    <div class="col-md-5">
                                        <div class="pull-right">
                                            <a class="export-report" href="#">
                                                <i class="fa fa-download"></i>Tải file
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </h5>
                        </div>

                    </div>
                    <div id="collapseResult" class="collapse show" role="tabpanel"
                         aria-labelledby="headingResult"
                         style="">
                        <div class="card-body">
                            <div class="row report-content table-scroll">

                            </div>

                            <div class="empty-box">
                                <span><i>Không thể tìm thấy dữ liệu trên chương trình</i></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
    @include('layouts.backend.elements.search._vehicle_search')
    @include('layouts.backend.elements.search._vehicle_team_search')

@endsection
@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <script>
        var comboVehiclesUri = '{{route('vehicle.combo-vehicle')}}',
            comboVehicleTeamUri = '{{route('vehicle-team.combo-vehicle-team')}}',
            comboPartnerUri = '{{route('partner.combo-partner')}}',
            backendUri = '{{getBackendDomain()}}',
            reportUri = '{{route('report-vehicle.report')}}';
    </script>

    <?php
    $jsFiles = [
        'vendor/jszip.min',
        'vendor/FileSaver.min',
        'autoload/object-select2',
        'autoload/report_utility'
    ]
    ?>
    {!! loadFiles($jsFiles, $area, 'js') !!}
@endpush
