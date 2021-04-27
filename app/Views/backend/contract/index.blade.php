<?php
    $attributes = getColumnConfig("contract");
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
                                    'entity'=>'contract',
                                    'is_action' => true,
                                    'attributes' => $attributes, 
                                    'configList'=> isset($configList) ? $configList : []])
                            </thead>
                            <tbody id="body_content">
                                @include('layouts.backend.elements.column_config._list',[
                                    'entity'=>'contract',
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
                                <th>{!! Sorting::aLink('contract_no') !!}</th>
                                <th>{!! Sorting::aLink('customer_id', trans('models.customer.name')) !!}</th>
                                <th>{!! Sorting::aLink('issue_date') !!}</th>
                                <th>{!! Sorting::aLink('expired_date') !!}</th>
                                <th>{!! Sorting::aLink('type') !!}</th>
                                <th>{!! Sorting::aLink('status') !!}</th>
                            </tr>
                            <tr class="filter-row">
                                <th></th>
                                <th></th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'contract_no'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'customer|full_name'])</th>
                                <th>@include('layouts.backend.elements._filter_number',['field' => 'issue_date', 'class' => 'datepicker'])</th>
                                <th>@include('layouts.backend.elements._filter_number',['field' => 'expired_date', 'class' => 'datepicker'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'contract_type|name'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'status', 'element' => 'dropDown', 'options' => config('system.contract_status')])</th>
                            </tr>
                            </thead>
                            <tbody id="body_content">
                            @include('backend.contract._list')
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

                <input type="hidden" id="list_info" data-url="{{ route('contract.ajaxSearch') }}"/>
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