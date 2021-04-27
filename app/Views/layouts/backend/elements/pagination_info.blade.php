@if($entities->total())
    @php
        $totalRecord  = $entities->total();
    @endphp
    <p>{!!trans('pagination.showing_of_result', ['total' => $entities->total()])!!}</p>
@endif
