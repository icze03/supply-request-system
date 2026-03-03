

<?php $__env->startSection('content'); ?>


<?php if(isset($locked) && $locked): ?>
<div id="pinLockScreen" class="fixed inset-0 bg-gray-900 bg-opacity-95 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 text-center">
        <div class="flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-full mx-auto mb-4">
            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900 mb-1">Department Management</h2>
        <p class="text-sm text-gray-500 mb-6">Enter your PIN to access this page</p>
        <div class="mb-4">
            <input type="password" id="pinInput" placeholder="Enter PIN"
                class="w-full text-center text-lg tracking-widest rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3"
                maxlength="20" autofocus>
            <p id="pinError" class="mt-2 text-xs text-red-600 hidden">Incorrect PIN. Please try again.</p>
        </div>
        <button onclick="submitPin()"
            class="w-full py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition">
            Unlock
        </button>
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="block mt-4 text-sm text-gray-400 hover:text-gray-600">
            ← Back to Dashboard
        </a>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
async function submitPin() {
    const pin     = document.getElementById('pinInput').value.trim();
    const errorEl = document.getElementById('pinError');
    errorEl.classList.add('hidden');
    if (!pin) return;
    try {
        const response = await fetch('/admin/departments/verify-pin', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ pin })
        });
        const data = await response.json();
        if (data.success) {
            location.reload();
        } else {
            errorEl.textContent = data.message || 'Incorrect PIN. Please try again.';
            errorEl.classList.remove('hidden');
            document.getElementById('pinInput').value = '';
            document.getElementById('pinInput').focus();
        }
    } catch (e) {
        errorEl.textContent = 'An error occurred. Please try again.';
        errorEl.classList.remove('hidden');
    }
}
document.getElementById('pinInput').addEventListener('keydown', e => { if (e.key === 'Enter') submitPin(); });
</script>
<?php $__env->stopPush(); ?>

<?php else: ?>


<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Department Management</h1>
            <p class="mt-1 text-sm text-gray-600">Manage departments and cost centers</p>
        </div>
        <button onclick="openCreateModal()" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create Department
        </button>
    </div>

    <div id="successMessage" class="hidden mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-sm text-green-800" id="successText"></p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500">Total Departments</p>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo e($departments->count()); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500">Total Users</p>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo e($departments->sum(fn($d) => $d->users->count())); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Departments Table -->
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cost Center</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Users</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="departmentsTableBody">
                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr id="dept-row-<?php echo e($department->id); ?>" class="hover:bg-gray-50">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <span class="text-indigo-600 font-bold text-sm"><?php echo e(substr($department->code, 0, 2)); ?></span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($department->name); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-mono font-semibold rounded bg-gray-100 text-gray-800"><?php echo e($department->code); ?></span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 font-mono"><?php echo e($department->cost_center); ?></div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <?php echo e($department->users->count()); ?> users
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openEditModal(<?php echo e($department->id); ?>)" class="text-indigo-600 hover:text-indigo-900 text-xs font-medium">Edit</button>
                                <button onclick="deleteDepartment(<?php echo e($department->id); ?>)" class="text-red-600 hover:text-red-900 text-xs font-medium">Delete</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-lg w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Create New Department</h3>
        <form id="createForm" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department Name <span class="text-red-600">*</span></label>
                    <input type="text" name="name" required placeholder="e.g., Information Technology"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="text-xs text-red-600 mt-1 hidden" id="create-error-name"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department Code <span class="text-red-600">*</span></label>
                    <input type="text" name="code" required placeholder="e.g., IT" maxlength="10"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 uppercase">
                    <p class="text-xs text-red-600 mt-1 hidden" id="create-error-code"></p>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cost Center <span class="text-red-600">*</span></label>
                <input type="text" name="cost_center" required placeholder="e.g., CC-IT-001"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="text-xs text-red-600 mt-1 hidden" id="create-error-cost_center"></p>
            </div>
            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700">Create Department</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-lg w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Edit Department</h3>
        <form id="editForm" class="space-y-4">
            <input type="hidden" id="edit-dept-id">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department Name <span class="text-red-600">*</span></label>
                    <input type="text" id="edit-name" name="name" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="text-xs text-red-600 mt-1 hidden" id="edit-error-name"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department Code <span class="text-red-600">*</span></label>
                    <input type="text" id="edit-code" name="code" required maxlength="10"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 uppercase">
                    <p class="text-xs text-red-600 mt-1 hidden" id="edit-error-code"></p>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cost Center <span class="text-red-600">*</span></label>
                <input type="text" id="edit-cost-center" name="cost_center" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="text-xs text-red-600 mt-1 hidden" id="edit-error-cost_center"></p>
            </div>
            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700">Update Department</button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
