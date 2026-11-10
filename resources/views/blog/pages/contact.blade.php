@extends('layouts.blog')

@section('content')
<section class="page-title py-5 bg-light border-bottom">
    <div class="container text-center">
        <span class="badge badge-pill badge-primary px-3 py-2 text-uppercase mb-2 font-weight-bold">Direct Channels</span>
        <h1 class="display-5 font-weight-bold text-dark mb-2">{{ $page->title }}</h1>
        <p class="lead text-muted mx-auto" style="max-width: 650px;">
            {{ $page->excerpt ?: 'Have a question, feedback, or enterprise inquiry? Our engineering team is here to assist.' }}
        </p>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <div class="card border-0 shadow-sm rounded-lg p-4 p-md-5 bg-light">
                    <h4 class="font-weight-bold mb-3">Send a Message</h4>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST">
                        @csrf
                        <input type="hidden" name="source" value="contact_form">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold small text-muted">Full Name *</label>
                                <input type="text" name="name" class="form-control" placeholder="Jane Doe" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold small text-muted">Email Address *</label>
                                <input type="email" name="email" class="form-control" placeholder="jane@company.com" required>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-muted">Subject</label>
                            <input type="text" name="subject" class="form-control" placeholder="Technical Inquiry / Partnership">
                        </div>
                        <div class="form-group mb-4">
                            <label class="font-weight-bold small text-muted">Your Message *</label>
                            <textarea name="message" rows="5" class="form-control" placeholder="Describe your request..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary px-4 font-weight-bold" style="background: linear-gradient(135deg, #4f46e5, #06b6d4); border: none;">
                            <i class="fas fa-paper-plane mr-2"></i> Dispatch Inquiry
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-lg p-4 p-md-5 bg-white border">
                    <h4 class="font-weight-bold mb-4">Contact Information</h4>
                    <div class="d-flex align-items-start mb-4">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3 flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold mb-1">Global Headquarters</h6>
                            <p class="text-muted small mb-0">XIVIG Maison Tech Hub, Boulevard Haussmann, Paris, France</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-4">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mr-3 flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold mb-1">Technical Support</h6>
                            <p class="text-muted small mb-0">contact@xivig.com</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start">
                        <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center mr-3 flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold mb-1">Response Window</h6>
                            <p class="text-muted small mb-0">Monday – Friday: 08:00 - 18:00 UTC</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
