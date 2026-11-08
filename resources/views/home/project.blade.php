@extends('home')
@section('title', 'Featured Portfolio - Xivig Platform')
@section('page-title', 'Selected Works')
@section('page-heading', 'Enterprise Portfolio')
@section('content')

<!-- Section Portfolio Start -->
<section class="section py-5 position-relative" style="background: #f8fafc;">
	<div class="container mb-5">
		<div class="row justify-content-center">
			<div class="col-lg-7 text-center">
				<span class="badge-elite mb-2">Architectural Showcases</span>
				<h2 class="font-weight-bold text-dark display-4" style="letter-spacing: -0.02em;">Pioneering Enterprise Deployments</h2>
				<p class="text-muted lead">Selected mission-critical software architectures designed and delivered by Xivig.</p>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="row">
			<!-- Project 1 -->
			<div class="col-lg-4 col-md-6 mb-4">
				<div class="blog-item p-0">
					<div class="blog-img-wrap" style="height: 240px;">
						<a href="{{ asset('xivig-blog/images/portfolio/1.jpg') }}" class="popup-gallery">
							<img src="{{ asset('xivig-blog/images/portfolio/1.jpg') }}" alt="Nexus Core" class="img-fluid w-100">
						</a>
						<span class="blog-category-tag">Cloud Microservices</span>
					</div>
					<div class="blog-item-content">
						<h4 class="font-weight-bold mb-2">Project Nexus Core</h4>
						<p class="text-muted small mb-3">Distributed high-throughput transaction platform handling millions of events daily with sub-second finality.</p>
						<span class="badge badge-light text-primary font-weight-bold">Laravel 13 &bull; Kafka &bull; Redis</span>
					</div>
				</div>
			</div>

			<!-- Project 2 -->
			<div class="col-lg-4 col-md-6 mb-4">
				<div class="blog-item p-0">
					<div class="blog-img-wrap" style="height: 240px;">
						<a href="{{ asset('xivig-blog/images/portfolio/2.jpg') }}" class="popup-gallery">
							<img src="{{ asset('xivig-blog/images/portfolio/2.jpg') }}" alt="Apex Financial" class="img-fluid w-100">
						</a>
						<span class="blog-category-tag">Fintech Hub</span>
					</div>
					<div class="blog-item-content">
						<h4 class="font-weight-bold mb-2">Apex Financial Engine</h4>
						<p class="text-muted small mb-3">Real-time asset accounting, multi-currency ledger management, and regulatory compliance reporting.</p>
						<span class="badge badge-light text-success font-weight-bold">Livewire 3 &bull; PostgreSQL &bull; Sanctum</span>
					</div>
				</div>
			</div>

			<!-- Project 3 -->
			<div class="col-lg-4 col-md-6 mb-4">
				<div class="blog-item p-0">
					<div class="blog-img-wrap" style="height: 240px;">
						<a href="{{ asset('xivig-blog/images/portfolio/3.jpg') }}" class="popup-gallery">
							<img src="{{ asset('xivig-blog/images/portfolio/3.jpg') }}" alt="CyberMatrix" class="img-fluid w-100">
						</a>
						<span class="blog-category-tag">Security & Identity</span>
					</div>
					<div class="blog-item-content">
						<h4 class="font-weight-bold mb-2">CyberMatrix Identity Hub</h4>
						<p class="text-muted small mb-3">Zero-trust authentication gateway supporting FIDO2 passkeys, TOTP 2FA, and RBAC policy enforcement.</p>
						<span class="badge badge-light text-danger font-weight-bold">Fortify &bull; Jetstream &bull; WebAuthn</span>
					</div>
				</div>
			</div>

			<!-- Project 4 -->
			<div class="col-lg-4 col-md-6 mb-4">
				<div class="blog-item p-0">
					<div class="blog-img-wrap" style="height: 240px;">
						<a href="{{ asset('xivig-blog/images/portfolio/4.jpg') }}" class="popup-gallery">
							<img src="{{ asset('xivig-blog/images/portfolio/4.jpg') }}" alt="Velocity Engine" class="img-fluid w-100">
						</a>
						<span class="blog-category-tag">Reactive UI</span>
					</div>
					<div class="blog-item-content">
						<h4 class="font-weight-bold mb-2">Velocity Reactive Studio</h4>
						<p class="text-muted small mb-3">Instantaneous server-driven dashboard interfaces with WebSocket broadcast push updates.</p>
						<span class="badge badge-light text-info font-weight-bold">Pusher &bull; Echo &bull; Vite 6</span>
					</div>
				</div>
			</div>

			<!-- Project 5 -->
			<div class="col-lg-4 col-md-6 mb-4">
				<div class="blog-item p-0">
					<div class="blog-img-wrap" style="height: 240px;">
						<a href="{{ asset('xivig-blog/images/portfolio/5.jpg') }}" class="popup-gallery">
							<img src="{{ asset('xivig-blog/images/portfolio/5.jpg') }}" alt="Horizon IoT" class="img-fluid w-100">
						</a>
						<span class="blog-category-tag">Telemetry Streaming</span>
					</div>
					<div class="blog-item-content">
						<h4 class="font-weight-bold mb-2">Horizon Telemetry Stream</h4>
						<p class="text-muted small mb-3">Time-series ingestion platform processing 50,000 sensor pulses per second with anomaly alerting.</p>
						<span class="badge badge-light text-warning font-weight-bold">TimescaleDB &bull; MQTT &bull; Horizon</span>
					</div>
				</div>
			</div>

			<!-- Project 6 -->
			<div class="col-lg-4 col-md-6 mb-4">
				<div class="blog-item p-0">
					<div class="blog-img-wrap" style="height: 240px;">
						<a href="{{ asset('xivig-blog/images/portfolio/6.jpg') }}" class="popup-gallery">
							<img src="{{ asset('xivig-blog/images/portfolio/6.jpg') }}" alt="Sovereign Cloud" class="img-fluid w-100">
						</a>
						<span class="blog-category-tag">Cloud Native</span>
					</div>
					<div class="blog-item-content">
						<h4 class="font-weight-bold mb-2">Sovereign Cloud Orchestration</h4>
						<p class="text-muted small mb-3">Automated multi-region failover mesh with zero-downtime Canary deployments.</p>
						<span class="badge badge-light text-dark font-weight-bold">Kubernetes &bull; Docker &bull; Terraform</span>
					</div>
				</div>
			</div>
		</div>

		<!-- Portfolio CTA -->
		<div class="mt-5 p-5 bg-white rounded shadow-sm border text-center" style="border-radius: 20px;">
			<h3 class="font-weight-bold mb-2 text-dark">Have an Ambitious Project in Mind?</h3>
			<p class="text-muted lead mb-4">Let's discuss how our engineering team can design and build your custom solution.</p>
			<a href="{{ url('/contact') }}" class="btn-elite btn-elite-primary">
				<i class="fas fa-paper-plane mr-2"></i> Request Consultation
			</a>
		</div>
	</div>
</section>
<!-- Section Portfolio End -->
@endsection
