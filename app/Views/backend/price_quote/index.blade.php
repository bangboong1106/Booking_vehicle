<?php
    $attributes = getColumnConfig("price_quote");
?>
@extends('layouts.backend.layouts.main')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card-box list-ajax">
                @include('backend.price_quote._index_head')

                {{-- @include('layouts.backend.elements.column_config._wrap_column_config',[
                    'entity' => 'price_quote',
                    'sort_field' => $sort_field,
                    'sort_type' => $sort_type,
                    'page_size' => $page_size,
                    'attributes' => $attributes, 
                    'configList'=> isset($configList) ? $configList : []]) --}}
                <div id="table-scroll" class="table-scroll">
                    <div id="main-table" class="main-table">
                        <table class="table table-bordered table-hover table-striped">
                            <thead id="head_content">
                                @include('layouts.backend.elements.column_config._head',[
                                    'entity'=>'price_quote',
                                    'is_action' => true,
                                    'attributes' => $attributes, 
                                    'configList'=> isset($configList) ? $configList : []])
                            </thead>
                            <tbody id="body_content">
                                @include('layouts.backend.elements.column_config._list',[
                                    'entity'=>'price_quote',
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

                <input type="hidden" id="list_info" data-url="{{ route('price-quote.ajaxSearch') }}"/>
                <input type="hidden" class="sort_field" value="">
                <input type="hidden" class="sort_type" value="">
                <input type="hidden" class="selected_item"
                       value="{{ isset($selectedItem) ? implode(',', $selectedItem) : '' }}">
            </div>
        </div>
    </div>
    @include('layouts.backend.elements._context_menu')
    @include('layouts.backend.elements._show_modal')
    <div class="modal fade" id="price_modal" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tính giá tự động</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body price-body">
                    <div class="row form-group">
                        <div class="col-md-12">
                            Loại thời gian<br/>
                            <div class="input-group">
                                <select class="select2" id="dayCondition">
                                    <option value="1" >Thời gian nhận hàng dự kiến
                                    </option>
                                    <option value="2" >Thời gian nhận hàng thực tế
                                    </option>
                                    <option value="3" >Thời gian trả hàng dự kiến
                                    </option>
                                    <option value="4" >Thời gian trả hàng thực tế
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-12">
                                Thời gian</br>
                                <div id="price-date-range"
                                        class="pull-right form-control">
                                    <span></span>
                                    <i class="pull-right glyphicon-calendar fa fa-calendar"></i>
                                </div>
                        </div>
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-success btn-price" id="btn-price" data-url="{{route('price-quote.auto-price-quote')}}">Tính giá
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <?php
    $jsFiles = [
        'vendor/utils/table-responsive'
    ];
    ?>
    {!! loadFiles($jsFiles, $area, 'js') !!}

@endpush