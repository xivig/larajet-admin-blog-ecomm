@extends('home')
@section('title', 'Contact Us - Xivig Platform')
@section('page-title', 'Contact Us')
@section('page-heading', 'Get in Touch')
@section('content')

<!-- Contact Section Start -->
<section class="section py-5 position-relative" style="background: #f8fafc;">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-lg-6 col-md-12 mb-5 mb-lg-0">
				<div class="p-5 bg-white rounded shadow-sm border" style="border-radius: 20px;">
					<span class="badge-elite mb-2">Direct Inquiry</span>
					<h3 class="font-weight-bold text-dark mb-4">Send Us a Message</h3>

					<form id="contact-form" class="contact__form" method="POST" action="#" onsubmit="event.preventDefault(); alert('Thank you! Your inquiry has been received by the Xivig engineering desk.');">
						@csrf
						<div class="form-group mb-3">
							<label class="font-weight-bold small text-dark">Full Name</label>
							<input name="name" type="text" class="form-control form-control-elite" placeholder="John Doe" required>
						</div>
						<div class="form-group mb-3">
							<label class="font-weight-bold small text-dark">Work Email</label>
							<input name="email" type="email" class="form-control form-control-elite" placeholder="name@company.com" required>
						</div>
						<div class="form-group mb-4">
							<label class="font-weight-bold small text-dark">Project Specifications</label>
							<textarea name="message" class="form-control form-control-elite" rows="4" placeholder="Briefly describe your objectives or questions..." required></textarea>
						</div>
						<button class="btn-elite btn-elite-primary w-100" name="submit" type="submit">
							<i class="fas fa-paper-plane mr-2"></i> Submit Inquiry
						</button>
					</form>
				</div>
			</div>

			<div class="col-lg-6 col-md-12">
				<div class="pl-lg-5">
					<span class="badge-elite mb-2">Global Headquarters</span>
					<h2 class="font-weight-bold text-dark mb-4 display-4" style="letter-spacing: -0.02em;">We are always accessible to innovators</h2>
					<p class="text-muted lead mb-5">Whether planning a ground-up greenfield deployment or auditing existing enterprise infrastructures, our solutions architects are ready to consult.</p>

					<div class="mb-4 d-flex align-items-center p-3 bg-white rounded border shadow-sm">
						<div class="icon-box-elite mr-3 mb-0" style="width: 50px; height: 50px; font-size: 20px;">
							<i class="fas fa-map-marker-alt"></i>
						</div>
						<div>
							<h6 class="mb-0 font-weight-bold text-dark">Location</h6>
							<span class="text-muted small">North Main Street, Brooklyn, New York &amp; Global Remote</span>
						</div>
					</div>

					<div class="mb-4 d-flex align-items-center p-3 bg-white rounded border shadow-sm">
						<div class="icon-box-elite mr-3 mb-0" style="width: 50px; height: 50px; font-size: 20px;">
							<i class="fas fa-envelope"></i>
						</div>
						<div>
							<h6 class="mb-0 font-weight-bold text-dark">Email Inquiries</h6>
							<span class="text-muted small">contact@xivig.com / support@xivig.com</span>
						</div>
					</div>

					<div class="mb-4 d-flex align-items-center p-3 bg-white rounded border shadow-sm">
						<div class="icon-box-elite mr-3 mb-0" style="width: 50px; height: 50px; font-size: 20px;">
							<i class="fas fa-phone-alt"></i>
						</div>
						<div>
							<h6 class="mb-0 font-weight-bold text-dark">Direct Line</h6>
							<span class="text-muted small">+91 98367 26662 / +1 (800) 555-0199</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Contact Section End -->
@endsection
