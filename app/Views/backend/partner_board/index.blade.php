@extends('layouts.backend.layouts.main')
@section('content')
    <script>
        var Application = {
            Parameters: {
                generalStatistic: {
                    fromDate: moment(),
                    toDate: moment()
                },
                incomeByTime: {
                    fromDate: moment().startOf('month'),
                    toDate: moment().endOf('month'),
                    type: 1
                },
                incomeByCustomer: {
                    fromDate: moment().startOf('month'),
                    toDate: moment().endOf('month'),
                    type: 2
                },
                turnByTime: {
                    fromDate: moment().startOf('month'),
                    toDate: moment().endOf('month'),
                    type: 3
                },
                turnByCustomer: {
                    fromDate: moment().startOf('month'),
                    toDate: moment().endOf('month'),
                    type: 4
                },
                goodsByTime: {
                    fromDate: moment().startOf('month'),
                    toDate: moment().endOf('month'),
                    type: 5
                },
            },
            /*-------------------------------------------*/
            Urls: {
                orderStatistic: '{{route('board.generalInfoOrder')}}',
                revenueStatistic: '{{route('board.generalInfoRevenue')}}',
                documentStatistic: '{{route('board.generalInfoDocument')}}',
                customerStatistic: '{{route('board.generalInfoCustomer')}}',
                incomeByTime: '{{route('board.report')}}',
                incomeByCustomer: '{{route('board.report')}}',
                turnByTime: '{{route('board.report')}}',
                turnByCustomer: '{{route('board.report')}}',
                goodsByTime: '{{route('board.report')}}',

            },
            urlCustomerDropdown: '{{route('customer.combo-customer')}}',

        }

    </script>
    <div class="content-wrapper" style="min-height: 1067px;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-sm-4">
                    <div class="card order-statistic-wrap">
                        <div class="modal" role="dialog" style="top: 20%;" id="order-statistic-modal">
                            <div class="modal-dialog modal-chart" style="width:30%;height:70%;width:400px;" chartid="1">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4>Chọn tham số</h4>
                                    </div>
                                    <div class="modal-body " style="padding: 1rem 1rem 0 1rem;">
                                        <form id="parameter-order-statistic" data-type=1>
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <label>Thời gian</label>
                                                    <div id="report-range-order-statistic"
                                                         class="pull-right form-control">
                                                        <span></span>
                                                        <i class="pull-right glyphicon-calendar fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer popup-footer">
                                        <button style="color: white; background: #05ACEB" type="button"
                                                class="btn btn-default btn-accept" id="btn-order-statistic"
                                                data-dismiss="modal">Đồng ý
                                        </button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header order-statistic-header">
                            <div class="d-flex justify-content-between">
                                <div class="title">
                                    <span class="card-title">Đơn hàng</span>
                                    <span
                                            class="order-statistic-param-info">trong ngày</span>
                                </div>
                                <div class="navigate-chart">
                                    <img src="{{public_url('/css/backend/img/icons8-refresh-80.png')}}"
                                         id="refresh-order-statistic" class="refresh-chart-button" title="Nạp">
                                    <img src="{{public_url('/css/backend/img/icons8-filter-80.png')}}" title="Tham số"
                                         data-toggle="modal" data-target="#order-statistic-modal">
                                </div>
                            </div>

                        </div>
                        <div class="order-statistic card-body">
                            <div id="chart-order-statistic-loader" style="display: none;"></div>
                            <div class="row order-statistic">
                                <div class="col-xl-12 col-md-12">
                                    <div class="card-box">
                                        <div class="row justify-content-between">
                                            <div class="col-4 align-self-center">
                                                <i class="fa"></i>
                                                <span class="general-percent">0</span>
                                                <span>(<span class="amount">0</span>)</span>
                                            </div>
                                            <div class="col-8">
                                                <div class="text-right">

                                                    <h3 class="mb-1 info" name="order"> 0 </h3>
                                                    <p class="text-muted mb-1">đơn hàng</p>
                                                </div>

                                            </div>
                                        </div>
                                        <hr/>
                                        <div class="row">
                                            <div class="col item">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <span class="badge badge-light">Khởi tạo</span>
                                                    </div>
                                                    <div class="col-8 text-right">
                                                        <span class=" item-count" data-status=1>0</span></div>
                                                </div>
                                            </div>
                                            <div class="w-100"></div>
                                            <div class="col item">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <span class="badge badge-secondary">Sẵn sàng</span>
                                                    </div>
                                                    <div class="col-8 text-right">
                                                        <span class=" item-count" data-status=2>0</span></div>
                                                </div>
                                            </div>
                                            <div class="w-100"></div>
                                            <div class="col item">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <span class="badge badge-stpink">Chờ xác nhận</span>
                                                    </div>
                                                    <div class="col-8 text-right">
                                                        <span class=" item-count" data-status=7>0</span></div>
                                                </div>
                                            </div>
                                            <div class="w-100"></div>
                                            <div class="col item">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <span class="badge badge-brown">Chờ nhận hàng</span>
                                                    </div>
                                                    <div class="col-8 text-right">
                                                        <span class=" item-count" data-status=3>0</span></div>
                                                </div>
                                            </div>
                                            <div class="w-100"></div>
                                            <div class="col item">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <span class="badge badge-blue">Đang vận chuyển</span>
                                                    </div>
                                                    <div class="col-8 text-right">
                                                        <span class=" item-count" data-status=4>0</span></div>
                                                </div>
                                            </div>
                                            <div class="w-100"></div>
                                            <div class="col item">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <span class="badge badge-success">Hoàn thành</span>
                                                    </div>
                                                    <div class="col-8 text-right">
                                                        <span class=" item-count" data-status=5>0</span></div>
                                                </div>
                                            </div>
                                            <div class="w-100"></div>
                                            <div class="col item">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <span class="badge badge-dark">Đã hủy</span>
                                                    </div>
                                                    <div class="col-8 text-right">
                                                        <span class=" item-count" data-status=6>0</span></div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div><!-- end col -->


                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="card document-statistic-wrap">
                        <div class="modal" role="dialog" style="top: 20%;" id="document-statistic-modal">
                            <div class="modal-dialog modal-chart" style="width:30%;height:70%;width:400px;">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4>Chọn tham số</h4>
                                    </div>
                                    <div class="modal-body " style="padding: 1rem 1rem 0 1rem;">
                                        <form id="parameter-document-statistic">
                                            <div class="advanced form-group row">
                                                <div class="col-md-12">
                                                    <label for="filter-customer-document-statistic-ids">Khách hàng</label>
                                                    <div class="input-group select2-bootstrap-prepend">
                                                        <select class="select2 select-customer" id="filter-customer-document-statistic-ids"
                                                                name="filter-customer-document-statistic" multiple>
                                                            <option>Vui lòng chọn khách hàng</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <label>Loại thời gian</label>
                                                    <select class="select2" id="report-day-condition-document-statistic"
                                                            name="">
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
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <label>Chu kì</label>
                                                    <select class="select2" id="report-document-statistic"
                                                            name="">
                                                        <option value="month" selected>Tháng
                                                        </option>
                                                        <option value="week">Tuần
                                                        </option>
                                                    </select>

                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer popup-footer">
                                        <button style="color: white; background: #05ACEB" type="button"
                                                class="btn btn-default btn-accept" id="btn-document-statistic"
                                                data-dismiss="modal">Đồng ý
                                        </button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header document-statistic-header">
                            <div class="d-flex justify-content-between">
                                <div class="title">
                                    <span class="card-title">Thống kê chứng từ</span>            <span
                                    class="document-statistic-param-info">trong ngày</span>
                                </div>
                                <div class="navigate-chart">
                                    <img src="{{public_url('/css/backend/img/icons8-refresh-80.png')}}"
                                         id="refresh-document-statistic" class="refresh-chart-button" title="Nạp">
                                    <img src="{{public_url('/css/backend/img/icons8-filter-80.png')}}" title="Tham số"
                                         data-toggle="modal" data-target="#document-statistic-modal">
                                </div>
                            </div>

                        </div>
                        <div class="document-statistic card-body">
                            <div id="chart-document-statistic-loader" style="display: none;"></div>
                            <div class="row document-statistic">
                                <div class="col-xl-12 col-md-12">
                                    <div class="card-box">
                                        <div class="row justify-content-between">
                                            <div class="col-4 align-self-center">
                                            </div>
                                            <div class="col-8">
                                                <div class="text-right">
                                                    <h3 class="mb-1 info" name="document"> 0 </h3>
                                                    <p class="text-muted mb-1">chứng từ</p>
                                                </div>

                                            </div>
                                        </div>
                                        <hr/>
                                        <div class="row">
                                            <div class="col item">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <span class="badge">Chứng từ chưa thu</span>
                                                    </div>
                                                    <div class="col-8 text-right">
                                                        <span class="item-count" data-document="total_not_collect">0</span></div>
                                                </div>
                                            </div>
                                            <div class="w-100"></div>
                                            <div class="col item">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <span class="badge">Chứng từ đã thu đủ</span>
                                                    </div>
                                                    <div class="col-8 text-right">
                                                        <span class="item-count" data-document="total_collect">0</span></div>
                                                </div>
                                            </div>
                                            <div class="w-100"></div>
                                            <div class="col item">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <span class="badge">Chứng từ quá hạn</span>
                                                    </div>
                                                    <div class="col-8 text-right">
                                                        <span class="item-count" data-document="total_out_of_date">0</span></div>
                                                </div>
                                            </div>
                                            <div class="w-100"></div>
                                            <div class="col item">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <span class="badge">Chứng từ đến hạn thu ngày hôm nay</span>
                                                    </div>
                                                    <div class="col-8 text-right">
                                                        <span class="item-count" data-document="total_today">0</span></div>
                                                </div>
                                            </div>
                                            <div class="w-100"></div>

                                            <div class="col item">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <span class="badge">Chứng từ đến hạn thu ngày hôm sau</span>
                                                    </div>
                                                    <div class="col-8 text-right">
                                                        <span class="item-count" data-document="total_next_day">0</span></div>
                                                </div>
                                            </div>
                                            <div class="w-100"></div>

                                            <div class="col item">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <span class="badge">Chứng từ thu đúng hạn</span>
                                                    </div>
                                                    <div class="col-8 text-right">
                                                        <span class="item-count" data-document="total_collect_on_time">0</span></div>
                                                </div>
                                            </div>
                                            <div class="w-100"></div>

                                            <div class="col item">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <span class="badge">Chứng từ thu trễ hạn</span>
                                                    </div>
                                                    <div class="col-8 text-right">
                                                        <span class="item-count" data-document="total_collect_late">0</span></div>
                                                </div>
                                            </div>
                                            <div class="w-100"></div>

                                            <div class="col item">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <span class="badge badge-danger">Tổng số ngày thu muộn</span>
                                                    </div>
                                                    <div class="col-8 text-right">
                                                        <span class="item-count" id="total_day_late">0</span></div>
                                                </div>
                                            </div>
                                            <div class="w-100"></div>
                                        </div>
                                    </div>
                                </div><!-- end col -->


                            </div>
                        </div>
                    </div>
                </div>

                @can('view revenue')
                <div class="col-12 col-sm-4">
                    <div class="card revenue-statistic-wrap">
                        <div class="modal" role="dialog" style="top: 20%;" id="revenue-statistic-modal">
                            <div class="modal-dialog modal-chart" style="width:30%;height:70%;width:400px;" chartid="1">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4>Chọn tham số</h4>
                                    </div>
                                    <div class="modal-body " style="padding: 1rem 1rem 0 1rem;">
                                        <form id="parameter-revenue-statistic" data-type=1>
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <label>Loại thời gian</label>
                                                    <select class="select2" id="report-day-condition-revenue-statistic"
                                                            name="">
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
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <label>Thời gian</label>
                                                    <div id="report-range-revenue-statistic"
                                                         class="pull-right form-control">
                                                        <span></span>
                                                        <i class="pull-right glyphicon-calendar fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer popup-footer">
                                        <button style="color: white; background: #05ACEB" type="button"
                                                class="btn btn-default btn-accept" id="btn-revenue-statistic"
                                                data-dismiss="modal">Đồng ý
                                        </button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header revenue-statistic-header">
                            <div class="d-flex justify-content-between">
                                <div class="title">
                                    <span class="card-title">Doanh thu</span>
                                    <span
                                            class="revenue-statistic-param-info">trong ngày</span>
                                </div>
                                <div class="navigate-chart">
                                    <img src="{{public_url('/css/backend/img/icons8-refresh-80.png')}}"
                                         id="refresh-revenue-statistic" class="refresh-chart-button" title="Nạp">
                                    <img src="{{public_url('/css/backend/img/icons8-filter-80.png')}}" title="Tham số"
                                         data-toggle="modal" data-target="#revenue-statistic-modal">
                                </div>
                            </div>

                        </div>
                        <div class="revenue-statistic card-body">
                            <div id="chart-revenue-statistic-loader" style="display: none;"></div>
                            <div class="row revenue-statistic">
                                <div class="col-xl-12 col-md-12" style="padding-right: 8px">
                                    <div class="card-box">

                                        <div class="row justify-content-between">
                                            <div class="col-4 align-self-center">
                                                <i class="fa"></i>
                                                <span class="general-percent">0</span>
                                                <span>(<span class="amount">0</span>)</span>
                                            </div>
                                            <div class="col-8">
                                                <div class="text-right">
                                                    <h3 class="mb-1 info" name="revenue">0 </h3>
                                                    <p class="text-muted mb-1">doanh thu</p>
                                                </div>

                                            </div>
                                        </div>
                                        <hr/>
                                        <div class="revenues row">
                                            <div class="col item">
                                                <div class="row revenues-title" style="display: none">
                                                    Top 5 đơn hàng doanh thu cao nhất
                                                </div>
                                            </div>
                                            <div class="w-100"></div>
                                        </div>
                                        <div class="revenues-content row">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card">
                        <div class="modal" role="dialog" style="top: 20%;" id="turn-by-time-modal">
                            <div class="modal-dialog modal-chart" style="width:30%;height:70%;width:400px;"
                                 chartid="3">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4>Chọn tham số biểu đồ</h4>
                                    </div>
                                    <div class="modal-body modal-parameter" style="padding: 1rem 1rem 0 1rem;">
                                        <form id="parameter-turn-by-time" data-type=3>
                                            <div class="col-md-12">
                                                <label>Thời gian</label>
                                                <div id="report-range-turn-by-time" class="pull-right form-control">
                                                    <span></span>
                                                    <i class="pull-right glyphicon-calendar fa fa-calendar"></i>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer popup-footer">
                                        <button style="color: white; background: #05ACEB" type="button"
                                                class="btn btn-default btn-accept" id="btn-turn-by-time"
                                                data-dismiss="modal">Đồng ý
                                        </button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Đóng
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title">Thống kê đơn hàng theo thời gian</h5>
                                <div class="navigate-chart">
                                    <img src="{{public_url('/css/backend/img/icons8-refresh-80.png')}}"
                                         id="refresh-turn-by-time" class="refresh-chart-button" title="Nạp">
                                    <span class="expand-chart-container"></span>
                                    <img src="{{public_url('/css/backend/img/icons8-filter-80.png')}}"
                                         title="Tham số"
                                         data-toggle="modal" data-target="#turn-by-time-modal">
                                </div>
                            </div>
                            <hr>
                            <div class="parameter-detail row">

                            </div>
                        </div>
                        <div class="card-body">
                            <div style="display:flex;justify-content:center; margin-top:0px;">
                                <div id="turn-by-time-label" style="display: block;">
                                    <label>Tổng đơn hàng: <span>0</span></label>
                                </div>
                            </div>
                            <div class="position-relative mb-4">
                                <div id="chart-turn-by-time-loader" style="display: none;"></div>
                                <div class="row" style="opacity: 1;">
                                    <canvas id="canvas-chart-turn-by-time"
                                            class="nomal-canvas chartjs-render-monitor"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @can('view revenue')
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card">
                        <div class="modal" role="dialog" style="top: 20%;" id="income-by-time-modal">
                            <div class="modal-dialog modal-chart" style="width:30%;height:70%;width:400px;"
                                 chartid="1">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4>Chọn tham số biểu đồ</h4>
                                    </div>
                                    <div class="modal-body modal-parameter" style="padding: 1rem 1rem 0 1rem;">
                                        <form id="parameter-income-by-time" data-type=1>
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <label>Loại thời gian</label>
                                                    <select class="select2" id="report-day-condition-income-by-time"
                                                            name="">
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
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <label>Thời gian</label>
                                                    <div id="report-range-income-by-time"
                                                         class="pull-right form-control">
                                                        <span></span>
                                                        <i class="pull-right glyphicon-calendar fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer popup-footer">
                                        <button style="color: white; background: #05ACEB" type="button"
                                                class="btn btn-default btn-accept" id="btn-income-by-time"
                                                data-dismiss="modal">Đồng ý
                                        </button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Đóng
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title">Thống kê doanh thu/chi phí theo thời gian</h5>
                                <div class="navigate-chart">
                                    <img src="{{public_url('/css/backend/img/icons8-refresh-80.png')}}"
                                         id="refresh-income-by-time" class="refresh-chart-button" title="Nạp">
                                    <span class="expand-chart-container"></span>
                                    <img src="{{public_url('/css/backend/img/icons8-filter-80.png')}}"
                                         title="Tham số"
                                         data-toggle="modal" data-target="#income-by-time-modal">
                                </div>
                            </div>
                            <hr>
                            <div class="parameter-detail row">

                            </div>
                        </div>
                        <div class="card-body">
                            <div style="display:flex;justify-content:center; margin-top:0px;">
                                <div id="income-by-time-label" style="display: block;">
                                    <label><span>0</span></label>
                                </div>
                            </div>
                            <div class="position-relative mb-4">
                                <div id="chart-income-by-time-loader" style="display: none;"></div>
                                <div class="row" style="opacity: 1;">
                                    <canvas id="canvas-chart-income-by-time"
                                            class="nomal-canvas chartjs-render-monitor"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
        </div>
    </div>

@endsection
@push('scripts')
    <script type="text/javascript"
            src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css"
          href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <?php
    $jsFiles = [
        'vendor/chart/Chart.min',
        'vendor/chart/chartjs-plugin-datalabels.min',
        'vendor/chart/chart-template',
        'autoload/object-select2'

    ];
    ?>
    {!! loadFiles($jsFiles, $area, 'js') !!}

@endpush
