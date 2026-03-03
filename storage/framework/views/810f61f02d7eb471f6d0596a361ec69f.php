

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Manager Dashboard</h1>
        <p class="mt-1 text-sm text-gray-600"><?php echo e(auth()->user()->department->name); ?> Department</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Approval</dt>
                            <dd class="text-2xl font-bold text-gray-900"><?php echo e($pendingCount); ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Approved Today</dt>
                            <dd class="text-2xl font-bold text-gray-900"><?php echo e($approvedToday); ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Rejected Today</dt>
                            <dd class="text-2xl font-bold text-gray-900"><?php echo e($rejectedToday); ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <?php if($pendingCount > 0): ?>
    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-blue-900">You have <?php echo e($pendingCount); ?> pending request(s)</h3>
                    <p class="text-sm text-blue-700">Review and approve employee requests</p>
                </div>
            </div>
<a href="<?php echo e(route('manager.approvals.index')); ?>" class="px-4 py-2 bg-blue-400 text-white text-sm font-medium rounded-md hover:bg-blue-500">                
    Review Now
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Pending Requests Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">Pending Requests</h2>
<a href="<?php echo e(route('manager.approvals.index')); ?>" class="text-sm text-indigo-600 hover:text-indigo-900">View All</a>        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $pendingRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo e($request->user->name); ?></div>
                                <div class="text-sm text-gray-500"><?php echo e($request->user->email); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo e($request->created_at->format('M d, Y')); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo e($request->request_type === 'special' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'); ?>">
                                    <?php echo e(ucfirst($request->request_type)); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php if($request->request_type === 'standard'): ?>
                                    <?php echo e($request->items->count()); ?> item(s)
                                <?php else: ?>
                                    Special Item
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                <?php echo e($request->purpose); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button onclick="showApprovalModal(<?php echo e($request->id); ?>, 'approve')" class="text-green-600 hover:text-green-900">Approve</button>
                                <button onclick="showApprovalModal(<?php echo e($request->id); ?>, 'reject')" class="text-red-600 hover:text-red-900">Reject</button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                No pending requests at this time.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        <?php if($pendingRequests->hasPages()): ?>
        <div class="px-6 py-4 border-t border-gray-200">
            <?php echo e($pendingRequests->links()); ?>

        </div>
        <?php endif; ?>
    </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div id="approvalModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 id="modalTitle" class="text-lg font-semibold text-gray-900 mb-4"></h3>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
            <textarea id="approvalNotes" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Add any notes or comments..."></textarea>
        </div>
        <div class="flex justify-end space-x-3">
            <button onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Cancel
            </button>
            <button id="confirmBtn" onclick="submitAction()" class="px-4 py-2 rounded-md text-sm font-medium text-white">
                Confirm
            </button>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
let currentRequestId = null;
let currentAction = null;

function showApprovalModal(requestId, action) {
    currentRequestId = requestId;
    currentAction = action;
    
    const modal = document.getElementById('approvalModal');
    const title = document.getElementById('modalTitle');
    const confirmBtn = document.getElementById('confirmBtn');
    
    if (action === 'approve') {
        title.textContent = 'Approve Request';
        confirmBtn.className = 'px-4 py-2 bg-green-600 rounded-md text-sm font-medium text-white hover:bg-green-700';
        confirmBtn.textContent = 'Approve';
    } else {
        title.textContent = 'Reject Request';
        confirmBtn.className = 'px-4 py-2 bg-red-600 rounded-md text-sm font-medium text-white hover:bg-red-700';
        confirmBtn.textContent = 'Reject';
    }
    
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('approvalModal').classList.add('hidden');
    document.getElementById('approvalNotes').value = '';
    currentRequestId = null;
    currentAction = null;
}

async function submitAction() {
    const notes = document.getElementById('approvalNotes').value.trim();
    const url = `/manager/approvals/${currentRequestId}/${currentAction}`;
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ notes })
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeModal();
            location.reload();
        } else {
            alert('Failed to process request');
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\supply-request-system\resources\views/manager/dashboard.blade.php ENDPATH**/ ?>