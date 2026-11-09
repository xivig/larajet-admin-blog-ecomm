@extends('layouts.admin')

@section('title', 'Newsletter & Subscribers - Xivig Admin')

@section('content')
<div class="page-newsletter">
    <!-- Header -->
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="page-title mb-1">Newsletter &amp; Subscriber Audience</h1>
            <nav class="breadcrumb mb-0">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
                <span class="breadcrumb-item active">Newsletter</span>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.newsletter.export') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-download me-1"></i> Export Subscribers (.CSV)
            </a>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#composeCampaignModal">
                <i class="bi bi-send me-1"></i> Send Broadcast Campaign
            </button>
        </div>
    </div>

    <!-- Audience Stat Counters -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="p-3 rounded-circle bg-primary-subtle text-primary fs-3">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold mb-0">{{ number_format($totalCount) }}</div>
                        <span class="text-muted small">Total Subscribers</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="p-3 rounded-circle bg-success-subtle text-success fs-3">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold mb-0">{{ number_format($activeCount) }}</div>
                        <span class="text-muted small">Active &amp; Verified</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="p-3 rounded-circle bg-warning-subtle text-warning fs-3">
                        <i class="bi bi-person-x-fill"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold mb-0">{{ number_format($unsubscribedCount) }}</div>
                        <span class="text-muted small">Unsubscribed</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscribers Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0 fw-bold">Subscriber List</h5>
            
            <form action="{{ route('admin.newsletter.index') }}" method="GET" class="d-flex gap-2">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Search email..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                </div>
                @if(request('search'))
                    <a href="{{ route('admin.newsletter.index') }}" class="btn btn-sm btn-light">Clear</a>
                @endif
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Subscriber</th>
                            <th>Status</th>
                            <th>Source</th>
                            <th>Joined Date</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subscribers as $sub)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $sub->email }}</div>
                                    @if($sub->name)
                                        <small class="text-muted">{{ $sub->name }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($sub->status === 'active')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Unsubscribed</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $sub->source }}</span>
                                </td>
                                <td class="small text-muted">
                                    {{ $sub->created_at->format('M d, Y h:i A') }}
                                </td>
                                <td class="text-end pe-4">
                                    <form action="{{ route('admin.newsletter.destroy', $sub->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove subscriber?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Subscriber">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-envelope-paper fs-1 d-block mb-2 text-secondary"></i>
                                    No subscribers registered yet. Website readers subscribing via footer will be shown here.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($subscribers->hasPages())
                <div class="p-3 border-top">
                    {{ $subscribers->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Compose Campaign Broadcast Modal -->
    <div class="modal fade" id="composeCampaignModal" tabindex="-1" aria-labelledby="composeCampaignModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.newsletter.send') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="composeCampaignModalLabel">
                            <i class="bi bi-send text-primary me-2"></i> Compose Newsletter Broadcast
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info py-2 small mb-3">
                            <i class="bi bi-info-circle me-1"></i> This campaign broadcast will be sent to all <strong>{{ $activeCount }}</strong> active subscribers.
                        </div>

                        <div class="mb-3">
                            <label for="campaignSubject" class="form-label fw-semibold">Subject Line <span class="text-danger">*</span></label>
                            <input type="text" name="subject" id="campaignSubject" class="form-control" placeholder="e.g. Weekly Digest: Highlights & Top Articles" required>
                        </div>

                        <div class="mb-3">
                            <label for="campaignContent" class="form-label fw-semibold">Email Content (HTML or Plain Text) <span class="text-danger">*</span></label>
                            <textarea name="content" id="campaignContent" rows="8" class="form-control font-monospace" placeholder="Enter newsletter broadcast content..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send-fill me-1"></i> Dispatch Broadcast
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
