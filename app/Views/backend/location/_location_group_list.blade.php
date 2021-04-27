<option value="">{{ trans('common.default_option') }}</option>
@foreach($locationGroup as $key => $group)
    <option value="{{$key}}">{{$group}}</option>
@endforeach