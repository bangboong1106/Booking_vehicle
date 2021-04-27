<!DOCTYPE html>
<html>
@include('layouts.frontend.elements.structures.head')
<body class=" {{$controllerName}}-{{$actionName}}">
    <div id="Wrap">
        <div class="container col-md-6 offset-md-3">
            @include('layouts.frontend.elements.messages')
            @yield('content')
        </div>
    </div>
@include('layouts.frontend.elements.structures.footer_js')
@stack('scripts')
</body>
</html>