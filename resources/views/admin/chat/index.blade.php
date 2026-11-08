@extends('layouts.admin')

@section('title', 'Team Chat & Discussions - XivigAdmin PRO')

@section('content')
<div class="page-chat">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Collaborative Team Chat</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <span class="breadcrumb-item active">Live Chat</span>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-success-subtle text-success px-3 py-2 border border-success-subtle">
        <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> Live Workspace
      </span>
    </div>
  </div>

  <div class="card overflow-hidden" style="min-height: 680px;">
    <div class="row g-0 h-100">

      <!-- Chat Sidebar -->
      <div class="col-lg-4 col-xl-3 border-end bg-body-tertiary">
        <div class="p-3 border-bottom">
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-body border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="chatSearchInput" class="form-control bg-body border-start-0" placeholder="Search team or channels...">
          </div>
        </div>

        <div class="chat-sidebar-scroll" style="max-height: 600px; overflow-y: auto;">
          <!-- Public Channels -->
          <div class="px-3 pt-3 pb-1 text-uppercase fw-bold text-muted" style="font-size: 11px; letter-spacing: 0.05em;">
            Channels
          </div>
          <div class="list-group list-group-flush mb-2">
            @foreach ($channels as $key => $chan)
              <a href="{{ route('admin.chat.index', ['channel' => $key]) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2.5 px-3 py-2 border-0 {{ (!$currentReceiver && $currentChannel === $key) ? 'active' : '' }}">
                <i class="bi {{ $chan['icon'] }} fs-5"></i>
                <div class="flex-grow-1 min-w-0">
                  <div class="fw-semibold text-truncate">{{ $chan['name'] }}</div>
                  <small class="text-truncate d-block opacity-75">{{ $chan['desc'] }}</small>
                </div>
              </a>
            @endforeach
          </div>

          <!-- Direct Messages -->
          <div class="px-3 pt-2 pb-1 text-uppercase fw-bold text-muted" style="font-size: 11px; letter-spacing: 0.05em;">
            Direct Messages
          </div>
          <div class="list-group list-group-flush mb-3">
            @foreach ($users as $u)
              <a href="{{ route('admin.chat.index', ['user_id' => $u->id]) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 px-3 py-2 border-0 {{ ($currentReceiver && $currentReceiver->id === $u->id) ? 'active' : '' }}">
                <div class="position-relative">
                  <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 34px; height: 34px; font-size: 13px;">
                    {{ strtoupper(substr($u->name, 0, 1)) }}
                  </div>
                  <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle" style="width: 10px; height: 10px;"></span>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <div class="fw-semibold text-truncate" style="font-size: 13.5px;">{{ $u->name }}</div>
                  <small class="text-muted text-truncate d-block">{{ $u->email }}</small>
                </div>
              </a>
            @endforeach
          </div>
        </div>
      </div>

      <!-- Chat Conversation Area -->
      <div class="col-lg-8 col-xl-9 d-flex flex-column" style="height: 680px;">

        <!-- Active Chat Header -->
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-body">
          <div class="d-flex align-items-center gap-2">
            @if ($currentReceiver)
              <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                {{ strtoupper(substr($currentReceiver->name, 0, 1)) }}
              </div>
              <div>
                <h6 class="mb-0 fw-bold">{{ $currentReceiver->name }}</h6>
                <small class="text-success"><i class="bi bi-circle-fill me-1" style="font-size: 7px;"></i> Direct Conversation ({{ $currentReceiver->email }})</small>
              </div>
            @else
              <div class="rounded-3 p-2 bg-primary-subtle text-primary">
                <i class="bi {{ $channels[$currentChannel]['icon'] ?? 'bi-hash' }} fs-5"></i>
              </div>
              <div>
                <h6 class="mb-0 fw-bold">{{ $channels[$currentChannel]['name'] ?? '# General' }}</h6>
                <small class="text-muted">{{ $channels[$currentChannel]['desc'] ?? 'Workspace team discussion' }}</small>
              </div>
            @endif
          </div>
          <div class="d-flex gap-2 text-muted">
            <button class="btn btn-sm btn-outline-secondary" title="Search this chat"><i class="bi bi-search"></i></button>
            <button class="btn btn-sm btn-outline-secondary" title="View participants"><i class="bi bi-people"></i></button>
          </div>
        </div>

        <!-- Chat Messages Container -->
        <div id="chatMessagesBox" class="flex-grow-1 p-3 overflow-y-auto" style="background-color: var(--bs-tertiary-bg);">
          @forelse ($messages as $msg)
            @php
              $isMe = (auth()->id() ?: 1) === $msg->sender_id;
            @endphp
            <div class="d-flex mb-3 gap-2 {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}">
              @if (!$isMe)
                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 32px; height: 32px; font-size: 12px;">
                  {{ strtoupper(substr($msg->sender->name ?? 'U', 0, 1)) }}
                </div>
              @endif
              <div style="max-width: 75%;">
                <div class="d-flex align-items-center gap-2 mb-1 {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}">
                  <small class="fw-bold" style="font-size: 11px;">{{ $isMe ? 'You' : ($msg->sender->name ?? 'Colleague') }}</small>
                  <small class="text-muted" style="font-size: 10px;">{{ $msg->created_at ? $msg->created_at->format('h:i A') : '' }}</small>
                </div>
                <div class="p-3 rounded-3 shadow-sm {{ $isMe ? 'bg-primary text-white' : 'bg-body border' }}" style="word-break: break-word;">
                  {{ $msg->message }}
                  @if ($msg->attachment)
                    <div class="mt-2 pt-2 border-top {{ $isMe ? 'border-white-50' : 'border-secondary-subtle' }}">
                      <a href="{{ asset($msg->attachment) }}" target="_blank" class="{{ $isMe ? 'text-white' : 'text-primary' }} small text-decoration-none fw-semibold">
                        <i class="bi bi-paperclip me-1"></i> View Attachment
                      </a>
                    </div>
                  @endif
                </div>
              </div>
              @if ($isMe)
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 32px; height: 32px; font-size: 12px;">
                  {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
              @endif
            </div>
          @empty
            <div class="text-center py-5 text-muted">
              <i class="bi bi-chat-heart fs-1 d-block mb-2"></i>
              No messages in this conversation yet. Send the first message below!
            </div>
          @endforelse
        </div>

        <!-- Chat Input Form -->
        <div class="p-3 border-top bg-body">
          <form action="{{ route('admin.chat.store') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
            @csrf
            @if ($currentReceiver)
              <input type="hidden" name="receiver_id" value="{{ $currentReceiver->id }}">
            @else
              <input type="hidden" name="channel" value="{{ $currentChannel }}">
            @endif

            <label for="chatAttachment" class="btn btn-outline-secondary mb-0" title="Attach file or screenshot">
              <i class="bi bi-paperclip"></i>
              <input type="file" name="attachment" id="chatAttachment" style="display: none;">
            </label>

            <input type="text" name="message" class="form-control" placeholder="Write a message to {{ $currentReceiver ? $currentReceiver->name : ('#' . $currentChannel) }}..." required autocomplete="off">

            <button type="submit" class="btn btn-primary px-3">
              <i class="bi bi-send-fill me-1"></i> Send
            </button>
          </form>
        </div>

      </div>

    </div>
  </div>

</div>

@push('scripts')
<script>
  // Auto-scroll chat box to bottom
  const chatBox = document.getElementById('chatMessagesBox');
  if (chatBox) {
    chatBox.scrollTop = chatBox.scrollHeight;
  }
</script>
@endpush
@endsection
