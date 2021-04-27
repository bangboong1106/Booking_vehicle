@extends('layouts.backend.layouts.main')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card-box list-ajax">
                @include('layouts.backend.elements._index_head')
                <div id="table-scroll" class="table-scroll">
                    <div id="main-table" class="main-table">
                        <table class="table table-bordered table-hover table-striped table-striped">
                            <thead>
                            <tr class="active">
                                @include('layouts.backend.elements.head_to_checkbox_all')
                                <th class="text-center">{{trans('actions.action')}}</th>

                                <th>{!! Sorting::aLink('name') !!}</th>
                                <th>{!! Sorting::aLink('title') !!}</th>
                                <th>{!! Sorting::aLink('alert_type') !!}</th>
                                <th>{!! Sorting::aLink('ins_date') !!}</th>
                                <th>{!! Sorting::aLink('upd_date') !!}</th>
                            </tr>
                            <tr class="filter-row">
                                <th></th>
                                <th></th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'name'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'title'])</th>
                                <th>@include('layouts.backend.elements._filter_string', ['field' => 'alert_type', 'element' => 'dropDown', 'options' => config('system.alert_logs_type')])</th>
                                <th>@include('layouts.backend.elements._filter_string',['field' => 'ins_date', 'class' => 'datepicker'])</th>
                                <th>@include('layouts.backend.elements._filter_string',['field' => 'upd_date', 'class' => 'datepicker'])</th>
                            </tr>
                            </thead>
                            <tbody id="body_content">
                            @include('backend.notification_log._list')
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="text-center" id="paginate_content">
                    @include('layouts.backend.elements.pagination', ['isAjax'=> true])
                </div>

                <input type="hidden" id="list_info" data-url="{{ route('notification-log.ajaxSearch') }}"/>
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