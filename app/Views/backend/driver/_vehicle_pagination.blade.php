@if(!$vehicle_list->total())
    <div class="error-message no-data text-center">
        <span>{{trans('messages.no_result_found')}}</span>
    </div>
@endif
{{ $vehicle_list->appends(Request::all())->links('backend.driver._vehicle_paging')}}