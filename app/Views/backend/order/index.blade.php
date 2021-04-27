<?php
    $attributes = getColumnConfig("order");
    if (!function_exists('countStatus')) {
        function countStatus($groupData, $status, $statusPartner ) {
            $st = $groupData->filter(function($item) use ($status, $statusPartner) {
                    return $item->status == $status && $item->status_partner == $statusPartner;
                });
            return $st->isEmpty() ? 0 : $st->first()->total;
        }
    }
?>
@extends('layouts.backend.layouts.main')
@section('content')
    <div class="flex-fill-right-menu">
        <div class="row flex-list-data-content">
            <div class="col-md-12">
                <div class="card-box list-ajax">
                    @include('backend.order.list_to_create')
                    @include('layouts.backend.elements.column_config._wrap_column_config',[
                        'entity' => 'order',
                        'sort_field' => $sort_field,
                        'sort_type' => $sort_type,
                        'page_size' => $page_size,
                        'attributes' => $attributes, 
                        'configList'=> isset($configList) ? $configList : []])
                    <div id="group-bar" class="btn-group btn-group-toggle" style="margin-bottom: 8px">
                        <label class="btn border-light" data-status={{ config("constant.KHOI_TAO") }}>
                            <span class="m-b-10 counter" data-status={{ config("constant.KHOI_TAO") }}>
                            {{ countStatus($groupData, config("constant.KHOI_TAO"), config("constant.PARTNER_CHO_GIAO_DOI_TAC_VAN_TAI")) }}
                            </span>
                            <span>Chờ giao</span>
                        </label>
                        <label class="btn border-light" data-status={{ config("constant.KHOI_TAO") }}>
                            <span class="m-b-10 counter" data-status={{ config("constant.KHOI_TAO") }}>
                            {{ countStatus($groupData, config("constant.KHOI_TAO"), config("constant.PARTNER_CHO_XAC_NHAN")) }}
                            </span>
                            <span>Chờ xác nhận</span>
                        </label>
                        <label class="btn border-light" data-status={{ config("constant.KHOI_TAO") }}>
                            <span class="m-b-10 counter" data-status={{ config("constant.KHOI_TAO") }}>
                            {{ countStatus($groupData, config("constant.KHOI_TAO"), config("constant.PARTNER_YEU_CAU_SUA")) }}
                            </span>
                            <span>Yêu cầu sửa</span>
                        </label>
                        <label class="btn border-secondary text-secondary" data-status={{ config("constant.SAN_SANG") }}>
                            <span class="m-b-10 counter" data-status={{ config("constant.SAN_SANG") }}>
                                {{ countStatus($groupData, config("constant.SAN_SANG"), config("constant.PARTNER_XAC_NHAN")) }}
                            </span>
                            <span>Sẵn sàng
                            </span>
                        </label>
                        <label class="btn border-stpink text-stpink" data-status={{ config("constant.TAI_XE_XAC_NHAN") }}>
                            <span class="m-b-10 counter" data-status={{ config("constant.TAI_XE_XAC_NHAN") }}>
                                {{ countStatus($groupData, config("constant.TAI_XE_XAC_NHAN"), config("constant.PARTNER_XAC_NHAN")) }}
                            </span>
                            <span>Chờ tài xế xác nhận</span>
                        </label>
                        <label class="btn border-brown text-brown" data-status={{ config("constant.CHO_NHAN_HANG") }}>
                            <span class="m-b-10 counter" data-status={{ config("constant.CHO_NHAN_HANG") }}>
                                {{ countStatus($groupData, config("constant.CHO_NHAN_HANG"), config("constant.PARTNER_XAC_NHAN")) }}
                            </span>
                            <span>
                                Chờ nhận hàng
                            </span>
                        </label>
                        <label class="btn border-blue text-blue" data-status={{ config("constant.DANG_VAN_CHUYEN") }}>
                            <span class="m-b-10 counter" data-status={{ config("constant.DANG_VAN_CHUYEN") }}>
                                {{ countStatus($groupData, config("constant.DANG_VAN_CHUYEN"), config("constant.PARTNER_XAC_NHAN")) }}
                            </span>
                            <span>Đang vận chuyển</span>
                        </label>
                        <label class="btn border-success text-success" data-status={{ config("constant.HOAN_THANH") }}>
                            <span class="m-b-10 counter" data-status={{ config("constant.HOAN_THANH") }}>
                                {{ countStatus($groupData, config("constant.HOAN_THANH"), config("constant.PARTNER_XAC_NHAN")) }}
                            </span>
                            <span>Hoàn thành</span>
                        </label>
                        <label class="btn border-dark text-dark" data-status={{ config("constant.HUY") }}>
                            <span class="m-b-10 counter" data-status={{ config("constant.HUY") }}>
                                {{ countStatus($groupData, config("constant.HUY"), config("constant.PARTNER_XAC_NHAN")) }}
                            </span>
                            <span>Hủy</span>
                        </label>
                    </div>
                    <div id="table-scroll" class="table-scroll">
                        <div id="main-table" class="main-table">
                            <table class="table table-bordered table-hover table-striped">
                                <thead id="head_content">
                                    @include('layouts.backend.elements.column_config._head',[
                                        'entity'=>'order',
                                        'is_action' => true,
                                        'attributes' => $attributes, 
                                        'configList'=> isset($configList) ? $configList : []])
                                </thead>
                                <tbody id="body_content">
                                    @include('layouts.backend.elements.column_config._list',[
                                        'entity'=>'order',
                                        'is_action' => true,
                                        'attributes' => $attributes, 
                                        'is_show_split_order' => true,
                                        'configList'=> isset($configList) ? $configList : []])
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row" id="paginate_content">
                        <div class="col-md-5 col-sm-12 m-t-15">
                            @include('layouts.backend.elements.pagination_info')
                        </div>
                        <div class="col-md-7 col-sm-12">
                            @include('layouts.backend.elements.pagination', ['isAjax'=> true])
                        </div>
                    </div>

                    <input type="hidden" id="list_info" data-url="{{ route('order.ajaxSearch') }}"
                           data-url_head="{{ route('order.generateHeadTable') }}"/>
                    <input type="hidden" class="sort_field" value="">
                    <input type="hidden" class="sort_type" value="">
                    <input type="hidden" class="selected_item" value="{{ isset($selectedItem) ? implode(',', $selectedItem) : '' }}">
                </div>
            </div>
        </div>
    </div>
    <div class="right-config">
        <div class="btn-menu-right"><i class="fa fa-angle-left"></i></div>
    </div>
    @include('backend.order.features')
    @include('backend.order.update_partner_modal')
    @include('backend.order._modal_split_order')
    <div id="merge_order_content"></div>
    <script>
        let orderHistoryUrl = '{{route('order.order-history')}}';
        let orderRouteMapUrl = '{{route('order.order-route-map')}}';
        let comboCustomerUri = '{{route('customer.combo-customer')}}';
        let declarationUri = '{{route('order.exportReportOrderTemplate')}}';
        let updateDocumentsUri = '{{route('order.updateDocuments')}}';
        let printBillFromUrlUri = '{{route('order.printBillFromUrl')}}';
        let vehicleTeamDropdownUri = '{{route('vehicle-team.combo-vehicle-team')}}';
        let vehicleDropdownUri = '{{route('vehicle.combo-vehicle')}}';
        let googleSheetUrl = '{{$googleSheetUrl}}';
        let editGoogleSheetUrl = '{{$editGoogleSheetUrl}}';
        let headerRow = 10;
        let qrcodeUri = '{{route('order.qrcode')}}';
        let driverDropdownUri = '{{route('driver.combo-driver')}}';
        let urlVehicle = '{{route('vehicle.combo-vehicle')}}';
        let urlVehicleDriver = '{{route('driver.getVehicleDriver')}}';
        var urlPartner = '{{route('partner.combo-partner')}}';
        let urlDriver = '{{route('driver.combo-driver')}}';
        let getDefaultDriverForVehicleUri = '{{route('vehicle.getDefaultDriver')}}';
    </script>
@endsection
@push('scripts')
    <script type="text/javascript"
            src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css"
          href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <?php
    $jsFiles = [
        'autoload/object-select2',
        'vendor/utils/table-responsive',
        'vendor/jszip.min',
        'vendor/FileSaver.min',
    ];
    ?>
    {!! loadFiles($jsFiles, $area, 'js') !!}
@endpush