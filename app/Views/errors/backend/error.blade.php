<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{$statusCode}} | {{config('constant.APP_NAME')}}</title>
    <link href="https://fonts.googleapis.com/css?family=Kanit:200" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="16x16" href="{{public_url('favicon.png')}}">

    @stack('before-css')
    <?php
        $cssFiles = [
            'autoload/error',
        ];
    ?>
    {!! loadFiles($cssFiles, isset($area) ? $area : 'backend') !!}
    @stack('after-css')
</head>

<body>
	<div id="notfound">
		<div class="notfound">
			<div class="notfound-404">
				<h1>{{$statusCode}}</h1>
			</div>
            <h2>{{trans('errors.'.$statusCode.'.header')}}</h2>
            <p>{{trans('errors.'.$statusCode.'.text')}}
            <a href="#" onclick="window.history.back()">{{trans('errors.go_back')}}</a>
            {{trans('errors.or')}}
            <a href="{{\Auth::user()->role == 'admin' ? route('board.index') : route('partner-board.index')}}">{{trans('errors.go_home')}}</a>

        </p>
			{{-- <div class="notfound-social">
				<a href="#"><i class="fa fa-facebook"></i></a>
				<a href="#"><i class="fa fa-twitter"></i></a>
				<a href="#"><i class="fa fa-pinterest"></i></a>
				<a href="#"><i class="fa fa-google-plus"></i></a>
			</div> --}}
		</div>
	</div>

</body>

</html>
