@if (!empty($messages))
    @foreach($messages as $message)
    <p class="text-warning"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> {{ $message }}</p>
    @endforeach
@endif