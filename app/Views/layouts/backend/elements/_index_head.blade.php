<div class="form-inline m-b-10 justify-content-between">
    <div class="row">
        <div class="col-md-12 text-xs-center" id="{{isset($issetHeaderFilter) ? 'collapse-filter' : '' }}">
            @if(isset($issetHeaderFilter))
                <a data-toggle="collapse" href="#header-filter" aria-expanded="true" aria-controls="header-filter" class="collapsed" style="color:black">
                    <h4 class="page-title">
                        {{$title}}  <i class="fa fa-angle-up rotate-icon"></i>
                    </h4>
                </a>
            @else
                <h4 class="page-title">{{$title}}</h4>
            @endif
        </div>
    </div>
    <div class="row">
        @include('layouts.backend.elements.list_to_create')
    </div>
    <input type="hidden" id="back_url_key" value="{{isset($backUrlKey) ? $backUrlKey : ''}}">
</div>