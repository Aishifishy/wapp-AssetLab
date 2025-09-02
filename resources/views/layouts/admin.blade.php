<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') - {{ config('app.name', 'ResourEase') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/common-utilities.js'])
    @stack('styles')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <style>
        /* Mobile responsive styles for admin */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 1050;
                width: 280px;
            }
            .admin-sidebar.show {
                transform: translateX(0);
            }
            .admin-content {
                margin-left: 0 !important;
                width: 100%;
            }
            .mobile-admin-header {
                display: block;
            }
        }
        @media (min-width: 769px) {
            .mobile-admin-header {
                display: none;
            }
            /* Create balanced spacing - remove sidebar right padding and restore equal content padding */
            .admin-sidebar {
                padding-right: 0 !important;
                width: 280px !important;
                flex-shrink: 0 !important;
            }
            .admin-content {
                margin-left: 0 !important;
                padding-left: 0 !important;
            }
            .admin-content .p-4 {
                padding-left: 1.5rem !important; /* Match Bootstrap p-4 spacing (top, right, bottom) */
            }
            /* Remove any flexbox gaps */
            .d-flex {
                gap: 0 !important;
            }
        }
        .sidebar-overlay {
            display: none;
        }
        @media (max-width: 768px) {
            .sidebar-overlay.show {
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1040;
            }
        }
    </style>
</head>
<body class="bg-light">
    <!-- Mobile Header -->
    <div class="mobile-admin-header bg-dark text-white p-3 d-flex justify-content-between align-items-center d-md-none">
        <button class="btn btn-outline-light mobile-menu-toggle" type="button">
            <i class="fas fa-bars"></i>
        </button>
        <h5 class="mb-0">ResourEase Admin</h5>
        <div class="dropdown">
            <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-user"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark admin-sidebar" id="adminSidebar">
            <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                <i class="fas fa-laptop-code me-2"></i>
                <span class="fs-4">ResourEase</span>
            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link admin-nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.academic.index') }}" class="nav-link admin-nav-link text-white {{ request()->routeIs('admin.academic.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Academic Calendar
                    </a>
                </li>
                <!-- Com Lab Section Header -->
                <li class="nav-item mt-2">
                    <div class="nav-link admin-nav-link text-white mb-2">
                        <i class="fas fa-desktop me-2"></i>
                        <span class="text-uppercase fw-bold admin-section-header">Computer Laboratory</span>
                    </div>
                    <!-- Com Lab Sub-items -->
                    <ul class="nav nav-pills flex-column border-start admin-border-start border-secondary ms-3">
                        <li class="nav-item">
                            <a href="{{ route('admin.laboratory.index') }}" class="nav-link admin-nav-link text-white {{ request()->routeIs('admin.laboratory.index') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-list me-2"></i>
                            Laboratories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.laboratory.reservations') }}" class="nav-link admin-nav-link text-white {{ request()->routeIs('admin.laboratory.reservations') || request()->routeIs('admin.laboratory.schedule-overrides') || request()->routeIs('admin.laboratory.create-override') ? 'active' : '' }}">
                            <i class="fas fa-calendar-check me-2"></i>
                            Reservations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.comlab.calendar') }}" class="nav-link admin-nav-link text-white {{ request()->routeIs('admin.comlab.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-week me-2"></i>
                            Lab Schedule
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Equipment Section Header -->
                <li class="nav-item mt-2">
                    <div class="nav-link admin-nav-link text-white mb-2">
                        <i class="fas fa-tools me-2"></i>
                        <span class="text-uppercase fw-bold admin-section-header">Equipment</span>
                    </div>
                    <!-- Equipment Sub-items -->
                    <ul class="nav nav-pills flex-column border-start admin-border-start border-secondary ms-3">
                        <li class="nav-item">
                            <a href="{{ route('admin.equipment.index') }}" 
                               class="nav-link admin-nav-link text-white ps-3 {{ request()->routeIs('admin.equipment.index') || request()->routeIs('admin.equipment.create') || request()->routeIs('admin.equipment.edit') ? 'active' : '' }}">
                                <i class="fas fa-cog me-2"></i>
                                Manage Equipment
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.equipment.borrow-requests') }}" 
                               class="nav-link admin-nav-link text-white ps-3 {{ request()->routeIs('admin.equipment.borrow-requests') ? 'active' : '' }}">
                                <i class="fas fa-clipboard-list me-2"></i>
                                Borrowing
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.equipment.categories.index') }}" 
                               class="nav-link admin-nav-link text-white ps-3 {{ request()->routeIs('admin.equipment.categories.*') ? 'active' : '' }}">
                                <i class="fas fa-tags me-2"></i>
                                Categories
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- User Management -->
                <li class="nav-item mt-2">
                    <div class="nav-link admin-nav-link text-white mb-2">
                        <i class="fas fa-users me-2"></i>
                        <span class="text-uppercase fw-bold admin-section-header">Users</span>
                    </div>
                    <!-- User Management Sub-items -->
                    <ul class="nav nav-pills flex-column border-start admin-border-start border-secondary ms-3">
                        <li class="nav-item">
                            <a href="{{ route('admin.users.index') }}" class="nav-link admin-nav-link text-white {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <i class="fas fa-users me-2"></i>
                                User Management
                            </a>
                        </li>
                        @if(Auth::guard('admin')->user() && Auth::guard('admin')->user()->isSuperAdmin())
                        <li class="nav-item">
                            <a href="{{ route('admin.super-admin.index') }}" class="nav-link admin-nav-link text-white {{ request()->routeIs('admin.super-admin.*') ? 'active' : '' }}">
                                <i class="fas fa-crown me-2"></i>
                                Super Admin
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
            </ul>
            <hr>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle fa-2x me-2"></i>
                    <strong>{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                    <li>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt me-2"></i>Sign out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex-grow-1 admin-content">
            <div class="p-4">
            <!-- Page Title -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h1 class="h2 mb-2 mb-md-0">@yield('page-title', 'Dashboard')</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        @yield('breadcrumbs')
                    </ol>
                </nav>
            </div>

            <!-- Flash Messages -->
            <!-- @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif -->

            <!-- Main Content -->
            @yield('content')
            </div>
        </div>
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle
            const mobileToggle = document.querySelector('.mobile-menu-toggle');
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (mobileToggle) {
                mobileToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    overlay.classList.toggle('show');
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                });
            }

            // Keep equipment submenu open when on equipment pages
            const currentPath = window.location.pathname;
            
            // Check if we're on an equipment-related page
            if (currentPath.includes('/admin/equipment')) {
                const equipmentSubmenu = document.getElementById('equipmentSubmenu');
                if (equipmentSubmenu) {
                    equipmentSubmenu.classList.add('show');
                }
            }

            // Prevent submenu from closing when clicking submenu items
            const submenuLinks = document.querySelectorAll('#equipmentSubmenu .nav-link');
            submenuLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.stopPropagation(); // Prevent event from bubbling up
                });
            });

            // Close mobile menu when clicking nav links on mobile
            const navLinks = document.querySelectorAll('.admin-sidebar .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('show');
                        overlay.classList.remove('show');
                    }
                });
            });
        });
    </script>
</body>
</html>