@extends('layouts.auth')

@section('title', 'Register')
@section('subtitle', 'Create a new account')

@section('content')
    <!-- Email Verification Notice -->
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Email Verification Required</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>After registration, you'll need to verify your email address before accessing your account. Please check your inbox for a verification link.</p>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <div class="mt-1">
                <input id="name" name="name" type="text" required
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    value="{{ old('name') }}">            </div>
            <x-form-error field="name" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <div class="mt-1">
                <input id="email" name="email" type="email" autocomplete="email" required
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    value="{{ old('email') }}">            </div>
            <x-form-error field="email" />
        </div>

        <!-- Role -->
        <div>
            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
            <div class="mt-1">
                <select id="role" name="role" required
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Select a role</option>
                    <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                    <option value="faculty" {{ old('role') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                    <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                </select>            </div>
            <x-form-error field="role" />
        </div>

        <!-- Department -->
        <div>
            <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
            <div class="mt-1">
                <input id="department" name="department" type="text" required
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    value="{{ old('department') }}">            </div>
            <x-form-error field="department" />
        </div>

        <!-- Password -->
        <div class="password-field-container">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <div class="mt-1 password-field-wrapper">
                <input id="password" name="password" type="password" required
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <button type="button" 
                        class="password-toggle"
                        onclick="togglePassword('password')"
                        aria-label="Toggle password visibility">
                    <svg class="h-4 w-4 text-gray-500" id="password-show-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg class="h-4 w-4 text-gray-500 hidden" id="password-hide-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                    </svg>
                </button>
            </div>
            <div class="error-message-container">
                <x-form-error field="password" />
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="password-field-container">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <div class="mt-1 password-field-wrapper">
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <button type="button" 
                        class="password-toggle"
                        onclick="togglePassword('password_confirmation')"
                        aria-label="Toggle password visibility">
                    <svg class="h-4 w-4 text-gray-500" id="password_confirmation-show-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg class="h-4 w-4 text-gray-500 hidden" id="password_confirmation-hide-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                    </svg>
                </button>
            </div>
            <div class="error-message-container">
                <x-form-error field="password_confirmation" />
            </div>
        </div>

        <div>
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Register
            </button>
        </div>

        <div class="text-sm text-center">
            <p class="text-gray-600">Already have an account?
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">Sign in here</a>
            </p>
        </div>
    </form>
@endsection 