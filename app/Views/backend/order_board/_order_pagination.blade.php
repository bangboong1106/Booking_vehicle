@if(!$entities->total())
    <div class="error-message no-data text-center">
        <span>{{trans('messages.no_result_found')}}</span>
    </div>
@endif
{{ $entities->appends(Request::all())->links('backend.order_board._order_paging')}}