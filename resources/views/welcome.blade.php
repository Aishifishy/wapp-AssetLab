@extends('layouts.app')

@section('title', ' - Welcome')

@push('styles')
<style>
    @keyframes gentle-float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    @keyframes gradient-shift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    .animate-gentle-float { animation: gentle-float 8s ease-in-out infinite; }
    .gradient-text { 
        background: linear-gradient(-45deg, #3b82f6, #6366f1, #8b5cf6, #06b6d4);
        background-size: 400% 400%;
        animation: gradient-shift 20s ease infinite;
    }
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 relative overflow-hidden">
    <!-- Clean Background Elements -->
    <div class="absolute inset-0">
        <!-- Subtle gradient orbs -->
        <div class="absolute top-20 left-20 w-80 h-80 bg-blue-400/5 rounded-full blur-3xl animate-gentle-float"></div>
        <div class="absolute bottom-20 right-20 w-96 h-96 bg-indigo-400/5 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-purple-400/3 rounded-full blur-3xl"></div>
    </div>

    <!-- Navigation Spacer -->
    <div class="relative z-10">
        <!-- Hero Section -->
        <div class="relative px-6 pt-20 pb-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="text-center">
                    <!-- Clean Logo Section -->
                    <div class="mb-5 relative">
                        <div class="relative z-10">
                            <h1 class="text-6xl lg:text-7xl font-bold tracking-tight mb-8">
                                <span class="block text-white drop-shadow-2xl">
                                    AssetLab
                                </span>
                            </h1>
                            
                            <div class="inline-flex items-center px-4 py-2 glass-card rounded-full mb-8">
                                <div class="w-2 h-2 bg-emerald-400 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-white/80 tracking-wide">Integrated Academic Platform</span>
                            </div>
                        </div>
                    </div>

                    <!-- Clean Features Preview -->
                    <div class="max-w-4xl mx-auto mb-20">
                        <!-- Simple Description -->
                        <div class="text-center mb-12">
                            <p class="text-xl text-white/70 font-light max-w-2xl mx-auto">
                                Streamline equipment requests, lab reservations, and resource management in one platform
                            </p>
                        </div>
                        
                        <!-- Minimalist Feature Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Equipment Requests -->
                            <div class="glass-card rounded-xl p-6 hover:bg-white/10 transition-all duration-300">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M9 12l-6-3"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-white mb-3 text-center">Equipment Requests</h3>
                                <p class="text-white/60 text-center text-sm leading-relaxed">
                                    Browse, request & track equipment with real-time status updates
                                </p>
                            </div>

                            <!-- Lab Reservations -->
                            <div class="glass-card rounded-xl p-6 hover:bg-white/10 transition-all duration-300">
                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-white mb-3 text-center">Lab Reservations</h3>
                                <p class="text-white/60 text-center text-sm leading-relaxed">
                                    Reserve laboratories with calendar view & recurring bookings
                                </p>
                            </div>

                            <!-- Dashboard Access -->
                            <div class="glass-card rounded-xl p-6 hover:bg-white/10 transition-all duration-300">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-white mb-3 text-center">Personal Dashboard</h3>
                                <p class="text-white/60 text-center text-sm leading-relaxed">
                                    Manage requests, reservations & view activity history
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Clean CTA Section -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-16">
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Access Platform
                        </a>

                        <a href="{{ route('register') }}"
                           class="inline-flex items-center px-8 py-4 glass-card text-white font-semibold rounded-xl border border-white/20 hover:bg-white/10 transition-all duration-300 hover:-translate-y-1">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            Create Account
                        </a>
                    </div>

                    <!-- Getting Started Steps -->
                    <div class="glass-card rounded-2xl p-8 max-w-4xl mx-auto">
                        <h3 class="text-xl font-semibold text-white mb-8 text-center">Get Started in 3 Steps</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="text-center">
                                <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span class="text-white font-bold">1</span>
                                </div>
                                <h4 class="text-white font-semibold mb-2">Create Account</h4>
                                <p class="text-white/60 text-sm">Sign up with your academic email and get instant access</p>
                            </div>
                            <div class="text-center">
                                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span class="text-white font-bold">2</span>
                                </div>
                                <h4 class="text-white font-semibold mb-2">Browse Resources</h4>
                                <p class="text-white/60 text-sm">Explore available equipment and laboratory spaces</p>
                            </div>
                            <div class="text-center">
                                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span class="text-white font-bold">3</span>
                                </div>
                                <h4 class="text-white font-semibold mb-2">Make Requests</h4>
                                <p class="text-white/60 text-sm">Submit equipment requests and lab reservations easily</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 