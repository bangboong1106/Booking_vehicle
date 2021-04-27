<div class="result-done text-center">
    @if(empty($done))
        <p class="text-danger"><i class="fa fa-times-circle fa-4x"></i></p>
        <p class="text-danger">{{ trans('messages.import_error') }}</p>
    @else
        <p class="text-success"><i class="fa fa-check-circle fa-4x"></i></p>
        <p>{{ trans('messages.import_success') }}</p>
        <p>{{ trans('messages.import_result', ['total' => $total, 'done' => $done]) }}</p>
    @endif
</div>