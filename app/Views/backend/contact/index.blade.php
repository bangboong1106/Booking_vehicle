<?php
    $attributes = getColumnConfig("contact");
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
                                    'entity'=>'contact',
                                    'is_action' => true,
                                    'attributes' => $attributes, 
                                    'configList'=> isset($configList) ? $configList : []])
                            </thead>
                            <tbody id="body_content">
                                @include('layouts.backend.elements.column_config._list',[
                                    'entity'=>'contact',
                                    'is_action' => true,
                                    'is_show_history' => false,
                                    'attributes' => $attributes, 
                                    'configList'=> isset($configList) ? $configList : []])
                            </tbody>
                        </table>
                </div>
                {{-- <div id="table-scroll" class="table-scroll">
                    <div id="main-table" class="main-table">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                            <tr class="active">
                                @include('layouts.backend.elements.head_to_checkbox_all')
                                <th class="text-center">{{trans('actions.action')}}</th>
                                <th style="width: 220px">{!! Sorting::aLink('contact_name') !!}</th>
                                <th style="width: 200px">{!! Sorting::aLink('phone_number') !!}</th>
                                <th style="width: 250px">{!! Sorting::aLink('email') !!}</th>
                                <th style="min-width: 350px">{!! Sorting::aLink('full_address') !!}</th>
                                <th style="width: 200px">{!! Sorting::aLink('active') !!}</th>
                            </tr>
                            <tr class="filter-row">
                                <th class="text-center"></th>
                                <th></th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'contact_name'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'phone_number'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'email'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'full_address'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'active', 'element' => 'dropDown', 'options' => config('system.active')])</th>
                            </tr>
                            </thead>
                            <tbody id="body_content">
                            @include('backend.contact._list')
                            </tbody>
                        </table>
                    </div> --}}

                </div>
                <div class="row" id="paginate_content">
                    <div class="col-md-5 col-sm-12 m-t-15">
                        @include('layouts.backend.elements.pagination_info')

                    </div>
                    <div class="col-md-7 col-sm-12">
                        @include('layouts.backend.elements.pagination', ['isAjax'=> true])
                    </div>
                </div>

                <input type="hidden" id="list_info" data-url="{{ route('contact.ajaxSearch') }}"/>
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