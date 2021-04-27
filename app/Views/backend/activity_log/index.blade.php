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
                            <thead>
                                <tr class="active">
                                    <th style="width: 300px;">{!! Sorting::aLink('username') !!}</th>
                                    <th style="width: 300px;">{!! Sorting::aLink('email') !!}</th>
                                    <th>{!! Sorting::aLink('description') !!}</th>
                                    <th>{!! Sorting::aLink('created_at') !!}</th>
                                </tr>
                                <tr class="filter-row">
                                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'username'])</th>
                                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'email'])</th>
                                    <th>@include('layouts.backend.elements._filter_string', ['field' => 'description'])</th>
                                    <th>@include('layouts.backend.elements._filter_string', [
                                        'field' => 'created_at',
                                        'class' => 'datepicker'
                                    ])</th>
                                </tr>
                            </thead>
                            <tbody id="body_content">
                                @include('backend.activity_log._list')
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

                <input type="hidden" id="list_info" data-url="{{ route('activity-log.ajaxSearch') }}"/>
                <input type="hidden" class="sort_field" value="">
                <input type="hidden" class="sort_type" value="">
                <input type="hidden" class="selected_item" value="{{ isset($selectedItem) ? implode(',', $selectedItem) : '' }}">
            </div>
        </div>
    </div>
@endsection
