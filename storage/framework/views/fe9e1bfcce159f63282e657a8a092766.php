

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-purple-100">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Role Permissions</h1>
                <p class="text-sm text-gray-500 mt-0.5">System → Configure which pages each role can access</p>
            </div>
        </div>
    </div>

    <!-- Role Tab Bar -->
    <div class="flex gap-2 mb-6 border-b border-gray-200">
        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleKey => $roleLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('super_admin.role_permissions.index', ['role' => $roleKey])); ?>"
           class="px-5 py-3 text-sm font-semibold rounded-t-lg border-b-2 transition
           <?php echo e($selectedRole === $roleKey
               ? 'border-indigo-500 text-indigo-700 bg-indigo-50'
               : 'border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50'); ?>">
            <?php echo e($roleLabel); ?>

        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Permission Form -->
    <form method="POST" action="<?php echo e(route('super_admin.role_permissions.update')); ?>" id="permForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="role" value="<?php echo e($selectedRole); ?>">

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <!-- Form Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        Permissions for:
                        <span class="ml-2 px-3 py-1 rounded-full text-sm font-bold
                            <?php echo e($selectedRole === 'admin' ? 'bg-red-100 text-red-700'
                             : ($selectedRole === 'manager' ? 'bg-blue-100 text-blue-700'
                             : 'bg-green-100 text-green-700')); ?>">
                            <?php echo e($roles[$selectedRole]); ?>

                        </span>
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Check the pages this role is allowed to access. Changes take effect immediately.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="toggleAll(true)"
                        class="text-xs text-indigo-600 hover:text-indigo-800 font-medium px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition">
                        Select All
                    </button>
                    <button type="button" onclick="toggleAll(false)"
                        class="text-xs text-gray-500 hover:text-gray-700 font-medium px-3 py-1.5 rounded-lg hover:bg-gray-50 transition">
                        Clear All
                    </button>
                </div>
            </div>

            <!-- Permission Checkboxes -->
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $checked = in_array($perm->id, $assignedIds); ?>
                    <label class="perm-card flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition
                                  <?php echo e($checked ? 'border-indigo-300 bg-indigo-50' : 'border-gray-200 bg-white hover:border-gray-300'); ?>"
                           id="card-<?php echo e($perm->id); ?>">

                        <input type="checkbox"
                               name="permission_ids[]"
                               value="<?php echo e($perm->id); ?>"
                               class="perm-checkbox mt-0.5 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                               <?php echo e($checked ? 'checked' : ''); ?>

                               onchange="updateCard(this)">

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800"><?php echo e($perm->name); ?></p>
                            <p class="text-xs text-gray-500 mt-0.5 truncate font-mono"><?php echo e($perm->url_prefix ?? '/'); ?></p>
                        </div>

                        <!-- Checkmark badge -->
                        <div class="perm-badge shrink-0 w-5 h-5 rounded-full flex items-center justify-center
                                    <?php echo e($checked ? 'bg-indigo-500' : 'bg-gray-200'); ?>"
                             id="badge-<?php echo e($perm->id); ?>">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <!-- Footer with Save -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <p class="text-xs text-gray-500">
                    <span id="checkedCount"><?php echo e(count($assignedIds)); ?></span> of <?php echo e($permissions->count()); ?> permissions enabled
                </p>
                <div class="flex items-center gap-3">
                    <a href="<?php echo e(route('super_admin.dashboard')); ?>"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Back to Dashboard
                    </a>
                    <button type="submit"
                            class="px-6 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 active:scale-95 transition shadow-sm">
                        Save Permissions
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Info box -->
    <div class="mt-6 bg-amber-50 border border-amber-200 rounded-xl p-4 flex gap-3">
        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm text-amber-800">
            <p class="font-semibold mb-1">How permissions work</p>
            <ul class="list-disc list-inside space-y-1 text-xs text-amber-700">
                <li>Unchecked pages are hidden from the navigation bar for that role.</li>
                <li>If a user manually visits a restricted URL, they receive a <strong>403 Unauthorized</strong> error.</li>
                <li>The <strong>Super Admin</strong> role always has full access and cannot be restricted here.</li>
                <li>Changes take effect immediately — no server restart needed.</li>
            </ul>
        </div>
    </div>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
function updateCard(checkbox) {
    const id    = checkbox.value;
    const card  = document.getElementById('card-' + id);
    const badge = document.getElementById('badge-' + id);

    if (checkbox.checked) {
        card.classList.add('border-indigo-300', 'bg-indigo-50');
        card.classList.remove('border-gray-200', 'bg-white');
        badge.classList.add('bg-indigo-500');
        badge.classList.remove('bg-gray-200');
    } else {
        card.classList.remove('border-indigo-300', 'bg-indigo-50');
        card.classList.add('border-gray-200', 'bg-white');
        badge.classList.remove('bg-indigo-500');
        badge.classList.add('bg-gray-200');
    }

    updateCount();
}

function toggleAll(state) {
    document.querySelectorAll('.perm-checkbox').forEach(cb => {
        cb.checked = state;
        updateCard(cb);
    });
}

function updateCount() {
    const total   = document.querySelectorAll('.perm-checkbox').length;
    const checked = document.querySelectorAll('.perm-checkbox:checked').length;
    document.getElementById('checkedCount').textContent = checked;
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\supply-request-system\resources\views/super_admin/role_permissions.blade.php ENDPATH**/ ?>