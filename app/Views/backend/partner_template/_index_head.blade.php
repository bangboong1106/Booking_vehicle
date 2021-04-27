<div class="form-inline m-b-10 justify-content-between">
    <div class="row">
        <div class="col-md-12 text-xs-center">
            <h4 class="page-title">{{$title}}</h4>
        </div>
    </div>
    <div class="row">
        @php
            $create_route = isset($create_route) ? $create_route : $routePrefix.'.create';
            $create_label = 'Thêm mới';
            $massDeleteRoute= isset($massDeleteRoute) ? $massDeleteRoute : $routePrefix.'.massDestroy';
            $permission = str_replace('-', '_', $routePrefix);
        @endphp
        <div class="btn-group flex-wrap list-to-create" role="group" aria-label="">
            <div class="toolbar btn-group" role="group" aria-label="Basic example">
                <button type="button" class="btn-create">
                    @can('add '.$permission)
                        <a class="btn l-create"
                           href="{{backUrl($create_route) }}">
                            <i class="fa fa-plus"></i>
                        </a>
                    @endcan
                </button>
                <div class="toolbar dropdown action">
                    <a class="btn dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-download" style="margin-right: 8px"></i>Tải trường trộn
                    </a>
                    <div class="dropdown-menu  dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item download-item" href="#" data-type={{config('constant.ORDER')}}
                                data-url="{{route('template.mergeTemplate', -1)}}">
                            Đơn hàng vận tải
                        </a>
                        <a class="dropdown-item download-item" target="_blank" data-type={{config('constant.DOCUMENT')}}
                                data-url="{{route('template.mergeTemplate', -1)}}">
                            Chứng từ
                        </a>
                        <a class="dropdown-item download-item" target="_blank" data-type={{config('constant.ROUTE')}}
                                data-url="{{route('template.mergeTemplate', -1)}}">
                            Chuyến xe
                        </a>
                        <a class="dropdown-item download-item" target="_blank" data-type={{config('constant.DRIVER')}}
                                data-url="{{route('template.mergeTemplate', -1)}}">
                            Tài xế
                        </a>
                        <a class="dropdown-item download-item" target="_blank" data-type={{config('constant.VEHICLE')}}
                                data-url="{{route('template.mergeTemplate', -1)}}">
                            Xe
                        </a>
                    </div>
                </div>
            </div>
            <div class=" selected-toolbar btn-group" style="display: none">
                <p id="selected_item_count">{{ trans('models.common.selected_count') }}: <span></span></p>
                <button type="button" class="unselected-all-btn">
                    <a class="btn">
                        <i class="fa fa-window-close" aria-hidden="true" title="{{trans('actions.unselected_all')}}"></i>
                        {{trans('actions.unselected_all')}}
                    </a>
                </button>
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
                <a class="btn dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                   data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">
                    ...
                </a>

                <div class="dropdown-menu  dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" target="_blank"
                       href="{{env('HELP_DOMAIN','').trans('helps.'.$routeName)}}"
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