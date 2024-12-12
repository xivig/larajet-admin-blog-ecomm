@extends('layouts.admin')
@section('title', 'Home page')
@section('content')

    @include('admin.main')
    @include('admin.main1')
    @include('admin.show')
    @include('admin.edit')
    @include('admin.index')

@endsection
