<?php
    $attributes = getColumnConfig("driver_config_file");
?>
@extends('layouts.backend.layouts.main')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card-box list-ajax">
                @include('layouts.backend.elements._index_head')
                <div id="table-scroll" class="table-scroll">
                    <div id="main-table" class="main-table">
                        <table class="table table-bordered table-hover table-striped">
                            <thead id="head_content">
                                @include('layouts.backend.elements.column_config._head',[
                                    'entity'=>'driver_config_file',
                                    'is_action' => true,
                                    'attributes' => $attributes, 
                                    'configList'=> isset($configList) ? $configList : []])
                            </thead>
                            <tbody id="body_content">
                                @include('layouts.backend.elements.column_config._list',[
                                    'entity'=>'driver_config_file',
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
                                <th>{!! Sorting::aLink('file_name') !!}</th>
                                <th>{!! Sorting::aLink('allow_extension') !!}</th>
                                <th>{!! Sorting::aLink('is_show_register') !!}</th>
                                <th>{!! Sorting::aLink('is_show_expired') !!}</th>
                                <th>{!! Sorting::aLink('active') !!}</th>
                            </tr>
                            <tr class="filter-row">
                                <th></th>
                                <th></th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'file_name'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'allow_extension', 'element' => 'dropDown', 'options' => config('system.file_type')])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'is_show_register', 'element' => 'dropDown', 'options' => config('system.option')])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'is_show_expired', 'element' => 'dropDown', 'options' => config('system.option')])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'active', 'element' => 'dropDown', 'options' => config('system.active')])</th>
                            </tr>
                            </thead>
                            <tbody id="body_content">
                            @include('backend.driver_config_file._list')
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

                <input type="hidden" id="list_info" data-url="{{ route('driver-config-file.ajaxSearch') }}"/>
                <input type="hidden" class="sort_field" value="">
                <input type="hidden" class="sort_type" value="">
                <input type="hidden" class="selected_item" value="{{ isset($selectedItem) ? implode(',', $selectedItem) : '' }}">
            </div>
        </div>
    </div>
    @include('layouts.backend.elements._context_menu')
@endsection
@push('scripts')
    <?php
    $jsFiles = [
        'vendor/utils/table-responsive'
    ];
    ?>
    {!! loadFiles($jsFiles, $area, 'js') !!}

@endpush