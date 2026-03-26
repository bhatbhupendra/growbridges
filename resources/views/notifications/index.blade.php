@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f4f6f9;
}

.page-container {
    max-width: 1100px;
    margin: 24px auto;
}

.card-box {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, .08);
    padding: 18px;
}

.noti-item {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 14px;
    margin-bottom: 12px;
    background: #fff;
}

.noti-item.unread {
    background: #f8fbff;
    border-color: #cfe2ff;
}

.noti-title {
    font-weight: 800;
    margin-bottom: 4px;
}

.noti-meta {
    font-size: 12px;
    color: #6c757d;
}
</style>

<div class="container page-container">
    <div class="card-box">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Notifications</h4>

            <form action="{{ route('notifications.readAll') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-dark">Mark All as Read</button>
            </form>
        </div>

        @if(session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
        @endif

        @forelse($notifications as $notification)
        <div class="noti-item {{ !$notification->is_read ? 'unread' : '' }}">
            <div class="d-flex justify-content-between align-items-start gap-3">
                <div class="flex-grow-1">
                    <div class="noti-title">
                        {{ $notification->title }}
                        @if(!$notification->is_read)
                        <span class="badge bg-primary ms-2">New</span>
                        @endif
                    </div>

                    <div>{{ $notification->message }}</div>

                    <div class="noti-meta mt-2">
                        Type: {{ $notification->notification_type }} |
                        {{ $notification->created_at->format('Y-m-d h:i A') }}
                    </div>
                </div>

                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('notifications.open', $notification) }}" class="btn btn-sm btn-primary">
                        Open
                    </a>

                    @if(!$notification->is_read)
                    <form action="{{ route('notifications.read', $notification) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                            Mark Read
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="alert alert-light border mb-0">No notifications found.</div>
        @endforelse

        <div class="mt-3">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@endsection