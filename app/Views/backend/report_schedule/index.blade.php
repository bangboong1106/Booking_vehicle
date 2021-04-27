<?php
    $attributes = getColumnConfig("report_schedule");
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
                                    'entity'=>'report_schedule',
                                    'is_action' => true,
                                    'attributes' => $attributes, 
                                    'configList'=> isset($configList) ? $configList : []])
                            </thead>
                            <tbody id="body_content">
                                @include('layouts.backend.elements.column_config._list',[
                                    'entity'=>'report_schedule',
                                    'is_action' => true,
                                    'is_show_history' => false,
                                    'attributes' => $attributes, 
                                    'configList'=> isset($configList) ? $configList : []])
                          
                        </tbody>
                        </table>
                        {{-- <table class="table table-bordered table-hover table-striped table-striped">
                            <thead>
                            <tr class="active">
                                @include('layouts.backend.elements.head_to_checkbox_all')
                                <th class="text-center">{{trans('actions.action')}}</th>

                                <th style="width: 200px">{!! Sorting::aLink('description') !!}</th>
                                <th style="width: 250px">{!! Sorting::aLink('email') !!}</th>
                                <th style="width: 180px">{!! Sorting::aLink('date_from') !!}</th>
                                <th style="width: 180px">{!! Sorting::aLink('date_to') !!}</th>
                                <th style="width: 180px">{!! Sorting::aLink('schedule_type') !!}</th>
                                <th style="width: 150px">{!! Sorting::aLink('time_to_send') !!}</th>
                            </tr>
                            <tr class="filter-row">
                                <th></th>
                                <th></th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'description'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'email'])</th>
                                <th>@include('layouts.backend.elements._filter_number', ['field' => 'date_from', 'class' => 'datepicker'])</th>
                                <th>@include('layouts.backend.elements._filter_number', ['field' => 'date_to', 'class' => 'datepicker'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'schedule_type', 'element' => 'dropDown', 'options' => config('system.schedule_type')])</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody id="body_content">
                            @include('backend.report_schedule._list')
                            </tbody>
                        </table> --}}
                    </div>

                </div>
                <div class="text-center" id="paginate_content">
                    @include('layouts.backend.elements.pagination', ['isAjax'=> true])
                </div>

                <input type="hidden" id="list_info" data-url="{{ route('report-schedule.ajaxSearch') }}"/>
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