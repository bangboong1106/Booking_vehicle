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
                @if(\Illuminate\Support\Facades\Auth::user()->role == 'partner')
                    <button type="button" class="btn-create">
                        @can('add '.$permission)
                            <a class="btn l-create"
                                href="{{backUrl($create_route) }}">
                                <i class="fa fa-plus"></i>
                            </a>
                        @endcan
                    </button>
                @else
                    <button type="button" class="btn-create" style="border-width: 0px 1px 0px 0px">
                    </button>
                @endif
                <button type="button" class="btn-import" style="border-radius: 0">
                    @can('import '.$permission)
                        <a class="btn upload" href="#" data-target="#import_excel" data-toggle="modal">
                            <i class="fa fa-upload"></i> Phê duyệt
                        </a>
                    @endcan
                </button>
                <button type="button" class="btn-import"
                        style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                    @can('export '.$permission)
                        <a class="btn update-data-export" href="{{route('route.export')}}" target="_blank">
                            <i class="fa fa-download"></i> {{ trans('actions.export_excel') }}
                        </a>
                    @endcan
                </button>
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
                           title="{{trans('actions.unselected_all')}}"></i>
                        {{trans('actions.unselected_all')}}
                    </a>
                </button>
            </div>
            <div class="selected-toolbar btn-group" style="display: none">
                @can('export '.$permission)
                    <button type="button" class="btn-first btn-import">
                        <a class="btn" href="#" data-url="{{route('route.export')}}" id="export_selected">
                            <i class="fa fa-download"></i> {{trans('actions.export_excel')}}
                        </a>
                    </button>
                @endcan
                @can('export '.$permission)
                    <button type="button" class="btn-print-template">
                        <a class="btn" href="#" data-url={{Auth::user()->role == 'admin' ? route('template.printCustom') : route('partner-template.printCustom')}}
                           data-type="{{config('constant.ROUTE')}}"
                           id="print_template_selected">
                            <i class="fa fa-print"></i> {{trans('actions.print-template')}}
                        </a>
                    </button>
                @endcan
            </div>
            <div class="selected-toolbar btn-group" style="display: none">
                @can('delete '.$permission)
                    <button type="button" class="mass-destroy-btn">
                        <a class="btn" href="#mass_destroy_confirm" data-toggle="modal"
                           title="{{trans('actions.destroy')}}"
                           data-action="{{route($massDeleteRoute)}}">
                            <i class="fa fa-trash" aria-hidden="true" title="{{trans('actions.destroy')}}"></i>
                            {{trans('actions.massDestroy')}}
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
                    @can('lock ' . $permission)
                        <a class="dropdown-item" data-url="{{ route('route.lock') }}" id="btn_lock">
                            <i class="fa fa-lock"></i>Khoá sổ
                        </a>
                    @endcan
                    @can('unlock ' . $permission)
                        <a class="dropdown-item" data-url="{{ route('route.unlock') }}" id="btn_unlock">
                            <i class="fa fa-unlock"></i>Mở khoá sổ
                        </a>
                    @endcan
                    @if(!empty($showDeleted))
                        <a class="dropdown-item" href="#" id="deleted_btn">
                            <i class="fa fa-trash" aria-hidden="true" data-toggle="tooltip" data-placement="top"
                               title=""
                               data-original-title="Danh sách bản ghi đã xóa"></i> Thùng rác
                        </a>
                        <div class="dropdown-divider"></div>
                    @endif
                    <a class="dropdown-item" target="_blank" href="{{env('HELP_DOMAIN','').trans('helps.'.$routeName)}}"
                       data-toggle="tooltip" data-placement="top" title=""
                       data-original-title="{{trans('actions.help')}}">
                        <i class="fa fa-question-circle"></i>
                        <span>Trợ giúp</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="back_url_key" value="{{isset($backUrlKey) ? $backUrlKey : ''}}">
</div>


