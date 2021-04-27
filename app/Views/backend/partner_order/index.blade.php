<?php
$attributes = getColumnConfig("partner_order");
?>
@extends('layouts.backend.layouts.main')
@section('content')
    <div class="flex-fill-right-menu">
        <div class="row flex-list-data-content">
            <div class="col-md-12">
                <div class="card-box list-ajax">
                    @include('backend.partner_order.list_to_create')
                    @include('layouts.backend.elements.column_config._wrap_column_config',[
                        'entity' => 'order',
                        'sort_field' => $sort_field,
                        'sort_type' => $sort_type,
                        'page_size' => $page_size,
                        'attributes' => $attributes, 
                        'configList'=> isset($configList) ? $configList : []])
                    <div id="table-scroll" class="table-scroll">
                        <div id="main-table" class="main-table">
                            <table class="table table-bordered table-hover table-striped">
                                <thead id="head_content">
                                @include('layouts.backend.elements.column_config._head',[
                                    'entity'=>'order',
                                    'is_action' => false,
                                    'attributes' => $attributes,
                                    'configList'=> isset($configList) ? $configList : []])
                                </thead>
                                <tbody id="body_content">
                                @include('layouts.backend.elements.column_config._list',[
                                    'entity'=>'order',
                                    'is_action' => false,
                                    'dbclick' => false,
                                    'attributes' => $attributes,
                                    'is_add' => false,
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

                    <input type="hidden" id="list_info" data-url="{{ route('partner-order.ajaxSearch') }}"
                           data-url_head="{{ route('partner-order.generateHeadTable') }}"/>
                    <input type="hidden" class="sort_field" value="">
                    <input type="hidden" class="sort_type" value="">
                    <input type="hidden" class="selected_item"
                           value="{{ isset($selectedItem) ? implode(',', $selectedItem) : '' }}">
                </div>
            </div>
        </div>
    </div>
    <div class="right-config">
        <div class="btn-menu-right"><i class="fa fa-angle-left"></i></div>
    </div>
    @include('layouts.backend.elements.search._routes_search')
    @include('backend.partner_order._create_route')
    @include('layouts.backend.elements._show_modal')
    @include('backend.partner_order._confirm_order')
    @include('backend.order.update_documents_modal')
    <div class="modal fade modal_add" id="modal_template">
        <div class="modal-dialog modal-md">
            <div class="modal-content"></div>
        </div>
    </div>

    <script>
        let vehicleDropdownUri = '{{route('vehicle.combo-vehicle')}}',
            driverDropdownUri = '{{route('driver.combo-driver')}}',
            routeDropdownUri = '{{route('route.combo-route')}}',
            mergeOrderFormUri = '{{route('merge-order.mergeOrderForm')}}',
            mergeOrderSaveUri = '{{route('merge-order.mergeOrderSave')}}',
            getRouteByVehiclesUri = '{{route('merge-order.getRouteByVehicles')}}',
            getVehicleDriverByRouteUri = '{{route('route.getVehicleDriverByRoute')}}',
            getDefaultDriverForVehicleUri = '{{route('vehicle.getDefaultDriver')}}',
            updateDocumentsUri = '{{route('order.updateDocuments')}}';
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