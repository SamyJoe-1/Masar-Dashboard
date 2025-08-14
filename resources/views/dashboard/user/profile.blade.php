@extends('layouts.app_dashboard')

@section('header')
    <link href="{{ asset('styles/css/pagination.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/formControl.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/profile.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container">
        @livewireStyles
        @livewire('users.profile')
        @stack('scripts')
        @livewireScripts
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/filterBar.js') }}"></script>
@endsection
