@php
    $label = isset($routePrefix) ? transb($routePrefix.'.name') : '';
@endphp
@if(strpos($routeName, 'duplicate'))
    <input type="hidden" name="duplicate" value="1">
@endif
<div class="submit-button text-right row">
    <div class="col-md-6 submit-button-title">
        <h4 class="m-t-0 header-title">
            {{$entity->id ? (strpos($routeName, 'duplicate') ? trans('actions.create').' '.$label : trans('actions.edit').' '.$label)
            : trans('actions.create').' '.$label}}
        </h4>
    </div>
    <div class="col-md-6 wrap-submit-button">
        <span class="padr20">
            <a class="btn btn-default back-button" href="{!! getBackUrl(false, route($routePrefix.'.index')) !!}">
                <i class="fa fa-backward"></i>{{trans('actions.back')}}
            </a>
        </span>
        <span>
            <button type="submit" class="btn btn-blue submit-btn">
                <i class="fa fa-save"></i>{{trans('actions.submit')}}
            </button>
        </span>
    </div>
</div>