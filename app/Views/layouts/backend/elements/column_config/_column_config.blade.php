
@if($index == 0)
    <label style="display: none">
        <input type="checkbox" checked
            data-name="{{$attribute}}">{{ trans('models.'.$entity.'.attributes.'.$attribute) }}
    </label>
@else
<label>
    <input type="checkbox"
        data-name="{{$attribute}}" 
        {{$show ? 'checked':''  }}>{{ trans('models.'.$entity.'.attributes.'.$attribute) }}
        <span class="float-right ml-2"><i class="fa fa-arrows"></i></span>
</label>
@endif