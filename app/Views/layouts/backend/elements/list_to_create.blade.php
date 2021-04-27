@php
    $create_route = isset($create_route) ? $create_route : $routePrefix.'.create';
    $create_label = 'Thêm mới';
    $massDeleteRoute= isset($massDeleteRoute) ? $massDeleteRoute : $routePrefix.'.massDestroy';
    $permission = str_replace('-', '_', $routePrefix);
    $exportType = isset($exportType) ? $exportType : config('constant.ORDER');
@endphp
<div class="btn-group flex-wrap list-to-create" role="group" aria-label="">
    <div class="toolbar btn-group" role="group" aria-label="Basic example">
        <button type="button" class="btn-create">
            @can('add ' . $permission)
                <a class="btn l-create" href="{{ backUrl($create_route) }}">
                    <i class="fa fa-plus"></i>
                </a>
            @endcan
        </button>
        @if (!empty($excel))
            @can('import ' . $permission)
                @if(Session::has('open_import_excel'))
                    <script type="text/javascript">
                        $(function () {
                            $('.upload').trigger('click');
                        })
                    </script>
                @endif
                <button type="button" class="btn-import" style="border-radius: 0">
                    <a class="btn upload" href="#" data-target="#import_excel" data-toggle="modal">
                        <i class="fa fa-upload"></i> {{ trans('actions.import') }}
                    </a>
                </button>
            @endcan
            @can('export ' . $permission)
                <button type="button" class="btn-import" style="border-radius: 0">
                    @if (empty($excelUpdate))
                        <a class="btn update-data-export" href="#" data-target="#export_excel"
                           data-url="{{ isset($routePrefix) ? route($routePrefix . '.exportConfirm') : '' }}">
                            <i class="fa fa-download"></i> {{ trans('actions.export_excel') }}
                        </a>
                    @else
                        <a class="btn" href="{{ route($routePrefix . '.exportTemplate', ['update' => 1]) }}"
                           target="_blank">
                            <i class="fa fa-download"></i> {{ trans('actions.export_excel') }}
                        </a>
                    @endif
                </button>
            @endcan
        @endif
        @if ($routePrefix !== 'quota' && $routePrefix !== 'location')
            <button type="button" class="btn-config">
                <a class="btn btn-config-toggle" data-toggle="tooltip" data-placement="top" href="#">
                    <i class="fa fa-cog"></i> Tùy chỉnh
                </a>
            </button>
        @endif
    </div>
    <div class="selected-toolbar btn-group" style="display: none">
        <p id="selected_item_count">{{ trans('models.common.selected_count') }}: <span></span></p>
        <button type="button" class="unselected-all-btn">
            <a class="btn">
                <i class="fa fa-window-close" aria-hidden="true" title="{{ trans('actions.unselected_all') }}"></i>
                {{ trans('actions.unselected_all') }}
            </a>
        </button>
        @can('export ' . $permission)
            @if ($excelUpdate)
                <button type="button" class="btn-import" style="    border-left: 1px solid #c5c5c5;
                            border-right: 1px solid #c5c5c5;">
                    <a class="btn" href="#" data-url="{{ route($routePrefix . '.exportTemplate') }}"
                       id="export_selected">
                        <i class="fa fa-download"></i> {{ trans('actions.export_excel') }}
                    </a>
                </button>
            @endif
            <button type="button" class="btn-print-template">
                <a class="btn" href="#" data-url={{Auth::user()->role == 'admin' ? route('template.printCustom') : route('partner-template.printCustom')}} data-type="{{ $exportType }}"
                   id="print_template_selected">
                    <i class="fa fa-print"></i> {{ trans('actions.print-template') }}
                </a>
            </button>
        @endcan
        @if (isset($routePrefix) && ($routePrefix == 'location' || $routePrefix == 'customer'))
            @can('edit ' . $permission)
                <button type="button" class="mass-deduplicate-btn">
                    <a class="btn" href="#" title="{{ trans('actions.deduplicate') }}" id="btn-deduplicate"
                       data-url="{{ route($routePrefix . '.deduplicate') }}">
                        <i class="fa fa-compress" aria-hidden="true" title="{{ trans('actions.deduplicate') }}"></i>
                        {{ trans('actions.massDeduplicate') }}
                    </a>
                </button>
            @endcan
        @endif
        @can('delete ' . $permission)
            <button type="button" class="mass-destroy-btn">
                <a class="btn" href="#mass_destroy_confirm" data-toggle="modal" title="{{ trans('actions.destroy') }}"
                   data-action="{{ route($massDeleteRoute) }}">
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
            {{--@if (isset($routePrefix) && $routePrefix == 'order-customer')
                @can('lock ' . $permission)
                    <a class="dropdown-item" data-url="{{ route('order-customer.lock') }}" id="btn_lock">
                        <i class="fa fa-lock"></i>Khoá sổ
                    </a>
                @endcan
                @can('unlock ' . $permission)
                    <a class="dropdown-item" data-url="{{ route('order-customer.unlock') }}" id="btn_unlock">
                        <i class="fa fa-unlock"></i>Mở khoá sổ
                    </a>
                @endcan
                <a class="dropdown-item" href="#" id="order_client_btn">
                    <i class="fa fa-barcode" aria-hidden="true" data-toggle="tooltip" data-placement="top" title=""
                        data-original-title="Danh sách đơn hàng vãng lai"></i> Đơn hàng vãng lai
                </a>
                <div class="dropdown-divider"></div>
            @endif--}}
            @if (!empty($showDeleted))
                <a class="dropdown-item" href="#" id="deleted_btn">
                    <i class="fa fa-trash" aria-hidden="true" data-toggle="tooltip" data-placement="top" title=""
                       data-original-title="Danh sách bản ghi đã xóa"></i> Thùng rác
                </a>
                <div class="dropdown-divider"></div>
            @endif
            <a class="dropdown-item" target="_blank" href="{{ env('HELP_DOMAIN', '') . trans('helps.' . $routeName) }}"
               data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ trans('actions.help') }}">
                <i class="fa fa-question-circle"></i>
                <span>Trợ giúp</span>
            </a>
        </div>
    </div>

</div>
