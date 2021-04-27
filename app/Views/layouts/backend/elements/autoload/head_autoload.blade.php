{{ isset($statics['css']) ? loadFiles($statics['css'], $area) : null }}
@if (isset($controllerName) && !empty($controllerName) && file_exists(public_path('css' . DIRECTORY_SEPARATOR . $area . DIRECTORY_SEPARATOR . 'autoload' . DIRECTORY_SEPARATOR . $controllerName . '.css')))
    {{ Html::style(buildVersion(public_url('css' . DIRECTORY_SEPARATOR . $area . DIRECTORY_SEPARATOR . 'autoload' . DIRECTORY_SEPARATOR . $controllerName . '.css'))) }}
@endif

@if (isset($map))
    @if (isset($sync) && ($sync = true))
        <script
            src="https://maps.googleapis.com/maps/api/js?key={{ config('services.GOOGLE_MAP_API_KEY') }}&libraries=places&language=vi">
        </script>
    @else
        <script
            src="https://maps.googleapis.com/maps/api/js?key={{ config('services.GOOGLE_MAP_API_KEY') }}&libraries=places&language=vi"
            async defer>
        </script>
    @endif

@endif
