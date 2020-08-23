<?php


namespace App\Http\Middleware;


use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;
use Closure;

class PermissionEditMiddleware extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      echo "PermissionEditMiddleware";

        return $next($request);
    }

}
