<?php
    $attributes = getColumnConfig("customer");
?>
@extends('layouts.backend.layouts.main')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card-box list-ajax">
                @include('layouts.backend.elements._index_head', [
                'exportType' => config('constant.CUSTOMER')
                ])
                    @include('layouts.backend.elements.column_config._wrap_column_config',[
                        'entity' => 'customer',
                        'attributes' => $attributes, 
                        'sort_field' => $sort_field,
                        'sort_type' => $sort_type,
                        'page_size' => $page_size,
                        'configList'=> isset($configList) ? $configList : []])
                    <div id="table-scroll" class="table-scroll">
                        <div id="main-table" class="main-table">
                            <table class="table table-bordered table-hover table-striped">
                                <thead id="head_content">
                                    @include('layouts.backend.elements.column_config._head',[
                                        'entity'=>'customer',
                                        'is_action' => true,
                                        'attributes' => $attributes, 
                                        'configList'=> isset($configList) ? $configList : []])
                                </thead>
                                <tbody id="body_content">
                                    @include('layouts.backend.elements.column_config._list',[
                                        'entity'=>'customer',
                                        'is_action' => true,
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

                <input type="hidden" id="list_info" data-url="{{ route('customer.ajaxSearch') }}"
                       data-url_head="{{ route('customer.generateHeadTable') }}"/>
                <input type="hidden" class="sort_field" value="">
                <input type="hidden" class="sort_type" value="">
                <input type="hidden" class="selected_item" value="{{ isset($selectedItem) ? implode(',', $selectedItem) : '' }}">
            </div>
        </div>
    </div>
    @include('layouts.backend.elements._context_menu')
    @include('layouts.backend.elements.modal_deduplicate')

    <div class="modal fade modal_add" id="modal_template">
        <div class="modal-dialog modal-md">
            <div class="modal-content"></div>
        </div>
    </div>
    <script>
        let headerRow = 9;
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