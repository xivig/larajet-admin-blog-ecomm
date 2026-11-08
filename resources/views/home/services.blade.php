@extends('home')
@section('title', 'Services & Engineering Capabilities - Xivig')
@section('page-title', 'Our Services')
@section('page-heading', 'What We Deliver')
@section('content')

<!-- Section Services Start -->
<section class="section py-5 position-relative">
	<div class="container">
		<div class="row justify-content-center mb-5">
			<div class="col-lg-7 text-center">
				<span class="badge-elite mb-2">Full-Lifecycle Engineering</span>
				<h2 class="font-weight-bold text-dark display-4" style="letter-spacing: -0.02em;">Tailored Technology Solutions</h2>
				<p class="text-muted lead">From initial systems architecture to global cloud deployment and 24/7 reliability engineering.</p>
			</div>
		</div>

		<div class="row justify-content-center">
			<div class="col-lg-4 col-md-6 mb-4">
				<div class="service-item">
					<div class="icon-box-elite"><i class="ti-desktop"></i></div>
					<h4 class="font-weight-bold text-dark mb-3">Web Platforms</h4>
					<p class="text-muted mb-0">High-concurrency web platforms built with modern Laravel, Livewire, and Vite reactive front-ends.</p>
				</div>
			</div>

			<div class="col-lg-4 col-md-6 mb-4">
				<div class="service-item">
					<div class="icon-box-elite"><i class="ti-layers"></i></div>
					<h4 class="font-weight-bold text-dark mb-3">Interface Design</h4>
					<p class="text-muted mb-0">User-centric interfaces optimized for conversion, visual hierarchy, accessibility, and delight.</p>
				</div>
			</div>

			<div class="col-lg-4 col-md-6 mb-4">
				<div class="service-item">
					<div class="icon-box-elite"><i class="ti-bar-chart"></i></div>
					<h4 class="font-weight-bold text-dark mb-3">Enterprise Consulting</h4>
					<p class="text-muted mb-0">Data-driven software consulting, architecture auditing, and enterprise systems integration.</p>
				</div>
			</div>

			<div class="col-lg-4 col-md-6 mb-4">
				<div class="service-item">
					<div class="icon-box-elite"><i class="ti-vector"></i></div>
					<h4 class="font-weight-bold text-dark mb-3">Brand Engineering</h4>
					<p class="text-muted mb-0">Distinctive visual brand languages, design tokens, and comprehensive component design systems.</p>
				</div>
			</div>

			<div class="col-lg-4 col-md-6 mb-4">
				<div class="service-item">
					<div class="icon-box-elite"><i class="ti-android"></i></div>
					<h4 class="font-weight-bold text-dark mb-3">Mobile & APIs</h4>
					<p class="text-muted mb-0">Native mobile integration, GraphQL endpoints, and resilient RESTful microservices.</p>
				</div>
			</div>

			<div class="col-lg-4 col-md-6 mb-4">
				<div class="service-item">
					<div class="icon-box-elite"><i class="ti-pencil-alt"></i></div>
					<h4 class="font-weight-bold text-dark mb-3">Technical Content</h4>
					<p class="text-muted mb-0">Developer documentation, API reference guides, and technical thought leadership publication.</p>
				</div>
			</div>
		</div>

		<!-- Bottom CTA Box -->
		<div class="mt-5 p-5 bg-white rounded shadow-sm border text-center" style="border-radius: 20px;">
			<h3 class="font-weight-bold mb-3 text-dark">Have a custom requirement?</h3>
			<p class="text-muted lead mb-4">We build custom microservices, automated queues, and real-time synchronization hubs.</p>
			<a href="{{ url('/contact') }}" class="btn-elite btn-elite-primary">
				Schedule an Architecture Review <i class="fas fa-chevron-right ml-2" style="font-size: 11px;"></i>
			</a>
		</div>
	</div>
</section>
<!-- Section Services End -->
@endsection
