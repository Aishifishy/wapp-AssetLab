<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;

class RadminAuthController extends Controller
{
    // Login methods removed - now handled by UnifiedAuthController

    public function logout(Request $request): RedirectResponse
    {
        // Log logout
        if ($user = Auth::guard('admin')->user()) {
            Log::info('Admin logout', ['email' => $user->email]);
        }

        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}