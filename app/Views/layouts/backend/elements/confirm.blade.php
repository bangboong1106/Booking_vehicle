@php
    $label = isset($routePrefix) ? transb($routePrefix.'.name') : '';
@endphp
<div class="confirm-group form-group text-right row">
    <div class="col-md-6 submit-button-title">
        <h4 class="m-t-0 header-title">{{trans('actions.confirm').' '.$label}}</h4>
    </div>
    <div class="col-md-6 wrap-submit-button">
        {!! MyForm::confirm($entity, $routePrefix, ['id' => 'confirm_form']) !!}
        <span class="pr-3">
            <a class="btn" href="{{getBackUrl(true)}}">
                <i class="fa fa-backward"></i>{{trans('actions.back')}}
            </a>
        </span>
        <button type="submit" class="btn btn-blue">
            <i class="fa fa-save"></i>{{trans('actions.submit')}}
        </button>
        @if(isset($renew))
            <button type="button" class="btn btn-blue btn-renew" onclick="renew();">
                <i class="fa fa-save"></i>{{trans('actions.submit_and_new')}}
            </button>
        @endif
        {!! MyForm::close() !!}
    </div>
</div>
<script>
    function renew() {
        let form = document.getElementById('confirm_form'),
            addedParameterField = document.createElement("input");
            addedParameterField.type = "hidden";
            addedParameterField.name = "renew";
            addedParameterField.value = "1";
        form.appendChild(addedParameterField);
        form.submit();
    }
</script>