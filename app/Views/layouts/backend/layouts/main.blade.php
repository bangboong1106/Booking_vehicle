<!DOCTYPE html>
<html>
@include('layouts.backend.elements.structures.head')
<body class="fixed-left {{$controllerName}}-{{$actionName}} {{$hideLeftSidebar ? 'widescreen fixed-left-void' : ''}}">
<div id="wrapper" class="{{$hideLeftSidebar ? 'enlarged forced' : ''}}">
    @if(backendGuard()->check())
        @include('layouts.backend.elements.structures.sidebar')
        @include('layouts.backend.elements.structures.left_sidebar')
    @endif
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                @include('layouts.backend.elements.messages')
                @yield('content')
            </div>

        </div>
        <div class="notification-box-item" style="right: 16px">
        </div>
    </div>
</div>
<div id="detail-panel" class="sidenav">
    <div class="header">
        <div class="info">
            <div class="header-detail-panel font-16"></div>
            <!--<a href="javascript:void(0)" class="fa fa-edit" onclick="editItem()"></a>-->
        </div>
        <div class="action">
            <a href="javascript:void(0)" class="fa fa-close closebtn" id="detail_panel_close"></a>
        </div>
    </div>
    <div id="divDetail">
    </div>
</div>

{{--@include('layouts.backend.elements._floating_add_button')--}}
@include('layouts.backend.elements.modal')
@include('layouts.backend.elements.structures.footer_js')
@stack('scripts')
@include('layouts.backend.elements.autoload.footer_autoload')
@stack('after_load_scripts')

</body>
</html>