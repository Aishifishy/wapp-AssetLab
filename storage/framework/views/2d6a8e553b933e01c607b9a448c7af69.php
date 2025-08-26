<nav class="bg-white shadow-custom">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <a href="<?php echo e(url('/')); ?>" class="text-2xl font-bold header-gradient">ResourEase</a>
                </div>

                <?php if(auth()->guard()->check()): ?>
                    <!-- Navigation Links for Authenticated Users -->
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <a href="<?php echo e(route('dashboard')); ?>" 
                           class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'text-accent-dark' : 'text-primary'); ?>">
                            Dashboard
                        </a>
                        <?php if(auth()->guard('admin')->check()): ?>
                            <a href="<?php echo e(route('admin.equipment.index')); ?>" 
                               class="nav-link <?php echo e(request()->routeIs('admin.equipment.*') ? 'text-accent-dark' : 'text-primary'); ?>">
                                Equipment
                            </a>
                            <a href="<?php echo e(route('admin.requests.index')); ?>" 
                               class="nav-link <?php echo e(request()->routeIs('admin.requests.*') ? 'text-accent-dark' : 'text-primary'); ?>">
                                Requests
                            </a>
                            <a href="<?php echo e(route('admin.users.index')); ?>" 
                               class="nav-link <?php echo e(request()->routeIs('admin.users.*') ? 'text-accent-dark' : 'text-primary'); ?>">
                                Users
                            </a>                        <?php else: ?>
                                                        <!-- Removed borrowed items and history links -->
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="flex items-center space-x-4">
                <?php if(Route::has('login')): ?>
                    <div class="flex items-center space-x-4">
                        <?php if(auth()->guard()->check()): ?>
                            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                <button @click="open = !open" 
                                        class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                                    <span><?php echo e(Auth::user()->name); ?></span>
                                    <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>                                <div x-show="open" 
                                     class="header-dropdown-menu"
                                     x-cloak>
                                    <a href="<?php echo e(route('profile.edit')); ?>" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Profile
                                    </a>
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" 
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav> <?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views/layouts/header.blade.php ENDPATH**/ ?>