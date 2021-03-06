{!! isset($statics['js']) ? loadFiles($statics['js'], $area, 'js') : null !!}
@if (env('USE_WEBPACK_BACKEND', false))
    @if (isset($controllerName) && !empty($controllerName) && file_exists(public_path('js' . DIRECTORY_SEPARATOR . $area . DIRECTORY_SEPARATOR . 'autoload' . DIRECTORY_SEPARATOR . 'min' . DIRECTORY_SEPARATOR . $controllerName . '.min.js')))
        {{ Html::script(buildVersion(public_url('js' . DIRECTORY_SEPARATOR . $area . DIRECTORY_SEPARATOR . 'autoload' . DIRECTORY_SEPARATOR . 'min' . DIRECTORY_SEPARATOR . $controllerName . '.min.js'))) }}
    @endif
@else
    @if (isset($controllerName) && !empty($controllerName) && file_exists(public_path('js' . DIRECTORY_SEPARATOR . $area . DIRECTORY_SEPARATOR . 'autoload' . DIRECTORY_SEPARATOR . $controllerName . '.js')))
        {{ Html::script(buildVersion(public_url('js' . DIRECTORY_SEPARATOR . $area . DIRECTORY_SEPARATOR . 'autoload' . DIRECTORY_SEPARATOR . $controllerName . '.js'))) }}
    @endif
@endif
