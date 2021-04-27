<option value="">{{ trans('common.default_option') }}</option>
@foreach($locationType as $key => $type)
    <option value="{{$key}}">{{$type}}</option>
@endforeach