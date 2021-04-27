<?php
    $attributes = getColumnConfig("import_history");
?>
@extends('layouts.backend.layouts.main')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card-box list-ajax">
                <div class="form-inline m-b-10 justify-content-between">
                    <div class="row">
                        <div class="col-md-12 text-xs-center">
                            <h4 class="page-title">{{$title}}</h4>
                        </div>
                    </div>
                </div>
                <div id="table-scroll" class="table-scroll">
                    <div id="main-table" class="main-table" data-disable-db-click="1">
                        <table class="table table-bordered table-hover table-striped">
                            <thead id="head_content">
                                @include('layouts.backend.elements.column_config._head',[
                                    'entity'=>'import_history',
                                    'is_action' => false,
                                    'attributes' => $attributes, 
                                    'configList'=> isset($configList) ? $configList : []])
                            </thead>
                            <tbody id="body_content">
                                @include('layouts.backend.elements.column_config._list',[
                                    'entity'=>'import_history',
                                    'is_action' => false,
                                    'is_show_history' => false,
                                    'attributes' => $attributes, 
                                    'configList'=> isset($configList) ? $configList : []])
                            </tbody>
                        </table>
                        {{-- <table class="table table-bordered table-hover table-striped">
                            <thead>
                            <tr class="active">
                                <th class="text-center" style="width: 100px">{{trans('actions.action')}}</th>
                                <th style="width: 400px;">{!! Sorting::aLink('file_name') !!}</th>
                                <th style="width: 200px">{!! Sorting::aLink('module') !!}</th>
                                <th style="width: 115px">{!! Sorting::aLink('type') !!}</th>
                                <th>{!! Sorting::aLink('success_record') !!}</th>
                                <th>{!! Sorting::aLink('error_record') !!}</th>
                                <th style="width: 115px">{!! Sorting::aLink('ins_id') !!}</th>
                                <th>{!! Sorting::aLink('ins_date') !!}</th>
                            </tr>
                            <tr class="filter-row">
                                <th></th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'file_name'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'module'])</th>
                                <th>{{ MyForm::dropDown('type_eq', isset($dataIndex['type_eq']) ? $dataIndex['status_eq'] : null,
                                    [
                                        'create' => trans('models.import_history.attributes.type_create'),
                                        'update' => trans('models.import_history.attributes.type_update'),
                                    ], true, ['class' => 'select2 filter-index']) }}</th>
                                <th>@include('layouts.backend.elements._filter_number', ['field' => 'success_record'])</th>
                                <th>@include('layouts.backend.elements._filter_number', ['field' => 'error_record'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'username'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'ins_date', 'class' => 'datepicker'])</th>
                            </tr>
                            </thead>
                            <tbody id="body_content">
                                @include('backend.import_history._list')
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

                <input type="hidden" id="list_info" data-url="{{ route('import-history.ajaxSearch') }}"/>
                <input type="hidden" class="sort_field" value="">
                <input type="hidden" class="sort_type" value="">
                <input type="hidden" class="selected_item" value="{{ isset($selectedItem) ? implode(',', $selectedItem) : '' }}">
            </div>
        </div>
    </div>
@endsection
