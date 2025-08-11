@extends('layouts.app_dashboard')

@section('header')
    <link href="{{ asset('styles/css/table.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/formControl.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/pagination.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/filterBar.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css" rel="stylesheet">
@endsection

@section('content')
    <div class="container">
        <div class="header">
            <h1>جميع الوظائف</h1>
        </div>

        @livewireStyles
        @livewire('jobs.index')
        @stack('scripts')
        @livewireScripts
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/filterBar.js') }}"></script>
@endsection
