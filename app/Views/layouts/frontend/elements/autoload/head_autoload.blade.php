{{isset($statics['css']) ? loadFiles($statics['css'], $area) : null}}
@if (isset($controllerName) && !empty($controllerName) && file_exists(public_path('css' . DIRECTORY_SEPARATOR . $area . DIRECTORY_SEPARATOR . 'autoload' . DIRECTORY_SEPARATOR . $controllerName . '.css')))
    {{Html::style(buildVersion('css' . DIRECTORY_SEPARATOR . $area . DIRECTORY_SEPARATOR . 'autoload' . DIRECTORY_SEPARATOR . $controllerName . '.css'))}}
@endif