@if(!$order_list->total())
    <div class="error-message no-data text-center">
        <span>{{trans('messages.no_result_found')}}</span>
    </div>
@endif
{{ $order_list->appends(Request::all())->links('backend.vehicle._order_paging')}}