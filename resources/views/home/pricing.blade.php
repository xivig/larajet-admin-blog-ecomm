@extends('home')
@section('title', 'Pricing - Xivig Platform')
@section('page-title', 'Pricing')
@section('page-heading', 'Transparent Plans')
@section('content')

<!-- Section Pricing Start -->
<section class="section py-5 position-relative">
	<div class="container">
		<div class="row justify-content-center mb-5">
			<div class="col-lg-7 text-center">
				<span class="badge-elite mb-2">Predictable Investment</span>
				<h2 class="fw-bold text-body display-5" style="letter-spacing: -0.02em;">Simple, Transparent Pricing</h2>
				<p class="text-muted lead">Choose the ideal capability tier for your team. Scale up or down at any time.</p>
			</div>
		</div>

		<div class="row justify-content-center align-items-stretch g-4">
			<!-- Starter Tier -->
			<div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
				<div class="pricing-card">
					<div class="mb-4">
						<span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-2 rounded-pill small">Starter</span>
						<div class="mt-3">
							<span class="price"><span class="price-currency">$</span>0</span>
							<span class="price-period">/ month</span>
						</div>
						<p class="text-muted small mt-2">Essential features for individuals &amp; hobbyists.</p>
					</div>

					<div class="py-3 border-top flex-grow-1">
						<h6 class="fw-bold text-body mb-3">Includes:</h6>
						<ul class="list-unstyled text-muted small" style="line-height: 2.2;">
							<li><i class="fas fa-check text-success me-2"></i> Single Developer Seat</li>
							<li><i class="fas fa-check text-success me-2"></i> Up to 100 System Items</li>
							<li><i class="fas fa-check text-success me-2"></i> 5,000 API Queries / mo</li>
							<li><i class="fas fa-check text-success me-2"></i> Basic Community Support</li>
						</ul>
					</div>

					<div class="mt-4 pt-3 border-top">
						<a href="{{ route('register') }}" class="btn-elite btn-elite-outline w-100 text-center">
							Get Started Free
						</a>
					</div>
				</div>
			</div>

			<!-- Professional Tier (Featured) -->
			<div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
				<div class="pricing-card featured">
					<span class="badge-popular"><i class="fas fa-bolt me-1"></i> Most Popular</span>
					<div class="mb-4 mt-2">
						<span class="badge bg-primary text-white fw-bold px-3 py-2 rounded-pill small">Professional</span>
						<div class="mt-3">
							<span class="price text-primary"><span class="price-currency">$</span>49</span>
							<span class="price-period">/ month</span>
						</div>
						<p class="text-muted small mt-2">High velocity for growing teams &amp; startups.</p>
					</div>

					<div class="py-3 border-top flex-grow-1">
						<h6 class="fw-bold text-body mb-3">Everything in Starter, plus:</h6>
						<ul class="list-unstyled text-body small" style="line-height: 2.2;">
							<li><i class="fas fa-check-circle text-primary me-2"></i> Up to 10 Team Members</li>
							<li><i class="fas fa-check-circle text-primary me-2"></i> 50,000 System Items</li>
							<li><i class="fas fa-check-circle text-primary me-2"></i> 250,000 API Queries / mo</li>
							<li><i class="fas fa-check-circle text-primary me-2"></i> Advanced Real-time Metrics</li>
							<li><i class="fas fa-check-circle text-primary me-2"></i> Fortify Multi-Tenant 2FA</li>
							<li><i class="fas fa-check-circle text-primary me-2"></i> Priority SLA Support</li>
						</ul>
					</div>

					<div class="mt-4 pt-3 border-top">
						<a href="{{ route('register') }}" class="btn-elite btn-elite-primary w-100 text-center">
							Start 14-Day Free Trial
						</a>
					</div>
				</div>
			</div>

			<!-- Enterprise Tier -->
			<div class="col-lg-4 col-md-6">
				<div class="pricing-card">
					<div class="mb-4">
						<span class="badge bg-secondary bg-opacity-10 text-body border fw-bold px-3 py-2 rounded-pill small">Enterprise</span>
						<div class="mt-3">
							<span class="price"><span class="price-currency">$</span>199</span>
							<span class="price-period">/ month</span>
						</div>
						<p class="text-muted small mt-2">Dedicated infrastructure for global organizations.</p>
					</div>

					<div class="py-3 border-top flex-grow-1">
						<h6 class="fw-bold text-body mb-3">Full Enterprise Capability:</h6>
						<ul class="list-unstyled text-muted small" style="line-height: 2.2;">
							<li><i class="fas fa-check text-success me-2"></i> Unlimited Team Seats</li>
							<li><i class="fas fa-check text-success me-2"></i> Unlimited Database Items</li>
							<li><i class="fas fa-check text-success me-2"></i> Unlimited Dedicated API Limits</li>
							<li><i class="fas fa-check text-success me-2"></i> Custom Single Sign-On (SAML/SSO)</li>
							<li><i class="fas fa-check text-success me-2"></i> 99.99% Uptime Guarantee</li>
							<li><i class="fas fa-check text-success me-2"></i> Dedicated Solutions Architect</li>
						</ul>
					</div>

					<div class="mt-4 pt-3 border-top">
						<a href="{{ url('/contact') }}" class="btn-elite btn-elite-outline w-100 text-center">
							Contact Enterprise Sales
						</a>
					</div>
				</div>
			</div>
		</div>

		<!-- Bottom CTA Box -->
		<div class="mt-5 p-4 p-lg-5 rounded-4 pricing-cta-box" style="border-radius: 24px;">
			<div class="row align-items-center g-3">
				<div class="col-lg-8 text-center text-lg-start mb-3 mb-lg-0">
					<span class="badge-elite badge-elite-dark mb-2">Need a custom contract?</span>
					<h3 class="text-white fw-bold mb-2">Tailored Deployments for Global Scale</h3>
					<p class="mb-0" style="color: rgba(226, 232, 240, 0.9);">We work directly with engineering leads and procurement departments.</p>
				</div>
				<div class="col-lg-4 text-center text-lg-end">
					<a href="{{ url('/contact') }}" class="btn-elite btn-elite-primary">
						Talk to an Architect <i class="fas fa-arrow-right ms-2" style="font-size: 11px;"></i>
					</a>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Section Pricing End -->
@endsection
