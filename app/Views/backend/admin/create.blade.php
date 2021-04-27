@extends('layouts.backend.layouts.main')
@section('content')
    @include($_form)
@endsection
@push('after_load_scripts')
    <script>
        @if($entity->role == 'partner')
        $(".partner_form").show();
        $(".admin_form").show();
        @else
        $(".partner_form").hide();
        $(".admin_form").hide();
        @endif
        Admin();
    </script>
@endpush