{{--@include('layouts.backend.elements.no_result_found')--}}

<div class="form-inline pagination-info">
    @if(empty($hidePerPage))
    <div class="pagination-per-page">
        {!! MyForm::label('per_page', trans('actions.number_of_records'), ['class' => 'l-per-page'], false) !!}
        {!! MyForm::dropDown('per_page', isset($dataIndex['per_page']) ? $dataIndex['per_page'] : (isset($page_size) ? $page_size : 50), config('system.per_page_list'),
            false, ['class' => 'input-sm m-l-10 range-per-page ajax-search']) !!}
    </div>
    @endif
    {{ $entities->appends(Request::all())->links('layouts.backend.elements._pagination', ['isAjax'=> isset($isAjax) ? $isAjax : false, 'entities' => $entities])}}
</div>
