@extends('layouts.app_dashboard')

@section('header')
    <link href="{{ asset('styles/css/pagination.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/filterBar.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/jobApply.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container">
        <div class="header">
            <h1>{{ __("words.Apply Job") }}: {{ $jobApp->title }}</h1>
        </div>

        @livewireStyles
        @livewire('jobs.apply', ['jobId' => $jobApp->id])
        @stack('scripts')
        @livewireScripts
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/filterBar.js') }}"></script>
@endsection
