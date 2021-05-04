<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ public_url('favicon.png') }}">
    <title>{{config('constant.APP_NAME')}}</title>
    <link rel="stylesheet" href="{{ public_url('.' . mix('css/frontend/app.css')) }}">
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
    crossorigin="anonymous"> --}}
    <link href="{{ public_url('css/frontend/autoload/bootstrap.min.css') }}" rel="stylesheet" type="text/css"
    media="all">
    <link href="https://unpkg.com/nprogress@0.2.0/nprogress.css" rel="stylesheet" />
    @include('layouts.backend.elements.structures.font_kanit')
    <script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDh7atycbDhWM6Qz-H4R9ZiTY4j0LnMA8w&libraries=places&language=vi"
        type="text/javascript"></script>
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>

    </script>
</head>

<body>
    <div class="wrapper" id="app">
        <App></App>
    </div>

    <script src="{{ public_url('.' . mix('js/frontend/Main.js')) }}"></script>
</body>

</html>
