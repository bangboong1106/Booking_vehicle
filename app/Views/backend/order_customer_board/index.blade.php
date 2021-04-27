@extends('layouts.backend.layouts.main')
@push('before-css')
    <?php $cssFiles = [
        'vendor/fullcalendar.min',
        'vendor/scheduler.min',
    ];
    ?>
    {!! loadFiles($cssFiles, isset($area) ? $area : 'backend') !!}
@endpush
@section('content')
    <script>
        let urlEvents = '{!! route('order-customer-board.event') !!}',
            urlOrderCustomerDetail = '{{route("order-customer-board.detail", -1)}}',
            urlCustomerDetail= '{{route("customer.show", -1)}}',
            urlComboCustomer = '{{route('customer.combo-customer')}}',
            urlUpdateDocument = '{{route('order.updateDocuments')}}';

            calendarID = '{!! $calendar->getId() !!}',
            resources = [],
            events = [],
            temp = '';

        let systemConfig = {
            viewType: '{{$dashboardViewType}}',
            dashboardGroup: '{{$dashboardGroup}}'
        };
    </script>
    {{-- <div class="title-info">
        <div class="row">
            <div class="col-sm-12">
                <div>
                    <h4 class="page-title">{{$title}}</h4>
                    @if($routeName == 'dashboard.index' || $routeName == 'report.index')
                        <a class="help-title-box" target="_blank"
                           href="{{env('HELP_DOMAIN','').trans('helps.'.$routeName)}}"
                           data-toggle="tooltip" data-placement="top" title=""
                           data-original-title="{{trans('actions.help')}}">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    @endif
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
                    <label class="btn border-brown text-brown" >
                        <input type="checkbox" autocomplete="off" data-status={{ config("constant.ORDER_CUSTOMER_STATUS.DA_XUAT_HANG") }}>
                        <span class="m-b-10 counter" data-status={{ config("constant.ORDER_CUSTOMER_STATUS.DA_XUAT_HANG") }}>
                           0
                        </span>
                        <span>Đã xuất hàng</span>
                    </label>
                    <label class="btn border-blue text-blue">
                        <input type="checkbox" autocomplete="off" data-status={{ config("constant.ORDER_CUSTOMER_STATUS.DANG_VAN_CHUYEN") }}>
                        <span class="m-b-10 counter" data-status={{ config("constant.ORDER_CUSTOMER_STATUS.DANG_VAN_CHUYEN") }}>
                            0
                        </span>
                        <span>Đang vận chuyển</span>
                    </label>
                    <label class="btn border-success text-success">
                        <input type="checkbox" autocomplete="off" data-status={{ config("constant.ORDER_CUSTOMER_STATUS.HOAN_THANH") }}>
                        <span class="m-b-10 counter" data-status={{ config("constant.ORDER_CUSTOMER_STATUS.HOAN_THANH") }}>
                            0
                        </span>
                        <span>Hoàn thành</span>
                    </label>
                    <label class="btn border-dark text-dark">
                        <input type="checkbox" autocomplete="off" data-status={{ config("constant.ORDER_CUSTOMER_STATUS.C20_HUY") }}>
                        <span class="m-b-10 counter" data-status={{ config("constant.ORDER_CUSTOMER_STATUS.C20_HUY") }}>
                            0
                        </span>
                        <span>Hủy</span>
                    </label>
                </div>
                <input type="hidden" id="status-listing">

            </div>
            <div class="col-md-1">
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

                <div class="col-md-4">
                    <label for="customer_ids">Chủ hàng</label>
                    <div class="input-group select2-bootstrap-prepend">
                        <select class="select2 select-customer" id="filter_customer_ids"
                                name="filter_customer_ids" multiple>
                            <option>Vui lòng chọn chủ hàng</option>
                        </select>
                        <span class="input-group-addon customer-search"
                              id="customer-search" data-type="multiple">
                            <div class="input-group-text bg-transparent">
                                <i class="fa fa-search"></i>
                            </div>
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="order_no">Số đơn hàng</label>
                    <br/>
                    <input placeholder="Nhập số đơn hàng" type="text" id="order_no" class="form-control"/>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 offset-md-5">
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
    </div>

    @include('layouts.backend.elements._show_modal')
    @include('layouts.backend.elements.search._customer_search')
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
