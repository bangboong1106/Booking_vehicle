<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{!! isset($title) ? sprintf("%s | ", $title) : ''!!}CETA by OneLog</title>
    <meta name="description" content="">
    @stack('before-css')
    <?php

        $cssFiles = [
            'login',
//            'vendor/font-awesome.min',
        ];
    ?>
    {!! loadFiles($cssFiles, isset($area) ? $area : 'backend') !!}
    @stack('after-css')

</head>                                                                                                                                                                        