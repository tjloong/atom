@extends('atom::layout')

@push('scripts')
    @routes
    <script src="{{ mix('js/manifest.js') }}" defer></script>
    <script src="{{ mix('js/vendor.js') }}" defer></script>
    <script src="{{ mix('js/app.js') }}" defer></script>
@endpush

@section('content')
    @inertia
@endsection