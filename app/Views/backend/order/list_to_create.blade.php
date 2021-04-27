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
                {{--       <button type="button" class="btn-create">
                           @can('add ' . $permission)
                               <a class="btn l-create" href="{{ backUrl($create_route) }}">
                                   <i class="fa fa-plus"></i>
                               </a>
                           @endcan
                       </button>
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
                           --}}{{-- <button id="btn-editor" type="button" class="btn-import"
                               style="border-radius: 0">
                               <i class="fa fa-edit"></i> {{ trans('actions.importSheet') }}
                           </button> --}}{{--
                       @endcan
                       @can('export ' . $permission)
                           <button type="button" class="btn-import" style="border-radius: 0">
                               <a class="btn update-data-export" href="{{ route('order.exportUpdate') }}" target="_blank">
                                   <i class="fa fa-download"></i> {{ trans('actions.export_excel') }}
                               </a>
                           </button>
                       @endcan--}}
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
                @can('delete ' . $permission)
                    <button type="button" class="mass-destroy-btn">
                        <a class="btn" href="#mass_destroy_confirm" data-toggle="modal"
                           title="{{ trans('actions.destroy') }}" data-action="{{ route($massDeleteRoute) }}">
                            <i class="fa fa-trash" aria-hidden="true" title="{{ trans('actions.destroy') }}"></i>
                            {{ trans('actions.massDestroy') }}
                        </a>
                    </button>
                @endcan

                 @can('export ' . $permission)
                     <button type="button" class="btn-print-template">
                         <a class="btn" href="#" data-url="{{ route('template.printCustom') }}"
                             data-type="{{ config('constant.ORDER') }}" id="print_template_selected">
                             <i class="fa fa-print"></i> {{ trans('actions.print-template') }}
                         </a>

                     </button>
                 @endcan


                 {{-- @can('export ' . $permission)
                     <button type="button" class="btn-import" style="border-radius: 0">
                         <a class="btn" href="#" data-url="{{ route('order.exportUpdate') }}" id="export_selected">
                             <i class="fa fa-download"></i> {{ trans('actions.export') }}
                         </a>
                     </button>
                 @endcan --}}
                @can('edit ' . $permission)
                    <button type="button" class="mass-destroy-btn" id="btn_confirm_merge_order"
                            data-url={{ route('order.mergeOrderConfirm') }}>
                        <a class="btn" data-toggle="modal"
                           title="Gộp đơn">
                            <i class="fa fa-compress" aria-hidden="true" title=""></i>
                            {{ trans('actions.merge_order') }}
                        </a>
                    </button>
                    <button type="button" id="btn_confirm_update_partner" class="btn-middle"
                            data-url={{ route('order.updatePartnerForm') }}>
                        <a class="btn" href="#mass_update_partners" data-toggle="modal"
                           title="Giao đối tác vận tải">
                            <i class="fa fa-car" aria-hidden="true" title=""></i>
                            {{ trans('actions.update_partner') }}
                        </a>
                    </button>
                    <button type="button" class="btn-update-document" id="btn_confirm_update_documents">
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

                    <div class="dropdown-menu  dropdown-menu-right" aria-labelledby="dropdownMenuLink"
                         style="width: 200px">
                        {{-- @can('import ' . $permission)
                             <button id="btn-selected-editor" style="border-radius: 0">
                                 <i class="fa fa-edit"></i> {{ trans('actions.importSheet') }}
                             </button>
                         @endcan--}}
                        @can('edit ' . $permission)
                            <button type="button" class="btn-update-revenue" id="btn_confirm_update_revenue"
                                    data-url={{ route('order.update-revenue') }}>
                                <a class="mass-update-revenue" href="#mass_update_revenue" data-toggle="modal"
                                   title="Cập nhật cước phí" style="color: black">
                                    <i class="fa fa-money" aria-hidden="true" title="Cập nhật cước phí"></i>
                                    {{ trans('actions.update_revenue') }}
                                </a>
                            </button>
                            {{--@if (env('SUPPORT_CAR_TRANSPORTATION', false))
                                <button type="button" class="btn-update-vin-no" id="btn_confirm_update_vin_no"
                                        data-url={{ route('order.update-vin-no') }}>
                                    <a class="mass-update-vin-no" href="#mass_update_vin_no" data-toggle="modal"
                                       title="Cập nhật số khung, số model" style="color: black">
                                        <i class="fa fa-truck" aria-hidden="true"
                                           title="Cập nhật số khung, số model"></i>
                                        {{ trans('actions.update_vin_no') }}
                                    </a>
                                </button>
                            @endif--}}
                        @endcan
                        @can('export ' . $permission)
                            <button type="button" class="btn-qrcode" style="border-radius: 0">
                                <i class="fa fa-qrcode" style="margin-right: 8px;"></i> {{ trans('actions.qr_code') }}
                            </button>
                            <button type="button" class="btn-print-bill">
                                <i class="fa fa-print" style="margin-right: 8px;"></i> In vận đơn giao
                            </button>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="toolbar dropdown action">
                <a class="btn dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">
                    ...
                </a>

                <div class="dropdown-menu  dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                    {{--                    @can('import ' . $permission)--}}
                    {{--                        <a class="dropdown-item" id="btn-editor">--}}
                    {{--                            <i class="fa fa-edit"></i> {{ trans('actions.importSheet') }}--}}
                    {{--                        </a>--}}
                    {{--                    @endcan--}}

                    {{--                    @can('lock ' . $permission)--}}
                    {{--                        <a class="dropdown-item" data-url="{{ route('order.lock') }}" id="btn_lock">--}}
                    {{--                            <i class="fa fa-lock"></i>Khoá sổ--}}
                    {{--                        </a>--}}
                    {{--                    @endcan--}}
                    {{--                    @can('unlock ' . $permission)--}}
                    {{--                        <a class="dropdown-item" data-url="{{ route('order.unlock') }}" id="btn_unlock">--}}
                    {{--                            <i class="fa fa-unlock"></i>Mở khoá sổ--}}
                    {{--                        </a>--}}
                    {{--                    @endcan--}}
                    @can('export ' . $permission)
                        <a class="dropdown-item import" id="declaration_export-button" href="#"
                           data-target="#declaration_export" data-toggle="modal">
                            <i class="fa fa-table"></i> Xuất bảng kê
                        </a>
                    @endcan
                    {{--                    @can('edit ' . $permission)--}}
                    {{--                        <a class="dropdown-item" href="{{ route('merge-order.index') }}">--}}
                    {{--                            <i class="fa fa-compress"></i> Ghép chuyến--}}
                    {{--                        </a>--}}
                    {{--                    @endcan--}}
                    {{--                    @can('export ' . $permission)--}}
                    {{--                        <a class="dropdown-item import" id="google_drive-button" href="#">--}}
                    {{--                            <i class="fa fa-google"></i> Google Drive--}}
                    {{--                        </a>--}}
                    {{--                    @endcan--}}
                    @if (!empty($showDeleted))
                        <a class="dropdown-item" href="#" id="deleted_btn">
                            <i class="fa fa-trash" aria-hidden="true" data-toggle="tooltip" data-placement="top"
                               title="" data-original-title="Danh sách bản ghi đã xóa"></i> Thùng rác
                        </a>
                    @endif
                    {{--                    <a class="dropdown-item" target="_blank" href="https://ceta.vn/tools/CETA_ConvertExcel.rar">--}}
                    {{--                        <i class="fa fa-scissors"></i> App đổi dữ liệu--}}
                    {{--                    </a>--}}
                    {{--                    <a class="dropdown-item" target="_blank" href="{{ route('excel-converter.index') }}">--}}
                    {{--                        <i class="fa fa-scissors"></i> Chuyển đổi dữ liệu--}}
                    {{--                    </a>--}}
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
