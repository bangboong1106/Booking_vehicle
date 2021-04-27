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
                <button type="button" class="btn-import">
                    @can('import '.$permission)
                        <a class="btn upload" href="#" data-target="#import_excel" data-toggle="modal">
                            <i class="fa fa-upload"></i> Cập nhật
                        </a>
                    @endcan
                </button>
                <button type="button" class="btn-import">
                    @can('export '.$permission)
                        <a class="btn update-data-export" href="{{route('document.exportUpdate')}}" target="_blank">
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
                        <i class="fa fa-window-close" aria-hidden="true" title="{{trans('actions.unselected_all')}}"></i>
                        {{trans('actions.unselected_all')}}
                    </a>
                </button>
                @can('export '.$permission)
                    <button type="button" class="btn-import">
                        <a class="btn" href="#" data-url="{{route('document.exportUpdate')}}" id="export_selected">
                            <i class="fa fa-download"></i> {{trans('actions.export_excel')}}
                        </a>
                    </button>

                    <button type="button" class="btn-print-template">
                        <a class="btn" href="#" data-url="{{route('template.printCustom')}}"
                           data-type="{{config('constant.DOCUMENT')}}"
                           id="print_template_selected">
                            <i class="fa fa-print"></i> {{trans('actions.print-template')}}
                        </a>
                    </button>
                @endcan
                @can('edit '.$permission)
                    <button type="button" class="btn-import">
                        <a class="btn mass-update-documents" href="#mass_update_documents"
                           data-toggle="modal"
                           title="Cập nhật chứng từ">
                            <i class="fa fa-toggle-up" aria-hidden="true" title=" Cập nhật chứng từ"></i>
                            {{trans('actions.update_license')}}
                        </a>
                    </button>
                @endcan
                <button type="button" class="btn-config">
                    <a class="btn btn-config-toggle" data-toggle="tooltip" data-placement="top" title=""
                       data-original-title="" href="#">
                        <i class="fa fa-cog"></i> Tùy chỉnh
                    </a>
                </button>
            </div>

        </div>
    </div>
    <input type="hidden" id="back_url_key" value="{{isset($backUrlKey) ? $backUrlKey : ''}}">
</div>


