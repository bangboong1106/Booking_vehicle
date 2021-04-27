@if(!$entities->total())
    <div class="error-message no-data">
        <span> {{trans('messages.no_result_found')}} </span>
    </div>
@endif