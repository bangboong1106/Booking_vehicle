@php
    $label = transb($routePrefix.'.name').'詳細';
    $show = isset($show) ? $show : $area.'.'.$controllerName.'._show';
@endphp
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading"><span class="{{$icon}}" aria-hidden="true">{{$label}}</span></div>
            <div class="panel-body sp-padb15">
                @include($show)
                @include('layouts.backend.elements.show_to_edit')
            </div>
        </div>
    </div>
</div>