<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Log the redirection
                Log::info('RedirectIfAuthenticated middleware redirecting', [
                    'guard' => $guard,
                    'user_id' => Auth::guard($guard)->id(),
                    'path' => $request->path()
                ]);

                if ($guard === 'admin') {
                    return redirect()->route('admin.dashboard');
                }

                return redirect()->route('ruser.dashboard');
            }
        }

        return $next($request);
    }
}