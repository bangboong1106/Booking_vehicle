<?php
    $attributes = getColumnConfig("customer_group");
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
                                    'entity'=>'customer_group',
                                    'is_action' => true,
                                    'attributes' => $attributes, 
                                    'configList'=> isset($configList) ? $configList : []])
                            </thead>
                            <tbody id="body_content">
                                @include('layouts.backend.elements.column_config._list',[
                                    'entity'=>'customer_group',
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
                                <th style="width: 200px;">{!! Sorting::aLink('customer_group.code',trans('models.customer_group.attributes.code')) !!}</th>
                                <th style="width: 200px;">{!! Sorting::aLink('name') !!}</th>
                                <th style="width: 250px;">{!! Sorting::aLink('customer_name','Danh sách khách hàng') !!}</th>
                                <th style="width: 180px;">{!! Sorting::aLink('ins_date') !!}</th>
                                <th style="width: 180px;">{!! Sorting::aLink('upd_date') !!}</th>
                            </tr>
                            <tr class="filter-row">
                                <th></th>
                                <th></th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'customer_group|code'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'name'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'customer_name'])</th>
                                <th>@include('layouts.backend.elements._filter_number',['field' => 'ins_date', 'class' => 'datepicker'])</th>
                                <th>@include('layouts.backend.elements._filter_number',['field' => 'upd_date', 'class' => 'datepicker'])</th>
                            </tr>
                            </thead>
                            <tbody id="body_content">
                            @include('backend.customer_group._list')
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

                <input type="hidden" id="list_info" data-url="{{ route('customer-group.ajaxSearch') }}"/>
                <input type="hidden" class="sort_field" value="">
                <input type="hidden" class="sort_type" value="">
                <input type="hidden" class="back-url" value="{{ getBackUrl() }}">
                <input type="hidden" class="selected_item"
                       value="{{ isset($selectedItem) ? implode(',', $selectedItem) : '' }}">
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
    @push('scripts')
        <script>
            var urlCustomer= '{{route('customer.combo-customer')}}?all=1',
                backendUri = '{{getBackendDomain()}}';
        </script>
    @endpush
@endpush