@extends('atom::layout')

@section('content')
    @include('web.partials.header')

    <div class="min-h-screen flex items-center justify-center">
        <img src="https://jiannius.com/logo.svg" class="w-60">
    </div>

    @include('web.partials.footer')
@endsection