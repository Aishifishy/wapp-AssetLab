@extends('layouts.auth')

@section('title', 'Verify Your Email')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg shadow-lg">
    <div class="px-8 py-12">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Verify Your Email</h2>
            
            <p class="mt-4 text-sm text-gray-600">
                We've sent a verification link to your email address. Please click the link in the email to verify your account and start using AssetLab.
            </p>
            
            @if (session('message'))
                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-md">
                    <p class="text-sm text-green-800">{{ session('message') }}</p>
                </div>
            @endif
            
            @if ($errors->any())
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-md">
                    <p class="text-sm text-red-800">{{ $errors->first() }}</p>
                </div>
            @endif
        </div>
        
        <div class="mt-8">
            <p class="text-center text-sm text-gray-600 mb-4">
                Didn't receive the email? Check your spam folder or request a new one.
            </p>
            
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    Resend Verification Email
                </button>
            </form>
        </div>
        
        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">or</span>
                </div>
            </div>
            
            <div class="mt-6">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        Use Different Account
                    </button>
                </form>
            </div>
        </div>
        
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500">
                If you're having trouble, please contact support at <a href="mailto:itso@nu-laguna.edu.ph" class="text-blue-600 hover:text-blue-500">itso@nu-laguna.edu.ph</a>
            </p>
        </div>
    </div>
</div>

<script>
// Auto-logout when user leaves the page (if still unverified)
window.addEventListener('beforeunload', function(e) {
    // This will trigger when the user navigates away or closes the tab
    fetch('{{ route('logout') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        keepalive: true // Ensures request completes even if page is unloading
    });
});

// Also auto-logout after 10 minutes on this page
setTimeout(function() {
    fetch('{{ route('logout') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    }).then(() => {
        window.location.href = '{{ route('login') }}';
    });
}, 600000); // 10 minutes
</script>
@endsection