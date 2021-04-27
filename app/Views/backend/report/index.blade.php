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
                            <ul class="nav nav-pills navtab-bg nav-justified entity">
                                <li class="nav-item">
                                    <a href="#vehicle" data-toggle="tab" aria-expanded="false" class="nav-link active"
                                       data-entity=2>
                                        Xe
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#vehicle-team" data-toggle="tab" aria-expanded="true"
                                       class="nav-link"
                                       data-entity=1>
                                        Đội xe
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#driver" data-toggle="tab" aria-expanded="false" class="nav-link"
                                       data-entity=3>
                                        Tài xế
                                    </a>
                                </li>
                                @if(Auth::user()->role == 'admin')
                                    <li class="nav-item">
                                        <a href="#customer" data-toggle="tab" aria-expanded="false" class="nav-link"
                                        data-entity=4>
                                            Khách Hàng
                                        </a>
                                    </li>
                                @endif
                            </ul>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="">Hiển thị báo cáo theo</label>
                                </div>
                            </div>
                            <div class="row display-type">
                                <div class="col-md-6">
                                    <div class="radio radio-info form-check-inline">
                                        <input type="radio" id="by_order" value="1"
                                               name="displayType" checked>
                                        <label for="by_order">Đơn hàng</label>
                                    </div>
                                </div>
                                @canany(['view revenue'])
                                <div class="col-md-6">
                                    <div class="radio radio-info form-check-inline">
                                        <input type="radio" id="by_income" value="2"
                                               name="displayType">
                                        <label for="by_income">Doanh thu</label>
                                    </div>
                                </div>
                                @endcanany
                                @canany(['view cost'])
                                <div class="col-md-6">
                                    <div class="radio radio-info form-check-inline">
                                        <input type="radio" id="by_cost" value="3" name="displayType">
                                        <label for="by_cost">Chi phí</label>
                                    </div>
                                </div>
                                @endcanany
                                @canany(['view revenue'])
                                <div class="col-md-6">
                                    <div class="radio radio-info form-check-inline">
                                        <input type="radio" id="by_profit" value="4" name="displayType">
                                        <label for="by_profit">Lợi nhuận</label>
                                    </div>
                                </div>
                                @endcanany
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
                                              id="secondary-vehicle-team-search" data-type="multiple" data-all="1">
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
                                              id="secondary-vehicle-search" data-type="multiple" {{Auth::user()->role == 'admin' ? 'data-all="1"' : ""}}>
                                                    <div class="input-group-text bg-transparent">
                                                        <i class="fa fa-truck"></i>
                                                    </div>
                                                </span>
                                    </div>
                                </div>

                            </div>
                            <div class="advanced form-group row">
                                <div class="col-md-12">
                                    <label for="driver_ids">Tài xế</label>
                                    <div class="input-group select2-bootstrap-prepend">
                                        <select class="select2 select-driver" id="driver_ids"
                                                name="driver_ids" multiple>
                                            <option>Vui lòng chọn tài xế</option>
                                        </select>
                                        <span class="input-group-addon driver-search"
                                              id="secondary-driver-search" data-type="multiple">
                                                    <div class="input-group-text bg-transparent">
                                                        <i class="fa fa-id-card"></i>
                                                    </div>
                                                </span>
                                    </div>
                                </div>
                            </div>
                            @if(Auth::user()->role == 'admin')
                                <div class="advanced form-group row">
                                    <div class="col-md-12">
                                        <label for="customer_ids">Khách hàng</label>
                                        <div class="input-group select2-bootstrap-prepend">
                                            <select class="select2 select-customer" id="customer_ids"
                                                    name="customer_ids" multiple>
                                                <option>Vui lòng chọn khách hàng</option>
                                            </select>
                                            <span class="input-group-addon customer-search"
                                                id="secondary-customer-search" data-type="multiple">
                                                        <div class="input-group-text bg-transparent">
                                                            <i class="fa fa-user-circle"></i>
                                                        </div>
                                                    </span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="row lbl-wrap-status">
                                <div class="col-md-12">
                                    <label>Tình trạng đơn hàng</label>
                                </div>
                            </div>
                            <div class="row wrap-status">
                                <div id="wrapper-status">
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn border-orange text-orange active">
                                            <input type="checkbox" autocomplete="off" data-status=1 checked>Chưa
                                            hoàn
                                            thành
                                        </label>
                                        <label class="btn border-success text-success active">
                                            <input type="checkbox" autocomplete="off" data-status=2 checked>Đã hoàn
                                            thành
                                        </label>
                                        <label class="btn border-dark text-dark active">
                                            <input type="checkbox" autocomplete="off" data-status=6 checked>Đã
                                            hủy
                                        </label>
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
                                <div class="col-md-12">
                                    <label for="">Thống kê theo</label>
                                    <div class="input-group">
                                        <select class="select2" id="statistic">
                                            <option value="day">Ngày</option>
                                            {{--<option value="week">Tuần</option>--}}
                                            <option value="month">Tháng</option>
                                            {{--<option value="quarter">Quý</option>--}}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12" id="wrap-day-condition" style="display:none;">
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
                                    <div class="col-md-10 row title">
                                        <span style="margin-right: 8px; margin-left: 8px"><a href="#" id="collapse"><i
                                                        class="fa fa-bars"></i></a></span>
                                        <span class="content">Báo cáo tình trạng</span>
                                        <span class="parameter"></span>
                                    </div>
                                    <div class="col-md-2">
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
    @include('layouts.backend.elements.search._driver_search')
    @include('layouts.backend.elements.search._customer_search')
    @include('layouts.backend.elements.search._vehicle_team_search')

@endsection
@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <script>
        var comboVehicleUri = '{{route('vehicle.combo-vehicle')}}?all=1',
            comboDriverUri = '{{route('driver.combo-driver')}}?all=1',
            comboCustomerUri = '{{route('customer.combo-customer')}}',
            comboVehicleTeamUri = '{{route('vehicle-team.combo-vehicle-team')}}',
            backendUri = '{{getBackendDomain()}}',
            reportUri = '{{route('report.getReportData')}}',
            comboPartnerUri = '{{route('partner.combo-partner')}}';
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
