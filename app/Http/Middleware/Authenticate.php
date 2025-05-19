<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (!$request->expectsJson()) {
            // Log the redirection
            Log::info('Authenticate middleware redirecting', [
                'path' => $request->path(),
                'is_admin_path' => $request->is('admin/*')
            ]);

            if ($request->is('admin/*')) {
                return route('admin.login');
            }
            return route('login');
        }
        return null;
    }
}