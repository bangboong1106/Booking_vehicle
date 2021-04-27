@php
    $deleteRoute = isset($deleteRoute) ? $deleteRoute : $routePrefix.'.destroy';
@endphp
<a class="delete-action" href="#del-confirm" style="display:inline-block"
   data-toggle="modal"
   title="{{trans('actions.destroy')}}"
   data-action="{{backUrl($deleteRoute,['id' => $entity->id, 'backUrlKey' => isset($backUrlKey) ? $backUrlKey : null])}}">
    <i class="fa fa-trash" aria-hidden="true" title="{{trans('actions.destroy')}}"></i>
</a>