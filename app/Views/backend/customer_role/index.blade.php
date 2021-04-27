<?php
    $attributes = getColumnConfig("role");
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
                                    'entity'=>'role',
                                    'is_action' => true,
                                    'attributes' => $attributes, 
                                    'configList'=> isset($configList) ? $configList : []])
                            </thead>
                            <tbody id="body_content">
                                @include('layouts.backend.elements.column_config._list',[
                                    'entity'=>'role',
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
                                <th>{!! Sorting::aLink('title') !!}</th>
                                <th>{!! Sorting::aLink('name') !!}</th>
                                <th>{!! Sorting::aLink('ins_date') !!}</th>
                                <th>{!! Sorting::aLink('upd_date') !!}</th>
                            </tr>
                            <tr class="filter-row">
                                <th></th>
                                <th></th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'title'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'name'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'ins_date', 'class' => 'datepicker'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'upd_date', 'class' => 'datepicker'])</th>
                            </tr>
                            </thead>
                            <tbody id="body_content">
                            @include('backend.role._list')
                            </tbody>
                        </table> --}}
                    </div>
                </div>
                <div class="text-center" id="paginate_content">
                    @include('layouts.backend.elements.pagination', ['isAjax'=> true])
                </div>

                <input type="hidden" id="list_info" data-url="{{ route('role.ajaxSearch') }}"/>
                <input type="hidden" class="sort_field" value="">
                <input type="hidden" class="sort_type" value="">
                <input type="hidden" class="selected_item" value="{{ isset($selectedItem) ? implode(',', $selectedItem) : '' }}">
            </div>
        </div>
    </div>
    @include('layouts.backend.elements._context_menu')
    @include('layouts.backend.elements._show_modal')
@endsection
@push('scripts')
    <?php
    $jsFiles = [
        'vendor/utils/table-responsive'
    ];
    ?>
    {!! loadFiles($jsFiles, $area, 'js') !!}

@endpush