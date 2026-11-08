@extends('home')
@section('title', 'About Us - Xivig Platform')
@section('page-title', 'About Us')
@section('page-heading', 'Our Mission & Architecture')
@section('content')

<!-- Section About Start -->
<section class="section py-5 position-relative">
	<div class="container">
		<div class="row align-items-center mb-5">
			<div class="col-lg-6 mb-5 mb-lg-0">
				<div class="about-item pr-lg-4">
					<span class="badge-elite mb-2">Our Origins</span>
					<h2 class="mt-2 mb-4 font-weight-bold text-dark display-4" style="letter-spacing: -0.02em;">We are a dynamic engineering collective</h2>
					<p class="text-muted lead mb-4">
						Founded on principles of simplicity and modularity, Xivig crafts software ecosystems that empower developers and enterprises to scale without technical debt.
					</p>
					<p class="text-muted mb-4">
						We continuously evaluate cutting-edge framework architectures, containerization patterns, and developer ergonomics to deliver solutions that stand the test of time.
					</p>
					<a href="{{ url('/services') }}" class="btn-elite btn-elite-primary">
						Explore Our Services <i class="fas fa-arrow-right ml-2" style="font-size: 11px;"></i>
					</a>
				</div>
			</div>
			<div class="col-lg-6">
				<img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&auto=format&fit=crop&q=60" alt="Team" class="img-fluid rounded-lg shadow-lg" style="border-radius: 20px;">
			</div>
		</div>

		<!-- Core Values -->
		<div class="row mt-5 pt-4">
			<div class="col-lg-4 col-md-6 mb-4">
				<div class="intro-item">
					<div class="icon-box-elite"><i class="ti-light-bulb"></i></div>
					<h4 class="font-weight-bold text-dark mb-3">Relentless Innovation</h4>
					<p class="text-muted mb-0">Adopting early modern standards to ensure your platform remains forward-compatible and future-proof.</p>
				</div>
			</div>
			<div class="col-lg-4 col-md-6 mb-4">
				<div class="intro-item">
					<div class="icon-box-elite"><i class="ti-shield"></i></div>
					<h4 class="font-weight-bold text-dark mb-3">Enterprise Security</h4>
					<p class="text-muted mb-0">Multi-layer authentication, token isolation, and automated validation guarding every endpoint.</p>
				</div>
			</div>
			<div class="col-lg-4 col-md-6 mb-4">
				<div class="intro-item">
					<div class="icon-box-elite"><i class="ti-pulse"></i></div>
					<h4 class="font-weight-bold text-dark mb-3">High Throughput</h4>
					<p class="text-muted mb-0">Optimized database query flows, deterministic caching, and lightning-fast page response times.</p>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Section About End -->
@endsection
