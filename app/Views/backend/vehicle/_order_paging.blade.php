@if ($paginator->hasPages())
    <ul class="pagination pagination-split m-t-30 justify-content-center">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
        @else
            <li onclick="handleOrderTableAction({{ $paginator->currentPage() - 1}})" class="page-item"><a
                        aria-label="Previous" class="page-link"><span aria-hidden='true'>&laquo;</span></a></li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item" onclick="handleOrderTableAction({{ $page }})"><a
                                    class="page-link">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li onclick="handleOrderTableAction({{ $paginator->currentPage() + 1}})" class="page-item"><a
                        aria-label="Next" class="page-link"><span aria-hidden='true'>&raquo;</span></a></li>
        @else
            <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
        @endif
    </ul>
@endif