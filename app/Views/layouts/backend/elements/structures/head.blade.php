<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{!! isset($title) ? sprintf("%s | ", $title) : ''!!}{{config('constant.APP_NAME')}}</title>
    <meta name="description" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="{{public_url('favicon.png')}}">
    @include('layouts.backend.elements.structures.font_kanit')

    @stack('before-css')
    <?php
    if (env('USE_WEBPACK_BACKEND', false)) {
        $jsFiles = ['vendor/jquery.min', 'vendor',];
        $cssFiles = ['bundle.min', 'style.min'];
    } else {
        $cssFiles = [
            'vendor/switchery.min',
            'vendor/bootstrap.min',
            'vendor/bootstrap-datetimepicker.min',
            'vendor/daterangepicker',
            'vendor/font-awesome.min',
            'vendor/select2.min',
            'vendor/jquery.bootstrap-touchspin.min',
//            'vendor/bootstrap-tagsinput.min',
            'vendor/jquery-ui.min',
            'vendor/dropzone',
            'vendor/toastr.min',
            'vendor/core',
            'style',
            'fast-order',
            'person',
            'table-scroll',
            'datatable',
            'control',
            'detail-view',
            'side-navigation',
            'select2-custom',
        ];
        $jsFiles = [
            'vendor/jquery.min',
            'vendor/utils/moment.min',
//            'vendor/bootstrap-tagsinput.min'
        ];
    }
    ?>
    {!! loadFiles($jsFiles, isset($area) ? $area : 'backend', 'js') !!}
    {!! loadFiles($cssFiles, isset($area) ? $area : 'backend') !!}
    @stack('after-css')
    @include('layouts.backend.elements.autoload.head_autoload')

    <!--[if lt IE 9]>
    {{Html::script('https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js')}}
    {{Html::script('https://oss.maxcdn.com/respond/1.4.2/respond.min.js')}}
    <![endif]-->
</head>