@extends('layouts.backend.layouts.main')
@section('content')
    @include($_form)
@endsection

@push('scripts')
    <script>
        $('.checkbox.checkbox-circle input').prop('checked', true).removeAttr("disabled");
    </script>
@endpush