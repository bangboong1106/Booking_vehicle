@if ($total > $page_size)
    <?php $total_page = $total/$page_size + 1 ?>
    <nav>
        <ul class="pagination">
            @if ($page_index == 1)
                <li class="page-item disabled"><a aria-label="Previous" class="page-link"><span aria-hidden='true'>&laquo;</span></a></li>
            @else
                <li onclick="pagingResource({{ $page_index - 1}}, true)" class="page-item"><a
                            aria-label="Previous" class="page-link"><span aria-hidden='true'>&laquo;</span></a></li>
            @endif
            @for ($element = 1; $element <= $total_page; $element++)
                @if ($element == $page_index)
                        <li class="page-item active disabled"><span class="page-link">{{ $element }}</span></li>
                    @else
                        <li class="page-item" onclick="pagingResource({{ $element }}, true)"><a
                            class="page-link"
                            href="#">{{ $element }}</a>
                @endif
            @endfor               
                {{-- @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page_index > 3 && $page === 2)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif

                        @if ($page == $page_index)
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $page }}<span class="sr-only">(current)</span></span>
                            </li>
                        @elseif ($page === $page_index + 1 ||
                            $page === $page_index - 1 ||
                            $page === $paginator->lastPage() ||
                            $page === 1)
                                <li class="page-item" onclick="pagingResource({{ $page }}, true)"><a
                                            class="page-link"
                                            href="#">{{ $page }}</a>
                            </li>
                        @endif

                        @if ($page_index < $paginator->lastPage() - 2 && $page === $paginator->lastPage() - 1)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                    @endforeach
                @endif 
            @endforeach--}}

            @if ($page_index == $total_page)
                <li  class="page-item disabled"><a
                            aria-label="Next" class="page-link"><span aria-hidden='true'>&raquo;</span></a></li>
            @else
                <li onclick="pagingResource({{ $page_index + 1}}, true)" class="page-item "><a aria-label="Next" class="page-link"><span aria-hidden='true'>&raquo;</span></a></li>
            @endif
        </ul>
    </nav>
@endif