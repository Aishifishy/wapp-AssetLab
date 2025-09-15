<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AdminProfileController extends Controller
{
    /**
     * Display the admin's profile form.
     */
    public function edit(Request $request): View
    {
        return view('admin.profile.edit', [
            'user' => auth()->guard('admin')->user(),
        ]);
    }

    /**
     * Update the admin's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $admin = auth()->guard('admin')->user();
        $admin->fill($request->validated());

        if ($admin->isDirty('email')) {
            $admin->email_verified_at = null;
        }

        $admin->save();

        return Redirect::route('admin.profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the admin's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password:admin'],
        ]);

        $admin = auth()->guard('admin')->user();

        Auth::guard('admin')->logout();

        $admin->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/admin/login');
    }
}
