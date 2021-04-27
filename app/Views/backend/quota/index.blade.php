<?php
    $attributes = getColumnConfig("quota");
?>
@extends('layouts.backend.layouts.main')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card-box list-ajax">
                @include('layouts.backend.elements._index_head', [
                'exportType' => config('constant.QUOTA')
                ])
                <div id="table-scroll" class="table-scroll">
                    <div id="main-table" class="main-table">
                        <table class="table table-bordered table-hover table-striped">
                            <thead id="head_content">
                                @include('layouts.backend.elements.column_config._head',[
                                    'entity'=>'quota',
                                    'is_action' => true,
                                    'attributes' => $attributes, 
                                    'configList'=> isset($configList) ? $configList : []])
                            </thead>
                            <tbody id="body_content">
                                @include('layouts.backend.elements.column_config._list',[
                                    'entity'=>'quota',
                                    'is_action' => true,
                                    'is_show_history' => false,
                                    'attributes' => $attributes, 
                                    'configList'=> isset($configList) ? $configList : []])
                            </tbody>
                        </table>
                        {{-- <table class="table table-bordered table-hover table-striped">
                            <thead>
                            <tr class="active">
                                @include('layouts.backend.elements.head_to_checkbox_all')
                                <th class="text-center">{{trans('actions.action')}}</th>
                                <th style="width: 150px">{!! Sorting::aLink('quota_code') !!}</th>
                                <th style="width: 150px">{!! Sorting::aLink('name') !!}</th>
                                <th style="width: 150px">{!! Sorting::aLink('vehicle_group_id') !!}</th>
                                <th style="width: 250px">{!! Sorting::aLink('title',trans('models.route.attributes.locations')) !!}</th>
                                <th style="width: 180px">{!! Sorting::aLink('total_cost') !!}</th>
                                <th style="width: 180px">{!! Sorting::aLink('distance') !!}</th>
                            </tr>
                            <tr class="filter-row">
                                <th></th>
                                <th></th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'quota_code'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'name'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'vehicle_group_id', 'element' => 'dropDown', 'options' => $vehicle_groups])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'title'])</th>
                                <th>@include('layouts.backend.elements._filter_number', ['field' => 'total_cost'])</th>
                                <th>@include('layouts.backend.elements._filter_number', ['field' => 'distance'])</th>
                            </tr>
                            </thead>
                            <tbody id="body_content">
                            @include('backend.quota._list')
                            </tbody>
                        </table> --}}
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

                <input type="hidden" id="list_info" data-url="{{ route('quota.ajaxSearch') }}"/>
                <input type="hidden" class="sort_field" value="">
                <input type="hidden" class="sort_type" value="">
                <input type="hidden" class="selected_item" value="{{ isset($selectedItem) ? implode(',', $selectedItem) : '' }}">
            </div>
        </div>
    </div>
    @include('layouts.backend.elements._context_menu')
    @include('layouts.backend.elements._show_modal')

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