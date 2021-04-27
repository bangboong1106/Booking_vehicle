@if(!$driver_list->total())
    <div class="error-message no-data text-center">
        <span>{{trans('messages.no_result_found')}}</span>
    </div>
@endif
{{ $driver_list->appends(Request::all())->links('backend.vehicle._driver_paging')}}