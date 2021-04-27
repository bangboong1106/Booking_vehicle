@php
$create_route = isset($create_route) ? $create_route : $routePrefix.'.create';
$create_label = 'Thêm mới';
$massDeleteRoute= isset($massDeleteRoute) ? $massDeleteRoute : $routePrefix.'.massDestroy';
$permission = str_replace('-', '_', $routePrefix);
@endphp

<div class="form-inline m-b-10 justify-content-between">
    <div class="row">
        <div class="btn-toolbar flex-wrap list-to-create" role="group" aria-label="">
            <div class="toolbar btn-group" role="group" aria-label="Basic example">
                <button type="button" class="btn-create">
                    @can('add ' . $permission)
                        <a class="btn l-create" href="{{ backUrl($create_route) }}">
                            <i class="fa fa-plus"></i>
                        </a>
                    @endcan
                </button>
                <button style="border: 1px solid #c5c5c5; border-left: 0;">
                    <a class="dropdown-item import" id="price-button" href="#"
                        data-target="#price_modal" data-toggle="modal">
                        <i class="fa fa-money"style="margin-right: 8px"></i>Tính giá tự động
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
            </div>
            <div class="selected-toolbar btn-group" style="display: none">
                @can('delete ' . $permission)
                    <button type="button" class="mass-destroy-btn">
                        <a class="btn" href="#mass_destroy_confirm" data-toggle="modal"
                            title="{{ trans('actions.destroy') }}" data-action="{{ route($massDeleteRoute) }}">
                            <i class="fa fa-trash" aria-hidden="true" title="{{ trans('actions.destroy') }}"></i>
                            {{ trans('actions.massDestroy') }}
                        </a>
                    </button>
                @endcan

            </div>
            <div class="toolbar dropdown action">
                <a class="btn dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    ...
                </a>

                <div class="dropdown-menu  dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                    @if (!empty($showDeleted))
                        <a class="dropdown-item" href="#" id="deleted_btn">
                            <i class="fa fa-trash" aria-hidden="true" data-toggle="tooltip" data-placement="top"
                                title="" data-original-title="Danh sách bản ghi đã xóa"></i> Thùng rác
                        </a>
                        <div class="dropdown-divider"></div>
                    @endif
                    <a class="dropdown-item" target="_blank"
                        href="{{ env('HELP_DOMAIN', '') . trans('helps.' . $routeName) }}" data-toggle="tooltip"
                        data-placement="top" title="" data-original-title="{{ trans('actions.help') }}">
                        <i class="fa fa-question-circle"></i>
                        <span>Trợ giúp</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="back_url_key" value="{{ isset($backUrlKey) ? $backUrlKey : '' }}">
</div>
