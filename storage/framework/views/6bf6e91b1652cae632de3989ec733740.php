<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Laravel')); ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="<?php echo e(route('dashboard')); ?>" class="text-xl font-bold text-gray-800">
                                Supply Request System
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="<?php echo e(route('dashboard')); ?>" class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('dashboard') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                Dashboard
                            </a>

                            <?php if(auth()->user()->isEmployee()): ?>
                                <a href="<?php echo e(route('employee.catalog')); ?>" class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('employee.catalog') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                    Catalog
                                </a>
                                <a href="<?php echo e(route('employee.requests.index')); ?>" class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('employee.requests.*') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                    My Requests
                                </a>
                            <?php endif; ?>
                            
                                <?php if(auth()->user()->isAdmin()): ?>
                            <a href="<?php echo e(route('admin.users.index')); ?>" class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('admin.users.*') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                    Users
                                </a>
                            <a href="<?php echo e(route('admin.supplies.index')); ?>" class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('admin.supplies.*') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                    Supplies
                                </a>
                            <!-- ... rest of admin links -->
                            <?php if(auth()->user()->isAdmin()): ?>
                            
                            <a href="<?php echo e(route('admin.releases.index')); ?>" class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('admin.releases.*') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                Releases
                                </a>

                            <a href="<?php echo e(route('admin.low-stock.index')); ?>" class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('admin.low-stock.*') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900">
                                Low Stock
                                </a>    
                            <?php endif; ?>
                            <?php endif; ?>

                            <?php if(auth()->user()->isManager()): ?>
                                <a href="<?php echo e(route('manager.approvals.index')); ?>" class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('manager.*') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                    Approvals
                                </a>
                            <?php endif; ?>
                                
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="ml-3 relative">
                            <div class="flex items-center space-x-3">
                                <div class="text-right">
                                    <div class="font-medium text-sm text-gray-800"><?php echo e(auth()->user()->name); ?></div>
                                    <div class="font-medium text-xs text-gray-500">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo e(auth()->user()->getRoleBadgeColor()); ?>">
                                            <?php echo e(ucfirst(auth()->user()->role)); ?>

                                        </span>
                                        <?php if(auth()->user()->department): ?>
                                            <span class="ml-1"><?php echo e(auth()->user()->department->code); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="text-sm text-gray-700 hover:text-gray-900">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-6">
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\laragon\www\supply-request-system\resources\views/layouts/app.blade.php ENDPATH**/ ?>