

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Request Details</h1>
            <p class="mt-1 text-sm text-gray-600">Full breakdown of this supply request</p>
        </div>
        <a href="<?php echo e(route('admin.releases.index')); ?>"
           class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 font-medium">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Releases
        </a>
    </div>

    <?php
        $transactions    = $request->releaseTransactions->sortBy('round');
        $txCount         = $transactions->count();
        $isPartial       = $request->items->contains(fn($i) => ($i->released_quantity ?? 0) > 0 && ($i->remaining_quantity ?? 0) > 0);
        $hasAnyRelease   = $request->items->contains(fn($i) => ($i->released_quantity ?? 0) > 0);
        $isFullyReleased = $request->status === 'admin_released';
        $isPending       = $request->status === 'manager_approved';
    ?>

    <div class="space-y-6">

        
        <div class="bg-white shadow rounded-xl overflow-hidden">

            
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">SR #<?php echo e($request->sr_number); ?></h2>
                    <p class="text-sm text-gray-500 mt-0.5">Submitted <?php echo e($request->created_at->format('F d, Y \a\t h:i A')); ?></p>
                </div>
                <div class="flex flex-col items-end gap-1">
                    <?php if($isPartial): ?>
                        <span class="px-3 py-1 text-sm font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-300">
                            Partially Released
                        </span>
                        <span class="text-xs text-blue-500"><?php echo e($txCount); ?> release round<?php echo e($txCount > 1 ? 's' : ''); ?> recorded</span>
                    <?php elseif($isFullyReleased): ?>
                        <span class="px-3 py-1 text-sm font-bold rounded-full bg-green-100 text-green-800 border border-green-300">
                            ✓ Fully Released
                        </span>
                        <span class="text-xs text-green-500"><?php echo e($txCount); ?> release round<?php echo e($txCount > 1 ? 's' : ''); ?> recorded</span>
                    <?php elseif($isPending): ?>
                        <span class="px-3 py-1 text-sm font-bold rounded-full bg-yellow-50 text-yellow-800 border border-yellow-300">
                            Awaiting Release
                        </span>
                    <?php else: ?>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full <?php echo e($request->getStatusBadgeColor()); ?>">
                            <?php echo e($request->getStatusLabel()); ?>

                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="divide-y divide-gray-100">

                
                <?php if($request->serial_number): ?>
                    <div class="px-6 py-4 <?php echo e($isPartial ? 'bg-blue-50' : 'bg-green-50'); ?>">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center <?php echo e($isPartial ? 'bg-blue-200' : 'bg-green-200'); ?>">
                                    <svg class="w-5 h-5 <?php echo e($isPartial ? 'text-blue-700' : 'text-green-700'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide <?php echo e($isPartial ? 'text-blue-600' : 'text-green-600'); ?>">
                                        <?php echo e($isPartial ? 'Partial Release — Serial Number' : 'Release Serial Number'); ?>

                                    </p>
                                    <p class="text-2xl font-mono font-bold <?php echo e($isPartial ? 'text-blue-900' : 'text-green-900'); ?>">
                                        <?php echo e($request->serial_number); ?>

                                    </p>
                                </div>
                            </div>
                            <?php if($request->ro_number): ?>
                                <div class="text-right">
                                    <p class="text-xs text-gray-400 uppercase font-semibold mb-0.5">RO Number</p>
                                    <p class="text-sm font-mono font-semibold text-gray-700"><?php echo e($request->ro_number); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if($isPartial): ?>
                            <p class="mt-3 text-xs text-blue-700 bg-blue-100 border border-blue-200 rounded px-3 py-2">
                                Release in progress. Items with remaining quantities are still in <strong>Pending Release</strong>.
                                Use <strong>Close &amp; Re-queue</strong> to close this request and send remaining items into a new pending request.
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                
                <div class="px-6 py-5">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Request Information</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Requester</p>
                            <p class="text-sm font-semibold text-gray-900"><?php echo e($request->user->name); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e($request->user->email); ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Department</p>
                            <p class="text-sm font-semibold text-gray-900"><?php echo e($request->department->name); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e($request->department->code); ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Request Type</p>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full <?php echo e($request->request_type === 'special' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'); ?>">
                                <?php echo e(ucfirst($request->request_type)); ?>

                            </span>
                        </div>
                        <?php if($request->budget_type): ?>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Budget</p>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full <?php echo e($request->budget_type === 'budgeted' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'); ?>">
                                    <?php echo e($request->budget_type === 'budgeted' ? 'Budgeted' : 'Not Budgeted'); ?>

                                </span>
                            </div>
                        <?php endif; ?>
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
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                Items (<?php echo e($request->items->count()); ?>)
                            </p>
                            <?php if($isPartial): ?>
                                <span class="text-xs text-blue-600 font-medium">Partial release in progress</span>
                            <?php endif; ?>
                        </div>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Code</th>
                                        <th class="px-4 py-3 text-left">Item Name</th>
                                        <?php if($hasAnyRelease): ?>
                                            <th class="px-4 py-3 text-center">Requested</th>
                                            <th class="px-4 py-3 text-center">Released</th>
                                            <th class="px-4 py-3 text-center">Remaining</th>
                                            <th class="px-4 py-3 text-center">Status</th>
                                        <?php else: ?>
                                            <th class="px-4 py-3 text-center">Qty</th>
                                            <th class="px-4 py-3 text-center">Unit</th>
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
                                        <tr class="<?php echo e($done ? 'bg-green-50' : ($partial ? 'bg-blue-50' : 'bg-white')); ?>">
                                            <td class="px-4 py-3 font-mono text-gray-600 text-xs"><?php echo e($item->item_code); ?></td>
                                            <td class="px-4 py-3 text-gray-900 font-medium"><?php echo e($item->item_name); ?></td>
                                            <?php if($hasAnyRelease): ?>
                                                <td class="px-4 py-3 text-center text-gray-600"><?php echo e($original); ?></td>
                                                <td class="px-4 py-3 text-center font-semibold <?php echo e($released > 0 ? 'text-green-700' : 'text-gray-300'); ?>">
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
                                <?php if($hasAnyRelease): ?>
                                    <tfoot class="bg-gray-50 border-t border-gray-200 text-xs font-bold">
                                        <tr>
                                            <td colspan="2" class="px-4 py-3 text-gray-500 uppercase">Totals</td>
                                            <td class="px-4 py-3 text-center text-gray-700"><?php echo e($request->items->sum(fn($i) => $i->original_quantity ?? $i->quantity)); ?></td>
                                            <td class="px-4 py-3 text-center text-green-700"><?php echo e($request->items->sum(fn($i) => $i->released_quantity ?? 0)); ?></td>
                                            <td class="px-4 py-3 text-center text-orange-600"><?php echo e($request->items->sum(fn($i) => $i->remaining_quantity ?? $i->quantity)); ?></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                <?php endif; ?>
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
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Approval Trail</p>
                    <ol class="relative border-l-2 border-gray-200 space-y-5 ml-3">

                        <li class="ml-6">
                            <p class="text-sm font-semibold text-gray-700">Request Submitted</p>
                            <p class="text-xs text-gray-500"><?php echo e($request->user->name); ?> · <?php echo e($request->created_at->format('M d, Y h:i A')); ?></p>
                        </li>

                        <?php if($request->manager_approved_at): ?>
                            <li class="ml-6">
                                <p class="text-sm font-semibold text-green-800">Manager Approved</p>
                                <p class="text-xs text-gray-500"><?php echo e($request->managerApprover->name ?? 'Manager'); ?> · <?php echo e($request->manager_approved_at->format('M d, Y h:i A')); ?></p>
                                <?php if($request->manager_notes): ?><p class="text-xs text-gray-600 italic mt-0.5"><?php echo e($request->manager_notes); ?></p><?php endif; ?>
                            </li>
                        <?php elseif(isset($request->manager_rejected_at) && $request->manager_rejected_at): ?>
                            <li class="ml-6">
                                <p class="text-sm font-semibold text-red-800">Manager Rejected</p>
                                <p class="text-xs text-gray-500"><?php echo e($request->managerApprover->name ?? 'Manager'); ?> · <?php echo e($request->manager_rejected_at->format('M d, Y h:i A')); ?></p>
                                <?php if($request->manager_notes): ?><p class="text-xs text-red-700 italic mt-0.5"><?php echo e($request->manager_notes); ?></p><?php endif; ?>
                            </li>
                        <?php else: ?>
                            <li class="ml-6">
                                <span class="absolute -left-3 w-6 h-6 bg-yellow-100 border-2 border-yellow-400 rounded-full flex items-center justify-center text-xs font-semibold text-yellow-600">2</span>
                                <p class="text-sm font-semibold text-yellow-700">Awaiting Manager Approval</p>
                            </li>
                        <?php endif; ?>

                        <?php if($isFullyReleased): ?>
                            <li class="ml-6">
                                <span class="absolute -left-3 w-6 h-6 bg-green-100 border-2 border-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <p class="text-sm font-semibold text-green-800">Fully Released</p>
                                <p class="text-xs text-gray-500"><?php echo e($request->adminReleaser->name ?? 'Admin'); ?> · <?php echo e($request->admin_released_at->format('M d, Y h:i A')); ?></p>
                                <p class="text-xs font-mono text-green-700 mt-0.5"><?php echo e($request->serial_number); ?></p>
                            </li>
                        <?php elseif($isPartial): ?>
                            <li class="ml-6">
                                <span class="absolute -left-3 w-6 h-6 bg-blue-100 border-2 border-blue-400 rounded-full flex items-center justify-center text-xs font-bold text-blue-600">~</span>
                                <p class="text-sm font-semibold text-blue-800">Partially Released (<?php echo e($txCount); ?> round<?php echo e($txCount > 1 ? 's' : ''); ?>)</p>
                                <p class="text-xs text-gray-500">Serial: <span class="font-mono font-semibold text-blue-700"><?php echo e($request->serial_number); ?></span></p>
                                <p class="text-xs text-blue-600 mt-0.5">Remaining items pending further release or re-queue.</p>
                            </li>
                            <li class="ml-6">
                                <span class="absolute -left-3 w-6 h-6 bg-gray-100 border-2 border-dashed border-gray-300 rounded-full flex items-center justify-center text-xs text-gray-400">…</span>
                                <p class="text-sm text-gray-400 italic">Awaiting full release or re-queue…</p>
                            </li>
                        <?php elseif(isset($request->admin_rejected_at) && $request->admin_rejected_at): ?>
                            <li class="ml-6">
                                <span class="absolute -left-3 w-6 h-6 bg-red-100 border-2 border-red-400 rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                </span>
                                <p class="text-sm font-semibold text-red-800">Admin Rejected</p>
                                <p class="text-xs text-gray-500"><?php echo e($request->admin_rejected_at->format('M d, Y h:i A')); ?></p>
                                <?php if($request->admin_notes): ?><p class="text-xs text-red-700 italic mt-0.5"><?php echo e($request->admin_notes); ?></p><?php endif; ?>
                            </li>
                        <?php elseif($isPending): ?>
                            <li class="ml-6">
                                <p class="text-sm text-gray-400 italic">Awaiting admin release…</p>
                            </li>
                        <?php endif; ?>

                    </ol>
                </div>

            </div>

            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                <div>
                    <?php if($isPending && !$isPartial): ?>
                        <p class="text-xs text-gray-500">Release this request from the <a href="<?php echo e(route('admin.releases.index')); ?>" class="text-indigo-600 hover:underline">Release Management</a> page.</p>
                    <?php elseif($isPartial): ?>
                        <button onclick="confirmRequeue(<?php echo e($request->id); ?>, '<?php echo e($request->sr_number); ?>')"
                            class="inline-flex items-center px-4 py-2 bg-orange-500 border border-orange-600 rounded-lg text-sm font-semibold text-white hover:bg-orange-600 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Close &amp; Re-queue Remaining
                        </button>
                    <?php endif; ?>
                </div>
                <div class="flex gap-3">
                    <?php if($request->serial_number): ?>
                        <a href="<?php echo e(route('admin.voucher', $request->id)); ?>" target="_blank"
                           class="inline-flex items-center px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                             View Voucher
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <?php if($transactions->isNotEmpty()): ?>
            <div class="bg-white shadow rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Release Transactions</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Full history of every release round — each has its own printable voucher</p>
                    </div>
                    <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-indigo-100 text-indigo-700">
                        <?php echo e($txCount); ?> Round<?php echo e($txCount > 1 ? 's' : ''); ?>

                    </span>
                </div>

                <div class="divide-y divide-gray-100">
                    <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="p-6">

                            
                            <div class="flex items-start justify-between mb-4">

                                
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm shrink-0
                                        <?php echo e($tx->is_final_release
                                            ? 'bg-green-100 text-green-700 border-2 border-green-400'
                                            : 'bg-blue-100 text-blue-700 border-2 border-blue-400'); ?>">
                                        <?php echo e($tx->round); ?>

                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">
                                            Round <?php echo e($tx->round); ?>

                                            <?php if($tx->is_final_release): ?>
                                                <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">Final Release</span>
                                            <?php else: ?>
                                                <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">Partial Release</span>
                                            <?php endif; ?>
                                        </p>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            <?php echo e($tx->releasedBy->name ?? 'Admin'); ?>

                                            · <?php echo e($tx->created_at->format('M d, Y h:i A')); ?>

                                        </p>
                                    </div>
                                </div>

                                
                                <div class="flex items-center gap-4 shrink-0">
                                    <div class="text-right">
                                        <p class="text-xs text-gray-400 mb-0.5">Serial</p>
                                        <p class="text-sm font-mono font-bold text-indigo-600"><?php echo e($tx->serial_number); ?></p>
                                        <?php if($tx->ro_number): ?>
                                            <p class="text-xs text-gray-400 mt-0.5">RO: <span class="font-mono text-gray-600"><?php echo e($tx->ro_number); ?></span></p>
                                        <?php endif; ?>
                                    </div>

                                    
                                    <a href="<?php echo e(route('admin.voucher', $request->id)); ?>?round=<?php echo e($tx->round); ?>"
                                       target="_blank"
                                       title="<?php echo e($tx->is_final_release ? 'View Final Release Voucher' : 'View Partial Release Voucher — Round '.$tx->round); ?>"
                                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold whitespace-nowrap transition
                                           <?php echo e($tx->is_final_release
                                               ? 'bg-green-600 text-white hover:bg-green-700'
                                               : 'bg-blue-600 text-white hover:bg-blue-700'); ?>">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <?php echo e($tx->is_final_release ? 'Final Voucher' : 'Round '.$tx->round.' Voucher'); ?>

                                    </a>
                                </div>
                            </div>

                            
                            <div class="grid grid-cols-3 gap-3 mb-4">
                                <div class="bg-gray-50 rounded-lg px-3 py-2 text-center">
                                    <p class="text-xs text-gray-400 mb-0.5">Items Released</p>
                                    <p class="text-xl font-bold text-green-700"><?php echo e($tx->items_fully_released_this_round); ?></p>
                                    <p class="text-xs text-gray-400">of <?php echo e($tx->total_items_in_request); ?> items</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg px-3 py-2 text-center">
                                    <p class="text-xs text-gray-400 mb-0.5">Qty Released</p>
                                    <p class="text-xl font-bold text-indigo-700"><?php echo e(collect($tx->items_snapshot)->sum('qty_released')); ?></p>
                                    <p class="text-xs text-gray-400">units this round</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg px-3 py-2 text-center">
                                    <p class="text-xs text-gray-400 mb-0.5">Still Pending</p>
                                    <p class="text-xl font-bold <?php echo e($tx->items_still_pending_after > 0 ? 'text-orange-600' : 'text-gray-300'); ?>">
                                        <?php echo e($tx->items_still_pending_after); ?>

                                    </p>
                                    <p class="text-xs text-gray-400">items after round</p>
                                </div>
                            </div>

                            
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <table class="min-w-full text-xs divide-y divide-gray-100">
                                    <thead class="bg-gray-50 text-gray-500 font-semibold uppercase">
                                        <tr>
                                            <th class="px-3 py-2 text-left">Item</th>
                                            <th class="px-3 py-2 text-center">Originally Requested</th>
                                            <th class="px-3 py-2 text-center">Released This Round</th>
                                            <th class="px-3 py-2 text-center">Cumulative Released</th>
                                            <th class="px-3 py-2 text-center">Remaining After</th>
                                            <th class="px-3 py-2 text-center">Stock Before</th>
                                            <th class="px-3 py-2 text-center">Stock After</th>
                                            <th class="px-3 py-2 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 bg-white">
                                        <?php $__currentLoopData = $tx->items_snapshot; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $snap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="<?php echo e($snap['status'] === 'fully_done' ? 'bg-green-50' : ($snap['status'] === 'partial' ? 'bg-blue-50' : '')); ?>">
                                                <td class="px-3 py-2">
                                                    <p class="font-medium text-gray-900"><?php echo e($snap['item_name']); ?></p>
                                                    <p class="font-mono text-gray-400"><?php echo e($snap['item_code']); ?></p>
                                                </td>
                                                <td class="px-3 py-2 text-center text-gray-600"><?php echo e($snap['original_requested']); ?></td>
                                                <td class="px-3 py-2 text-center font-bold <?php echo e($snap['qty_released'] > 0 ? 'text-green-700' : 'text-gray-300'); ?>">
                                                    <?php echo e($snap['qty_released'] > 0 ? $snap['qty_released'] : '—'); ?>

                                                </td>
                                                <td class="px-3 py-2 text-center text-indigo-700 font-semibold"><?php echo e($snap['qty_cumulative']); ?></td>
                                                <td class="px-3 py-2 text-center font-semibold <?php echo e($snap['qty_remaining_after'] > 0 ? 'text-orange-600' : 'text-gray-300'); ?>">
                                                    <?php echo e($snap['qty_remaining_after'] > 0 ? $snap['qty_remaining_after'] : '—'); ?>

                                                </td>
                                                <td class="px-3 py-2 text-center text-gray-500"><?php echo e($snap['stock_before']); ?></td>
                                                <td class="px-3 py-2 text-center text-gray-500"><?php echo e($snap['stock_after']); ?></td>
                                                <td class="px-3 py-2 text-center">
                                                    <?php if($snap['status'] === 'fully_done'): ?>
                                                        <span class="px-1.5 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold">✓ Done</span>
                                                    <?php elseif($snap['status'] === 'partial'): ?>
                                                        <span class="px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-700 font-semibold">◑ Partial</span>
                                                    <?php else: ?>
                                                        <span class="px-1.5 py-0.5 rounded-full bg-gray-100 text-gray-500 font-semibold">— Skipped</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if($tx->notes): ?>
                                <p class="mt-3 text-xs text-gray-500 italic bg-gray-50 border border-gray-200 rounded px-3 py-2">
                                    Note: <?php echo e($tx->notes); ?>

                                </p>
                            <?php endif; ?>

                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
async function confirmRequeue(id, srNumber) {
    if (!confirm(`Close SR #${srNumber} and automatically create a new pending request for all remaining items?\n\nThe employee will NOT need to submit a new request — it will appear directly in Pending Release.`)) return;

    const spinner = document.createElement('div');
    spinner.className = 'fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center';
    spinner.innerHTML = `<div class="bg-white rounded-xl px-8 py-6 flex items-center gap-4 shadow-xl">
        <svg class="animate-spin h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-sm font-medium text-gray-800">Re-queuing remaining items…</span>
    </div>`;
    document.body.appendChild(spinner);

    try {
        const res  = await fetch(`/admin/releases/${id}/requeue`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await res.json();
        spinner.remove();

        if (data.success) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center p-4';
            modal.innerHTML = `
                <div class="bg-white rounded-xl shadow-xl max-w-sm w-full p-6 text-center">
                    <div class="mx-auto w-14 h-14 rounded-full bg-orange-100 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Re-queued Successfully</h3>
                    <p class="text-sm text-gray-600 mb-1">SR #${srNumber} has been closed.</p>
                    <p class="text-sm text-gray-600 mb-4">Remaining items are now queued as:</p>
                    <p class="text-xl font-mono font-bold text-orange-600 mb-5">${data.new_sr_number}</p>
                    <p class="text-xs text-gray-400 bg-gray-50 rounded px-3 py-2 mb-5">
                        The new request is pre-approved and visible in <strong>Pending Release</strong> immediately.
                    </p>
                    <button onclick="window.location.href='/admin/releases'"
                        class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">
                        Go to Pending Release
                    </button>
                </div>`;
            document.body.appendChild(modal);
        } else {
            alert('Error: ' + (data.message || 'Failed to re-queue.'));
        }
    } catch(e) {
        spinner.remove();
        alert('A network error occurred. Please try again.');
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\supply-request-system\resources\views/admin/releases/show.blade.php ENDPATH**/ ?>