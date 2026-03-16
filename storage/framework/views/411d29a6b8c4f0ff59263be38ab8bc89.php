

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-purple-100">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Super Admin Dashboard</h1>
                <p class="text-sm text-gray-500 mt-0.5">Full system control &amp; permission management</p>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <!-- Total Users -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Users</span>
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900"><?php echo e($totalUsers); ?></p>
        </div>

        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleKey => $roleLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider"><?php echo e($roleLabel); ?>s</span>
                <span class="text-xs font-medium px-2 py-1 rounded-full
                    <?php echo e($roleKey === 'admin' ? 'bg-red-100 text-red-700' : ($roleKey === 'manager' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700')); ?>">
                    <?php echo e($rolePermissionCounts[$roleKey]); ?> perms
                </span>
            </div>
            <p class="text-3xl font-bold text-gray-900"><?php echo e($roleStats[$roleKey] ?? 0); ?></p>
            <a href="<?php echo e(route('super_admin.role_permissions.index', ['role' => $roleKey])); ?>"
               class="text-xs text-indigo-600 hover:text-indigo-800 mt-2 inline-block font-medium">
                Configure permissions →
            </a>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Two-column layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-5">Quick Actions</h2>
            <div class="space-y-3">
                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleKey => $roleLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('super_admin.role_permissions.index', ['role' => $roleKey])); ?>"
                   class="flex items-center justify-between p-4 rounded-lg border border-gray-100 hover:border-indigo-200 hover:bg-indigo-50 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center
                            <?php echo e($roleKey === 'admin' ? 'bg-red-100' : ($roleKey === 'manager' ? 'bg-blue-100' : 'bg-green-100')); ?>">
                            <svg class="w-4 h-4 <?php echo e($roleKey === 'admin' ? 'text-red-600' : ($roleKey === 'manager' ? 'text-blue-600' : 'text-green-600')); ?>"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Configure <?php echo e($roleLabel); ?> Permissions</p>
                            <p class="text-xs text-gray-500"><?php echo e($rolePermissionCounts[$roleKey]); ?> of <?php echo e($permissions->count()); ?> pages enabled</p>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <!-- All Permissions Status -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-5">Permission Coverage</h2>
            <div class="space-y-2">
                <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <span class="text-sm font-medium text-gray-700"><?php echo e($perm->name); ?></span>
                    <div class="flex items-center gap-1">
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleKey => $roleLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $hasIt = \App\Models\RolePermission::where('role', $roleKey)
                                    ->where('permission_id', $perm->id)->exists();
                            ?>
                            <span title="<?php echo e($roleLabel); ?>"
                                  class="inline-flex items-center justify-center w-6 h-6 rounded text-xs font-bold
                                  <?php echo e($hasIt
                                     ? ($roleKey === 'admin' ? 'bg-red-100 text-red-700' : ($roleKey === 'manager' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'))
                                     : 'bg-gray-100 text-gray-400'); ?>">
                                <?php echo e(strtoupper(substr($roleLabel, 0, 1))); ?>

                            </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <p class="text-xs text-gray-400 mt-3">A = Admin &nbsp; M = Manager &nbsp; E = Employee &nbsp; (colored = has access)</p>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\supply-request-system\resources\views/super_admin/dashboard.blade.php ENDPATH**/ ?>