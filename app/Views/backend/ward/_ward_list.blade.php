<option value="">{{ trans('common.default_option') }}</option>
@foreach($wards as $key => $ward)
    <option value="{{$key}}">{{$ward}}</option>
@endforeach