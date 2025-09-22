<?php $__env->startSection('title', ' - Welcome'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    @keyframes gentle-float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        33% { transform: translateY(-8px) rotate(1deg); }
        66% { transform: translateY(-4px) rotate(-1deg); }
    }
    @keyframes gradient-shift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
        50% { box-shadow: 0 0 30px rgba(99, 102, 241, 0.5), 0 0 40px rgba(139, 92, 246, 0.3); }
    }
    @keyframes slide-in-up {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    @keyframes fade-in-scale {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    @keyframes bounce-in {
        0% {
            transform: scale(0.3);
            opacity: 0;
        }
        50% {
            transform: scale(1.05);
        }
        70% {
            transform: scale(0.9);
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
    
    .animate-gentle-float { 
        animation: gentle-float 12s ease-in-out infinite; 
    }
    .animate-pulse-glow {
        animation: pulse-glow 4s ease-in-out infinite;
    }
    .animate-slide-in-up {
        animation: slide-in-up 0.6s ease-out forwards;
    }
    .animate-fade-in-scale {
        animation: fade-in-scale 0.8s ease-out forwards;
    }
    .animate-bounce-in {
        animation: bounce-in 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
    }
    
    .gradient-text { 
        background: linear-gradient(-45deg, #3b82f6, #6366f1, #8b5cf6, #06b6d4);
        background-size: 400% 400%;
        animation: gradient-shift 20s ease infinite;
    }
    
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .glass-card:hover {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .feature-card-delay-1 { animation-delay: 0.1s; }
    .feature-card-delay-2 { animation-delay: 0.2s; }
    .feature-card-delay-3 { animation-delay: 0.3s; }
    
    .btn-hover-lift {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .btn-hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }
    
    .btn-hover-lift:active {
        transform: translateY(0);
    }
    
    /* Mobile optimizations */
    @media (max-width: 640px) {
        .glass-card {
            backdrop-filter: blur(8px);
        }
        
        .animate-gentle-float {
            animation-duration: 15s;
        }
        
        /* Touch-friendly sizing */
        .btn-hover-lift {
            min-height: 48px;
            min-width: 48px;
        }
        
        /* Reduce motion for mobile performance */
        .group:hover .group-hover\:scale-110 {
            transform: scale(1.05);
        }
        
        .group:hover .group-hover\:rotate-3 {
            transform: rotate(1.5deg);
        }
    }
    
    /* Accessibility improvements */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
        
        .animate-gentle-float,
        .animate-pulse-glow,
        .gradient-text {
            animation: none !important;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 relative overflow-hidden">
    <!-- Enhanced Background Elements -->
    <div class="absolute inset-0">
        <!-- Animated gradient orbs with responsive sizing -->
        <div class="absolute top-10 sm:top-20 left-4 sm:left-20 w-60 sm:w-80 h-60 sm:h-80 bg-blue-400/5 rounded-full blur-3xl animate-gentle-float"></div>
        <div class="absolute bottom-10 sm:bottom-20 right-4 sm:right-20 w-72 sm:w-96 h-72 sm:h-96 bg-indigo-400/5 rounded-full blur-3xl animate-gentle-float" style="animation-delay: -4s;"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[400px] sm:w-[600px] h-[400px] sm:h-[600px] bg-purple-400/3 rounded-full blur-3xl animate-gentle-float" style="animation-delay: -8s;"></div>
        <!-- Additional subtle orbs for depth -->
        <div class="absolute top-1/4 right-1/4 w-40 sm:w-60 h-40 sm:h-60 bg-cyan-400/3 rounded-full blur-2xl animate-gentle-float" style="animation-delay: -2s;"></div>
        <div class="absolute bottom-1/4 left-1/4 w-48 sm:w-72 h-48 sm:h-72 bg-pink-400/2 rounded-full blur-2xl animate-gentle-float" style="animation-delay: -6s;"></div>
    </div>

    <!-- Navigation Spacer -->
    <div class="relative z-10">
        <!-- Hero Section -->
        <div class="relative px-6 pt-20 pb-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="text-center">
                    <!-- Enhanced Hero Section -->
                    <div class="mb-12 relative">
                        <div class="relative z-10">
                            <h1 class="text-5xl sm:text-6xl lg:text-7xl xl:text-8xl font-black tracking-tight mb-6">
                                <span class="block text-white drop-shadow-2xl">
                                    Asset<span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Lab</span>
                                </span>
                            </h1>
                            
                            <div class="inline-flex items-center px-6 py-3 glass-card rounded-full mb-10 shadow-2xl border border-white/20">
                                <div class="w-3 h-3 bg-gradient-to-r from-emerald-400 to-teal-400 rounded-full mr-4 shadow-lg"></div>
                                <span class="text-base font-semibold text-white/90 tracking-wide">Integrated Academic Platform</span>
                            </div>

                            <p class="text-xl sm:text-2xl text-white/70 font-light max-w-3xl mx-auto leading-relaxed mb-12">
                                Streamline equipment requests, lab reservations, and resource management in one seamless platform
                            </p>
                        </div>
                    </div>

                    <!-- Enhanced Features Section -->
                    <div class="max-w-6xl mx-auto mb-20">
                        <!-- Section Header -->
                        <div class="text-center mb-16">
                            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">
                                Everything You Need
                            </h2>
                            <div class="w-16 h-1 bg-gradient-to-r from-blue-400 to-indigo-400 mx-auto rounded-full mb-6"></div>
                        </div>
                        
                        <!-- Enhanced Feature Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <!-- Equipment Requests -->
                            <div class="group glass-card rounded-2xl p-8 hover:bg-white/10 transition-all duration-500 hover:transform hover:-translate-y-2 hover:shadow-2xl border border-white/10 animate-fade-in-scale feature-card-delay-1">
                                <div class="relative mb-6">
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mx-auto shadow-xl group-hover:shadow-blue-500/25 transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                                        <svg class="w-8 h-8 text-white transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M9 12l-6-3"/>
                                        </svg>
                                    </div>
                                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-emerald-400 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 animate-bounce-in">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-xl font-bold text-white mb-4 text-center group-hover:text-blue-300 transition-colors duration-300">Equipment Requests</h3>
                                <p class="text-white/70 text-center leading-relaxed group-hover:text-white/80 transition-colors duration-300">
                                    Browse, request & track equipment with real-time status updates and automated notifications
                                </p>
                            </div>

                            <!-- Lab Reservations -->
                            <div class="group glass-card rounded-2xl p-8 hover:bg-white/10 transition-all duration-500 hover:transform hover:-translate-y-2 hover:shadow-2xl border border-white/10 animate-fade-in-scale feature-card-delay-2">
                                <div class="relative mb-6">
                                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center mx-auto shadow-xl group-hover:shadow-indigo-500/25 transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                                        <svg class="w-8 h-8 text-white transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-emerald-400 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 animate-bounce-in">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-xl font-bold text-white mb-4 text-center group-hover:text-indigo-300 transition-colors duration-300">Lab Reservations</h3>
                                <p class="text-white/70 text-center leading-relaxed group-hover:text-white/80 transition-colors duration-300">
                                    Reserve laboratories with calendar view, recurring bookings & conflict prevention
                                </p>
                            </div>

                            <!-- Dashboard Access -->
                            <div class="group glass-card rounded-2xl p-8 hover:bg-white/10 transition-all duration-500 hover:transform hover:-translate-y-2 hover:shadow-2xl border border-white/10 animate-fade-in-scale feature-card-delay-3">
                                <div class="relative mb-6">
                                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto shadow-xl group-hover:shadow-purple-500/25 transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                                        <svg class="w-8 h-8 text-white transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                    </div>
                                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-emerald-400 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 animate-bounce-in">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-xl font-bold text-white mb-4 text-center group-hover:text-purple-300 transition-colors duration-300">Personal Dashboard</h3>
                                <p class="text-white/70 text-center leading-relaxed group-hover:text-white/80 transition-colors duration-300">
                                    Manage requests, reservations & view comprehensive activity history
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced CTA Section -->
                    <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-16">
                        <a href="<?php echo e(route('login')); ?>"
                           class="group relative inline-flex items-center px-10 py-5 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white font-bold rounded-2xl shadow-2xl hover:shadow-blue-500/25 btn-hover-lift transform transition-all duration-300 hover:scale-105 animate-pulse-glow overflow-hidden">
                            <!-- Shine effect overlay -->
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent translate-x-[-200%] group-hover:translate-x-[200%] transition-transform duration-700 ease-in-out"></div>
                            <div class="relative z-10 flex items-center">
                                <div class="w-6 h-6 mr-4 bg-white/20 rounded-full flex items-center justify-center group-hover:rotate-180 transition-transform duration-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                </div>
                                <span class="text-lg tracking-wide">Access Platform</span>
                            </div>
                        </a>

                        <a href="<?php echo e(route('register')); ?>"
                           class="group relative inline-flex items-center px-10 py-5 glass-card text-white font-bold rounded-2xl border-2 border-white/30 hover:bg-white/10 hover:border-white/50 btn-hover-lift transform transition-all duration-300 hover:scale-105 overflow-hidden">
                            <!-- Animated border gradient -->
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-2xl opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                            <div class="relative z-10 flex items-center">
                                <div class="w-6 h-6 mr-4 bg-gradient-to-br from-emerald-400 to-blue-400 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    </svg>
                                </div>
                                <span class="text-lg tracking-wide group-hover:text-blue-200 transition-colors duration-300">Create Account</span>
                            </div>
                        </a>
                    </div>

                    <!-- Enhanced Getting Started Steps -->
                    <div class="glass-card rounded-3xl p-8 sm:p-12 max-w-5xl mx-auto border border-white/20 animate-slide-in-up">
                        <div class="text-center mb-12">
                            <h3 class="text-2xl sm:text-3xl font-bold text-white mb-4">Get Started in 3 Simple Steps</h3>
                            <div class="w-20 h-1 bg-gradient-to-r from-emerald-400 via-blue-400 to-purple-400 mx-auto rounded-full"></div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
                            <div class="group text-center transform hover:scale-105 transition-all duration-300">
                                <div class="relative mb-6">
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl flex items-center justify-center mx-auto shadow-xl group-hover:shadow-emerald-500/25 transition-all duration-300 group-hover:rotate-6">
                                        <span class="text-white text-xl sm:text-2xl font-black">1</span>
                                    </div>
                                    <!-- Connection line for larger screens -->
                                    <div class="hidden md:block absolute top-8 sm:top-10 left-full w-8 lg:w-16 h-0.5 bg-gradient-to-r from-emerald-400/50 to-blue-400/50"></div>
                                </div>
                                <h4 class="text-white text-lg sm:text-xl font-bold mb-3 group-hover:text-emerald-300 transition-colors duration-300">Create Account</h4>
                                <p class="text-white/70 text-sm sm:text-base leading-relaxed group-hover:text-white/80 transition-colors duration-300">
                                    Sign up with your academic email and get instant access to all resources
                                </p>
                            </div>
                            
                            <div class="group text-center transform hover:scale-105 transition-all duration-300">
                                <div class="relative mb-6">
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center mx-auto shadow-xl group-hover:shadow-blue-500/25 transition-all duration-300 group-hover:rotate-6">
                                        <span class="text-white text-xl sm:text-2xl font-black">2</span>
                                    </div>
                                    <!-- Connection line for larger screens -->
                                    <div class="hidden md:block absolute top-8 sm:top-10 left-full w-8 lg:w-16 h-0.5 bg-gradient-to-r from-blue-400/50 to-purple-400/50"></div>
                                </div>
                                <h4 class="text-white text-lg sm:text-xl font-bold mb-3 group-hover:text-blue-300 transition-colors duration-300">Browse Resources</h4>
                                <p class="text-white/70 text-sm sm:text-base leading-relaxed group-hover:text-white/80 transition-colors duration-300">
                                    Explore available equipment and laboratory spaces with real-time availability
                                </p>
                            </div>
                            
                            <div class="group text-center transform hover:scale-105 transition-all duration-300">
                                <div class="relative mb-6">
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto shadow-xl group-hover:shadow-purple-500/25 transition-all duration-300 group-hover:rotate-6">
                                        <span class="text-white text-xl sm:text-2xl font-black">3</span>
                                    </div>
                                </div>
                                <h4 class="text-white text-lg sm:text-xl font-bold mb-3 group-hover:text-purple-300 transition-colors duration-300">Make Requests</h4>
                                <p class="text-white/70 text-sm sm:text-base leading-relaxed group-hover:text-white/80 transition-colors duration-300">
                                    Submit equipment requests and lab reservations with automated tracking
                                </p>
                            </div>
                        </div>
                        
                        <!-- Progress indicator -->
                        <div class="flex justify-center mt-12">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-emerald-400 rounded-full"></div>
                                <div class="w-8 h-0.5 bg-gradient-to-r from-emerald-400 to-blue-400"></div>
                                <div class="w-3 h-3 bg-blue-400 rounded-full"></div>
                                <div class="w-8 h-0.5 bg-gradient-to-r from-blue-400 to-purple-400"></div>
                                <div class="w-3 h-3 bg-purple-400 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wapp-AssetLab\resources\views/welcome.blade.php ENDPATH**/ ?>