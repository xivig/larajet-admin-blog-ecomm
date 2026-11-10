<!-- Comments Section -->
<div class="comment-area pt-4">
    <h4 class="mb-4 font-weight-bold text-body d-flex align-items-center justify-content-between">
        <span><i class="far fa-comments text-primary mr-2"></i> Discussion ({{ $post->comments ? $post->comments->where('status', 'approved')->count() : 0 }})</span>
        <span class="badge badge-light border text-muted small font-weight-normal">Moderated & Protected</span>
    </h4>

    @php
        $approvedComments = $post->comments ? $post->comments->where('status', 'approved') : collect();
    @endphp

    @if($approvedComments->count() > 0)
        @foreach($approvedComments as $comment)
            <div class="comment-area-box mb-4 p-3 rounded-lg bg-light border shadow-sm" id="comment-item-{{ $comment->id }}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center">
                        <div class="comment-avatar mr-3" style="width: 40px; height: 40px; font-size: 16px; background: linear-gradient(135deg, #4f46e5, #06b6d4); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                            {{ strtoupper(substr($comment->author_name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="d-flex align-items-center">
                                <h6 class="mb-0 font-weight-bold text-body mr-2">{{ $comment->author_name }}</h6>
                                @if($comment->user_id)
                                    <span class="badge badge-primary px-1 py-0 small" style="font-size: 10px;">Member</span>
                                @else
                                    <span class="badge badge-secondary px-1 py-0 small" style="font-size: 10px;">Guest</span>
                                @endif
                            </div>
                            <span class="text-muted small">{{ $comment->created_at ? $comment->created_at->diffForHumans() : 'Just now' }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        {{-- Upvote / Like Button --}}
                        <form action="{{ route('comments.like', $comment->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary py-1 px-2 text-decoration-none" title="Upvote comment" style="border-radius: 999px; font-size: 12px;">
                                <i class="far fa-thumbs-up mr-1 text-primary"></i> <span>{{ $comment->likes_count ?? 0 }}</span>
                            </button>
                        </form>
                        {{-- Reply Trigger --}}
                        <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2" data-toggle="collapse" data-target="#reply-form-{{ $comment->id }}" data-bs-toggle="collapse" data-bs-target="#reply-form-{{ $comment->id }}" style="border-radius: 999px; font-size: 12px;">
                            <i class="fas fa-reply mr-1"></i> Reply
                        </button>
                        {{-- Report Flag --}}
                        <form action="{{ route('comments.report', $comment->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-link text-muted p-0 ml-1" title="Report spam or inappropriate content">
                                <i class="far fa-flag" style="font-size: 12px;"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="comment-content mt-2 text-body" style="font-size: 14.5px;">
                    <p class="mb-1">{{ $comment->comment }}</p>
                </div>

                <!-- Nested Replies -->
                @php
                    $commentReplies = $comment->replies ? $comment->replies->where('status', 'approved') : collect();
                @endphp
                @if($commentReplies->count() > 0)
                    <div class="pl-4 mt-3 pt-3" style="border-left: 2px solid #cbd5e1;">
                        @foreach($commentReplies as $reply)
                            <div class="mb-3 p-2 rounded bg-white border" id="reply-item-{{ $reply->id }}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="d-flex align-items-center">
                                        <strong class="text-body small mr-2">{{ $reply->author_name }}</strong>
                                        @if($reply->user_id)
                                            <span class="badge badge-info px-1 py-0 mr-2" style="font-size: 9px;">Verified</span>
                                        @endif
                                        <span class="text-muted" style="font-size: 11px;">{{ $reply->created_at ? $reply->created_at->diffForHumans() : 'Recent' }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <form action="{{ route('comment-replies.like', $reply->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-link text-muted py-0 px-1" title="Like reply">
                                                <i class="far fa-thumbs-up mr-1 text-primary"></i> <span>{{ $reply->likes_count ?? 0 }}</span>
                                            </button>
                                        </form>
                                        <form action="{{ route('comment-replies.report', $reply->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-link text-muted py-0 px-1" title="Report reply">
                                                <i class="far fa-flag" style="font-size: 11px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <p class="mb-0 small text-body">{{ $reply->reply }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Collapsible In-Place Reply Form -->
                <div class="collapse mt-3" id="reply-form-{{ $comment->id }}">
                    <form action="{{ route('comment-replies.store') }}" method="POST" class="p-3 bg-white border rounded">
                        @csrf
                        <input type="hidden" name="comment_id" value="{{ $comment->id }}">
                        <input type="text" name="website_hp" style="display:none;" tabindex="-1" autocomplete="off">

                        @guest
                            <div class="row mb-2">
                                <div class="col-md-6 mb-2">
                                    <input type="text" name="guest_name" class="form-control form-control-sm" placeholder="Your Name" required>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <input type="email" name="guest_email" class="form-control form-control-sm" placeholder="Your Email" required>
                                </div>
                            </div>
                        @endguest

                        <div class="form-group mb-2">
                            <textarea name="reply" rows="2" class="form-control form-control-sm" placeholder="Write your reply to {{ $comment->author_name }}..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm font-weight-bold">
                            <i class="fas fa-reply mr-1"></i> Post Reply
                        </button>
                    </form>
                </div>

            </div>
        @endforeach
    @else
        <div class="text-center py-4 bg-light rounded mb-4">
            <p class="text-muted mb-0">No comments yet. Start the conversation below!</p>
        </div>
    @endif
</div>

<!-- Comment Submission Form -->
<div class="mt-5 p-4 card-elite rounded shadow-sm border">
    <h4 class="mb-3 font-weight-bold text-body">Leave a Comment</h4>
    <p class="text-muted small mb-3">Share your perspectives or ask a question regarding this article.</p>

    @auth
        <div class="alert alert-info py-2 px-3 small mb-3 d-flex align-items-center">
            <i class="fas fa-user-check mr-2"></i> Logged in as <strong class="ml-1">{{ auth()->user()->name }}</strong>.
        </div>
    @endauth

    <form action="{{ route('comments.store') }}" method="POST">
        @csrf
        <input type="hidden" name="post_id" value="{{ $post->id }}">
        {{-- Honeypot Spam Protection --}}
        <input type="text" name="website_hp" style="display:none;" tabindex="-1" autocomplete="off">

        @guest
            <div class="row mb-3">
                <div class="col-md-4 mb-2">
                    <label class="font-weight-bold small text-body">Name *</label>
                    <input type="text" name="guest_name" class="form-control form-control-elite" placeholder="Alex Morgan" required>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="font-weight-bold small text-body">Email *</label>
                    <input type="email" name="guest_email" class="form-control form-control-elite" placeholder="alex@domain.com" required>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="font-weight-bold small text-body">Website (Optional)</label>
                    <input type="url" name="guest_website" class="form-control form-control-elite" placeholder="https://example.com">
                </div>
            </div>
        @endguest

        <div class="form-group mb-3">
            <label class="font-weight-bold small text-body">Your Perspective *</label>
            <textarea class="form-control form-control-elite" name="comment" rows="4" placeholder="Share your technical feedback, thoughts, or questions..." required></textarea>
        </div>

        <button type="submit" class="btn-elite btn-elite-primary btn-sm font-weight-bold">
            <i class="fas fa-paper-plane mr-2"></i> Submit Comment
        </button>
    </form>
</div>
