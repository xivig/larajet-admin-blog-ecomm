@extends('layouts.admin')
@section('title', 'Admin Dashboard - Xivig')
@section('content')

    <!-- Executive KPI Metric Widgets -->
    @include('admin.main')

    <!-- Analytical Overview Table -->
    @include('admin.main1')

    <!-- System Records & Data Management -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="font-weight-bold text-dark mb-0">System Records &amp; Data Management</h4>
                <a href="{{ route('admin.posts.create') }}" class="btn-elite btn-elite-primary btn-sm">
                    <i class="bi bi-plus-lg me-1 mr-1"></i> Create Post
                </a>
            </div>
            @include('admin.index')
        </div>
    </div>

@endsection
