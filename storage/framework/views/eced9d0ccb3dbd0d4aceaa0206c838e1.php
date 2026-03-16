

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Request Details</h1>
            <p class="mt-1 text-sm text-gray-500">Track your supply request status</p>
        </div>
        <a href="<?php echo e(route('employee.requests.index')); ?>"
           class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 font-medium">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            My Requests
        </a>
    </div>

    <?php
        $transactions    = $request->releaseTransactions->sortBy('round');
        $txCount         = $transactions->count();
        $isPartial       = $request->items->contains(fn($i) => ($i->released_quantity ?? 0) > 0 && ($i->remaining_quantity ?? 0) > 0);
        $hasAnyRelease   = $request->items->contains(fn($i) => ($i->released_quantity ?? 0) > 0);
        $isFullyReleased = $request->status === 'admin_released';
    ?>

    <div class="space-y-5">

        
        <div class="bg-white shadow rounded-xl overflow-hidden">

            
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">SR #<?php echo e($request->sr_number); ?></h2>
                    <p class="text-sm text-gray-500">Submitted <?php echo e($request->created_at->format('M d, Y h:i A')); ?></p>
                </div>
                <div class="flex flex-col items-end gap-1">
                    <?php if($isPartial): ?>
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-300">
                            Partially Released
                        </span>
                    <?php elseif($isFullyReleased): ?>
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800 border border-green-300">
                            Fully Released
                        </span>
                    <?php elseif($request->status === 'manager_approved'): ?>
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-yellow-50 text-yellow-800 border border-yellow-300">
                            Awaiting Release
                        </span>
                    <?php elseif($request->status === 'pending'): ?>
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-700 border border-gray-300">
                            Pending Approval
                        </span>
                    <?php else: ?>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full <?php echo e($request->getStatusBadgeColor()); ?>">
                            <?php echo e($request->getStatusLabel()); ?>

                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="divide-y divide-gray-100">

                
                <?php if($request->serial_number): ?>
                    <div class="px-6 py-4 <?php echo e($isPartial ? 'bg-blue-50' : 'bg-green-50'); ?>">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide <?php echo e($isPartial ? 'text-blue-600' : 'text-green-600'); ?> mb-0.5">
                                    <?php echo e($isPartial ? 'Partial Release Serial' : 'Release Serial Number'); ?>

                                </p>
                                <p class="text-xl font-mono font-bold <?php echo e($isPartial ? 'text-blue-900' : 'text-green-900'); ?>">
                                    <?php echo e($request->serial_number); ?>

                                </p>
                            </div>
                            <?php if($request->ro_number): ?>
                                <div class="text-right">
                                    <p class="text-xs text-gray-400 mb-0.5">RO Number</p>
                                    <p class="text-sm font-mono font-semibold text-gray-700"><?php echo e($request->ro_number); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if($isPartial): ?>
                            <p class="mt-2 text-xs text-blue-700">
                                Some items have been released. The remaining items are still being processed.
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                
                <div class="px-6 py-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Department</p>
                            <p class="text-sm font-semibold text-gray-900"><?php echo e($request->department->name); ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Request Type</p>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full <?php echo e($request->request_type === 'special' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'); ?>">
                                <?php echo e(ucfirst($request->request_type)); ?>

                            </span>
                        </div>
                    </div>
                    <?php if($request->purpose): ?>
                        <div class="mt-4">
                            <p class="text-xs text-gray-400 mb-1">Purpose</p>
                            <p class="text-sm text-gray-800"><?php echo e($request->purpose); ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                
                <div class="px-6 py-5">
                    <?php if($request->request_type === 'standard'): ?>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Items</p>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Item</th>
                                        <?php if($hasAnyRelease): ?>
                                            <th class="px-4 py-2 text-center">Requested</th>
                                            <th class="px-4 py-2 text-center">Released</th>
                                            <th class="px-4 py-2 text-center">Remaining</th>
                                            <th class="px-4 py-2 text-center">Status</th>
                                        <?php else: ?>
                                            <th class="px-4 py-2 text-center">Qty</th>
                                            <th class="px-4 py-2 text-center">Unit</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php $__currentLoopData = $request->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $released  = $item->released_quantity  ?? 0;
                                            $remaining = $item->remaining_quantity ?? $item->quantity;
                                            $original  = $item->original_quantity  ?? $item->quantity;
                                            $done      = $released > 0 && $remaining == 0;
                                            $partial   = $released > 0 && $remaining > 0;
                                        ?>
                                        <tr class="<?php echo e($done ? 'bg-green-50' : ($partial ? 'bg-blue-50' : '')); ?>">
                                            <td class="px-4 py-3">
                                                <p class="font-medium text-gray-900"><?php echo e($item->item_name); ?></p>
                                                <p class="text-xs font-mono text-gray-400"><?php echo e($item->item_code); ?></p>
                                            </td>
                                            <?php if($hasAnyRelease): ?>
                                                <td class="px-4 py-3 text-center text-gray-600"><?php echo e($original); ?></td>
                                                <td class="px-4 py-3 text-center font-bold <?php echo e($released > 0 ? 'text-green-700' : 'text-gray-300'); ?>">
                                                    <?php echo e($released > 0 ? $released : '—'); ?>

                                                </td>
                                                <td class="px-4 py-3 text-center font-semibold <?php echo e($remaining > 0 ? 'text-orange-600' : 'text-gray-300'); ?>">
                                                    <?php echo e($remaining > 0 ? $remaining : '—'); ?>

                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <?php if($done): ?>
                                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">✓ Done</span>
                                                    <?php elseif($partial): ?>
                                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">◑ Partial</span>
                                                    <?php else: ?>
                                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">⏳ Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php else: ?>
                                                <td class="px-4 py-3 text-center font-semibold text-gray-900"><?php echo e($item->quantity); ?></td>
                                                <td class="px-4 py-3 text-center text-gray-500"><?php echo e($item->supply->unit ?? 'N/A'); ?></td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Special Request Description</p>
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <p class="text-sm text-gray-900"><?php echo e($request->special_item_description); ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                
                <div class="px-6 py-5">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Request Timeline</p>
                    <ol class="relative border-l-2 border-gray-200 space-y-5 ml-3">
                        <li class="ml-6">
                         
                            <p class="text-sm font-semibold text-gray-700">Request Submitted</p>
                            <p class="text-xs text-gray-500"><?php echo e($request->created_at->format('M d, Y h:i A')); ?></p>
                        </li>

                        <?php if($request->manager_approved_at): ?>
                            <li class="ml-6">
                                <p class="text-sm font-semibold text-green-800">Manager Approved</p>
                                <p class="text-xs text-gray-500"><?php echo e($request->manager_approved_at->format('M d, Y h:i A')); ?></p>
                            </li>
                        <?php endif; ?>

                        <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="ml-6">
                                
                                <p class="text-sm font-semibold <?php echo e($tx->is_final_release ? 'text-green-800' : 'text-blue-800'); ?>">
                                    <?php echo e($tx->is_final_release ? 'Final Release' : 'Partial Release'); ?> — Round <?php echo e($tx->round); ?>

                                </p>
                                <p class="text-xs text-gray-500">
                                    <?php echo e($tx->created_at->format('M d, Y h:i A')); ?>

                                    · Serial: <span class="font-mono font-semibold text-indigo-600"><?php echo e($tx->serial_number); ?></span>
                                </p>
                                <p class="text-xs text-gray-500">
                                    <?php echo e(collect($tx->items_snapshot)->sum('qty_released')); ?> units released
                                    <?php if($tx->items_still_pending_after > 0): ?>
                                        · <?php echo e($tx->items_still_pending_after); ?> item(s) still pending
                                    <?php endif; ?>
                                </p>
                                <?php if($tx->notes): ?>
                                    <p class="text-xs text-gray-400 italic mt-0.5"><?php echo e($tx->notes); ?></p>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <?php if($isPartial && $txCount > 0): ?>
                            <li class="ml-6">
                                
                                <p class="text-sm text-gray-400 italic">Remaining items being processed…</p>
                            </li>
                        <?php elseif($request->status === 'manager_approved' && $txCount === 0): ?>
                            <li class="ml-6">
                                
                                <p class="text-sm text-gray-400 italic">Awaiting admin release…</p>
                            </li>
                        <?php endif; ?>
                    </ol>
                </div>

            </div>

            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <?php if($request->serial_number): ?>
                    <a href="<?php echo e(route('employee.requests.voucher', $request->id)); ?>" target="_blank"
                       class="inline-flex items-center px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                        View Voucher
                    </a>
                <?php endif; ?>
            </div>
        </div>

        
        <?php if($transactions->isNotEmpty()): ?>
            <div class="bg-white shadow rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-base font-bold text-gray-900">Release History</h3>
                    <p class="text-xs text-gray-500 mt-0.5">What has been released so far and when</p>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="px-6 py-4">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <p class="text-sm font-bold <?php echo e($tx->is_final_release ? 'text-green-800' : 'text-blue-800'); ?>">
                                        Round <?php echo e($tx->round); ?> — <?php echo e($tx->is_final_release ? 'Final Release' : 'Partial Release'); ?>

                                    </p>
                                    <p class="text-xs text-gray-500"><?php echo e($tx->created_at->format('M d, Y h:i A')); ?></p>
                                </div>
                                <span class="text-xs font-mono font-bold text-indigo-600"><?php echo e($tx->serial_number); ?></span>
                            </div>

                            <div class="bg-gray-50 rounded-lg divide-y divide-gray-100">
                                <?php $__currentLoopData = $tx->items_snapshot; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $snap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($snap['qty_released'] > 0): ?>
                                        <div class="flex items-center justify-between px-4 py-2">
                                            <span class="text-sm text-gray-800"><?php echo e($snap['item_name']); ?></span>
                                            <div class="flex items-center gap-3">
                                                <span class="text-xs text-gray-500">released</span>
                                                <span class="text-sm font-bold text-green-700"><?php echo e($snap['qty_released']); ?></span>
                                                <?php if($snap['qty_remaining_after'] > 0): ?>
                                                    <span class="text-xs text-orange-600">· <?php echo e($snap['qty_remaining_after']); ?> still pending</span>
                                                <?php else: ?>
                                                    <span class="text-xs text-green-600">· complete</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                            <?php if($tx->notes): ?>
                                <p class="mt-2 text-xs text-gray-500 italic">Note: <?php echo e($tx->notes); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\supply-request-system\resources\views/employee/requests/show.blade.php ENDPATH**/ ?>