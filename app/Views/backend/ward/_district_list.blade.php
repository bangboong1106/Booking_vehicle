<option value="">{{ trans('common.default_option') }}</option>
@foreach($districtList as $key => $district)
    <option value="{{$key}}">{{$district}}</option>
@endforeach