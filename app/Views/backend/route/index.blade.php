<?php
    $attributes = getColumnConfig("route");
    if (!function_exists('countStatus')) {
        function countStatus($groupData, $status ) {
            $st = $groupData->filter(function($item) use ($status) {
                    return $item->route_status == $status;
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
                @include('backend.route._index_head')
                <div id="group-bar" class="btn-group btn-group-toggle" style="margin-bottom: 8px">
                    <label class="btn border-secondary text-secondary" data-status={{ config("constant.status_incomplete") }}>
                        <span class="m-b-10 counter" data-status={{ config("constant.status_incomplete") }} data-field="route_status">
                            {{ countStatus($groupData, config("constant.status_incomplete")) }}
                        </span>
                        <span>Chưa hoàn thành</span>
                    </label>
                    <label class="btn border-success text-success" data-status={{ config("constant.status_complete") }}>
                        <span class="m-b-10 counter" data-status={{ config("constant.status_complete") }} data-field="route_status">
                            {{ countStatus($groupData, config("constant.status_complete")) }}
                        </span>
                        <span>Hoàn thành</span>
                    </label>
                    <label class="btn border-dark text-dark" data-status={{ config("constant.status_cancel") }}>
                        <span class="m-b-10 counter" data-status={{ config("constant.status_cancel") }} data-field="route_status">
                            {{ countStatus($groupData, config("constant.status_cancel")) }}
                        </span>
                        <span>Hủy</span>
                    </label>
                </div>
                <div id="table-scroll" class="table-scroll">
                    <div id="main-table" class="main-table">
                        <table class="table table-bordered table-hover table-striped">
                            <thead id="head_content">
                                @include('layouts.backend.elements.column_config._head',[
                                    'entity'=>'route',
                                    'is_action' => true,
                                    'attributes' => $attributes, 
                                    'configList'=> isset($configList) ? $configList : []])
                            </thead>
                            <tbody id="body_content">
                                @include('layouts.backend.elements.column_config._list',[
                                    'entity'=>'route',
                                    'is_action' => true,
                                    'is_show_history' => false,
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

                <input type="hidden" id="list_info" data-url="{{ route('route.ajaxSearch') }}"
                       data-url_head="{{ route('route.generateHeadTable') }}"/>
                <input type="hidden" class="sort_field" value="">
                <input type="hidden" class="sort_type" value="">
                <input type="hidden" class="selected_item" value="{{ isset($selectedItem) ? implode(',', $selectedItem) : '' }}">
            </div>
        </div>
    </div>
    @include('layouts.backend.elements._context_menu')
    @include('layouts.backend.elements._show_modal')
    @include('layouts.backend.elements.excel._import_export_modal')
    @include('layouts.backend.elements.modal_lock') 

    <div class="modal fade modal_add" id="modal_approval">
        <div class="modal-dialog modal-lg">
            <div class="modal-content"></div>
        </div>
    </div>
    <div class="modal fade modal_add" id="modal_price_policy" data-price-policy={{route('price-quote.combo-price-quote')}}>
        <div class="modal-dialog modal-xlg">
            <div class="modal-content"></div>
        </div>
    </div>
    <div class="modal fade modal_add" id="modal_payroll" data-payroll={{route('payroll.combo-payroll')}}>
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
        let headerRow = 9;
        let headerCost = 10;

    </script>
@endsection
@push('scripts')

    <?php
    $jsFiles = [
        'vendor/utils/table-responsive'
    ];
    ?>
    {!! loadFiles($jsFiles, $area, 'js') !!}

@endpush