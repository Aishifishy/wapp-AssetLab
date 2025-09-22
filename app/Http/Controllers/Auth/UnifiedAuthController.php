<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Ruser;
use App\Models\Radmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use App\Mail\UserRegistrationWelcome;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;

class UnifiedAuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.unified.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Check if email exists in admins table first
        $admin = Radmin::where('email', $credentials['email'])->first();
        
        if ($admin) {
            // Attempt admin login
            if (Hash::check($credentials['password'], $admin->password)) {
                // Clear any existing auth
                Auth::guard('web')->logout();
                Auth::guard('admin')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Login as admin
                Auth::guard('admin')->login($admin, $request->boolean('remember'));
                
                Log::info('Admin login successful via unified login', [
                    'email' => $credentials['email'], 
                    'id' => $admin->id
                ]);

                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }
        }

        // Check if email exists in users table
        $user = Ruser::where('email', $credentials['email'])->first();
        
        if ($user) {
            // Attempt user login
            if (Hash::check($credentials['password'], $user->password)) {
                // Clear any existing auth
                Auth::guard('web')->logout();
                Auth::guard('admin')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Login user (verified or unverified)
                Auth::guard('web')->login($user, $request->boolean('remember'));

                // Check if email is verified after login
                if (!$user->hasVerifiedEmail()) {
                    // Redirect to existing verification notice page
                    return redirect()->route('verification.notice')
                        ->with('message', 'Please verify your email address to access your account.');
                }
                
                Log::info('User login successful via unified login', [
                    'email' => $credentials['email'], 
                    'id' => $user->id
                ]);

                $request->session()->regenerate();
                return redirect()->intended(route('ruser.dashboard'));
            }
        }

        // If we get here, login failed
        Log::warning('Unified login failed', ['email' => $credentials['email']]);        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle unified logout for both users and admins
     */
    public function logout(Request $request): RedirectResponse
    {
        // Log logout attempt with user info if available
        if ($admin = Auth::guard('admin')->user()) {
            Log::info('Admin logout via unified logout', ['email' => $admin->email]);
        } elseif ($user = Auth::guard('web')->user()) {
            Log::info('User logout via unified logout', ['email' => $user->email]);
        }

        // Logout from both guards to ensure clean logout
        Auth::guard('admin')->logout();
        Auth::guard('web')->logout();
        
        // Invalidate session and regenerate token
        $request->session()->invalidate();
        $request->session()->regenerateToken();        return redirect()->route('login');
    }

    /**
     * Show the user registration form
     */
    public function showRegistrationForm(): View
    {
        return view('auth.ruser.register');
    }

    /**
     * Handle user registration
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:rusers'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:student,faculty,staff'],
            'department' => ['required', 'string', 'max:255'],
        ]);

        $user = Ruser::create($validated);
        
        Log::info('User registration successful', [
            'email' => $user->email,
            'id' => $user->id,
            'role' => $user->role
        ]);

        // Trigger email verification
        event(new Registered($user));

        // Log the user in so they can access the verification notice
        Auth::guard('web')->login($user);

        return redirect()->route('verification.notice')->with('success', 'Registration successful! Please check your email to verify your account.');
    }


}
