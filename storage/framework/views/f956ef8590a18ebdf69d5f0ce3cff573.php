

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Approval Queue</h1>
        <p class="mt-1 text-sm text-gray-600">Review and manage supply requests</p>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8">
            <button onclick="switchTab('pending')" id="pendingTab" class="border-b-2 border-indigo-500 py-4 px-1 text-sm font-medium text-indigo-600">
                Pending (<?php echo e($pendingRequests->count()); ?>)
            </button>
            <button onclick="switchTab('approved')" id="approvedTab" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Recently Approved (<?php echo e($approvedRequests->count()); ?>)
            </button>
        </nav>
    </div>

    <!-- Pending Requests Tab -->
    <div id="pendingContent">
        <?php if($pendingRequests->count() > 0): ?>
            <div class="space-y-6">
                <?php $__currentLoopData = $pendingRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                        <!-- Request Header -->
                        <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900"><?php echo e($request->user->name); ?></h3>
                                    <p class="text-sm text-gray-600"><?php echo e($request->user->email); ?></p>
                                    <p class="text-xs text-gray-500 mt-1"><?php echo e($request->created_at->diffForHumans()); ?></p>
                                </div>
                                <div class="text-right">
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full <?php echo e($request->request_type === 'special' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'); ?>">
                                        <?php echo e(ucfirst($request->request_type)); ?>

                                    </span>
                                    <p class="text-sm font-mono font-semibold text-indigo-600 mt-2"><?php echo e($request->sr_number); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Request Body -->
                        <div class="px-6 py-4 space-y-4">
                            <!-- Purpose -->
                            <div>
                                <p class="text-sm font-medium text-gray-700">Purpose:</p>
                                <p class="text-sm text-gray-900 mt-1"><?php echo e($request->purpose); ?></p>
                            </div>

                            <!-- Items or Special Description -->
                            <?php if($request->request_type === 'standard'): ?>
                                <div>
                                    <p class="text-sm font-medium text-gray-700 mb-2">Items (<?php echo e($request->items->count()); ?>):</p>
                                    <div class="bg-gray-50 rounded-md p-4">
                                        <table class="min-w-full">
                                            <thead>
                                                <tr class="border-b border-gray-200">
                                                    <th class="text-left text-xs font-medium text-gray-500 uppercase pb-2">Item</th>
                                                    <th class="text-left text-xs font-medium text-gray-500 uppercase pb-2">Code</th>
                                                    <th class="text-right text-xs font-medium text-gray-500 uppercase pb-2">Quantity</th>
                                                    <th class="text-left text-xs font-medium text-gray-500 uppercase pb-2">Unit</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                <?php $__currentLoopData = $request->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td class="py-2 text-sm text-gray-900"><?php echo e($item->item_name); ?></td>
                                                        <td class="py-2 text-sm text-gray-500 font-mono"><?php echo e($item->item_code); ?></td>
                                                        <td class="py-2 text-sm text-gray-900 text-right font-semibold"><?php echo e($item->quantity); ?></td>
                                                        <td class="py-2 text-sm text-gray-500"><?php echo e($item->supply->unit ?? 'N/A'); ?></td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div>
                                    <p class="text-sm font-medium text-gray-700 mb-2">Special Item Description:</p>
                                    <div class="bg-purple-50 border border-purple-200 rounded-md p-4">
                                        <p class="text-sm text-gray-900"><?php echo e($request->special_item_description); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Actions -->
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row justify-end gap-3">
                            <a href="<?php echo e(route('manager.requests.show', $request->id)); ?>" 
                               class="w-full sm:w-auto text-center px-6 py-2 border border-indigo-600 rounded-md text-sm font-medium text-indigo-600 hover:bg-indigo-50">
                                View Details
                            </a>
                            <button 
                                onclick="showApprovalModal(<?php echo e($request->id); ?>, 'reject')" 
                                class="w-full sm:w-auto px-6 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                                Reject
                            </button>
                            <button 
                                onclick="showApprovalModal(<?php echo e($request->id); ?>, 'approve')" 
                                class="w-full sm:w-auto px-6 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                Approve
                            </button>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="bg-white shadow rounded-lg p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No pending requests</h3>
                <p class="mt-1 text-sm text-gray-500">All requests have been processed.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recently Approved Tab -->
    <div id="approvedContent" class="hidden">
        <?php if($approvedRequests->count() > 0): ?>
            <div class="bg-white shadow rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SR Number</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Approved</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $approvedRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-mono text-indigo-600 font-semibold">
                                    <?php echo e($request->sr_number); ?>

                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($request->user->name); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo e($request->user->email); ?></div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo e($request->request_type === 'special' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'); ?>">
                                        <?php echo e(ucfirst($request->request_type)); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($request->manager_approved_at->format('M d, Y')); ?>

                                    <div class="text-xs text-gray-400"><?php echo e($request->manager_approved_at->format('h:i A')); ?></div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Awaiting Release
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <a href="<?php echo e(route('manager.requests.show', $request->id)); ?>" 
                                       class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="bg-white shadow rounded-lg p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No recent approvals</h3>
                <p class="mt-1 text-sm text-gray-500">Approved requests will appear here.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Approval Modal -->
<div id="approvalModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4" id="modalTitle"></h3>
        <form id="approvalForm">
            <input type="hidden" id="request-id">
            <input type="hidden" id="action-type">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Notes <span id="required-note" class="text-red-600"></span>
                </label>
                <textarea 
                    id="approval-notes" 
                    rows="4" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Add notes..."></textarea>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeApprovalModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" id="submitBtn" class="px-4 py-2 rounded-md text-sm font-medium text-white">
                    Confirm
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function switchTab(tab) {
    const pendingTab = document.getElementById('pendingTab');
    const approvedTab = document.getElementById('approvedTab');
    const pendingContent = document.getElementById('pendingContent');
    const approvedContent = document.getElementById('approvedContent');
    
    if (tab === 'pending') {
        pendingTab.className = 'border-b-2 border-indigo-500 py-4 px-1 text-sm font-medium text-indigo-600';
        approvedTab.className = 'border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300';
        pendingContent.classList.remove('hidden');
        approvedContent.classList.add('hidden');
    } else {
        approvedTab.className = 'border-b-2 border-indigo-500 py-4 px-1 text-sm font-medium text-indigo-600';
        pendingTab.className = 'border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300';
        approvedContent.classList.remove('hidden');
        pendingContent.classList.add('hidden');
    }
}

function showApprovalModal(requestId, action) {
    document.getElementById('request-id').value = requestId;
    document.getElementById('action-type').value = action;
    
    if (action === 'approve') {
        document.getElementById('modalTitle').textContent = 'Approve Request';
        document.getElementById('required-note').textContent = '';
        document.getElementById('approval-notes').placeholder = 'Add approval notes (optional)';
        document.getElementById('submitBtn').className = 'px-4 py-2 rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700';
    } else {
        document.getElementById('modalTitle').textContent = 'Reject Request';
        document.getElementById('required-note').textContent = '*';
        document.getElementById('approval-notes').placeholder = 'Reason for rejection (required)';
        document.getElementById('submitBtn').className = 'px-4 py-2 rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700';
    }
    
    document.getElementById('approvalModal').classList.remove('hidden');
}

function closeApprovalModal() {
    document.getElementById('approvalModal').classList.add('hidden');
    document.getElementById('approvalForm').reset();
}

document.getElementById('approvalForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const requestId = document.getElementById('request-id').value;
    const action = document.getElementById('action-type').value;
    const notes = document.getElementById('approval-notes').value.trim();
    
    if (action === 'reject' && !notes) {
        alert('Please provide a reason for rejection');
        return;
    }
    
    try {
        const response = await fetch(`/manager/approvals/${requestId}/${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ notes: notes })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Action failed');
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\supply-request-system\resources\views/manager/approvals.blade.php ENDPATH**/ ?>