{{--<x-app-layout>--}}
{{--    <x-slot name="header">--}}
{{--        <h2 class="font-semibold text-xl text-gray-800 leading-tight">--}}
{{--            {{ __('Dashboard') }}--}}
{{--        </h2>--}}
{{--    </x-slot>--}}

{{--    <div class="py-12">--}}
{{--        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">--}}
{{--            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">--}}
{{--                <x-welcome />--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</x-app-layout>--}}

@extends('layouts.user')

@section('title', 'Xivig User registration ')

@section('content')

    <!-- ============================================================== -->
    <!-- dashboard -->
    <!-- ============================================================== -->


    <div class="p-5 mb-4 bg-light rounded-3">
        <div class="container-fluid py-5">

            @session('success')
            <div class="alert alert-success" role="alert">
                {{ $value }}
            </div>
            @endsession

            <h1 class="display-5 fw-bold">Hi, {{ auth()->user()->name }}</h1>
            <h2>Welcome to dashboard</h2>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- dashboard -->
    <!-- ============================================================== -->
@endsection
