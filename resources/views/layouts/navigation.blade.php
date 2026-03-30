
<style>
    .top-navbar {
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
        position: sticky;
        top: 0;
        z-index: 1050;
    }

    .brand-logo {
        width: 52px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: #f8fafc;
        overflow: hidden;
    }

    .brand-logo img {
        max-width: 42px;
        max-height: 42px;
        object-fit: contain;
    }

    .top-nav-link {
        display: inline-flex;
        align-items: center;
        padding: 10px 14px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        color: #334155;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .top-nav-link:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .top-nav-link.active {
        background: #eff6ff;
        color: #2563eb;
    }

    .nav-icon-btn {
        position: relative;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #334155;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .nav-icon-btn:hover {
        background: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
        transform: translateY(-1px);
    }

    .nav-icon-btn i {
        font-size: 18px;
    }

    .notification-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border-radius: 999px;
        background: #ef4444;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
        line-height: 1;
    }

    .notification-menu {
        width: 370px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
    }

    .notification-header {
        background: #f8fafc;
        padding: 14px 16px;
        border-bottom: 1px solid #e5e7eb;
    }

    .notification-item {
        display: block;
        padding: 14px 16px;
        text-decoration: none;
        color: inherit;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.2s ease;
    }

    .notification-item:hover {
        background: #f8fafc;
    }

    .notification-item.unread {
        background: #f8fbff;
    }

    .notification-title {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 2px;
    }

    .notification-message {
        font-size: 13px;
        color: #64748b;
        line-height: 1.4;
    }

    .notification-time {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 6px;
    }

    .user-trigger {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 7px 12px;
        border-radius: 14px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .user-trigger:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .user-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        text-transform: uppercase;
    }

    .user-meta {
        text-align: left;
        line-height: 1.1;
    }

    .user-meta .name {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
    }

    .user-meta .role {
        font-size: 11px;
        color: #64748b;
        text-transform: capitalize;
    }

    .user-dropdown-menu {
        min-width: 220px;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
        padding: 8px;
    }

    .user-dropdown-menu a {
        border-radius: 10px;
    }

    @media (max-width: 640px) {
        .notification-menu {
            width: 320px;
        }

        .user-meta {
            display: none;
        }
    }
</style>

<nav x-data="{ open: false }" class="top-navbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-18 py-2">
            <!-- Left -->
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 text-decoration-none">
                    <div class="brand-logo">
                        <img src="{{ asset('images/grow.png') }}" alt="Company Logo">
                    </div>
                    <div class="hidden sm:block">
                        <div class="text-sm font-semibold text-slate-900">Grow Bridges</div>
                        <div class="text-xs text-slate-500">Dashboard Panel</div>
                    </div>
                </a>

                <div class="hidden sm:flex items-center gap-2 ms-3">
                    <a href="{{ route('dashboard') }}"
                       class="top-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        Dashboard
                    </a>
                </div>
            </div>

            <!-- Right -->
            <div class="hidden sm:flex items-center gap-3">
                <!-- Notification -->
                <div class="dropdown">
                    <a class="nav-icon-btn"
                       href="#"
                       id="notificationDropdown"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        🔔

                        @if(($unreadNotificationCount ?? 0) > 0)
                            <span class="notification-badge">
                                {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                            </span>
                        @endif
                    </a>

                    <div class="dropdown-menu dropdown-menu-end p-0 notification-menu" aria-labelledby="notificationDropdown">
                        <div class="notification-header d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold text-dark">Notifications</div>
                                <div class="small text-muted">Latest updates</div>
                            </div>
                            <a href="{{ route('notifications.index') }}" class="small text-decoration-none fw-semibold">
                                View All
                            </a>
                        </div>

                        <div style="max-height: 400px; overflow-y: auto;">
                            @forelse($latestNotifications as $notification)
                                <a href="{{ route('notifications.open', $notification) }}"
                                   class="notification-item {{ !$notification->is_read ? 'unread' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div class="flex-grow-1">
                                            <div class="notification-title">{{ $notification->title }}</div>
                                            <div class="notification-message">{{ $notification->message }}</div>
                                            <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                                        </div>

                                        @if(!$notification->is_read)
                                            <span class="badge rounded-pill text-bg-primary">New</span>
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <div class="px-4 py-4 text-center text-muted small">
                                    No notifications found.
                                </div>
                            @endforelse
                        </div>

                        <div class="border-top p-3 bg-white">
                            <form action="{{ route('notifications.readAll') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary w-100 rounded-3">
                                    Mark All as Read
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="user-trigger border-0">
                            <div class="user-avatar">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>

                            <div class="user-meta">
                                <div class="name">{{ Auth::user()->name }}</div>
                                <div class="role">{{ Auth::user()->role ?? 'User' }}</div>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4 text-slate-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="user-dropdown-menu">
                            <div class="px-3 py-2 border-bottom mb-2">
                                <div class="fw-semibold text-dark">{{ Auth::user()->name }}</div>
                                <div class="small text-muted">{{ Auth::user()->email }}</div>
                            </div>

                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-200 bg-white">
        <div class="pt-2 pb-3 space-y-1 px-3">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-3 pb-4 border-t border-gray-200 px-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
