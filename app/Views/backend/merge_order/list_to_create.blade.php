@php
$create_route = isset($create_route) ? $create_route : $routePrefix.'.create';
$create_label = 'Thêm mới';
$massDeleteRoute= isset($massDeleteRoute) ? $massDeleteRoute : $routePrefix.'.massDestroy';
$permission = str_replace('-', '_', $routePrefix);
@endphp

<div class="form-inline m-b-10 justify-content-between">
    <div class="row">
        <div class="col-md-12 text-xs-center">
            <h4 class="page-title">{{ $title }}</h4>
        </div>
    </div>
    <div class="row">
        <div class="btn-toolbar flex-wrap list-to-create" role="group" aria-label="">

            <div class="toolbar btn-group" role="group" aria-label="Basic example">
                <button type="button" class="btn-config">
                    <a class="btn btn-config-toggle" data-toggle="tooltip" data-placement="top" title=""
                        data-original-title="" href="#">
                        <i class="fa fa-cog"></i> Tùy chỉnh
                    </a>
                </button>
            </div>
            <div class="selected-toolbar btn-group" style="display: none">
                <p id="selected_item_count">{{ trans('models.common.selected_count') }}: <span></span></p>
                <button type="button" class="unselected-all-btn">
                    <a class="btn">
                        <i class="fa fa-window-close" aria-hidden="true"
                            title="{{ trans('actions.unselected_all') }}"></i>
                        {{ trans('actions.unselected_all') }}
                    </a>
                </button>
                @can('edit ' . $permission)
                    <button type="button" class="btn-confirm-create-route" id="btn-confirm-create-route"
                        data-default={{ route('merge-order.default') }}
                        data-url="{{ route('merge-order.mergeOrderSave') }}">
                        <a class="btn" href="#">
                            <i class="fa fa-plus"></i> Tạo chuyến
                        </a>
                    </button>
                    <button type="button" class="btn-merge-order" id="btn-merge-order"
                        data-url="{{ route('merge-order.mergeOrderSave') }}">
                        <a class="btn" href="#">
                            <i class="fa fa-compress"></i> Ghép chuyến
                        </a>
                    </button>
                @endcan
            </div>

        </div>
    </div>
    <input type="hidden" id="back_url_key" value="{{ isset($backUrlKey) ? $backUrlKey : '' }}">
</div>
