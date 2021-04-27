{{-- @php
    $show_route = isset($show_route) ? $show_route : $routePrefix.'.show';
    $edit_route = isset($edit_route) ? $edit_route : $routePrefix.'.edit';
    $delete_route = isset($delete_route) ? $delete_route : $routePrefix.'.destroy';
    $copy_route = isset($copy_route) ? $copy_route : $routePrefix.'.duplicate';
    $deleted_route = isset($deleted_route) ? $deleted_route : $routePrefix.'.deleted';
    $permission = str_replace('-', '_', $routePrefix);
@endphp
<div class="dropdown-menu dropdown-menu-sm" id="context-menu">
    <a class="dropdown-item view"
       href="{{backUrl($show_route, ['id'=>0, 'backUrlKey' => isset($backUrlKey) ? $backUrlKey : null], $show_route)}}">
        <i class="fa fa-eye"></i>{{ trans('actions.view') }}
    </a>
    @can('edit '.$permission)
    <a class="dropdown-item edit"
       href="{{backUrl($edit_route, ['id'=>0, 'backUrlKey' => isset($backUrlKey) ? $backUrlKey : null], $edit_route)}}">
        <i class="fa fa-edit"></i>{{ trans('actions.edit') }}
    </a>
    @endcan
    @can('delete '.$permission)
        <a class="dropdown-item delete"
           href="{{backUrl($delete_route, ['id'=>0, 'backUrlKey' => isset($backUrlKey) ? $backUrlKey : null], $delete_route)}}">
            <i class="fa fa-trash"></i>{{ trans('actions.destroy') }}
        </a>
    @endcan
    @can('add '.$permission)
        <a class="dropdown-item copy"
           href="{{backUrl($copy_route, ['id'=>0, 'backUrlKey' => isset($backUrlKey) ? $backUrlKey : null], $copy_route)}}">
            <i class="fa fa-copy"></i>{{ trans('actions.duplicate') }}
        </a>
    @endcan
</div>
<div id="deleted_modal" class="modal fade" role="dialog" aria-labelledby="deletedModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">{{ trans('messages.deleted_list', ['modal' => trans('models.' . $routePrefix . '.name')]) }}</h4>
            </div>
            <div class="modal-body">
                <div id="list_deleted"></div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="deleted_url" value="{{ route($deleted_route) }}">
                <div class="text-right">
                    <span class="padr20">
                        <button class="btn btn-default" data-dismiss="modal">
                            <span class="ls-icon ls-icon-reply" aria-hidden="true"></span> {{trans('actions.close')}}
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div> --}}