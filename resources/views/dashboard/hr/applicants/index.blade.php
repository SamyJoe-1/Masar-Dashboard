@extends('layouts.app_dashboard')

@section('header')
    <link href="{{ asset('styles/css/pagination.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/filterBar.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/formControl.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container">
        <div class="header">
            <h1>{{ __("words.All Applicants") }}</h1>
        </div>

        @livewireStyles
        @livewire('applicants.index', ['status' => $status])
        @stack('scripts')
        @livewireScripts
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/filterBar.js') }}"></script>
@endsection
