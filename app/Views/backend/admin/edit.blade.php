@extends('layouts.backend.layouts.main')
@section('content')
    @include($_form)
@endsection
@push('after_load_scripts')
    <script>
        @if($entity->role == 'partner')
            $(".partner_form").show();
            $(".admin_form").hide();
        @else
            $(".partner_form").hide();
            $(".admin_form").hide();
            Admin();
        @endif    
    </script>
@endpush