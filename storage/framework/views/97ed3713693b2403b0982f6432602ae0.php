<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'ResourEase')); ?> - <?php echo $__env->yieldContent('title'); ?></title>

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    
    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>
    
    <!-- Bootstrap Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Page Styles -->
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="font-sans antialiased">
    <div class=" bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="<?php echo e(route('dashboard')); ?>" class="text-xl sm:text-2xl font-bold text-gray-900">
                                <span class="hidden sm:inline">ResourEase</span>
                                <span class="sm:hidden">RE</span>
                            </a>
                        </div>
                        
                        <!-- Navigation Links - Hidden on mobile -->
                        <div class="hidden md:flex md:space-x-8 md:-my-px md:ml-10">
                            <a href="<?php echo e(route('dashboard')); ?>" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?> text-sm font-medium">
                                <i class="fas fa-tachometer-alt mr-2 md:hidden"></i>
                                Dashboard
                            </a>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden flex items-center">
                        <button type="button" class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- User Menu - Desktop -->
                    <div class="hidden md:flex md:items-center md:ml-6">
                        <div class="dropdown ml-3 relative">
                            <a href="#" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="hidden lg:block"><?php echo e(Auth::user()->name); ?></div>
                                <i class="fas fa-user lg:hidden"></i>
                            </a>
                            
                            <ul class="dropdown-menu shadow-lg mt-2" aria-labelledby="dropdownUser1">
                                <li>
                                    <a href="<?php echo e(route('profile.edit')); ?>" class="dropdown-item">
                                        <i class="fas fa-user me-2"></i>Profile
                                    </a>
                                </li>
                                <li>
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu -->
                <div class="mobile-menu hidden md:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <a href="<?php echo e(route('dashboard')); ?>" 
                           class="block pl-3 pr-4 py-2 border-l-4 <?php echo e(request()->routeIs('dashboard') ? 'border-blue-500 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300'); ?> text-base font-medium">
                            <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                        </a>
                    </div>
                    <div class="pt-4 pb-3 border-t border-gray-200">
                        <div class="flex items-center px-4">
                            <div class="flex-shrink-0">
                                <i class="fas fa-user h-10 w-10 rounded-full bg-gray-300 p-2 text-gray-600"></i>
                            </div>
                            <div class="ml-3">
                                <div class="text-base font-medium text-gray-800"><?php echo e(Auth::user()->name); ?></div>
                                <div class="text-sm font-medium text-gray-500"><?php echo e(Auth::user()->email); ?></div>
                            </div>
                        </div>
                        <div class="mt-3 space-y-1">
                            <a href="<?php echo e(route('profile.edit')); ?>" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                                <i class="fas fa-user mr-3"></i>Profile
                            </a>
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-3"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>        <!-- Page Heading -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                    <?php echo $__env->yieldContent('header'); ?>
                </h2>
            </div>
        </header>
        
        <!-- Page Content -->
        <main>
            <div class="py-4">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <?php if (isset($component)) { $__componentOriginalcca61bfded94b5a7635453a4dc55dd1d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcca61bfded94b5a7635453a4dc55dd1d = $attributes; } ?>
<?php $component = App\View\Components\FlashMessages::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flash-messages'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\FlashMessages::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcca61bfded94b5a7635453a4dc55dd1d)): ?>
<?php $attributes = $__attributesOriginalcca61bfded94b5a7635453a4dc55dd1d; ?>
<?php unset($__attributesOriginalcca61bfded94b5a7635453a4dc55dd1d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcca61bfded94b5a7635453a4dc55dd1d)): ?>
<?php $component = $__componentOriginalcca61bfded94b5a7635453a4dc55dd1d; ?>
<?php unset($__componentOriginalcca61bfded94b5a7635453a4dc55dd1d); ?>
<?php endif; ?>
                    <?php echo $__env->yieldContent('content'); ?>
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
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views/layouts/ruser.blade.php ENDPATH**/ ?>