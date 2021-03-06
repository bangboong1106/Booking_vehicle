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
        let urlVehicle = '{!! route('order-board.vehicle') !!}',
            urlEvent = '{!! route('order-board.event') !!}',
            urlInvoice = '{!! route('order-board.order-list') !!}',
            urlOrderDetail = '{!! route('order.show', -1) !!}',
            urlAddTrip = '{{route('order-board.addTrip')}}',
            urlLocation = '{{route('location.combo-location')}}',
            urlResizeDateTrip = '{{route('order-board.resizeDateTrip')}}',
            urlRemoveTripFromVehicle = '{!! route('order-board.remove-trip-from-vehicle') !!}',
            urlRemoveTrip = '{!! route('order-board.remove-trip') !!}',
            urlChangeVehicleForTrip = '{!! route('order-board.change-vehicle-for-trip') !!}',

            urlDriverDropdown = '{{route('driver.combo-driver')}}',
            urlVehicleDropdown = '{{route('vehicle.combo-vehicle')}}',
            urlCustomerDropdown = '{{route('customer.combo-customer')}}',
            urlVehicleTeamDropdown = '{{route('vehicle-team.combo-vehicle-team')}}',

            urDefaultDriverForVehicle = '{{route('vehicle.getDefaultDriver')}}',
            urlChooseVehicle = '{{route('order-board.mass-add-trip')}}',
            urlUpdateDocument = '{{route('order.updateDocuments')}}',
            urlLoadOrder = '{{route('order-board.order')}}';

        let systemConfig = {
            reload: Number('{{$dashboardReload}}'),
            notifyVehicle: Number('{{$dashboardNotifyVehicle}}'),
            vehiclePageSize: Number('{{$dashboardVehiclePageSize}}'),
            viewType: '{{$dashboardViewType}}'
        };

        let calendarID = '{!! $calendar->getId() !!}',
            resources = [],
            events = [],
            temp = '',
            locationDestinationId = '{{empty($entity->location_destination_id) ? 0 : $entity->location_destination_id}}',
            locationArrivalId = '{{empty($entity->location_arrival_id) ? 0 : $entity->location_arrival_id}}';

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
            <div class="col-md-10 status-content">
                <div class="btn-group btn-group-toggle">
                    <label class="btn border-primary text-primary active" id="chkAll">
                        <span class="m-b-10 counter" data-status=-1>0</span>
                        <span>T???t c??? chuy???n ??i</span>
                    </label>
                    <label class="btn border-stpink text-stpink" data-status={{ config("constant.TAI_XE_XAC_NHAN") }}>
                        <span class="m-b-10 counter" data-status={{ config("constant.TAI_XE_XAC_NHAN") }}>0</span>
                        <span>Ch??? t??i x??? x??c nh???n</span>
                    </label>
                    <label class="btn border-brown text-brown" data-status={{ config("constant.CHO_NHAN_HANG") }}>
                        <span class="m-b-10 counter" data-status={{ config("constant.CHO_NHAN_HANG") }}>0</span>
                        <span>Ch??? nh???n h??ng</span>
                    </label>
                    <label class="btn border-blue text-blue" data-status={{ config("constant.DANG_VAN_CHUYEN") }}>
                        <span class="m-b-10 counter" data-status={{ config("constant.DANG_VAN_CHUYEN") }}>0</span>
                        <span>??ang v???n chuy???n</span>
                    </label>
                    <label class="btn border-success text-success" data-status={{ config("constant.HOAN_THANH") }}>
                        <span class="m-b-10 counter" data-status={{ config("constant.HOAN_THANH") }}>0</span>
                        <span>Ho??n th??nh</span>
                    </label>
                    <label class="btn border-dark text-dark" data-status={{ config("constant.HUY") }}>
                        <span class="m-b-10 counter" data-status={{ config("constant.HUY") }}>0</span>
                        <span>H???y</span>
                    </label>
                </div>
                <input type="hidden" id="status-listing">

            </div>
            <div class="col-md-2">

                <div class="float-right" id="click-toggle-right-div" data-toggle="tooltip" data-placement="top" title=""
                     data-original-title="Hi???n th??? danh s??ch ????n h??ng">
                    <i class="fa fa-arrow-circle-o-left"></i>
                </div>
                <div class="float-right" data-toggle="tooltip" data-placement="top" title=""
                     data-original-title="Hi???n th??? b??? l???c">
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
                    <label for="vehicle_team_ids">?????i t??i x???</label>
                    <div class="input-group select2-bootstrap-prepend">
                        <select class="select2 select-vehicle-team" id="filter_vehicle_team_ids"
                                name="filter_vehicle_team_ids" multiple>
                            <option>Vui l??ng ch???n ?????i t??i x???</option>
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
                            <option>Vui l??ng ch???n xe</option>
                        </select>
                        <span class="input-group-addon vehicle-search"
                              id="filter-vehicle-search" data-type="multiple">
                                                            <div class="input-group-text bg-transparent">
                                                                <i class="fa fa-search"></i>
                                                            </div>
                                                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="filter_vehicle_group_ids">Ch???ng lo???i xe</label>
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
            </div>
            <div class="advanced form-group row">
                <div class="col-md-4">
                    <label for="filter_customer_ids">Kh??ch h??ng</label>
                    <div class="input-group select2-bootstrap-prepend">
                        <select class="select2 select-customer" id="filter_customer_ids"
                                name="filter_customer_ids" multiple>
                            <option>Vui l??ng ch???n kh??ch h??ng</option>
                        </select>
                        <span class="input-group-addon customer-search"
                              id="filter-customer-search" data-type="multiple">
                            <div class="input-group-text bg-transparent">
                                <i class="fa fa-search"></i>
                            </div>
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <label>Hi???n th??? to??n b??? KH</label>
                    <input hidden="hidden" name="hdf_is_show_customer" id="hdf_is_show_customer" value="0"/>
                    <div class="input-group select2-bootstrap-prepend">
                    {!! MyForm::checkbox('is_show_customer', 'is_show_customer', false
                    , ['data-plugin' => "switchery", 'data-color' => "#11509b", 'class' => 'switchery', 'id' => 'is_show_customer']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 offset-md-4">
                    <button class="btn btn-success" id="btnApplyFilter">??p d???ng</button>
                    <button class="btn btn-default" id="btnCancelFilter">H???y b???</button>
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
                {{--<h5>S??? h??a ????n</h5>--}}
                <div>
                    <div class="input-group wrap-invoice-search">
                        <input id="invoice-search" class="form-control py-2 border-right-0 border" type="search"
                               placeholder="T??m ki???m ????n h??ng" id="example-search-input">
                        <div class="input-group-append">
                            <div class="input-group-text bg-transparent">
                                <i class="fa fa-search"></i>
                            </div>
                        </div>
                    </div>
                    <div class="input-group wrap-choose-vehicle" style="display: none">
                        <a class="btn btn-choose-vehicle" href="#">
                            <i class="fa fa-plus"></i>
                            <span>Ch???n nhanh xe</span>
                            <input type="hidden" id="hdfOrderIDs"/>
                        </a>
                    </div>

                </div>
                <div class="wrap-loader" style="display: none">
                    <div id="loader" role="status">
                        <span class="sr-only">??ang t???i d??? li???u...</span>
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

    {{--X??c nh???n x??a ????n h??ng kh???i xe--}}
    <div class="modal" id="delete_order_vehicle_confirm" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">??</button>
                    <h4 class="modal-title"
                        id="modal-label">{{trans('messages.confirm_delete_order_vehicle_title')}}</h4>
                </div>
                <div class="modal-body">
                    <form method="post" id="form_delete_order_vehicle">
                        <span>{{trans('messages.confirm_delete_order_vehicle')}}</span>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="delete_order_vehicle_close"
                            data-dismiss="modal">{{trans('actions.cancel')}}</button>
                    <button type="submit" class="btn btn-primary">{{trans('actions.ok')}}</button>
                </div>
            </div>
        </div>
    </div>

    {{--X??c nh???n x??a ????n h??ng kh???i h??? th???ng--}}
    <div class="modal" id="delete_order_confirm" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">??</button>
                    <h4 class="modal-title" id="modal-label">{{trans('messages.confirm_delete_order_title')}}</h4>
                </div>
                <div class="modal-body">
                    <form method="post" id="form_delete_order">
                        <span></span> {{trans('messages.confirm_delete_order')}}
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="delete_order_close"
                            data-dismiss="modal">{{trans('actions.cancel')}}</button>
                    <button type="submit" class="btn btn-primary">{{trans('actions.ok')}}</button>
                </div>
            </div>
        </div>
    </div>
    {{--X??c nh???n b??? sung h??a ????n cho chuy???n xe--}}
    <div class="modal" id="add_order_confirm" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">??</button>
                    <h4 class="modal-title" id="modal-label">{{trans('messages.confirm_add_order_to_trip')}}</h4>
                </div>
                <div class="modal-body">
                    {{trans('messages.are_you_sure_add_order_to_trip')}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">{{trans('actions.cancel')}}</button>
                    <button type="submit" class="btn btn-primary">{{trans('actions.ok')}}</button>
                </div>
            </div>
        </div>
    </div>

    {{--X??c nh???n reload l???i th??ng tin tr??n B???ng ??i???u jhieern--}}
    <div class="modal" id="reload_dashboard_confirm" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-label">X??c nh???n</h4>
                </div>
                <div class="modal-body">
                    H??? th???ng kh??ng t??m th???y d??? li???u ????n h??ng. Vui l??ng t???i l???i b???ng ??i???u khi???n
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{trans('actions.ok')}} </button>
                </div>
            </div>
        </div>
    </div>

    {{--X??c nh???n b??? sung ????n h??ng cho xe--}}
    @include('backend.order_board._add_order_to_vehicle')

    {{--X??c nh???n thay ?????i th???i gian chuy???n xe--}}
    @include('backend.order_board._trip_confirm')

    {{--X??c nh???n thay ?????i xe v?? th???i gian giao nh???n--}}
    @include('backend.order_board._change_vehicle_confirm')

    {{--Ch???n nhanh xe cho ????n h??ng--}}
    @include('backend.order_board._choose_vehicle')

    @include('layouts.backend.elements._show_modal')

    @include('layouts.backend.elements.search._vehicle_team_search')
    @include('layouts.backend.elements.search._customer_search')

    @include('layouts.backend.elements.search._vehicle_search',
     ['modal' => 'filter_vehicle_modal',
     'table'=>'table_filter_vehicles',
     'button'=> 'btn-filter-vehicle'])


    {{--Back to top--}}
    {{--    <a href="javascript:" id="return-to-top"><i class="fa fa-arrow-circle-up fa-2x"></i></a>--}}
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