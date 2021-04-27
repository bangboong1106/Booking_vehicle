<?php
    $attributes = getColumnConfig("order_customer");
    if (!function_exists('countStatus')) {
        function countStatus($groupData, $status ) {
            $st = $groupData->filter(function($item) use ($status) {
                    return $item->status == $status;
                });
            return $st->isEmpty() ? 0 : $st->first()->total;
        }
    }
?>
@extends('layouts.backend.layouts.main')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card-box list-ajax">
                @include('backend.order_customer.list_to_create')
                @include('layouts.backend.elements.column_config._wrap_column_config',[
                    'entity' => 'order_customer',
                    'sort_field' => $sort_field,
                    'sort_type' => $sort_type,
                    'page_size' => $page_size,
                    'attributes' => $attributes, 
                    'configList'=> isset($configList) ? $configList : []])

                <div id="group-bar" class="btn-group btn-group-toggle" style="margin-bottom: 8px">
                    <label class="btn border-brown text-brown" data-status={{ config("constant.ORDER_CUSTOMER_STATUS.DA_XUAT_HANG") }}>
                        <span class="m-b-10 counter" data-status={{ config("constant.ORDER_CUSTOMER_STATUS.DA_XUAT_HANG") }}>
                            {{ countStatus($groupData, config("constant.ORDER_CUSTOMER_STATUS.DA_XUAT_HANG")) }}
                        </span>
                        <span>Đã xuất hàng</span>
                    </label>
                    <label class="btn border-blue text-blue" data-status={{ config("constant.ORDER_CUSTOMER_STATUS.DANG_VAN_CHUYEN") }}>
                        <span class="m-b-10 counter" data-status={{ config("constant.ORDER_CUSTOMER_STATUS.DANG_VAN_CHUYEN") }}>
                            {{ countStatus($groupData, config("constant.ORDER_CUSTOMER_STATUS.DANG_VAN_CHUYEN")) }}
                        </span>
                        <span>Đang vận chuyển</span>
                    </label>
                    <label class="btn border-success text-success" data-status={{ config("constant.ORDER_CUSTOMER_STATUS.HOAN_THANH") }}>
                        <span class="m-b-10 counter" data-status={{ config("constant.ORDER_CUSTOMER_STATUS.HOAN_THANH") }}>
                            {{ countStatus($groupData, config("constant.ORDER_CUSTOMER_STATUS.HOAN_THANH")) }}
                        </span>
                        <span>Hoàn thành</span>
                    </label>
                    <label class="btn border-dark text-dark" data-status={{ config("constant.ORDER_CUSTOMER_STATUS.C20_HUY") }}>
                        <span class="m-b-10 counter" data-status={{ config("constant.ORDER_CUSTOMER_STATUS.C20_HUY") }}>
                            {{ countStatus($groupData, config("constant.ORDER_CUSTOMER_STATUS.C20_HUY")) }}
                        </span>
                        <span>Hủy</span>
                    </label>
                </div>    
                <div id="table-scroll" class="table-scroll">
                    <div id="main-table" class="main-table">
                        <table class="table table-bordered table-hover table-striped">
                            <thead id="head_content">
                                @include('layouts.backend.elements.column_config._head',[
                                    'entity'=>'order_customer',
                                    'is_action' => true,
                                    'attributes' => $attributes, 
                                    'configList'=> isset($configList) ? $configList : []])
                            </thead>
                            <tbody id="body_content">
                                @include('layouts.backend.elements.column_config._list',[
                                    'entity'=>'order_customer',
                                    'is_action' => false,
                                    'attributes' => $attributes, 
                                    'is_show_history' => false,
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

                <input type="hidden" id="list_info" data-url="{{ route('order-customer.ajaxSearch') }}"
                       data-url_head="{{ route('order-customer.generateHeadTable') }}"/>
                <input type="hidden" class="sort_field" value="">
                <input type="hidden" class="sort_type" value="">
                <input type="hidden" class="selected_item"
                       value="{{ isset($selectedItem) ? implode(',', $selectedItem) : '' }}">
            </div>
        </div>
    </div>
    @include('layouts.backend.elements._context_menu')
    @include('layouts.backend.elements._show_modal')
    @include('backend.order_customer.order_client')
    @include('layouts.backend.elements.modal_lock')

    <div class="modal fade modal_add" id="modal_update_revenue">
        <div class="modal-dialog modal-xlg">
            <div class="modal-content"></div>
        </div>
    </div>

    <div class="modal fade modal_add" id="modal_template">
        <div class="modal-dialog modal-md">
            <div class="modal-content"></div>
        </div>
    </div>
    <script>
        let headerRow = 10;
        let urlOrderClient = '{{route('order-customer.orderClient')}}';
        let urlOrderApprove = '{{route('order-customer.approvedOrderClient')}}';

    </script>
@endsection
@push('scripts')
    <script>
        var is_create = @json(Request::is('*/create') ? true : false);
    </script>
    <?php
    $jsFiles = [
        'vendor/utils/table-responsive'
    ];
    ?>
    {!! loadFiles($jsFiles, $area, 'js') !!}

@endpush