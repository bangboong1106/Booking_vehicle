@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled"><a aria-label="Previous" class="page-link"><span aria-hidden='true'>&laquo;</span></a></li>
            @else
                <li onclick="loadVehiclePaging({{ $paginator->currentPage() - 1}}, true)" class="page-item"><a
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
                        @if ($paginator->currentPage() > 3 && $page === 2)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif

                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $page }}<span class="sr-only">(current)</span></span>
                            </li>
                        @elseif ($page === $paginator->currentPage() + 1 ||
                            $page === $paginator->currentPage() - 1 ||
                            $page === $paginator->lastPage() ||
                            $page === 1)
                                <li class="page-item" onclick="loadVehiclePaging({{ $page }}, true)"><a
                                            class="page-link"
                                            href="#">{{ $page }}</a>
                            </li>
                        @endif

                        @if ($paginator->currentPage() < $paginator->lastPage() - 2 && $page === $paginator->lastPage() - 1)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li onclick="loadVehiclePaging({{ $paginator->currentPage() + 1}}, true)" class="page-item"><a
                            aria-label="Next" class="page-link"><span aria-hidden='true'>&raquo;</span></a></li>
            @else
                <li class="page-item disabled"><a aria-label="Next" class="page-link"><span aria-hidden='true'>&raquo;</span></a></li>
            @endif
        </ul>
    </nav>
@endif