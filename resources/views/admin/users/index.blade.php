@extends('layouts.app')

@section('content')

{{-- ── PIN Lock Screen ── --}}
@if(isset($locked) && $locked)
<div id="pinLockScreen" class="fixed inset-0 bg-gray-900 bg-opacity-95 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 text-center">
        <div class="flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mx-auto mb-4">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900 mb-1">User Management</h2>
        <p class="text-sm text-gray-500 mb-6">Enter your PIN to access this page</p>

        <div class="mb-4">
            <input type="password" id="pinInput" placeholder="Enter PIN"
                class="w-full text-center text-lg tracking-widest rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3"
                maxlength="20" autofocus>
            <p id="pinError" class="mt-2 text-xs text-red-600 hidden">Incorrect PIN. Please try again.</p>
        </div>

        <button onclick="submitPin()"
            class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
            Unlock
        </button>

        
    </div>
</div>

@push('scripts')
<script>
async function submitPin() {
    const pin     = document.getElementById('pinInput').value.trim();
    const errorEl = document.getElementById('pinError');
    errorEl.classList.add('hidden');
    if (!pin) return;
    try {
        const response = await fetch('/admin/users/verify-pin', {
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
@endpush

@else

{{-- ── Main Page Content ── --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
            <p class="mt-1 text-sm text-gray-600">Create and manage employee and manager accounts</p>
        </div>
        <button onclick="openCreateModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create New User
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

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="usersTableBody">
                @foreach($users as $user)
                    <tr id="user-row-{{ $user->id }}" class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $user->getRoleBadgeColor() }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $user->department?->name ?? '—' }}</div>
                            <div class="text-xs text-gray-500">{{ $user->department?->code ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <button onclick="openEditModal({{ $user->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                            <button onclick="openPasswordModal({{ $user->id }})" class="text-blue-600 hover:text-blue-900">Password</button>
                            <button onclick="deleteUser({{ $user->id }})" class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Create User Modal -->
<div id="createModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Create New User</h3>
        <form id="createForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" name="name" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="text-xs text-red-600 mt-1 hidden" id="create-error-name"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="text-xs text-red-600 mt-1 hidden" id="create-error-email"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select Role</option>
                    <option value="employee">Employee</option>
                    <option value="manager">Manager</option>
                </select>
                <p class="text-xs text-red-600 mt-1 hidden" id="create-error-role"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select name="department_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select Department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                    @endforeach
                </select>
                <p class="text-xs text-red-600 mt-1 hidden" id="create-error-department_id"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="text-xs text-red-600 mt-1 hidden" id="create-error-password"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700">Create User</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Edit User</h3>
        <form id="editForm" class="space-y-4">
            <input type="hidden" id="edit-user-id">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" id="edit-name" name="name" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="text-xs text-red-600 mt-1 hidden" id="edit-error-name"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="edit-email" name="email" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="text-xs text-red-600 mt-1 hidden" id="edit-error-email"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select id="edit-role" name="role" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="employee">Employee</option>
                    <option value="manager">Manager</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select id="edit-department" name="department_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700">Update User</button>
            </div>
        </form>
    </div>
</div>

<!-- Password Change Modal -->
<div id="passwordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Change Password</h3>
        <form id="passwordForm" class="space-y-4">
            <input type="hidden" id="password-user-id">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input type="password" name="password" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="text-xs text-red-600 mt-1 hidden" id="password-error-password"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closePasswordModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700">Change Password</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const users = @json($users);

function openCreateModal() { document.getElementById('createModal').classList.remove('hidden'); }
function closeCreateModal() { document.getElementById('createModal').classList.add('hidden'); document.getElementById('createForm').reset(); clearErrors('create'); }

function openEditModal(userId) {
    const user = users.find(u => u.id === userId);
    if (!user) return;
    document.getElementById('edit-user-id').value   = user.id;
    document.getElementById('edit-name').value       = user.name;
    document.getElementById('edit-email').value      = user.email;
    document.getElementById('edit-role').value       = user.role;
    document.getElementById('edit-department').value = user.department_id;
    document.getElementById('editModal').classList.remove('hidden');
}
function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); clearErrors('edit'); }

function openPasswordModal(userId) {
    document.getElementById('password-user-id').value = userId;
    document.getElementById('passwordModal').classList.remove('hidden');
}
function closePasswordModal() { document.getElementById('passwordModal').classList.add('hidden'); document.getElementById('passwordForm').reset(); clearErrors('password'); }

document.getElementById('createForm').addEventListener('submit', async (e) => {
    e.preventDefault(); clearErrors('create');
    const data = Object.fromEntries(new FormData(e.target));
    try {
        const response = await fetch('/admin/users', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify(data) });
        const result = await response.json();
        if (result.success) { showSuccess(result.message); closeCreateModal(); location.reload(); }
        else { displayErrors(result.errors, 'create'); }
    } catch (error) { alert('An error occurred. Please try again.'); }
});

document.getElementById('editForm').addEventListener('submit', async (e) => {
    e.preventDefault(); clearErrors('edit');
    const userId = document.getElementById('edit-user-id').value;
    const data   = Object.fromEntries(new FormData(e.target));
    try {
        const response = await fetch(`/admin/users/${userId}`, { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify(data) });
        const result = await response.json();
        if (result.success) { showSuccess(result.message); closeEditModal(); location.reload(); }
        else { displayErrors(result.errors, 'edit'); }
    } catch (error) { alert('An error occurred. Please try again.'); }
});

document.getElementById('passwordForm').addEventListener('submit', async (e) => {
    e.preventDefault(); clearErrors('password');
    const userId = document.getElementById('password-user-id').value;
    const data   = Object.fromEntries(new FormData(e.target));
    try {
        const response = await fetch(`/admin/users/${userId}/password`, { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify(data) });
        const result = await response.json();
        if (result.success) { showSuccess(result.message); closePasswordModal(); }
        else { displayErrors(result.errors, 'password'); }
    } catch (error) { alert('An error occurred. Please try again.'); }
});

async function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) return;
    try {
        const response = await fetch(`/admin/users/${userId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
        const result = await response.json();
        if (result.success) { showSuccess(result.message); document.getElementById(`user-row-${userId}`).remove(); }
        else { alert(result.message); }
    } catch (error) { alert('An error occurred. Please try again.'); }
}

function displayErrors(errors, prefix) {
    for (const [field, messages] of Object.entries(errors)) {
        const el = document.getElementById(`${prefix}-error-${field}`);
        if (el) { el.textContent = Array.isArray(messages) ? messages[0] : messages; el.classList.remove('hidden'); }
    }
}
function clearErrors(prefix) { document.querySelectorAll(`[id^="${prefix}-error-"]`).forEach(el => { el.textContent = ''; el.classList.add('hidden'); }); }
function showSuccess(message) {
    const div = document.getElementById('successMessage');
    document.getElementById('successText').textContent = message;
    div.classList.remove('hidden');
    setTimeout(() => div.classList.add('hidden'), 5000);
}
</script>
@endpush

@endif
@endsection