<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View as ViewContract;

class RadminAuthController extends Controller
{
    public function showLoginForm(): ViewContract|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.radmin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Log attempt
        Log::info('Admin login attempt', ['email' => $credentials['email']]);

        // First, make sure we're logged out of all guards
        Auth::guard('web')->logout();
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            // Log successful login
            Log::info('Admin login successful', [
                'email' => $credentials['email'], 
                'id' => Auth::guard('admin')->id()
            ]);

            $request->session()->regenerate();

            // Check if the session was regenerated
            Log::info('Session regenerated', [
                'session_id' => $request->session()->getId(),
                'admin_id' => Auth::guard('admin')->id()
            ]);

            // Double check authentication
            if (Auth::guard('admin')->check()) {
                Log::info('Admin still authenticated after session regeneration', [
                    'admin_id' => Auth::guard('admin')->id()
                ]);
                return redirect()->intended(route('admin.dashboard'));
            }

            // If we lost authentication, log the error and logout
            Log::error('Admin authentication lost after session regeneration');
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return back()->withErrors([
                'email' => 'Authentication was lost during login process.',
            ])->onlyInput('email');
        }

        // Log failed login
        Log::warning('Admin login failed', ['email' => $credentials['email']]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        // Log logout
        if ($user = Auth::guard('admin')->user()) {
            Log::info('Admin logout', ['email' => $user->email]);
        }

        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
} 