@extends('layouts.app_dashboard')

@section('header')
    <link href="{{ asset('styles/css/pagination.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/filterBar.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/jobOffers.css') }}" rel="stylesheet">

@endsection

@section('content')
    <div class="container">
        <div class="header">
            <h1>{{ __("words.My Orders") }}</h1>
        </div>

        @livewireStyles
        @livewire('jobs.my-offers')
        @stack('scripts')
        @livewireScripts
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/filterBar.js') }}"></script>
@endsection
