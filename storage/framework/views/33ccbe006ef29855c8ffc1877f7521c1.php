

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Supply Management</h1>
            <p class="mt-1 text-sm text-gray-600">Manage catalog items and inventory</p>
        </div>
        <a href="<?php echo e(route('admin.supplies.create')); ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New Supply
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" id="searchInput" placeholder="Search by name or code..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select id="categoryFilter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Categories</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category); ?>"><?php echo e($category); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="statusFilter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-green-800"><?php echo e(session('success')); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-red-800"><?php echo e(session('error')); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Supplies Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Cost</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="suppliesTable">
                <?php $__empty_1 = true; $__currentLoopData = $supplies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supply): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 supply-row" 
                        data-category="<?php echo e($supply->category); ?>" 
                        data-status="<?php echo e($supply->is_active ? 'active' : 'inactive'); ?>" 
                        data-name="<?php echo e(strtolower($supply->name)); ?>" 
                        data-code="<?php echo e(strtolower($supply->item_code)); ?>">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-mono font-medium text-gray-900"><?php echo e($supply->item_code); ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900"><?php echo e($supply->name); ?></div>
                            <?php if($supply->description): ?>
                                <div class="text-sm text-gray-500 truncate max-w-xs"><?php echo e($supply->description); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                <?php echo e($supply->category); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo e($supply->unit); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <?php
                                $stockQty = $supply->stock_quantity ?? 0;
                                $minStock = $supply->minimum_stock ?? 10;
                                $isLow = $stockQty <= $minStock;
                            ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded <?php echo e($isLow ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'); ?>">
                                <?php echo e($stockQty); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            <?php echo e($supply->unit_cost ? '₱' . number_format($supply->unit_cost, 2) : '—'); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button onclick="toggleStatus(<?php echo e($supply->id); ?>)" class="status-toggle-<?php echo e($supply->id); ?>">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo e($supply->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                    <?php echo e($supply->is_active ? 'Active' : 'Inactive'); ?>

                                </span>
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-3">
                            <a href="<?php echo e(route('admin.supplies.edit', $supply->id)); ?>" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            <button onclick="deleteSupply(<?php echo e($supply->id); ?>)" class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="text-gray-400">
                                <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="mt-2 text-sm font-medium text-gray-900">No supplies found</p>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating a new supply item.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if($supplies->hasPages()): ?>
            <div class="px-6 py-4 border-t border-gray-200">
                <?php echo e($supplies->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('searchInput').addEventListener('input', filterSupplies);
document.getElementById('categoryFilter').addEventListener('change', filterSupplies);
document.getElementById('statusFilter').addEventListener('change', filterSupplies);

function filterSupplies() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const category = document.getElementById('categoryFilter').value;
    const status = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('.supply-row');
    
    rows.forEach(row => {
        const name = row.dataset.name;
        const code = row.dataset.code;
        const rowCategory = row.dataset.category;
        const rowStatus = row.dataset.status;
        
        const matchesSearch = name.includes(search) || code.includes(search);
        const matchesCategory = !category || rowCategory === category;
        const matchesStatus = !status || rowStatus === status;
        
        row.style.display = (matchesSearch && matchesCategory && matchesStatus) ? '' : 'none';
    });
}

async function toggleStatus(id) {
    try {
        const response = await fetch(`/admin/supplies/${id}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        if (data.success) location.reload();
        else alert('Failed to toggle status');
    } catch (error) {
        alert('An error occurred. Please try again.');
    }
}

async function deleteSupply(id) {
    if (!confirm('Are you sure you want to delete this supply? This action cannot be undone.')) return;
    try {
        const response = await fetch(`/admin/supplies/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        if (data.success) { alert(data.message); location.reload(); }
        else alert(data.message || 'Failed to delete supply');
    } catch (error) {
        alert('An error occurred. Please try again.');
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\supply-request-system\resources\views/admin/supplies/index.blade.php ENDPATH**/ ?>