<div class="btn-container mt-md-4 mt-2 mb-2">
    @if (isset($import))
        <a class="btn mr-md-2 mb-2 mr-0 mb-sm-0" href="{{route('redirect-to-page.redirect', ['route' => $import] )}}">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
            &nbsp;Nhập từ excel
        </a>
    @endif
    
    @if (isset($create))
        <a class="btn mb-2 mb-sm-0" href="{{route($create)}}">
            <i class="fa fa-keyboard-o" aria-hidden="true"></i>
            &nbsp;Tự khai báo
        </a>
    @endif

    @if (isset($approveOrder))
        <a class="btn mb-2 mb-sm-0" href="{{route($approveOrder)}}">
            <i class="fa fa-check" aria-hidden="true"></i>
            &nbsp;Tiếp nhận đơn hàng
        </a>
    @endif
</div>