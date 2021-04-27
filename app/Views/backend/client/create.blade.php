@extends('layouts.backend.layouts.main')
@section('content')
    @include($_form)
@endsection
@push('after_load_scripts')
    <script>
        @if($entity !=null && $entity->type==2)
        $(".individual").show();
        $(".corporate").hide();
        @else
        $(".corporate").show();
        $(".individual").hide();
        @endif
        Customer();
    </script>
@endpush