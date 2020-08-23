<?php


namespace App\Http\Middleware;


use App\models\NotificationManager;
use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;

class NotificationMiddleware extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            Auth::user()->notification_unread_count = NotificationManager::getOrNew(Auth::user())->unread_count;
        }
        return $next($request);
    }

}
