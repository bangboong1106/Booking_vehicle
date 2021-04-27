<?php
$attributes = getColumnConfig("merge_order");
?>
@extends('layouts.backend.layouts.main')
@section('content')
    <div class="flex-fill-right-menu">
        <div class="row flex-list-data-content">
            <div class="col-md-12">
                <div class="card-box list-ajax">
                    @include('backend.merge_order.list_to_create')
                    @include('layouts.backend.elements.column_config._wrap_column_config',[
                        'entity' => 'merge_order',
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
                                    'entity'=>'merge_order',
                                    'is_action' => false,
                                    'attributes' => $attributes,
                                    'configList'=> isset($configList) ? $configList : []])
                                </thead>
                                <tbody id="body_content">
                                @include('layouts.backend.elements.column_config._list',[
                                    'entity'=>'merge_order',
                                    'is_action' => false,
                                    'dbclick' => false,
                                    'attributes' => $attributes,
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

                    <input type="hidden" id="list_info" data-url="{{ route('merge-order.ajaxSearch') }}"
                           data-url_head="{{ route('merge-order.generateHeadTable') }}"/>
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
    @include('backend.merge_order._create_route')
    @include('layouts.backend.elements._show_modal')

    <script>
        let vehicleDropdownUri = '{{route('vehicle.combo-vehicle')}}',
            driverDropdownUri = '{{route('driver.combo-driver')}}',
            routeDropdownUri = '{{route('route.combo-route')}}',
            mergeOrderFormUri = '{{route('merge-order.mergeOrderForm')}}',
            mergeOrderSaveUri = '{{route('merge-order.mergeOrderSave')}}',
            getRouteByVehiclesUri = '{{route('merge-order.getRouteByVehicles')}}',
            getVehicleDriverByRouteUri = '{{route('route.getVehicleDriverByRoute')}}',
            getDefaultDriverForVehicleUri = '{{route('vehicle.getDefaultDriver')}}';
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

{{-- <div class="modal fade" id="merge-order" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ghép đơn hàng</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body" id="merge-order-content">

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-success btn-declaration-export" id="declartion-export-btn">Xuất
                </button>
            </div>
        </div>
    </div>
</div> --}}