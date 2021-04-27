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

           {{-- @can('import ' . $permission)
                <button type="button" class="btn-import" style="border-radius: 0">
                    <a class="btn upload" href="#" data-target="#import_excel" data-toggle="modal">
                        <i class="fa fa-upload"></i> {{ trans('actions.import') }}
                    </a>
                </button>
            @endcan--}}

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

                {{--@can('export ' . $permission)
                    <button type="button" class="btn-print-template">
                        <a class="btn" href="#" data-url="{{ route('partner-template.printCustom') }}"
                            data-type="{{ config('constant.ORDER') }}" id="print_template_selected">
                            <i class="fa fa-print"></i> {{ trans('actions.print-template') }}
                        </a>

                    </button>
                @endcan

                @can('export ' . $permission)
                    <button type="button" class="btn-import" style="border-radius: 0">
                        <a class="btn" href="#" data-url="{{ route('order.exportUpdate') }}" id="export_selected">
                            <i class="fa fa-download"></i> {{ trans('actions.export') }}
                        </a>
                    </button>
                @endcan--}}

                @can('edit ' . $permission)
                    <button type="button" class="btn-create" id="btn-confirm-create-route"
                            data-default={{ route('partner-order.default') }}
                                    data-url="{{ route('partner-order.mergeOrderSave') }}">
                        <a class="btn" href="#">
                            <i class="fa fa-plus"></i> Tạo chuyến
                        </a>
                    </button>
                    <button type="button" class="btn-import" id="btn-merge-order"
                            data-url="{{ route('partner-order.mergeOrderSave') }}">
                        <a class="btn" href="#">
                            <i class="fa fa-compress"></i> Ghép chuyến
                        </a>
                    </button>
                    <button type="button" class="btn-config" id="btn_confirm_update_documents">
                        <a class="btn mass-update-documents" href="#mass_update_documents" data-toggle="modal"
                           title="Đã thu đủ chứng từ">
                            <i class="fa fa-toggle-up" aria-hidden="true" title=" Đã thu đủ chứng từ"></i>
                            {{ trans('actions.update_license') }}
                        </a>
                    </button>
                @endcan
                <div class="dropdown action">
                    <a class="btn dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        ...
                    </a>

                    <div class="dropdown-menu  dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                        @can('edit ' . $permission)
                            <a class="dropdown-item" data-toggle="modal"
                               data-target="#dialog_accept_order">
                                <i class="fa fa-check"></i>Xác nhận
                            </a>
                            <div class="dropdown-divider"></div>

                            <a class="dropdown-item" data-toggle="modal"
                               data-target="#dialog_order_complete">
                                <i class="fa fa-check-circle"></i>Hoàn thành
                            </a>
                            <div class="dropdown-divider"></div>

                            <a class="dropdown-item" data-toggle="modal"
                               data-target="#dialog_request_edit">
                                <i class="fa fa-edit"></i>Yêu cầu sửa
                            </a>
                            <div class="dropdown-divider"></div>

                            <a class="dropdown-item" data-toggle="modal"
                               data-target="#dialog_order_cancel">
                                <i class="fa fa-remove"></i>Hủy
                            </a>
                            <div class="dropdown-divider"></div>

                           {{-- <a class="dropdown-item" id="btn_confirm_update_revenue"
                               data-url={{ route('order.update-revenue') }}>
                                <i class="fa fa-money"
                                   title="Cập nhật doanh thu"></i>{{ trans('actions.update_revenue') }}
                            </a>
                            <div class="dropdown-divider"></div>

                            @if (env('SUPPORT_CAR_TRANSPORTATION', false))
                                <a class="dropdown-item" id="btn_confirm_update_vin_no">
                                    <i class="fa fa-truck"
                                       title="Cập nhật số khung, số model"></i>{{ trans('actions.update_vin_no') }}
                                </a>
                                <div class="dropdown-divider"></div>
                            @endif--}}
                        @endcan
                       {{-- @can('export ' . $permission)
                            <a class="dropdown-item" class="btn-qrcode" style="border-radius: 0">
                                <i class="fa fa-qrcode" style="margin-right: 8px;"></i> {{ trans('actions.qr_code') }}
                            </a>
                            <a class="dropdown-item" class="btn-print-bill">
                                <i class="fa fa-print" style="margin-right: 8px;"></i> In vận đơn giao
                            </a>
                        @endcan--}}
                    </div>
                </div>
            </div>
            <div class="toolbar dropdown action">
                <a class="btn dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">
                    ...
                </a>

                <div class="dropdown-menu  dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                    @can('export ' . $permission)
                        <a class="dropdown-item import" id="declaration_export-button" href="#"
                           data-target="#declaration_export" data-toggle="modal">
                            <i class="fa fa-table"></i> Xuất bảng kê
                        </a>
                    @endcan
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
