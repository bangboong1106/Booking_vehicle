@extends('layouts.backend.layouts.main')
@push('before-css')
    <?php $cssFiles = [
        'vendor/fullcalendar.min',
        'vendor/scheduler.min',
    ];
    ?>
    {!! loadFiles($cssFiles, isset($area) ? $area : 'backend') !!}
@endpush
@push('after-css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
@endpush
@section('content')
    <script>
        let urlVehicleList = '{!! route('route-board.vehicle-list') !!}',
            urlRouteList = '{!! route('route-board.route-list') !!}',
            urlOrderList = '{!! route('route-board.order-list') !!}',
            urlOrderDetail = '{!! route('order.show', -1) !!}',
            urlVehicleDetail = '{!! route('route-board.vehicle-detail') !!}',
            urlRouteDetail  = '{!! route('route.show', -1) !!}',
            urlChooseRoute  = '{!! route('route-board.choose-route') !!}',

            urlDriverDropdown = '{{route('driver.combo-driver')}}',
            urlVehicleDropdown = '{{route('vehicle.combo-vehicle')}}',
            urlCustomerDropdown = '{{route('customer.combo-customer')}}',
            urlVehicleTeamDropdown = '{{route('vehicle-team.combo-vehicle-team')}}';

        let systemConfig = {
            reload: Number('{{$dashboardReload}}'),
            notifyVehicle: Number('{{$dashboardNotifyVehicle}}'),
            vehiclePageSize: Number('{{$dashboardVehiclePageSize}}'),
            viewType: '{{$dashboardViewType}}'
        };

        let calendarID = '{!! $calendar->getId() !!}',
            resources = [],
            events = [],
            temp = '';

    </script>
    {{-- <div class="title-info">
        <div class="row">
            <div class="col-sm-12">
                <div>
                    <h4 class="page-title">{{$title}}</h4>
                        <a class="help-title-box" target="_blank"
                           href="{{env('HELP_DOMAIN','').trans('helps.'.$routeName)}}"
                           data-toggle="tooltip" data-placement="top" title=""
                           data-original-title="{{trans('actions.help')}}">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="header-dashboard">
        <div id="wrapper-status" class="row">
            <div class="col-md-11 status-content">
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn border-primary text-primary active">
                        <input id="chkAll" type="checkbox" checked autocomplete="off" data-status=0>
                        <span class="m-b-10 counter" data-status=-1>0</span>
                        <span>Tất cả</span>
                    </label>
                    <label class="btn border-blue text-blue">
                        <input type="checkbox" autocomplete="off" data-status={{ config("constant.status_incomplete") }}>
                        <span class="m-b-10 counter" data-status={{ config("constant.status_incomplete") }}>0</span>
                        <span>Chưa hoàn thành</span>
                    </label>
                    <label class="btn border-success text-success">
                        <input type="checkbox" autocomplete="off" data-status={{ config("constant.status_complete") }}>
                        <span class="m-b-10 counter" data-status={{ config("constant.status_complete") }}>0</span>
                        <span>Hoàn thành</span>
                    </label>
                    <label class="btn border-dark text-dark">
                        <input type="checkbox" autocomplete="off" data-status={{ config("constant.status_cancel") }}>
                        <span class="m-b-10 counter" data-status={{ config("constant.status_cancel") }}>0</span>
                        <span>Huỷ</span>
                    </label>
                </div>
                <input type="hidden" id="status-listing">

            </div>
            <div class="col-md-1">
                <div class="float-right" id="click-toggle-right-div" data-toggle="tooltip" data-placement="top" title=""
                     data-original-title="Hiển thị danh sách đơn hàng">
                    <i class="fa fa-arrow-circle-o-left"></i>
                </div>
                <div class="float-right" data-toggle="tooltip" data-placement="top" title=""
                     data-original-title="Hiển thị bộ lọc">
                        <span class="collapsed filter" data-toggle="collapse" data-target="#filter-event"
                              id="collapseP">
                                <i class="fa"></i>
                        </span>
                </div>
            </div>
        </div>
        <div class="collapse" id="filter-event">
            <div class="advanced form-group row">
                @if(\Illuminate\Support\Facades\Auth::user()->role == 'admin')
                    <div class="col-md-4">
                        <label for="partner_id">Đối tác vận tải</label>
                        <div class="input-group select2-bootstrap-prepend">
                            <select class="select2 " id="partner_id"
                                    name="partner_id" >
                                <option>Vui lòng chọn đối tác vận tải</option>
                                @foreach($partners as $partner)
                                    <option value="{{ $partner->id }}" title="{{ $partner->full_name }}">{{ $partner->full_name }}</option>
                                @endforeach
                            </select>
                            {{--<span class="input-group-addon vehicle-team-search"
                                  id="secondary-vehicle-team-search" data-type="multiple">
                                                                <div class="input-group-text bg-transparent">
                                                                    <i class="fa fa-search"></i>
                                                                </div>
                                                            </span>--}}
                        </div>
                    </div>
                @endif
                <div class="col-md-4">
                    <label for="vehicle_team_ids">Đội tài xế</label>
                    <div class="input-group select2-bootstrap-prepend">
                        <select class="select2 select-vehicle-team" id="filter_vehicle_team_ids"
                                name="filter_vehicle_team_ids" multiple>
                            <option>Vui lòng chọn đội tài xế</option>
                            @foreach($vehicleTeams as $vehicleTeam)
                            <option selected="selected" value="{{ $vehicleTeam->id }}" title="{{ $vehicleTeam->title }}">{{ $vehicleTeam->title }}}</option>
                            @endforeach
                        </select>
                        <span class="input-group-addon vehicle-team-search"
                              id="secondary-vehicle-team-search" data-type="multiple">
                                                            <div class="input-group-text bg-transparent">
                                                                <i class="fa fa-search"></i>
                                                            </div>
                                                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="vehicle_ids">Xe</label>
                    <div class="input-group select2-bootstrap-prepend">
                        <select class="select2 select-vehicle" id="filter_vehicle_ids"
                                name="filter_vehicle_ids" multiple>
                            <option>Vui lòng chọn xe</option>
                        </select>
                        <span class="input-group-addon vehicle-search"
                              id="filter-vehicle-search" data-type="multiple">
                                                            <div class="input-group-text bg-transparent">
                                                                <i class="fa fa-search"></i>
                                                            </div>
                                                        </span>
                    </div>
                </div>
            </div>
            <div class="advanced form-group row">
                <div class="col-md-4">
                    <label for="filter_vehicle_group_ids">Chủng loại xe</label>
                    <div class="input-group select2-bootstrap-prepend">
                        <select class="select2 select-vehicle-group" id="filter_vehicle_group_ids"
                                name="filter_vehicle_group_ids" multiple>
                            @foreach($vehicle_groups as $key=>$value)
                                <option value="{{$key}}"
                                        title="{{$value}}">{{$value}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="filter_customer_ids">Khách hàng</label>
                    <div class="input-group select2-bootstrap-prepend">
                        <select class="select2 select-customer" id="filter_customer_ids"
                                name="filter_customer_ids" multiple>
                            <option>Vui lòng chọn khách hàng</option>
                        </select>
                        <span class="input-group-addon customer-search"
                              id="filter-customer-search" data-type="multiple">
                            <div class="input-group-text bg-transparent">
                                <i class="fa fa-search"></i>
                            </div>
                        </span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 offset-md-4">
                    <button class="btn btn-success" id="btnApplyFilter">Áp dụng</button>
                    <button class="btn btn-default" id="btnCancelFilter">Hủy bỏ</button>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-12 full-calendar" id="content-div-caledar">
            {!! $calendar->calendar() !!}
            <div class="row" id="pager">
            </div>
        </div>
        <div class="col-md-3" id="content-right-div">
            <div id='external-events'>
                <div>
                    <div class="input-group wrap-invoice-search">
                        <input id="invoice-search" class="form-control py-2 border-right-0 border" type="search"
                               placeholder="Tìm kiếm đơn hàng" id="example-search-input">
                        <div class="input-group-append">
                            <div class="input-group-text bg-transparent">
                                <i class="fa fa-search"></i>
                            </div>
                        </div>
                    </div>
                    <div class="input-group wrap-choose-vehicle" style="display: none">
                        <a class="btn btn-choose-vehicle" href="#">
                            <i class="fa fa-plus"></i>
                            <span>Chọn nhanh xe</span>
                            <input type="hidden" id="hdfOrderIDs"/>
                        </a>
                    </div>

                </div>
                <div class="wrap-loader" style="display: none">
                    <div id="loader" role="status">
                        <span class="sr-only">Đang tải dữ liệu...</span>
                    </div>
                </div>
                <div id='external-events-listing'>
                    @include('backend.order_board._order_list', ['entities' => $orders])
                </div>
                <div id="invoice-pager">
                    @include('backend.order_board._order_pagination', ['entities' => $orders])
                </div>
            </div>
        </div>
    </div>


    {{--Xem chi tiet vi tri xe hoac chi tiet xe--}}
    @include('backend.order_board._vehicle_detail')

    {{--Xác nhận reload lại thông tin trên Bảng điều jhieern--}}
    <div class="modal" id="reload_dashboard_confirm" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-label">Xác nhận</h4>
                </div>
                <div class="modal-body">
                    Hệ thống không tìm thấy dữ liệu đơn hàng. Vui lòng tải lại bảng điều khiển
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{trans('actions.ok')}} </button>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.backend.elements._show_modal')

    @include('layouts.backend.elements.search._vehicle_team_search')
    @include('layouts.backend.elements.search._customer_search')

    @include('layouts.backend.elements.search._vehicle_search',
     ['modal' => 'filter_vehicle_modal',
     'table'=>'table_filter_vehicles',
     'button'=> 'btn-filter-vehicle'])

    @include('backend.route_board._choose_route_modal')

@endsection
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.4.1/jspdf.min.js"></script>
    <script src="https://anseki.github.io/leader-line/js/libs-1b14e4a-190412215531.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <?php
    $jsFiles = [
        'vendor/fullcalendar.min',
        'vendor/scheduler.min',
        'vendor/locale-all',
        'autoload/object-select2'
    ];
    ?>
    {!! loadFiles($jsFiles, $area, 'js') !!}
    {!! $calendar->script() !!}
@endpush