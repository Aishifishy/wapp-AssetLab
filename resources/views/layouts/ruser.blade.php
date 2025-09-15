<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AssetLab') }} - @yield('title')</title>

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>
    
    <!-- Bootstrap Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Modern UI Styles -->
    <style>
        @keyframes gentle-float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-4px); }
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 10px rgba(59, 130, 246, 0.2); }
            50% { box-shadow: 0 0 20px rgba(99, 102, 241, 0.4); }
        }
        
        @keyframes slide-down {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .glass-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            border-color: rgba(59, 130, 246, 0.2);
        }
        
        .btn-modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            padding: 12px 24px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            padding: 12px 24px;
        }
        
        .btn-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        

        
        .animate-gentle-float { animation: gentle-float 6s ease-in-out infinite; }
        .animate-pulse-glow { animation: pulse-glow 3s ease-in-out infinite; }
        .animate-slide-down { animation: slide-down 0.3s ease-out forwards; }
        
        .nav-link-modern {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .nav-link-modern::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        

        
        .sidebar-modern {
            background: linear-gradient(180deg, rgba(255,255,255,0.95) 0%, rgba(248,250,252,0.95) 100%);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(226, 232, 240, 0.5);
        }
        
        .content-modern {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: calc(100vh - 128px);
        }
        
        /* Mobile optimizations */
        @media (max-width: 768px) {
            .glass-card {
                backdrop-filter: blur(10px);
            }
            
            .btn-modern {
                padding: 10px 20px;
            }
        }
    </style>
    
    <!-- Page Styles -->
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <!-- Modern Background -->
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 relative overflow-hidden">
        <!-- Subtle background elements -->
        <div class="absolute inset-0">
            <div class="absolute top-20 left-20 w-96 h-96 bg-blue-200/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-20 w-80 h-80 bg-indigo-200/20 rounded-full blur-3xl"></div>
        </div>

        <!-- Modern Navigation -->
        <nav class="glass-nav sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <!-- Enhanced Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="flex items-center group">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="hidden sm:block">
                                    <h1 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                                        AssetLab
                                    </h1>
                                    <div class="text-xs text-gray-500 -mt-1">User Portal</div>
                                </div>
                            </a>
                        </div>
                        
                        <!-- Modern Navigation Links -->
                        <div class="hidden md:flex md:space-x-1 md:ml-10">
                            <a href="{{ route('dashboard') }}" 
                               class="nav-link-modern inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold {{ request()->routeIs('dashboard') ? 'active bg-blue-50 text-blue-700' : 'text-gray-600' }}">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('ruser.equipment.borrow') }}" 
                               class="nav-link-modern inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold {{ request()->routeIs('ruser.equipment.*') ? 'active bg-emerald-50 text-emerald-700' : 'text-gray-600' }}">
                                <i class="fas fa-tools mr-2"></i>
                                Equipment
                            </a>
                            <a href="{{ route('ruser.laboratory.index') }}" 
                               class="nav-link-modern inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold {{ request()->routeIs('ruser.laboratory.*') ? 'active bg-purple-50 text-purple-700' : 'text-gray-600' }}">
                                <i class="fas fa-flask mr-2"></i>
                                Laboratories
                            </a>
                        </div>
                    </div>

                    <!-- Enhanced User Menu -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications Bell -->
                        <button class="relative p-2 text-gray-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-3.5-3.5c-.3-.3-.5-.7-.5-1.1V9c0-4.1-3.4-7.5-7.5-7.5S1 4.9 1 9v3.4c0 .4-.2.8-.5 1.1L-2 17h5m8 0v1c0 2.2-1.8 4-4 4s-4-1.8-4-4v-1m8 0H7"/>
                            </svg>
                            <span class="absolute top-0 right-0 block h-2 w-2 bg-red-400 rounded-full animate-pulse"></span>
                        </button>

                        <!-- Desktop User Menu -->
                        <div class="hidden md:flex md:items-center">
                            <div class="dropdown relative">
                                <button class="flex items-center space-x-3 p-2 rounded-xl hover:bg-blue-50/50 transition-all duration-300 dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <span class="text-white font-semibold text-sm">{{ substr(Auth::user()->name, 0, 2) }}</span>
                                    </div>
                                    <div class="hidden lg:block text-left">
                                        <div class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</div>
                                        <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                
                                <ul class="dropdown-menu shadow-xl mt-2 glass-card rounded-xl border-0 animate-slide-down" aria-labelledby="dropdownUser1">
                                    <li>
                                        <a href="{{ route('profile.edit') }}" class="dropdown-item flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 rounded-lg transition-colors">
                                            <i class="fas fa-user w-5 h-5 mr-3 text-blue-500"></i>
                                            Profile Settings
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1 border-gray-100"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item w-full text-left flex items-center px-4 py-3 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i>
                                                Sign Out
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Mobile menu button -->
                        <div class="md:hidden">
                            <button type="button" class="mobile-menu-button p-2 rounded-xl bg-gray-100/50 hover:bg-gray-200/50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500" aria-controls="mobile-menu" aria-expanded="false">
                                <span class="sr-only">Open main menu</span>
                                <svg class="block h-6 w-6 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <svg class="hidden h-6 w-6 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Mobile menu -->
                <div class="mobile-menu hidden md:hidden glass-card mx-4 my-4 rounded-2xl animate-slide-down">
                    <div class="p-4 space-y-2">
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700 shadow-sm' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                            <i class="fas fa-tachometer-alt w-5 h-5 mr-3"></i>
                            <span class="font-medium">Dashboard</span>
                        </a>
                        <a href="{{ route('ruser.equipment.borrow') }}" 
                           class="flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('ruser.equipment.*') ? 'bg-emerald-100 text-emerald-700 shadow-sm' : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-600' }}">
                            <i class="fas fa-tools w-5 h-5 mr-3"></i>
                            <span class="font-medium">Equipment</span>
                        </a>
                        <a href="{{ route('ruser.laboratory.index') }}" 
                           class="flex items-center px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('ruser.laboratory.*') ? 'bg-purple-100 text-purple-700 shadow-sm' : 'text-gray-600 hover:bg-purple-50 hover:text-purple-600' }}">
                            <i class="fas fa-flask w-5 h-5 mr-3"></i>
                            <span class="font-medium">Laboratories</span>
                        </a>
                    </div>
                    
                    <hr class="border-gray-200 mx-4">
                    
                    <div class="p-4">
                        <div class="flex items-center px-4 py-3 mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                <span class="text-white font-bold">{{ substr(Auth::user()->name, 0, 2) }}</span>
                            </div>
                            <div class="ml-3">
                                <div class="text-base font-semibold text-gray-900">{{ Auth::user()->name }}</div>
                                <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-xl transition-all duration-300">
                                <i class="fas fa-user w-5 h-5 mr-3"></i>
                                <span class="font-medium">Profile Settings</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center w-full px-4 py-3 text-red-600 hover:bg-red-50 rounded-xl transition-all duration-300">
                                    <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i>
                                    <span class="font-medium">Sign Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>        <!-- Enhanced Page Heading -->
        <header class="glass-card relative z-10">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-2 h-8 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full"></div>
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                            @yield('header')
                        </h2>
                    </div>
                    <!-- Breadcrumb could go here -->
                    <div class="hidden md:flex items-center space-x-2 text-sm text-gray-500">
                        <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Home</a>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span class="font-medium text-gray-700">@yield('header')</span>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Enhanced Page Content -->
        <main class="content-modern relative z-10">
            <div class="py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <x-flash-messages />
                    @yield('content')
                </div>
            </div>
        </main>
    </div>
    
    <!-- Mobile Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.querySelector('.mobile-menu-button');
            const mobileMenu = document.querySelector('.mobile-menu');
            
            // Check if mobile menu elements exist before proceeding
            if (!mobileMenuButton || !mobileMenu) {
                return;
            }
            
            const menuIcon = mobileMenuButton.querySelector('svg:first-child');
            const closeIcon = mobileMenuButton.querySelector('svg:last-child');
            
            // Check if menu icons exist
            if (!menuIcon || !closeIcon) {
                return;
            }

            mobileMenuButton.addEventListener('click', function() {
                const isExpanded = mobileMenuButton.getAttribute('aria-expanded') === 'true';
                
                mobileMenuButton.setAttribute('aria-expanded', !isExpanded);
                
                if (isExpanded) {
                    mobileMenu.classList.add('hidden');
                    menuIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                } else {
                    mobileMenu.classList.remove('hidden');
                    menuIcon.classList.add('hidden');
                    closeIcon.classList.remove('hidden');
                }
            });

            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                    mobileMenu.classList.add('hidden');
                    mobileMenuButton.setAttribute('aria-expanded', 'false');
                    menuIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                }
            });
        });
    </script>
    
    <!-- Page Scripts -->
    @stack('scripts')
</body>
</html>
