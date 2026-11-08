@include('partials.admin.head')

<body>
<!-- ============================================================== -->
<!-- Preloader -->
<!-- ============================================================== -->
<div class="preloader">
    <div class="lds-ripple">
        <div class="lds-pos"></div>
        <div class="lds-pos"></div>
    </div>
</div>

<!-- ============================================================== -->
<!-- Main wrapper -->
<!-- ============================================================== -->
<div id="main-wrapper">

    @include('partials.admin.header')
    @include('partials.admin.sidebar')

    <!-- ============================================================== -->
    <!-- Page wrapper  -->
    <!-- ============================================================== -->
    <div class="page-wrapper">

        <!-- Breadcrumb -->
        <div class="page-breadcrumb">
            <div class="row align-items-center">
                <div class="col-6 align-self-center">
                    <h4 class="page-title mb-0">Control Center</h4>
                </div>
                <div class="col-6 align-self-center">
                    <div class="d-flex align-items-center justify-content-end">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 bg-transparent">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted">Home</a></li>
                                <li class="breadcrumb-item active text-primary" aria-current="page">Admin Dashboard</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Container fluid  -->
        <div class="container-fluid pt-3">
            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="footer text-center py-4 text-muted small" style="border-top: 1px solid #e2e8f0; background: #ffffff;">
            &copy; {{ date('Y') }} <a href="{{ url('/') }}" class="font-weight-bold text-dark">Xivig</a>. All rights reserved. Crafted for enterprise performance.
        </footer>
    </div>
</div>

@include('partials.admin.customizer')
@include('partials.admin.footer')
