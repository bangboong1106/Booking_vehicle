@php
    $create_route = isset($create_route) ? $create_route : $routePrefix.'.create';
    $create_label = 'Thêm mới';
    $massDeleteRoute= isset($massDeleteRoute) ? $massDeleteRoute : $routePrefix.'.massDestroy';
    $permission = str_replace('-', '_', $routePrefix);
@endphp

<div class="form-inline justify-content-between">
    <div class="row">
        <div class="btn-toolbar flex-wrap list-to-create" role="group" aria-label="">
            <div class="toolbar btn-group" role="group" aria-label="Basic example">
            </div>
            <div class="selected-toolbar btn-group" style="display: none">
                <p id="selected_item_count">{{ trans('models.common.selected_count') }}: <span></span></p>
                <button type="button" class="unselected-all-btn">
                    <a class="btn">
                        <i class="fa fa-window-close" aria-hidden="true" title="{{trans('actions.unselected_all')}}"></i>
                        {{trans('actions.unselected_all')}}
                    </a>
                </button>
                @can('edit order')
                    <button type="button" class="btn-import" 
                    id="btn-update-price" 
                    data-url="{{route('order-price.price')}}"
                    style="margin-right: 8px; border-left: 1px solid #c5c5c5">
                        <a class="btn" href="#"
                           title="Cập nhật cước phí vận chuyển đơn hàng">
                            <i class="fa fa-toggle-up" aria-hidden="true" title=" Cập nhật cước phí vận chuyển đơn hàng"></i>
                            Cập nhật cước phí
                        </a>
                    </button>
                @endcan
            </div>

        </div>
    </div>
    <input type="hidden" id="back_url_key" value="{{isset($backUrlKey) ? $backUrlKey : ''}}">
</div>


