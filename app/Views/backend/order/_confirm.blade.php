@php
    $show = isset($show) ? $show : $area.'.'.$controllerName.'._show';
@endphp
<div class="row">
    <div class="col-md-12">
        <div class="card-box confirm-display">
            <p class="m-b-30 font-14 text-danger">{{trans('messages.confirm_text')}}</p>
            @include($show)
            @include('layouts.backend.elements.confirm', ['renew' => true])
        </div>
    </div>
</div>