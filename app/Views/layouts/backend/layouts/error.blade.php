<!DOCTYPE html>
<html>
<body>
@if(backendGuard()->check())
@endif
<div id="Wrap">
    <div class="container">
        @yield('content')
    </div>
</div>
@stack('scripts')
</body>
</html>