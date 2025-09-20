@extends('layouts.app_dashboard')

@section('header')
    <link href="{{ asset('styles/css/pagination.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/filterBar.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/showJob.css') }}" rel="stylesheet">
@endsection

@section('content')
    <a href="{{ route('dashboard.hr.jobs.index') }}" class="btn btn-white">Back</a>
    @livewireStyles
    @livewire('applicants.show', ['jobApp' => $jobApp])
    @stack('scripts')
    @livewireScripts
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/filterBar.js') }}"></script>
@endsection
