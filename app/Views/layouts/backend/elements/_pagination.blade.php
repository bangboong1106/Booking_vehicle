@php
    $totalRecord  = $entities->total();
    $currentPage = $entities->currentPage();
    $perPage = $entities->perPage();
    // paging info variables
    $fromRecord = (int)($currentPage - 1) * $perPage + 1;
    $toRecord = (($currentPage * $perPage) - $totalRecord) > 0 ? $totalRecord : ($currentPage * $perPage);
@endphp
<ul class="pagination pagination-split">
    @if ($paginator->hasPages())
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
        @else
            <li class="page-item"><a class="page-link" data-page="{!! $paginator->currentPage() - 1 !!}"
                                     href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a></li>
        @endif

        {{-- Pagination Elements --}}
        {{--        @foreach ($elements as $element)--}}
        {{--            --}}{{-- "Three Dots" Separator --}}
        {{--            @if (is_string($element))--}}
        {{--                <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>--}}
        {{--            @endif--}}

        {{-- Array Of Links --}}
        {{--            @if (is_array($element))--}}
        {{--                @foreach ($element as $page => $url)--}}
        {{--                    @if ($page == $paginator->currentPage())--}}
        {{--                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>--}}
        {{--                    @else--}}
        {{--                        <li class="page-item">--}}
        {{--                            <a class="page-link"--}}
        {{--                               data-page="{{ $page }}" {{ $isAjax ? 'data-href='.$url : 'href='.$url }}>{{ $page }}</a>--}}
        {{--                        </li>--}}
        {{--                    @endif--}}
        {{--                @endforeach--}}
        {{--            @endif--}}
        {{--        @endforeach--}}

        <li><p class="pagination-result-info"><b>{{$fromRecord }}</b> - <b>{{$toRecord}}</b></p></li>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item"><a class="page-link" data-page="{!! $paginator->currentPage() + 1 !!}"
                                     href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a></li>
        @else
            <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
        @endif
    @else
        <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
        <li><p class="pagination-result-info"><b>{{$fromRecord }}</b> - <b>{{$toRecord}}</b></p></li>
        <li class="page-item disabled"><span class="page-link">&raquo;</span></li>

    @endif
</ul>
