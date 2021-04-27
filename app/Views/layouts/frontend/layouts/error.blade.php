<!DOCTYPE html>
<html>
@include('layouts.frontend.elements.structures.head')
<body>
<div id="Wrap">
    <div class="container">
        @yield('content')
        @include('layouts.frontend.elements.structures.footer')
    </div>
</div>
@stack('scripts')
</body>
</html>