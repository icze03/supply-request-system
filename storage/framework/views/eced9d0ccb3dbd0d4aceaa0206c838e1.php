

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Request Details</h1>
                <p class="mt-1 text-sm text-gray-600">View your supply request information</p>
            </div>
            <a href="<?php echo e(route('employee.requests.index')); ?>" class="text-sm text-indigo-600 hover:text-indigo-900">
                ← Back to Requests
            </a>
        </div>
    </div>

    <!-- Request Card -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Request Information</h2>
                    <p class="text-sm text-gray-600">Submitted <?php echo e($request->created_at->format('F d, Y')); ?></p>
                </div>
                <span class="px-3 py-1 text-sm font-semibold rounded-full <?php echo e($request->getStatusBadgeColor()); ?>">
                    <?php echo e($request->getStatusLabel()); ?>

                </span>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Serial Number (if released) -->
            <?php if($request->serial_number): ?>
                <div class="bg-indigo-50 border-l-4 border-indigo-600 p-4">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-indigo-900">Serial Number</p>
                            <p class="text-xl font-mono font-bold text-indigo-900"><?php echo e($request->serial_number); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Request Type & Purpose -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Request Type</h3>
                    <span class="px-3 py-1 text-sm font-semibold rounded <?php echo e($request->request_type === 'special' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'); ?>">
                        <?php echo e(ucfirst($request->request_type)); ?>

                    </span>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Department</h3>
                    <p class="text-sm text-gray-900"><?php echo e($request->department->name); ?> (<?php echo e($request->department->code); ?>)</p>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">Purpose</h3>
                <p class="text-sm text-gray-900"><?php echo e($request->purpose); ?></p>
            </div>

            <!-- Items -->
            <?php if($request->request_type === 'standard'): ?>
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Requested Items (<?php echo e($request->items->count()); ?>)</h3>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Code</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Name</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__currentLoopData = $request->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-mono text-gray-900"><?php echo e($item->item_code); ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo e($item->item_name); ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-900 text-right font-semibold"><?php echo e($item->quantity); ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-500"><?php echo e($item->supply->unit ?? 'N/A'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Special Item Description</h3>
                    <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                        <p class="text-sm text-gray-900"><?php echo e($request->special_item_description); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Timeline -->
            <div>
                <h3 class="text-sm font-medium text-gray-700 mb-3">Request Timeline</h3>
                <div class="flow-root">
                    <ul class="-mb-8">
                        <!-- Submitted -->
                        <li>
                            <div class="relative pb-8">
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-900 font-medium">Request Submitted</p>
                                            <p class="text-xs text-gray-500">By <?php echo e($request->user->name); ?></p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            <?php echo e($request->created_at->format('M d, Y h:i A')); ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <!-- Manager Approval -->
                        <?php if($request->manager_approved_at): ?>
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-900 font-medium">Manager Approved</p>
                                                <p class="text-xs text-gray-500">By <?php echo e($request->managerApprover->name ?? 'N/A'); ?></p>
                                                <?php if($request->manager_notes): ?>
                                                    <p class="text-xs text-gray-600 mt-1 italic">"<?php echo e($request->manager_notes); ?>"</p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <?php echo e($request->manager_approved_at->format('M d, Y h:i A')); ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endif; ?>

                        <?php if($request->manager_rejected_at): ?>
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-900 font-medium">Manager Rejected</p>
                                                <p class="text-xs text-gray-500">By <?php echo e($request->managerApprover->name ?? 'N/A'); ?></p>
                                                <?php if($request->manager_notes): ?>
                                                    <p class="text-xs text-red-600 mt-1 italic">"<?php echo e($request->manager_notes); ?>"</p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <?php echo e($request->manager_rejected_at->format('M d, Y h:i A')); ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endif; ?>

                        <!-- Admin Release -->
                        <?php if($request->admin_released_at): ?>
                            <li>
                                <div class="relative">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-900 font-medium">Request Released</p>
                                                <p class="text-xs text-gray-500">By <?php echo e($request->adminReleaser->name ?? 'N/A'); ?></p>
                                                <?php if($request->admin_notes): ?>
                                                    <p class="text-xs text-gray-600 mt-1 italic">"<?php echo e($request->admin_notes); ?>"</p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <?php echo e($request->admin_released_at->format('M d, Y h:i A')); ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endif; ?>

                        <?php if($request->admin_rejected_at): ?>
                            <li>
                                <div class="relative">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-900 font-medium">Admin Rejected</p>
                                                <p class="text-xs text-gray-500">By <?php echo e($request->adminReleaser->name ?? 'N/A'); ?></p>
                                                <?php if($request->admin_notes): ?>
                                                    <p class="text-xs text-red-600 mt-1 italic">"<?php echo e($request->admin_notes); ?>"</p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <?php echo e($request->admin_rejected_at->format('M d, Y h:i A')); ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <?php if($request->isPending()): ?>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                <button onclick="cancelRequest()" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                    Cancel Request
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
async function cancelRequest() {
    if (!confirm('Are you sure you want to cancel this request?')) {
        return;
    }
    
    try {
        const response = await fetch('/employee/requests/<?php echo e($request->id); ?>', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.location.href = '<?php echo e(route("employee.requests.index")); ?>';
        } else {
            alert('Failed to cancel request');
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\supply-request-system\resources\views/employee/requests/show.blade.php ENDPATH**/ ?>