const departments = <?php echo json_encode($departments, 15, 512) ?>;

function openCreateModal() { document.getElementById('createModal').classList.remove('hidden'); }
function closeCreateModal() { document.getElementById('createModal').classList.add('hidden'); document.getElementById('createForm').reset(); clearErrors('create'); }

function openEditModal(deptId) {
    const dept = departments.find(d => d.id === deptId);
    if (!dept) { alert('Department not found'); return; }
    document.getElementById('edit-dept-id').value    = dept.id;
    document.getElementById('edit-name').value        = dept.name || '';
    document.getElementById('edit-code').value        = dept.code || '';
    document.getElementById('edit-cost-center').value = dept.cost_center || '';
    document.getElementById('edit-passcode').value    = '';
    document.getElementById('editModal').classList.remove('hidden');
}
function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); clearErrors('edit'); }

document.getElementById('createForm').addEventListener('submit', async (e) => {
    e.preventDefault(); clearErrors('create');
    const data = Object.fromEntries(new FormData(e.target));
    try {
        const response = await fetch('/admin/departments', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        if (result.success) { showSuccess(result.message); closeCreateModal(); setTimeout(() => location.reload(), 1000); }
        else if (result.errors) { displayErrors(result.errors, 'create'); }
        else { alert(result.message || 'Failed to create department.'); }
    } catch (error) { alert('An error occurred. Please try again.'); }
});

document.getElementById('editForm').addEventListener('submit', async (e) => {
    e.preventDefault(); clearErrors('edit');
    const deptId = document.getElementById('edit-dept-id').value;
    const data   = Object.fromEntries(new FormData(e.target));
    try {
        const response = await fetch(`/admin/departments/${deptId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        if (result.success) { showSuccess(result.message); closeEditModal(); setTimeout(() => location.reload(), 1000); }
        else if (result.errors) { displayErrors(result.errors, 'edit'); }
        else { alert(result.message || 'Failed to update department.'); }
    } catch (error) { alert('An error occurred. Please try again.'); }
});

async function deleteDepartment(deptId) {
    const dept = departments.find(d => d.id === deptId);
    if (!dept) return;
    if (dept.users && dept.users.length > 0) {
        alert(`Cannot delete "${dept.name}" — it has ${dept.users.length} user(s). Please reassign users first.`);
        return;
    }
    if (!confirm(`Delete "${dept.name}"?\n\nThis action cannot be undone.`)) return;
    try {
        const response = await fetch(`/admin/departments/${deptId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const result = await response.json();
        if (result.success) { showSuccess(result.message); document.getElementById(`dept-row-${deptId}`).remove(); }
        else { alert(result.message); }
    } catch (error) { alert('An error occurred. Please try again.'); }
}

function displayErrors(errors, prefix) {
    for (const [field, messages] of Object.entries(errors)) {
        const el = document.getElementById(`${prefix}-error-${field}`);
        if (el) { el.textContent = Array.isArray(messages) ? messages[0] : messages; el.classList.remove('hidden'); }
    }
}
function clearErrors(prefix) {
    document.querySelectorAll(`[id^="${prefix}-error-"]`).forEach(el => { el.textContent = ''; el.classList.add('hidden'); });
}
function showSuccess(message) {
    const div = document.getElementById('successMessage');
    document.getElementById('successText').textContent = message;
    div.classList.remove('hidden');
    setTimeout(() => div.classList.add('hidden'), 5000);
}
document.querySelectorAll('input[name="code"]').forEach(input => {
    input.addEventListener('input', e => e.target.value = e.target.value.toUpperCase());
});
</script>
<?php $__env->stopPush(); ?>

<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\supply-request-system\resources\views/admin/departments/index.blade.php ENDPATH**/ ?>