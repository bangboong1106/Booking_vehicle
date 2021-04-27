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
                            <div class="advanced form-group row">
                                <div class="col-md-12">
                                    <label for="customer_group_ids">Nhóm khách hàng</label>
                                    <div class="input-group select2-bootstrap-prepend">
                                        <select class="select2 select-customer-group" id="customer_group_ids"
                                                name="customer_group_ids" multiple>
                                            <option>Vui lòng chọn nhóm khách hàng</option>
                                        </select>
                                        {{-- <span class="input-group-addon customer-group-search"
                                              id="customer-group-search" data-type="multiple">
                                                    <div class="input-group-text bg-transparent">
                                                        <i class="fa fa-users"></i>
                                                    </div>
                                        </span> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="advanced form-group row">
                                <div class="col-md-12">
                                    <label for="customer_ids">Khách hàng</label>
                                    <div class="input-group select2-bootstrap-prepend">
                                        <select class="select2 select-customer" id="customer_ids"
                                                name="customer_ids" multiple>
                                            <option>Vui lòng chọn khách hàng</option>
                                        </select>
                                        <span class="input-group-addon customer-search"
                                              id="customer-search" data-type="multiple" data-all="1">
                                                    <div class="input-group-text bg-transparent">
                                                        <i class="fa fa-user"></i>
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
            <div class="card-box result" style="display: block;">
                <div class="container-fluid">
                    <div class="card-header" role="tab" id="headingResult">
                        <div class="panel-heading">
                            <h5 class="mb-0 mt-0 font-16">
                                <div class="row">
                                    <div class="col-md-7 row title">
                                        <span style="margin-right: 8px; margin-left: 8px"><a href="#" id="collapse"><i
                                                        class="fa fa-bars"></i></a></span>
                                        <span class="content" id="report-title">Báo cáo doanh thu theo khách hàng</span>
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
    @include('layouts.backend.elements.search._customer_search')

@endsection
@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <script>
        var comboCustomerUri = '{{route('customer.combo-customer')}}',
            comboCustomerGroupUri = '{{route('customer-group.combo-customer-group')}}',
            backendUri = '{{getBackendDomain()}}',
            reportUri = '{{route('reportCustomer.report')}}';
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
