@extends('user')

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
