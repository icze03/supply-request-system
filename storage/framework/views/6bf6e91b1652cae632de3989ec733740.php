<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'Laravel')); ?></title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
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

                        <?php $user = auth()->user(); ?>

                        
                        <a href="<?php echo e(route('dashboard')); ?>"
                           class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('dashboard') || request()->routeIs('*.dashboard') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                            Dashboard
                        </a>

                        
                        <?php if($user->isSuperAdmin()): ?>
                        <a href="<?php echo e(route('super_admin.role_permissions.index')); ?>"
                        class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('super_admin.role_permissions*') ? 'border-purple-500' : 'border-transparent'); ?> text-sm font-medium leading-5 text-purple-700 hover:border-purple-300 transition duration-150 ease-in-out">
                        Role Permissions
                        </a>
                        <a href="<?php echo e(route('admin.audit-logs.index')); ?>"
                        class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('admin.audit-logs*') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                            Audit Trail

                        </a>
                        <a href="<?php echo e(route('admin.users.index')); ?>"
                        class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('admin.users.*') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                            Users
                        </a>
                        <a href="<?php echo e(route('admin.departments.index')); ?>"
                        class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('admin.departments.*') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                            Departments
                        </a>
                        <?php endif; ?>

                        
                        <?php if($user->isEmployee()): ?>
                            <?php if($user->hasPermission('catalog')): ?>
                                <a href="<?php echo e(route('employee.catalog')); ?>"
                                   class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('employee.catalog') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                                    Catalog
                                </a>
                            <?php endif; ?>
                            <?php if($user->hasPermission('my_requests')): ?>
                                <a href="<?php echo e(route('employee.requests.index')); ?>"
                                   class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('employee.requests.*') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                                    My Requests
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        
                        <?php if($user->isManager()): ?>
                            <?php if($user->hasPermission('approvals')): ?>
                                <a href="<?php echo e(route('manager.approvals.index')); ?>"
                                   class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('manager.*') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                                    Approvals
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                        
<?php if($user->isHrManager()): ?>
    <?php if($user->hasPermission('users')): ?>
        <a href="<?php echo e(route('admin.users.index')); ?>"
           class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('admin.users.*') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
            Users
        </a>
    <?php endif; ?>
    <?php if($user->hasPermission('departments')): ?>
        <a href="<?php echo e(route('admin.departments.index')); ?>"
           class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('admin.departments.*') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
            Departments
        </a>
    <?php endif; ?>
    <?php if($user->hasPermission('audit_trail')): ?>
        <a href="<?php echo e(route('admin.audit-logs.index')); ?>"
           class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('admin.audit-logs*') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
            Audit Trail
        </a>
    <?php endif; ?>
<?php endif; ?>

                        
                        <?php if($user->isAdmin()): ?>
                            <?php if($user->hasPermission('releases')): ?>
                                <a href="<?php echo e(route('admin.releases.index')); ?>"
                                   class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('admin.releases.*') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                                    Releases
                                </a>
                            <?php endif; ?>
                            <?php if($user->hasPermission('supplies')): ?>
                                <a href="<?php echo e(route('admin.supplies.index')); ?>"
                                   class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('admin.supplies.*') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                                    Supplies
                                </a>
                            <?php endif; ?>
                            <?php if($user->hasPermission('low_stock')): ?>
                                <a href="<?php echo e(route('admin.low-stock.index')); ?>"
                                   class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('admin.low-stock.*') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                                    Low Stock
                                </a>
                            <?php endif; ?>
                            <?php if($user->hasPermission('audit_trail')): ?>
                                <a href="<?php echo e(route('admin.audit-logs.index')); ?>"
                                   class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('admin.audit-logs*') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                                    Audit Trail
                                </a>
                            <?php endif; ?>
                            <?php if($user->hasPermission('users')): ?>
                                <a href="<?php echo e(route('admin.users.index')); ?>"
                                   class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('admin.users.*') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                                    Users
                                </a>
                            <?php endif; ?>
                            <?php if($user->hasPermission('departments')): ?>
                                <a href="<?php echo e(route('admin.departments.index')); ?>"
                                   class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('admin.departments.*') ? 'border-indigo-400' : 'border-transparent'); ?> text-sm font-medium leading-5 text-gray-900 hover:border-gray-300 transition duration-150 ease-in-out">
                                    Departments
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                    </div>
                </div>

                <!-- User Info & Logout -->
                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <div class="ml-3 relative">
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <div class="font-medium text-sm text-gray-800"><?php echo e(auth()->user()->name); ?></div>
                                <div class="font-medium text-xs text-gray-500">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo e(auth()->user()->getRoleBadgeColor()); ?>">
                                        <?php echo e(auth()->user()->getRoleLabel()); ?>

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

    <!-- Flash Messages -->
    <?php if(session('success')): ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <?php echo e(session('success')); ?>

            </div>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <?php echo e(session('error')); ?>

            </div>
        </div>
    <?php endif; ?>

    <!-- Page Content -->
    <main class="py-6">
        <?php echo $__env->yieldContent('content'); ?>
    </main>
</div>

<?php echo $__env->yieldPushContent('scripts'); ?>

<!-- Footer -->
<footer class="bg-gradient-to-r from-gray-800 via-gray-900 to-gray-800 text-white py-3 mt-auto border-t border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
            <div class="text-center md:text-left">
                <h3 class="text-sm font-bold mb-0.5 text-blue-400">Supply Request Management System</h3>
                <p class="text-xs text-gray-400">supply tracking and approval workflow</p>
            </div>
            <div class="text-center">
                <h4 class="text-xs font-semibold mb-1 text-gray-300">Built With</h4>
                <div class="flex flex-wrap justify-center gap-1">
                    <span class="px-1.5 py-0.5 bg-red-600 bg-opacity-20 border border-red-500 rounded text-xs font-mono">Laravel 12</span>
                    <span class="px-1.5 py-0.5 bg-blue-600 bg-opacity-20 border border-blue-500 rounded text-xs font-mono">PHP 8.3</span>
                    <span class="px-1.5 py-0.5 bg-cyan-600 bg-opacity-20 border border-cyan-500 rounded text-xs font-mono">Tailwind CSS</span>
                    <span class="px-1.5 py-0.5 bg-orange-600 bg-opacity-20 border border-orange-500 rounded text-xs font-mono">MySQL</span>
                </div>
            </div>
            <div class="text-center md:text-right">
                <h4 class="text-xs font-semibold mb-1 text-gray-300">Developed By</h4>
                <p class="text-sm font-bold text-blue-400">Klein Imperio</p>
            </div>
        </div>
        <div class="border-t border-gray-700 pt-2">
            <div class="flex flex-col md:flex-row justify-between items-center text-xs text-gray-400">
                <p>© <?php echo e(date('Y')); ?> Supply Requisition System.</p>
                <p class="mt-0.5 md:mt-0">Version 1.0.0 | Last Updated: <?php echo e(date('F Y')); ?></p>
            </div>
        </div>
    </div>
</footer>
</body>
</html>
<?php /**PATH C:\laragon\www\supply-request-system\resources\views/layouts/app.blade.php ENDPATH**/ ?>