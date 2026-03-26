<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $unreadNotificationCount = 0;
            $latestNotifications = collect();

            if (Auth::check()) {
                $unreadNotificationCount = Notification::where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->count();

                $latestNotifications = Notification::where('user_id', Auth::id())
                    ->latest()
                    ->take(8)
                    ->get();
            }

            $view->with('unreadNotificationCount', $unreadNotificationCount)
                 ->with('latestNotifications', $latestNotifications);
        });
    }
}